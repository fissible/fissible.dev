// Workflow animation — Intersection Observer
const steps = document.querySelector('.workflow-steps');
if (steps) {
    const observer = new IntersectionObserver(
        ([entry]) => {
            if (entry.isIntersecting) {
                steps.classList.add('animate');
                observer.disconnect();
            }
        },
        { threshold: 0.3 }
    );
    observer.observe(steps);
}

// Mobile nav — hamburger toggle
const navToggle = document.querySelector('.nav-toggle');
const navMenu = document.getElementById('nav-menu');
if (navToggle && navMenu) {
    const setNavOpen = (open) => {
        navMenu.classList.toggle('open', open);
        navToggle.setAttribute('aria-expanded', String(open));
    };
    navToggle.addEventListener('click', (event) => {
        event.stopPropagation();
        setNavOpen(navToggle.getAttribute('aria-expanded') !== 'true');
    });
    navMenu.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => setNavOpen(false));
    });
    document.addEventListener('click', (event) => {
        if (
            navMenu.classList.contains('open') &&
            !navMenu.contains(event.target) &&
            !navToggle.contains(event.target)
        ) {
            setNavOpen(false);
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') setNavOpen(false);
    });
}
