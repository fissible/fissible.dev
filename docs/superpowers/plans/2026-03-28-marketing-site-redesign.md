# Marketing Site Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Rewrite fissible.dev to position Station as the primary product with clear audience sequencing (decision-maker → developer → both).

**Architecture:** In-place Astro refactor on a feature branch. New homepage with 6 sections, simplified nav, tool pages moved under `/tools/`, 301 redirects via `vercel.json`. No new frameworks or dependencies.

**Tech Stack:** Astro 5.1.5, vanilla CSS, vanilla JS (IntersectionObserver for scroll animation), Formspree, Vercel

**Spec:** `docs/superpowers/specs/2026-03-28-marketing-site-redesign-design.md`

---

## File Structure

### Files to Create
- `apps/site/src/pages/tools/index.astro` — ecosystem index page
- `apps/site/src/pages/tools/[slug].astro` — dynamic route for OSS tool pages under `/tools/`
- `apps/site/src/components/Hero.astro` — homepage hero section
- `apps/site/src/components/WorkflowAnimation.astro` — scroll-triggered workflow section
- `apps/site/src/components/UseCase.astro` — use case section
- `apps/site/src/components/TechCredibility.astro` — technical credibility section
- `apps/site/src/components/Ecosystem.astro` — ecosystem preview section
- `apps/site/src/components/ConvergeCTA.astro` — final CTA section with waitlist form
- `vercel.json` — 301 redirects from old tool routes

### Files to Modify
- `apps/site/src/components/Nav.astro` — replace dropdowns with flat nav
- `apps/site/src/pages/index.astro` — replace with 6-section homepage
- `apps/site/src/styles/global.css` — evolved type scale, spacing, intentional color
- `apps/site/src/data/packages.ts` — add `formspreeId` to PaidProduct interface
- `apps/site/src/components/PackageCard.astro` — update default href to `/tools/` prefix

### Files to Delete
- `apps/site/src/pages/accord/index.astro`
- `apps/site/src/pages/drift/index.astro`
- `apps/site/src/pages/forge/index.astro`
- `apps/site/src/pages/seed/index.astro`
- `apps/site/src/pages/shellframe/index.astro`
- `apps/site/src/pages/ptyunit/index.astro`
- `apps/site/src/pages/shellql/index.astro`
- `apps/site/src/pages/watch/index.astro`
- `apps/site/src/pages/fault/index.astro`

---

## Task 1: Create feature branch

**Files:**
- None (git operation only)

- [ ] **Step 1: Create and switch to feature branch**

```bash
cd /Users/allenmccabe/lib/fissible/fissible.dev
git checkout -b feat/marketing-redesign
```

- [ ] **Step 2: Verify branch**

```bash
git branch --show-current
```

Expected: `feat/marketing-redesign`

---

## Task 2: Rewrite Nav.astro (flat, 4 items)

**Files:**
- Modify: `apps/site/src/components/Nav.astro`

- [ ] **Step 1: Replace Nav.astro with flat navigation**

Replace the entire contents of `apps/site/src/components/Nav.astro` with:

```astro
---
// apps/site/src/components/Nav.astro
---
<nav class="nav">
  <a href="/" class="nav-logo">fissible</a>
  <div class="nav-links">
    <a href="/station" class="nav-link">Station</a>
    <a href="/tools" class="nav-link">Tools</a>
    <a href="https://docs.fissible.dev" class="nav-link">Docs</a>
    <a
      href="https://github.com/fissible"
      target="_blank"
      rel="noopener noreferrer"
      class="nav-link"
    >GitHub</a>
  </div>
</nav>
```

- [ ] **Step 2: Update nav CSS in global.css**

In `apps/site/src/styles/global.css`, replace the entire `/* Nav */` section (from `.nav {` through `.nav-github:hover { color: var(--text); }`) with:

```css
/* Nav */
.nav {
  display: flex;
  align-items: center;
  gap: 2rem;
  padding: 0 2rem;
  height: 56px;
  border-bottom: 1px solid var(--border);
  background: var(--bg);
  position: sticky;
  top: 0;
  z-index: 100;
}

.nav-logo {
  font-weight: 600;
  font-size: 1.1rem;
  color: var(--text);
  letter-spacing: -0.02em;
}

.nav-links { display: flex; align-items: center; gap: 0.25rem; margin-left: auto; }

.nav-link {
  color: var(--text-muted);
  font-size: 0.9rem;
  padding: 0.4rem 0.75rem;
  border-radius: var(--radius);
  transition: color 0.15s, background 0.15s;
}
.nav-link:hover { color: var(--text); background: var(--bg-elevated); }
```

