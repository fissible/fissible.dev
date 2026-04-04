# ShellQL Page Redesign Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the generic MarketingPage template for /tools/shellql with a dedicated custom layout (ShellQLPage.astro) that leads with screenshots, surfaces the SSH differentiator above the fold, and converts HN scanners in ~10 seconds.

**Architecture:** A new `ShellQLPage.astro` component contains all shellql-specific layout and hardcoded content. The `[slug].astro` dynamic route detects `pkg.slug === 'shellql'` and renders `ShellQLPage` instead of `MarketingPage`. `packages.ts` and `MarketingPage.astro` are not modified.

**Tech Stack:** Astro (static site), TypeScript, CSS custom properties (var(--accent), var(--text-muted), etc. — defined in `src/styles/global.css`)

---

## File Map

| File | Action | Purpose |
|------|--------|---------|
| `apps/site/src/components/ShellQLPage.astro` | Create | Custom full-page layout for shellql |
| `apps/site/src/pages/tools/[slug].astro` | Modify | Add conditional: shellql → ShellQLPage |

**Nothing else changes.** `packages.ts`, `MarketingPage.astro`, and all other tool pages are untouched.

---

## CSS Custom Properties Reference

These variables are defined in `apps/site/src/styles/global.css` and available in all components:

```css
--bg: #0a0a0a
--bg-elevated: #1a1a1a
--text: #e5e5e5
--text-muted: #737373
--accent: #7dd3fc
--border: #262626
--radius: 6px
--max-w: 1100px
--font-mono: 'JetBrains Mono', 'Fira Code', 'Cascadia Code', Consolas, monospace
```

---

## CSS Button Classes Reference

The following button classes are defined globally (used in MarketingPage and Hero):

```css
.btn-primary  /* filled, accent background */
.btn-secondary  /* outlined */
```

Use these — do not define new button styles.

---

### Task 1: Create ShellQLPage.astro

**Files:**
- Create: `apps/site/src/components/ShellQLPage.astro`

- [ ] **Step 1: Create the file with the full component**

Create `apps/site/src/components/ShellQLPage.astro` with this exact content:

```astro
---
// apps/site/src/components/ShellQLPage.astro
import type { OssPackage } from '../data/packages';
interface Props { pkg: OssPackage; }
const { pkg } = Astro.props;

const screenshots = [
  { src: '/screenshots/shellql/fission1.png', caption: 'Jump between databases instantly' },
  { src: '/screenshots/shellql/fission4.png', caption: 'Filter and scan large tables fast' },
  { src: '/screenshots/shellql/fission5.png', caption: 'Edit rows with schema-aware fields' },
];

const features = [
  { title: 'Do real work, not just read', body: 'Insert, update, delete. Most terminal tools stop at read-only.' },
  { title: 'Built for remote environments', body: 'Run over SSH with zero setup. No tunnels, no file syncing.' },
  { title: 'Multi-tab workflow', body: 'Open tables and queries side by side. Switch instantly with tabs and shortcuts.' },
  { title: 'Mouse or keyboard', body: 'Use it like vim or like a GUI. Both paths work.' },
];
---
<main class="shellql-page">

  <!-- Hero -->
  <header class="shellql-hero">
    <div class="hero-headline-row">
      <h1 class="hero-headline">SQLite workbench that runs over SSH</h1>
      <img src="/shellql/SHellQL_SQLite_in_your_terminal.png" alt="ShellQL" class="hero-logo" />
    </div>
    <p class="hero-subhead">Browse tables, run queries, and edit rows directly on the server. No GUI. No port forwarding.</p>
  </header>

  <!-- Quick Install -->
  <section class="shellql-install">
    <pre class="install-code"><code>{pkg.install}
shql my.db</code></pre>
    <p class="install-meta">Runs on bash 3.2–5.2 (macOS + Linux)</p>
    <p class="install-meta">No dependencies beyond sqlite3</p>
    <div class="install-cta">
      <a href={pkg.githubUrl} target="_blank" rel="noopener noreferrer" class="btn-secondary">GitHub ↗</a>
      <a href={pkg.docsUrl} class="btn-secondary">View Docs</a>
    </div>
  </section>

  <!-- Proof Strip -->
  <section class="shellql-proof">
    <div class="proof-grid">
      {screenshots.map(({ src, caption }) => (
        <div class="proof-item">
          <img src={src} alt={caption} class="proof-img" loading="lazy" />
          <p class="proof-caption">{caption}</p>
        </div>
      ))}
    </div>
  </section>

  <!-- Terminal Preview -->
  <section class="shellql-preview">
    <pre class="terminal-preview"><code><span class="term-prompt">&gt;</span> shql production.db

[products] [orders] [+SQL]

id  name                price
1   Classic Bouquet     46.32
2   Wildflower Mix      33.12

<span class="term-hint">[Enter] Inspect   [f] Filter   [x] Export</span></code></pre>
  </section>

  <!-- Why This Exists -->
  <section class="shellql-why">
    <p>You SSH into a server. The SQLite database is right there — and every GUI tool you own stops working.</p>
    <p>ShellQL lets you browse and edit it directly, without leaving the terminal.</p>
  </section>

  <!-- Feature Blocks -->
  <section class="shellql-features">
    {features.map(({ title, body }) => (
      <div class="feature-block">
        <h3 class="feature-title">{title}</h3>
        <p class="feature-body">{body}</p>
      </div>
    ))}
  </section>

  <!-- Deep-Dive Sections -->
  {pkg.marketingSections && pkg.marketingSections.length > 0 && (
    <div class="shellql-sections">
      {pkg.marketingSections.map(section => (
        <section class="shellql-section">
          <h2>{section.title}</h2>
          <p>{section.body}</p>
          {section.code && <pre><code>{section.code}</code></pre>}
        </section>
      ))}
    </div>
  )}

  <!-- Bottom Install -->
  <section class="shellql-install shellql-install--bottom">
    <pre class="install-code"><code>{pkg.install}</code></pre>
    <div class="install-cta">
      <a href={pkg.githubUrl} target="_blank" rel="noopener noreferrer" class="btn-secondary">GitHub ↗</a>
      <a href={pkg.docsUrl} class="btn-secondary">View Docs</a>
    </div>
  </section>

</main>

<style>
.shellql-page { max-width: var(--max-w); margin: 0 auto; padding: 3rem 2rem; display: flex; flex-direction: column; gap: 3.5rem; }

/* Hero */
.shellql-hero { max-width: 700px; }
.hero-headline-row { display: flex; align-items: flex-start; justify-content: space-between; gap: 1.5rem; margin-bottom: 1rem; }
.hero-headline { font-size: clamp(1.5rem, 3.5vw, 2.25rem); font-weight: 800; letter-spacing: -0.03em; line-height: 1.15; }
.hero-logo { height: 40px; width: auto; flex-shrink: 0; margin-top: 4px; }
.hero-subhead { font-size: 1rem; color: var(--text-muted); line-height: 1.6; max-width: 560px; }

/* Install */
.shellql-install { display: flex; flex-direction: column; gap: 0.4rem; max-width: 480px; }
.install-code { background: var(--bg-elevated); border: 1px solid var(--border); border-radius: var(--radius); padding: 0.75rem 1rem; font-family: var(--font-mono); font-size: 0.875rem; color: var(--accent); white-space: pre; }
.install-meta { font-size: 0.82rem; color: var(--text-muted); }
.install-cta { display: flex; gap: 0.75rem; margin-top: 0.5rem; flex-wrap: wrap; }

/* Proof strip */
.proof-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; }
.proof-item { display: flex; flex-direction: column; gap: 0.5rem; }
.proof-img { width: 100%; border-radius: var(--radius); border: 1px solid var(--border); display: block; }
.proof-caption { font-size: 0.75rem; color: var(--text-muted); text-align: center; }
@media (max-width: 768px) { .proof-grid { grid-template-columns: 1fr; } }

/* Terminal preview */
.shellql-preview { max-width: 560px; }
.terminal-preview { background: var(--bg-elevated); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.25rem 1.5rem; font-family: var(--font-mono); font-size: 0.82rem; line-height: 1.7; color: var(--text); overflow-x: auto; }
.term-prompt { color: var(--accent); }
.term-hint { color: var(--text-muted); }

/* Why */
.shellql-why { max-width: 600px; display: flex; flex-direction: column; gap: 0.5rem; }
.shellql-why p { color: var(--text-muted); font-size: 0.95rem; line-height: 1.7; }

/* Feature blocks */
.shellql-features { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
.feature-block { background: var(--bg-elevated); border: 1px solid var(--border); border-radius: var(--radius); padding: 1.25rem 1.5rem; }
.feature-title { font-size: 0.95rem; font-weight: 600; color: var(--text); margin-bottom: 0.4rem; }
.feature-body { font-size: 0.875rem; color: var(--text-muted); line-height: 1.55; }
@media (max-width: 600px) { .shellql-features { grid-template-columns: 1fr; } }

/* Deep-dive sections */
.shellql-sections { display: flex; flex-direction: column; gap: 2.5rem; }
.shellql-section h2 { font-size: 1.1rem; font-weight: 600; letter-spacing: -0.01em; color: var(--text); margin-bottom: 0.75rem; }
.shellql-section p { color: var(--text-muted); font-size: 0.95rem; line-height: 1.65; max-width: 600px; margin-bottom: 1rem; }
.shellql-section pre { margin-top: 0; }

/* Bottom install modifier */
.shellql-install--bottom { border-top: 1px solid var(--border); padding-top: 3rem; }
</style>
```

