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