- [ ] **Step 3: Run dev server and verify nav renders**

```bash
cd /Users/allenmccabe/lib/fissible/fissible.dev
npm run dev:site
```

Verify in browser: nav shows `fissible | Station Tools Docs GitHub` — no dropdowns. Station links to `/station`, Tools links to `/tools`.

- [ ] **Step 4: Commit**

```bash
git add apps/site/src/components/Nav.astro apps/site/src/styles/global.css
git commit -m "feat: replace dropdown nav with flat 4-item navigation

Station, Tools, Docs, GitHub — no dropdowns."
```

---

## Task 3: Create homepage section components

**Files:**
- Create: `apps/site/src/components/Hero.astro`
- Create: `apps/site/src/components/WorkflowAnimation.astro`
- Create: `apps/site/src/components/UseCase.astro`
- Create: `apps/site/src/components/TechCredibility.astro`
- Create: `apps/site/src/components/Ecosystem.astro`
- Create: `apps/site/src/components/ConvergeCTA.astro`

- [ ] **Step 1: Create Hero.astro**

Create `apps/site/src/components/Hero.astro`:

```astro
---
// apps/site/src/components/Hero.astro
---
<section class="hero">
  <p class="hero-brand">fissible</p>
  <h1 class="hero-headline">Stop accidental publishing.<br>Ship APIs that don't break.</h1>
  <p class="hero-clarifying">Approval workflows and API contract enforcement that prevent mistakes before they go live.</p>
  <p class="hero-descriptor">Self-hosted CMS and API platform. One-time license.</p>
  <div class="hero-cta">
    <a href="/station" class="btn-primary">Join the Waitlist</a>
    <a href="#workflow" class="btn-secondary">See How It Works ↓</a>
  </div>
</section>

<style>
.hero {
  text-align: center;
  padding: 5rem 2rem 4rem;
}
.hero-brand {
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.15em;
  color: var(--accent);
  margin-bottom: 1rem;
}
.hero-headline {
  font-size: clamp(1.75rem, 4vw, 2.5rem);
  font-weight: 800;
  line-height: 1.15;
  letter-spacing: -0.03em;
  margin-bottom: 1rem;
}
.hero-clarifying {
  font-size: 1rem;
  color: var(--text-muted);
  max-width: 420px;
  margin: 0 auto 0.5rem;
  line-height: 1.5;
}
.hero-descriptor {
  font-size: 0.9rem;
  color: var(--text-muted);
  opacity: 0.7;
  margin-bottom: 2rem;
}
.hero-cta {
  display: flex;
  gap: 0.75rem;
  justify-content: center;
  flex-wrap: wrap;
}
</style>
```

- [ ] **Step 2: Create WorkflowAnimation.astro (static first, animation in Task 6)**

Create `apps/site/src/components/WorkflowAnimation.astro`:

```astro
---
// apps/site/src/components/WorkflowAnimation.astro
const steps = [
  { label: 'Draft', desc: 'Write safely', accent: false },
  { label: 'Submit', desc: 'Request review', accent: false },
  { label: 'Approve', desc: 'Must sign off', accent: true },
  { label: 'Publish', desc: 'Goes live', accent: false },
  { label: 'Audit Log', desc: 'Everything tracked', accent: false },
];
---
<section class="workflow" id="workflow">
  <h2 class="workflow-heading">Nothing goes live without approval</h2>
  <p class="workflow-sub">Every step is enforced. Every action is logged.</p>
  <div class="workflow-steps">
    {steps.map((step, i) => (
      <>
        <div class:list={['workflow-step', { 'workflow-step--accent': step.accent }]}>
          {step.accent && <span class="workflow-check">✓</span>}
          <span class="workflow-label">{step.label}</span>
          <span class="workflow-desc">{step.desc}</span>
        </div>
        {i < steps.length - 1 && <span class="workflow-arrow">→</span>}
      </>
    ))}
  </div>
</section>

<style>
.workflow {
  padding: 3.5rem 2rem;
  text-align: center;
  background: var(--bg-subtle);
}
.workflow-heading {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 0.25rem;
}
.workflow-sub {
  font-size: 0.8rem;
  color: var(--text-muted);
  margin-bottom: 2rem;
}
.workflow-steps {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}
.workflow-step {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 0.15rem;
  background: var(--bg-elevated);
  border: 1px solid var(--border);
  padding: 0.75rem 1rem;
  border-radius: 8px;
  min-width: 80px;
}
.workflow-step--accent {
  border: 2px solid var(--accent);
  background: color-mix(in srgb, var(--accent) 5%, var(--bg-elevated));
  box-shadow: 0 0 20px rgba(125,211,252,0.15);
  padding: 0.85rem 1.1rem;
}
.workflow-check {
  color: var(--accent);
  font-size: 0.7rem;
  font-weight: 700;
}
.workflow-label {
  font-size: 0.875rem;
  font-weight: 600;
}
.workflow-step--accent .workflow-label {
  color: var(--accent);
  font-size: 0.95rem;
  font-weight: 800;
}
.workflow-desc {
  font-size: 0.7rem;
  color: var(--text-muted);
}
.workflow-step--accent .workflow-desc {
  color: var(--text-muted);
  opacity: 0.9;
}
.workflow-arrow {
  color: var(--border);
  font-size: 1rem;
}
@media (max-width: 600px) {
  .workflow-steps { flex-direction: column; }
  .workflow-arrow { transform: rotate(90deg); }
}
</style>
```