- [ ] **Step 2: Verify the file was created**

```bash
ls apps/site/src/components/ShellQLPage.astro
```

Expected: file exists (no "No such file" error).

- [ ] **Step 3: Commit**

```bash
git add apps/site/src/components/ShellQLPage.astro
git commit -m "feat: add ShellQLPage custom layout component"
```

---

### Task 2: Wire ShellQLPage into the dynamic route

**Files:**
- Modify: `apps/site/src/pages/tools/[slug].astro`

Current file content:
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

- [ ] **Step 1: Update [slug].astro**

Replace the entire file with:

```astro
---
// apps/site/src/pages/tools/[slug].astro
import Layout from '../../layouts/Layout.astro';
import MarketingPage from '../../components/MarketingPage.astro';
import ShellQLPage from '../../components/ShellQLPage.astro';
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
  {pkg.slug === 'shellql' ? <ShellQLPage pkg={pkg} /> : <MarketingPage pkg={pkg} />}
</Layout>
```

- [ ] **Step 2: Build to verify**

```bash
cd apps/site && npm run build 2>&1 | tail -10
```

Expected: `15 page(s) built`, `[build] Complete!`, no errors.

- [ ] **Step 3: Run tests**

```bash
npm test 2>&1 | tail -8
```

Expected: `9 passed (9)`, no failures.

- [ ] **Step 4: Spot-check in preview**

```bash
npm run preview &
sleep 2
```

Open `http://localhost:4321/tools/shellql` and verify:
- Logo appears right of headline (small, ~40px)
- Install command shows two stacked lines
- Three fission screenshots appear in a row
- Terminal preview block renders with green prompt
- "Why this exists" block appears
- Feature grid shows 4 blocks in 2 columns
- Deep-dive sections (Works where your data lives, etc.) render below
- Bottom install block appears with GitHub and Docs buttons

Open `http://localhost:4321/tools/ptyunit` and verify: looks identical to before (MarketingPage still used for other tools).

```bash
kill %1
```

- [ ] **Step 5: Commit**

```bash
git add apps/site/src/pages/tools/\[slug\].astro
git commit -m "feat: route shellql slug to dedicated ShellQLPage component"
```

---

## Self-Review

**Spec coverage check:**

| Spec requirement | Task |
|-----------------|------|
| ShellQLPage.astro created | Task 1 |
| [slug].astro conditional render | Task 2 |
| packages.ts unchanged | ✅ No task needed |
| MarketingPage.astro unchanged | ✅ No task needed |
| Logo inline with headline, ~40px | Task 1 (hero-headline-row + hero-logo CSS) |
| Headline: "SQLite workbench that runs over SSH" | Task 1 |
| Subhead copy | Task 1 |
| Install: two stacked lines | Task 1 (install-code pre) |
| Badge lines with weight | Task 1 (install-meta, two separate `<p>`) |
| GitHub + Docs buttons at top | Task 1 |
| Proof strip: fission1, fission4, fission5 | Task 1 (screenshots array) |
| Proof strip captions | Task 1 |
| Desktop 3-col / mobile 1-col | Task 1 (@media 768px) |
| Terminal preview block | Task 1 (shellql-preview section) |
| Why this exists block | Task 1 (shellql-why section) |
| Feature blocks 2×2 grid | Task 1 (shellql-features grid) |
| Feature titles and bodies exact copy | Task 1 (features array) |
| Deep-dive sections from pkg.marketingSections | Task 1 |
| Bottom install repeat | Task 1 (shellql-install--bottom) |
| Build passes | Task 2 Step 2 |
| Tests pass | Task 2 Step 3 |
| Other tool pages unaffected | Task 2 Step 4 |

**Placeholder scan:** None found.

**Type consistency:** `OssPackage` type is imported from `../data/packages` — same import path as `MarketingPage.astro`. `pkg.install`, `pkg.githubUrl`, `pkg.docsUrl`, `pkg.marketingSections` all exist on the current type.