- [ ] **Step 3: Create UseCase.astro**

Create `apps/site/src/components/UseCase.astro`:

```astro
---
// apps/site/src/components/UseCase.astro
---
<section class="usecase">
  <div class="usecase-inner">
    <h2 class="usecase-heading">Built for teams that can't afford mistakes</h2>
    <div class="usecase-callout">
      <p>Financial advisor publishes content</p>
      <p>→ must be approved by compliance</p>
      <p>→ nothing goes live early</p>
      <p>→ every change is tracked</p>
    </div>
    <div class="usecase-meta">
      <div class="usecase-meta-item">
        <span class="usecase-meta-label">Who</span>
        <span class="usecase-meta-value">Teams with approval requirements</span>
      </div>
      <div class="usecase-meta-item">
        <span class="usecase-meta-label">Why</span>
        <span class="usecase-meta-value">Compliance, legal, editorial control</span>
      </div>
      <div class="usecase-meta-item">
        <span class="usecase-meta-label">How</span>
        <span class="usecase-meta-value">Self-hosted, your infrastructure</span>
      </div>
    </div>
  </div>
</section>

<style>
.usecase {
  padding: 3.5rem 2rem;
}
.usecase-inner {
  max-width: 540px;
  margin: 0 auto;
}
.usecase-heading {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 1.25rem;
}
.usecase-callout {
  background: var(--bg-subtle);
  border-left: 3px solid var(--accent);
  padding: 1.25rem 1.5rem;
  border-radius: 4px;
  margin-bottom: 1.5rem;
}
.usecase-callout p {
  font-size: 0.9rem;
  color: var(--text);
  line-height: 1.8;
}
.usecase-meta {
  display: flex;
  gap: 1.5rem;
}
.usecase-meta-item {
  flex: 1;
}
.usecase-meta-label {
  display: block;
  font-size: 0.7rem;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--accent);
  margin-bottom: 0.25rem;
}
.usecase-meta-value {
  font-size: 0.8rem;
  color: var(--text-muted);
}
@media (max-width: 500px) {
  .usecase-meta { flex-direction: column; gap: 0.75rem; }
}
</style>
```

- [ ] **Step 4: Create TechCredibility.astro**

Create `apps/site/src/components/TechCredibility.astro`:

```astro
---
// apps/site/src/components/TechCredibility.astro
const cards = [
  { title: 'Self-Hosted', benefit: 'Your data stays under your control' },
  { title: 'Laravel', benefit: 'Built on a framework your team already knows' },
  { title: 'API Contracts', benefit: 'Prevent breaking changes before deployment' },
];
---
<section class="techcred">
  <h2 class="techcred-heading">Built for developers who ship carefully</h2>
  <div class="techcred-grid">
    {cards.map(c => (
      <div class="techcred-card">
        <h3>{c.title}</h3>
        <p>{c.benefit}</p>
      </div>
    ))}
  </div>
</section>

<style>
.techcred {
  padding: 3.5rem 2rem;
  background: var(--bg-subtle);
  text-align: center;
}
.techcred-heading {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 1.5rem;
}
.techcred-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 1rem;
  max-width: 580px;
  margin: 0 auto;
}
.techcred-card {
  background: var(--bg-elevated);
  border: 1px solid var(--border);
  padding: 1.25rem 1rem;
  border-radius: 8px;
  text-align: left;
}
.techcred-card h3 {
  font-size: 0.9rem;
  font-weight: 700;
  margin-bottom: 0.4rem;
}
.techcred-card p {
  font-size: 0.8rem;
  color: var(--text-muted);
  line-height: 1.5;
}
@media (max-width: 600px) {
  .techcred-grid { grid-template-columns: 1fr; }
}
</style>
```

- [ ] **Step 5: Create Ecosystem.astro**

Create `apps/site/src/components/Ecosystem.astro`:

```astro
---
// apps/site/src/components/Ecosystem.astro
const tools = [
  { name: 'accord', desc: 'OpenAPI contract validator', url: 'https://github.com/fissible/accord' },
  { name: 'drift', desc: 'API drift detection', url: 'https://github.com/fissible/drift' },
  { name: 'forge', desc: 'OpenAPI spec scaffolding', url: 'https://github.com/fissible/forge' },
];
---
<section class="ecosystem">
  <h2 class="ecosystem-heading">Built on open-source tools</h2>
  <p class="ecosystem-sub">Station's API layer is powered by tools you can use independently.</p>
  <div class="ecosystem-tools">
    {tools.map(t => (
      <a href={t.url} target="_blank" rel="noopener noreferrer" class="ecosystem-tool">
        <span class="ecosystem-tool-name">{t.name}</span>
        <span class="ecosystem-tool-desc">{t.desc}</span>
      </a>
    ))}
  </div>
  <a href="/tools" class="ecosystem-link">Explore all tools →</a>
</section>

<style>
.ecosystem {
  padding: 3rem 2rem;
  text-align: center;
}
.ecosystem-heading {
  font-size: 1.25rem;
  font-weight: 700;
  margin-bottom: 0.25rem;
}
.ecosystem-sub {
  font-size: 0.8rem;
  color: var(--text-muted);
  margin-bottom: 1.5rem;
}
.ecosystem-tools {
  display: flex;
  gap: 0.75rem;
  justify-content: center;
  flex-wrap: wrap;
}
.ecosystem-tool {
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
  background: var(--bg-elevated);
  border: 1px solid var(--border);
  padding: 0.75rem 1.25rem;
  border-radius: 8px;
  color: var(--text);
  transition: border-color 0.15s;
}
.ecosystem-tool:hover { border-color: var(--accent); color: var(--text); }
.ecosystem-tool-name {
  font-size: 0.875rem;
  font-weight: 600;
}
.ecosystem-tool-desc {
  font-size: 0.75rem;
  color: var(--text-muted);
}
.ecosystem-link {
  display: inline-block;
  margin-top: 1.25rem;
  font-size: 0.875rem;
  color: var(--accent);
}
.ecosystem-link:hover { color: var(--accent-hover); }
</style>
```

- [ ] **Step 6: Create ConvergeCTA.astro**

Create `apps/site/src/components/ConvergeCTA.astro`:

```astro
---
// apps/site/src/components/ConvergeCTA.astro
---
<section class="converge-cta">
  <h2 class="converge-heading">Put approval between your team and production.</h2>
  <p class="converge-sub">Station is launching soon. Be the first to try it.</p>
  <div class="converge-actions">
    <a href="/station" class="btn-primary">Join the Waitlist</a>
    <a href="/tools" class="btn-secondary">Explore Tools</a>
  </div>
</section>

<style>
.converge-cta {
  padding: 4rem 2rem;
  text-align: center;
}
.converge-heading {
  font-size: 1.35rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}
.converge-sub {
  font-size: 0.9rem;
  color: var(--text-muted);
  margin-bottom: 2rem;
}
.converge-actions {
  display: flex;
  gap: 0.75rem;
  justify-content: center;
  flex-wrap: wrap;
}
</style>
```

- [ ] **Step 7: Commit all section components**

```bash
git add apps/site/src/components/Hero.astro \
  apps/site/src/components/WorkflowAnimation.astro \
  apps/site/src/components/UseCase.astro \
  apps/site/src/components/TechCredibility.astro \
  apps/site/src/components/Ecosystem.astro \
  apps/site/src/components/ConvergeCTA.astro
git commit -m "feat: add homepage section components

Hero, WorkflowAnimation, UseCase, TechCredibility, Ecosystem, ConvergeCTA.
Workflow animation is static layout — scroll trigger added in Task 6."
```

---

## Task 4: Rewrite homepage to use section components

**Files:**
- Modify: `apps/site/src/pages/index.astro`
- Modify: `apps/site/src/layouts/Layout.astro` (update default description)

- [ ] **Step 1: Update default meta description in Layout.astro**

In `apps/site/src/layouts/Layout.astro`, change the default description:

```typescript
const {
  title,
  description = 'Self-hosted CMS and API platform with enforced approvals and contract validation.',
} = Astro.props;
```

- [ ] **Step 2: Replace index.astro with section-based homepage**

Replace the entire contents of `apps/site/src/pages/index.astro` with:

```astro
---
// apps/site/src/pages/index.astro
import Layout from '../layouts/Layout.astro';
import Hero from '../components/Hero.astro';
import WorkflowAnimation from '../components/WorkflowAnimation.astro';
import UseCase from '../components/UseCase.astro';
import TechCredibility from '../components/TechCredibility.astro';
import Ecosystem from '../components/Ecosystem.astro';
import ConvergeCTA from '../components/ConvergeCTA.astro';
---
<Layout title="fissible">
  <main>
    <Hero />
    <WorkflowAnimation />
    <UseCase />
    <TechCredibility />
    <Ecosystem />
    <ConvergeCTA />
  </main>
</Layout>
```

- [ ] **Step 3: Verify in browser**

```bash
npm run dev:site
```

Verify: homepage shows 6 sections in order. Hero headline is prominent. Workflow shows 5 steps with Approve highlighted. Use case shows 4 scannable lines. Tech cards show 3 benefits. Ecosystem shows accord/drift/forge. CTA at bottom.

- [ ] **Step 4: Commit**

```bash
git add apps/site/src/pages/index.astro apps/site/src/layouts/Layout.astro
git commit -m "feat: rewrite homepage with 6-section Station-led layout

Decision-maker sections 1-3, developer sections 4-5, converge section 6."
```

---

## Task 5: Evolve typography and spacing system

**Files:**
- Modify: `apps/site/src/styles/global.css`

- [ ] **Step 1: Add type scale and spacing variables to :root**

In `apps/site/src/styles/global.css`, add these variables inside the existing `:root` block, after the existing variables:

```css
  /* Type scale */
  --text-xs: 0.7rem;
  --text-sm: 0.8rem;
  --text-base: 0.9rem;
  --text-lg: 1rem;
  --text-xl: 1.25rem;
  --text-2xl: 1.35rem;
  --text-hero: clamp(1.75rem, 4vw, 2.5rem);

  /* Spacing scale */
  --space-section: 3.5rem;
  --space-section-lg: 5rem;
```

- [ ] **Step 2: Verify the site still builds**

```bash
cd /Users/allenmccabe/lib/fissible/fissible.dev
npm run build --workspace=apps/site
```

Expected: build succeeds with no errors.

- [ ] **Step 3: Commit**

```bash
git add apps/site/src/styles/global.css
git commit -m "feat: add type scale and spacing variables to design system"
```

---

## Task 6: Add scroll-triggered workflow animation

**Files:**
- Modify: `apps/site/src/components/WorkflowAnimation.astro`

- [ ] **Step 1: Add animation CSS and IntersectionObserver script**

In `apps/site/src/components/WorkflowAnimation.astro`, add the following inside the existing `<style>` block, before the closing `</style>` tag:

```css
/* Animation */
.workflow-step,
.workflow-arrow {
  opacity: 0;
  transform: translateY(8px);
  transition: opacity 0.25s ease, transform 0.25s ease;
}
.workflow-steps.animate .workflow-step,
.workflow-steps.animate .workflow-arrow {
  opacity: 1;
  transform: translateY(0);
}
.workflow-steps.animate .workflow-step:nth-child(1) { transition-delay: 0ms; }
.workflow-steps.animate .workflow-arrow:nth-child(2) { transition-delay: 100ms; }
.workflow-steps.animate .workflow-step:nth-child(3) { transition-delay: 200ms; }
.workflow-steps.animate .workflow-arrow:nth-child(4) { transition-delay: 300ms; }
.workflow-steps.animate .workflow-step:nth-child(5) { transition-delay: 400ms; }
.workflow-steps.animate .workflow-arrow:nth-child(6) { transition-delay: 500ms; }
.workflow-steps.animate .workflow-step:nth-child(7) { transition-delay: 600ms; }
.workflow-steps.animate .workflow-arrow:nth-child(8) { transition-delay: 700ms; }
.workflow-steps.animate .workflow-step:nth-child(9) { transition-delay: 800ms; }
```

Then add the following script after the closing `</style>` tag:

```html
<script>
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
</script>
```

- [ ] **Step 2: Verify animation in browser**

```bash
npm run dev:site
```

Scroll down to the workflow section. Steps should fade in sequentially (left to right) as the section enters the viewport. Approve step should be visually dominant. Animation fires once and doesn't replay.

- [ ] **Step 3: Commit**

```bash
git add apps/site/src/components/WorkflowAnimation.astro
git commit -m "feat: add scroll-triggered animation to workflow section

Steps fade in sequentially via IntersectionObserver + CSS transitions."
```

---

## Task 7: Create /tools/ index page and dynamic route

**Files:**
- Create: `apps/site/src/pages/tools/index.astro`
- Create: `apps/site/src/pages/tools/[slug].astro`

- [ ] **Step 1: Create /tools/ index page**

Create `apps/site/src/pages/tools/index.astro`:

```astro
---
// apps/site/src/pages/tools/index.astro
import Layout from '../../layouts/Layout.astro';
import { phpPackages, tuiPackages } from '../../data/packages';
---
<Layout title="Tools" description="Open-source developer tools from fissible — API validation, TUI frameworks, and CLI utilities.">
  <main class="tools-index">
    <header class="tools-header">
      <h1>Tools</h1>
      <p>Open-source developer tools you can use independently or alongside Station.</p>
    </header>

    <section class="tools-group">
      <h2>API Tools</h2>
      <div class="tools-list">
        {phpPackages.map(p => (
          <div class="tool-entry">
            <div class="tool-entry-info">
              <a href={`/tools/${p.slug}`} class="tool-entry-name">{p.name}</a>
              <span class="tool-entry-tagline">{p.tagline}</span>
            </div>
            <a href={p.githubUrl} target="_blank" rel="noopener noreferrer" class="tool-entry-github">GitHub ↗</a>
          </div>
        ))}
      </div>
    </section>

    <section class="tools-group">
      <h2>CLI / TUI Tools</h2>
      <div class="tools-list">
        {tuiPackages.map(p => (
          <div class="tool-entry">
            <div class="tool-entry-info">
              <a href={`/tools/${p.slug}`} class="tool-entry-name">{p.name}</a>
              <span class="tool-entry-tagline">{p.tagline}</span>
            </div>
            <a href={p.githubUrl} target="_blank" rel="noopener noreferrer" class="tool-entry-github">GitHub ↗</a>
          </div>
        ))}
      </div>
    </section>
  </main>
</Layout>

<style>
.tools-index { max-width: var(--max-w); margin: 0 auto; padding: 3rem 2rem; }
.tools-header { margin-bottom: 3rem; }
.tools-header h1 { font-size: 2rem; font-weight: 700; letter-spacing: -0.03em; margin-bottom: 0.5rem; }
.tools-header p { color: var(--text-muted); font-size: 1rem; }
.tools-group { margin-bottom: 2.5rem; }
.tools-group h2 {
  font-size: 0.75rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  color: var(--text-muted);
  margin-bottom: 1rem;
}
.tools-list { display: flex; flex-direction: column; gap: 0.5rem; }
.tool-entry {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0.875rem 1.25rem;
  background: var(--bg-subtle);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  transition: border-color 0.15s;
}
.tool-entry:hover { border-color: var(--text-muted); }
.tool-entry-info { display: flex; align-items: baseline; gap: 0.75rem; }
.tool-entry-name { font-weight: 600; font-size: 0.95rem; color: var(--text); }
.tool-entry-name:hover { color: var(--accent); }
.tool-entry-tagline { font-size: 0.85rem; color: var(--text-muted); }
.tool-entry-github { font-size: 0.8rem; color: var(--text-muted); white-space: nowrap; }
.tool-entry-github:hover { color: var(--accent); }
@media (max-width: 600px) {
  .tool-entry { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
}
</style>
```

- [ ] **Step 2: Create dynamic route for individual tool pages**

Create `apps/site/src/pages/tools/[slug].astro`:

```astro
---
// apps/site/src/pages/tools/[slug].astro
import Layout from '../../layouts/Layout.astro';
import MarketingPage from '../../components/MarketingPage.astro';
import { allOssPackages } from '../../data/packages';
import type { GetStaticPaths } from 'astro';

export const getStaticPaths: GetStaticPaths = () => {
  return allOssPackages.map(pkg => ({
    params: { slug: pkg.slug },
    props: { pkg },
  }));
};

const { pkg } = Astro.props;
---
<Layout title={pkg.name} description={pkg.tagline}>
  <MarketingPage pkg={pkg} />
</Layout>
```

- [ ] **Step 3: Verify /tools/ and /tools/accord/ render**

```bash
npm run dev:site
```

Verify:
- `/tools` shows grouped list with API Tools and CLI/TUI Tools sections
- `/tools/accord` shows the accord marketing page
- Each tool entry links to `/tools/{slug}` and has a GitHub ↗ link

- [ ] **Step 4: Commit**

```bash
git add apps/site/src/pages/tools/index.astro apps/site/src/pages/tools/\[slug\].astro
git commit -m "feat: add /tools/ index page and dynamic tool routes

Tools grouped by API and CLI/TUI. Individual pages at /tools/{slug}."
```

---

## Task 8: Remove old top-level tool pages and update PackageCard

**Files:**
- Delete: `apps/site/src/pages/accord/index.astro` (and all 8 other tool page dirs)
- Modify: `apps/site/src/components/PackageCard.astro`

- [ ] **Step 1: Delete old tool page directories**

```bash
cd /Users/allenmccabe/lib/fissible/fissible.dev
rm -rf apps/site/src/pages/accord \
  apps/site/src/pages/drift \
  apps/site/src/pages/forge \
  apps/site/src/pages/seed \
  apps/site/src/pages/shellframe \
  apps/site/src/pages/ptyunit \
  apps/site/src/pages/shellql \
  apps/site/src/pages/watch \
  apps/site/src/pages/fault
```

- [ ] **Step 2: Update PackageCard default href**

In `apps/site/src/components/PackageCard.astro`, change the default href:

```astro
const { pkg, href = `/tools/${pkg.slug}` } = Astro.props;
```

- [ ] **Step 3: Verify old routes are gone, new routes work**

```bash
npm run dev:site
```

Verify:
- `/accord` returns 404
- `/tools/accord` renders correctly
- PackageCard links (if used anywhere) point to `/tools/` prefix

- [ ] **Step 4: Commit**

```bash
git add -A apps/site/src/pages/accord apps/site/src/pages/drift \
  apps/site/src/pages/forge apps/site/src/pages/seed \
  apps/site/src/pages/shellframe apps/site/src/pages/ptyunit \
  apps/site/src/pages/shellql apps/site/src/pages/watch \
  apps/site/src/pages/fault apps/site/src/components/PackageCard.astro
git commit -m "feat: remove old top-level tool routes, update PackageCard href

Tool pages now live exclusively at /tools/{slug}."
```

---

## Task 9: Add 301 redirects via vercel.json

**Files:**
- Create: `vercel.json` (project root)

- [ ] **Step 1: Create vercel.json with redirect rules**

Create `vercel.json` at the project root:

```json
{
  "redirects": [
    { "source": "/accord", "destination": "/tools/accord", "permanent": true },
    { "source": "/drift", "destination": "/tools/drift", "permanent": true },
    { "source": "/forge", "destination": "/tools/forge", "permanent": true },
    { "source": "/seed", "destination": "/tools/seed", "permanent": true },
    { "source": "/shellframe", "destination": "/tools/shellframe", "permanent": true },
    { "source": "/ptyunit", "destination": "/tools/ptyunit", "permanent": true },
    { "source": "/shellql", "destination": "/tools/shellql", "permanent": true },
    { "source": "/watch", "destination": "/tools/watch", "permanent": true },
    { "source": "/fault", "destination": "/tools/fault", "permanent": true }
  ]
}
```

- [ ] **Step 2: Verify build still succeeds**

```bash
cd /Users/allenmccabe/lib/fissible/fissible.dev
npm run build --workspace=apps/site
```

Expected: build succeeds. Redirects will be handled by Vercel at the edge, not at build time.

- [ ] **Step 3: Commit**

```bash
git add vercel.json
git commit -m "feat: add 301 redirects from old tool routes to /tools/*

Preserves SEO and existing links after route migration."
```

---

## Task 10: Update packages.ts data and add formspreeId to PaidProduct

**Files:**
- Modify: `apps/site/src/data/packages.ts`

- [ ] **Step 1: Add formspreeId to PaidProduct interface and data**

In `apps/site/src/data/packages.ts`, update the `PaidProduct` interface:

```typescript
export interface PaidProduct {
  slug: string;
  name: string;
  tagline: string;
  description: string;
  status: 'coming-soon' | 'purchase-pending';
  formspreeId: string;
}
```

Then update the `paidProducts` array to include `formspreeId` on each item:

```typescript
export const paidProducts: PaidProduct[] = [
  {
    slug: 'guit',
    name: 'guit',
    tagline: 'A terminal git client',
    description: 'A keyboard-driven terminal git client built on shellframe. Working copy, history, cherry-pick, branch graph, and a 3-pane merge resolver that registers as git mergetool.',
    status: 'coming-soon',
    formspreeId: 'xzdkzjyn',
  },
  {
    slug: 'sigil',
    name: 'sigil',
    tagline: 'Developer credential and connection broker',
    description: 'A local-first CLI that stores secrets, database connections, SSH profiles, and API tokens — with a pipeline-composable interface and OS keychain backend. Built in Rust.',
    status: 'purchase-pending',
    formspreeId: 'xeepornj',
  },
  {
    slug: 'station',
    name: 'station',
    tagline: 'A self-hosted Laravel CMS platform',
    description: 'Schema-driven content types, a Draft→Review→Published approval workflow, and a browser-based installer. One-time per-site license.',
    status: 'coming-soon',
    formspreeId: 'xzeobzpo',
  },
  {
    slug: 'conduit',
    name: 'conduit',
    tagline: 'A terminal HTTP client',
    description: 'Request builder, collections, response viewer, and sigil credential integration in the free tier. Paid tier adds accord/drift contract validation in-terminal.',
    status: 'coming-soon',
    formspreeId: 'xzwrlzyq',
  },
];
```

- [ ] **Step 2: Update paid product pages to use centralized formspreeId**

Update `apps/site/src/pages/station/index.astro` to pull formspreeId from data:

```astro
---
import Layout from '../../layouts/Layout.astro';
import ComingSoonPage from '../../components/ComingSoonPage.astro';
import { paidProducts } from '../../data/packages';
const product = paidProducts.find(p => p.slug === 'station')!;
---
<Layout title={product.name} description={product.tagline}>
  <ComingSoonPage product={product} formspreeId={product.formspreeId} />
</Layout>
```

Apply the same pattern to `guit/index.astro`, `sigil/index.astro`, and `conduit/index.astro` (changing the slug in the `find()` call accordingly). For sigil, keep the existing `statusNote` prop.

- [ ] **Step 3: Run existing tests**

```bash
cd /Users/allenmccabe/lib/fissible/fissible.dev
npm test
```

Expected: all tests pass (the existing `packages.test.ts` should still work).

- [ ] **Step 4: Commit**

```bash
git add apps/site/src/data/packages.ts \
  apps/site/src/pages/station/index.astro \
  apps/site/src/pages/guit/index.astro \
  apps/site/src/pages/sigil/index.astro \
  apps/site/src/pages/conduit/index.astro
git commit -m "feat: centralize formspreeId in packages.ts data layer

Paid product pages now pull formspreeId from the data file."
```

---

## Task 11: Full build verification and polish

**Files:**
- Various (verification, no new files)

- [ ] **Step 1: Full build**

```bash
cd /Users/allenmccabe/lib/fissible/fissible.dev
npm run build
```

Expected: both site and docs build successfully.

- [ ] **Step 2: Verify all routes**

```bash
npm run dev:site
```

Check in browser:
- `/` — 6-section homepage, correct headline, workflow animation fires on scroll
- `/station` — waitlist page renders
- `/tools` — grouped index with all 9 tools
- `/tools/accord` — individual tool page
- `/tools/seed` — individual tool page
- `/guit` — coming soon page
- `/sigil` — coming soon page with status note
- `/conduit` — coming soon page

- [ ] **Step 3: Verify nav links**

- `Station` → `/station`
- `Tools` → `/tools`
- `Docs` → `https://docs.fissible.dev`
- `GitHub` → `https://github.com/fissible`

- [ ] **Step 4: Verify deep links**

Check that ecosystem section tool cards link to individual GitHub repos:
- accord → `https://github.com/fissible/accord`
- drift → `https://github.com/fissible/drift`
- forge → `https://github.com/fissible/forge`

Check that /tools/ index GitHub ↗ links go to individual repos (not org profile).

- [ ] **Step 5: Mobile check**

Resize browser to 375px width. Verify:
- Nav is usable (may need hamburger — if items overflow, flag for fix)
- Workflow steps stack vertically
- Use case meta stacks vertically
- Tech cards stack to single column
- All text is readable

- [ ] **Step 6: Commit any polish fixes**

```bash
git add -A
git commit -m "fix: polish pass — verify all routes, links, and responsive layout"
```

---

## Checkpoint Summary

| Checkpoint | Tasks | Shippable? |
|------------|-------|------------|
| 1. Nav + Homepage | Tasks 1–4 | Yes — new homepage with static workflow |
| 2. Typography | Task 5 | Yes — refined spacing/type |
| 3. Route Migration | Tasks 7–9 | Yes — tools at new routes with redirects |
| 4. Animation | Task 6 | Yes — scroll-triggered workflow |
| 5. Data + Polish | Tasks 10–11 | Yes — centralized data, full verification |
