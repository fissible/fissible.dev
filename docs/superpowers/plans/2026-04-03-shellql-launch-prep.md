# ShellQL Launch Prep Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Update fissible.dev to convert HN traffic for the ShellQL launch — full tool page with pain-first copy, screenshots, and a homepage announcement bar.

**Architecture:** Content lives in `packages.ts` (data) and `MarketingPage.astro` (template). Two new optional fields (`installNote`, `screenshots`) are added to the `OssPackage` interface and rendered by the template. The announcement bar is a standalone component dropped into `index.astro`. No routing or build changes needed.

**Tech Stack:** Astro (static site), TypeScript, CSS custom properties (var(--accent) etc.)

---

## File Map

| File | Action | Purpose |
|------|--------|---------|
| `apps/site/src/data/packages.ts` | Modify | Add `installNote?` and `screenshots?` to `OssPackage`; update shellql entry |
| `apps/site/src/components/MarketingPage.astro` | Modify | Render installNote under install block; render screenshots after description |
| `apps/site/src/components/AnnouncementBar.astro` | Create | Slim launch banner for homepage |
| `apps/site/src/pages/index.astro` | Modify | Add `<AnnouncementBar />` above `<Hero />` |
| `docs/shellql-blog-post-outline.md` | Create | Dev.to blog draft outline (not deployed) |

---

### Task 1: Extend OssPackage type with `installNote` and `screenshots`

**Files:**
- Modify: `apps/site/src/data/packages.ts:9-22`

- [ ] **Step 1: Add the two optional fields to the interface**

Open `apps/site/src/data/packages.ts`. Replace the `OssPackage` interface (lines 9–22) with:

```typescript
export interface OssPackage {
  slug: string;
  name: string;
  tagline: string;
  description?: string;
  install: string;
  installLabel: 'brew' | 'composer';
  installNote?: string;
  docsUrl: string;
  githubUrl: string;
  features: [string, string, string, string];
  codeExample: string;
  screenshots?: string[];
  suite: 'tui' | 'php';
  marketingSections?: MarketingSection[];
}
```

- [ ] **Step 2: Verify TypeScript compiles**

```bash
cd apps/site && npx tsc --noEmit
```

Expected: no errors.

- [ ] **Step 3: Commit**

```bash
git add apps/site/src/data/packages.ts
git commit -m "feat: add installNote and screenshots fields to OssPackage"
```

---

### Task 2: Update MarketingPage to render installNote and screenshots

**Files:**
- Modify: `apps/site/src/components/MarketingPage.astro`

- [ ] **Step 1: Replace MarketingPage.astro with updated version**

The new template adds: (a) `installNote` rendered as a small note under the install block, and (b) a screenshots section rendered after the description and before the features list.

```astro
---
// apps/site/src/components/MarketingPage.astro
import type { OssPackage } from '../data/packages';
interface Props { pkg: OssPackage; }
const { pkg } = Astro.props;
---
<main class="marketing-page">
  <header class="marketing-hero">
    <h1>{pkg.name}</h1>
    <p class="marketing-tagline">{pkg.tagline}</p>
    <div class="install-block">
      <code>{pkg.install}</code>
    </div>
    {pkg.installNote && (
      <p class="install-note">{pkg.installNote}</p>
    )}
    <div class="cta-row">
      <a href={pkg.docsUrl} class="btn-primary">View Docs</a>
      <a href={pkg.githubUrl} target="_blank" rel="noopener noreferrer" class="btn-secondary">GitHub ↗</a>
    </div>
  </header>
  {pkg.description && (
    <p class="marketing-description">{pkg.description}</p>
  )}
  {pkg.screenshots && pkg.screenshots.length > 0 && (
    <section class="marketing-screenshots">
      <p class="screenshots-label">Schema browser · Table view · Record editor</p>
      <div class="screenshots-grid">
        {pkg.screenshots.map(src => (
          <img src={src} alt="" class="screenshot" loading="lazy" />
        ))}
      </div>
    </section>
  )}
  <section class="marketing-features">
    <h2>Features</h2>
    <ul>
      {pkg.features.map(f => <li>{f}</li>)}
    </ul>
  </section>
  <section class="marketing-example">
    <h2>Example</h2>
    <pre><code>{pkg.codeExample}</code></pre>
  </section>
  {pkg.marketingSections && pkg.marketingSections.length > 0 && (
    <div class="marketing-sections">
      {pkg.marketingSections.map(section => (
        <section class="marketing-section">
          <h2>{section.title}</h2>
          <p>{section.body}</p>
          {section.code && (
            <pre><code>{section.code}</code></pre>
          )}
        </section>
      ))}
    </div>
  )}
</main>

<style>
.marketing-page { max-width: var(--max-w); margin: 0 auto; padding: 3rem 2rem; }
.marketing-hero { margin-bottom: 3rem; max-width: 600px; }
.marketing-hero h1 { font-size: 2rem; font-weight: 700; letter-spacing: -0.03em; margin-bottom: 0.5rem; }
.marketing-tagline { color: var(--text-muted); font-size: 1.1rem; margin-bottom: 1.5rem; }
.install-block { margin-bottom: 0.5rem; }
.install-block code { font-size: 0.9rem; color: var(--accent); background: var(--bg-elevated); padding: 0.5rem 0.875rem; border-radius: var(--radius); border: 1px solid var(--border); }
.install-note { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.25rem; }
.cta-row { display: flex; gap: 0.75rem; flex-wrap: wrap; }
.marketing-screenshots { margin-bottom: 2.5rem; }
.screenshots-label { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); margin-bottom: 0.75rem; }
.screenshots-grid { display: flex; flex-direction: column; gap: 1rem; }
.screenshot { width: 100%; border-radius: var(--radius); border: 1px solid var(--border); display: block; }
.marketing-features, .marketing-example { margin-bottom: 2.5rem; }
.marketing-features h2, .marketing-example h2 { font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); margin-bottom: 1rem; }
.marketing-features ul { list-style: none; display: flex; flex-direction: column; gap: 0.5rem; }
.marketing-features li::before { content: '→ '; color: var(--accent); }
.marketing-description { color: var(--text-muted); font-size: 1rem; line-height: 1.6; margin-bottom: 2.5rem; max-width: 600px; }
.marketing-sections { display: flex; flex-direction: column; gap: 2.5rem; }
.marketing-section h2 { font-size: 1.15rem; font-weight: 600; letter-spacing: -0.01em; color: var(--text); margin-bottom: 0.75rem; }
.marketing-section p { color: var(--text-muted); font-size: 0.95rem; line-height: 1.65; max-width: 600px; margin-bottom: 1rem; }
.marketing-section pre { margin-top: 0; }
</style>
```

- [ ] **Step 2: Build to verify no template errors**

```bash
cd apps/site && npm run build 2>&1 | tail -20
```

Expected: build completes, no Astro errors. Output ends with something like `✓ Built in Xs`.

- [ ] **Step 3: Commit**

```bash
git add apps/site/src/components/MarketingPage.astro
git commit -m "feat: add installNote and screenshots rendering to MarketingPage"
```

---

### Task 3: Update shellql entry in packages.ts

**Files:**
- Modify: `apps/site/src/data/packages.ts` (shellql object, currently lines ~137–157)

- [ ] **Step 1: Replace the shellql object**

Find the `shellql` entry (slug: 'shellql') and replace it entirely:

```typescript
  {
    slug: 'shellql',
    name: 'shellql',
    tagline: 'SQLite workbench that runs where your data lives',
    description: `You SSH into a server. The SQLite database is right there. Every GUI tool you own stops working.\n\nShellQL is a full SQLite workbench that runs in your terminal. Open a database on any server you can SSH into. Browse schemas, run queries, and edit records without leaving the shell. Runs on bash 3.2–5.2 across macOS and Linux, tested in CI.`,
    install: 'brew install fissible/tap/shellql',
    installLabel: 'brew',
    installNote: 'Works over SSH. No GUI. No port forwarding.',
    docsUrl: 'https://docs.fissible.dev/shellql',
    githubUrl: 'https://github.com/fissible/shellql',
    suite: 'tui',
    features: [
      'Schema browser, table view, query tab, and record inspector',
      'Full CRUD — insert, update, and delete rows with an intuitive UI',
      'Works over SSH — browse and edit data directly on the machine',
      'Mouse and keyboard support; bash 3.2–5.2, tested in CI across macOS and Linux',
    ],
    codeExample: `# Open a database directly
shql myapp.db

# Use a named connection from sigil
shql --connection production`,
    screenshots: [
      '/screenshots/shellql/schema-browser.png',
      '/screenshots/shellql/table-data-view.png',
      '/screenshots/shellql/row-editor.png',
    ],
    marketingSections: [
      {
        title: 'Works where your data lives',
        body: 'Remote servers. Docker containers. CI jobs. If the machine has bash and sqlite3, you can open the database directly. No GUI install. No port forwarding. No syncing files back to your laptop. SSH in and run it.',
        code: `# Local
shql myapp.db

# Remote — SSH and run directly
ssh user@server
shql /var/app/production.db

# Named connection from sigil
shql --connection production`,
      },
      {
        title: 'Browse, query, and mutate',
        body: 'ShellQL has four screens: a schema browser for listing all tables and views, a table view with inline filtering and multi-tab support (open several tables at once), a query tab for raw SQL execution, and a record editor — a schema-aware form overlay that shows column types and constraints. Insert, update, and delete are first-class. Table creation uses a SQL tab preloaded with a CREATE TABLE template, giving you full DDL control.',
      },
      {
        title: 'Keyboard-driven, mouse-friendly',
        body: 'Most terminal tools pick one input model. ShellQL supports both. Navigate screens and records with keyboard shortcuts for speed, or use the mouse when that\'s faster. Keybindings are shown at the bottom of each screen — you don\'t need to read the docs to get started.',
      },
      {
        title: 'Zero dependencies, real architecture',
        body: 'No runtime dependencies beyond bash and sqlite3 — both available on most systems. Built on shellframe, a TUI framework for bash that provides screen management, keyboard routing, and component lifecycle. Tested on bash 3.2–5.2 across macOS and Linux in CI.',
        code: `brew install fissible/tap/shellql`,
      },
    ],
  },
```

- [ ] **Step 2: Verify TypeScript and build**

```bash
cd apps/site && npx tsc --noEmit && npm run build 2>&1 | tail -10
```

Expected: no TypeScript errors, build succeeds.

- [ ] **Step 3: Spot-check the page**

```bash
cd apps/site && npm run preview &
```

Open `http://localhost:4321/tools/shellql` in browser. Verify:
- Pain-hook description is visible near the top
- Three screenshots appear (schema browser, table view, row editor)
- "Works over SSH. No GUI. No port forwarding." appears under the install command
- Four marketing sections render below the features list
- GitHub button is visible without scrolling

Kill preview with `kill %1` when done.

- [ ] **Step 4: Commit**

```bash
git add apps/site/src/data/packages.ts
git commit -m "feat: add full marketing content to shellql tool page"
```

---

### Task 4: Create AnnouncementBar component

**Files:**
- Create: `apps/site/src/components/AnnouncementBar.astro`

- [ ] **Step 1: Create the component**

```astro
---
// apps/site/src/components/AnnouncementBar.astro
---
<div class="announcement-bar">
  <span class="announcement-label">New</span>
  ShellQL — SQLite workbench that runs over SSH (bash, no GUI)
  <a href="/tools/shellql" class="announcement-link">→ Check it out</a>
</div>

<style>
.announcement-bar {
  background: var(--accent);
  color: #fff;
  text-align: center;
  padding: 0.5rem 1rem;
  font-size: 0.8rem;
  line-height: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}
.announcement-label {
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.06em;
  font-size: 0.7rem;
  background: rgba(0,0,0,0.2);
  padding: 0.15rem 0.4rem;
  border-radius: 3px;
}
.announcement-link {
  color: #fff;
  font-weight: 600;
  text-decoration: underline;
  text-underline-offset: 2px;
}
.announcement-link:hover {
  opacity: 0.85;
}
</style>
```

- [ ] **Step 2: Commit**

```bash
git add apps/site/src/components/AnnouncementBar.astro
git commit -m "feat: add AnnouncementBar component for ShellQL launch"
```

---

### Task 5: Add AnnouncementBar to homepage

**Files:**
- Modify: `apps/site/src/pages/index.astro`

- [ ] **Step 1: Update index.astro**

```astro
---
// apps/site/src/pages/index.astro
import Layout from '../layouts/Layout.astro';
import AnnouncementBar from '../components/AnnouncementBar.astro';
import Hero from '../components/Hero.astro';
import WorkflowAnimation from '../components/WorkflowAnimation.astro';
import UseCase from '../components/UseCase.astro';
import TechCredibility from '../components/TechCredibility.astro';
import Ecosystem from '../components/Ecosystem.astro';
import ConvergeCTA from '../components/ConvergeCTA.astro';
---
<Layout title="fissible">
  <AnnouncementBar />
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

- [ ] **Step 2: Build and spot-check**

```bash
cd apps/site && npm run build 2>&1 | tail -10
```

Expected: build succeeds.

```bash
cd apps/site && npm run preview &
```

Open `http://localhost:4321`. Verify:
- Announcement bar appears at the very top in accent color
- "New" badge, text, and "→ Check it out" link are visible
- Link navigates to `/tools/shellql`
- Hero section appears below the bar, unchanged

Kill preview with `kill %1` when done.

- [ ] **Step 3: Commit**

```bash
git add apps/site/src/pages/index.astro
git commit -m "feat: add ShellQL announcement bar to homepage"
```

---

### Task 6: Write blog post outline

**Files:**
- Create: `docs/shellql-blog-post-outline.md`

- [ ] **Step 1: Create the outline**

```markdown
# Blog Post Outline: "Why I built a SQLite workbench in bash"

**Post on:** dev.to
**Target length:** 800–1200 words
**Link targets:** /tools/shellql, https://github.com/fissible/shellql

---

## Hook

You SSH into a server. The SQLite database is right there — you can see it in the filesystem.

Every GUI tool you own stops working. TablePlus, DB Browser, Beekeeper — all of them need a local connection. sqlite3 is available, but it's raw SQL with no browsing. litecli is read-biased and still needs installing.

You need to look at some rows, update a field, check an index. You end up writing SELECT statements into a CLI, copying output into a notes file, writing UPDATE statements by hand.

There's a better way.

---

## The build

ShellQL is built on shellframe — a TUI framework I wrote in bash. shellframe handles screen management, keyboard routing, dirty-region rendering, and component lifecycle. Writing a new application on top of it is closer to writing a React app than writing a bash script.

The surprising parts:
- Mouse support in bash is real, and it's not that hard once you understand xterm escape sequences
- SQLite's `.schema` output is parseable enough to build a schema browser without any external tools
- Tab management (multiple tables open simultaneously) required rethinking shellframe's focus model

---

## The SSH use case

This is the thing that makes ShellQL different from every other SQLite tool.

If the machine has bash and sqlite3, ShellQL runs. That means:
- Production servers (read and write, with care)
- Docker containers
- CI environments for debugging test databases
- Remote dev boxes

No GUI install. No port forwarding. No pulling the file to your laptop and pushing it back.

SSH in, run `shql /var/app/production.db`, browse your data.

---

## Full CRUD

Most TUI database tools are read-only. ShellQL isn't.

The record editor is a schema-aware form overlay. It shows column types and NOT NULL constraints. Tab through fields, edit values, press Enter to submit. Insert new rows the same way.

Table creation uses a SQL query tab preloaded with a `CREATE TABLE` template — you get full DDL control without a rigid GUI wizard.

---

## Mouse support

This one surprised people in early demos. Most bash tools are keyboard-only by design. ShellQL supports both.

Keyboard navigation is fast once you learn it — the keybindings are shown at the bottom of every screen. Mouse works for everything else: clicking into tables, scrolling rows, selecting records.

This matters for adoption. Not everyone who SSHes into a server is a power user.

---

## Install and try it

```bash
brew install fissible/tap/shellql
shql my.db
```

- Tool page: https://fissible.dev/tools/shellql
- GitHub: https://github.com/fissible/shellql
```

- [ ] **Step 2: Commit**

```bash
git add docs/shellql-blog-post-outline.md
git commit -m "docs: add ShellQL blog post outline for dev.to"
```

---

## Self-Review Notes

**Spec coverage check:**
- ✅ Pain-first description hook → Task 3 (description field)
- ✅ installNote "Works over SSH" → Task 3 (installNote field) + Task 2 (rendered)
- ✅ GitHub link above fold → already in existing CTA row, verified in Task 3 spot-check
- ✅ Screenshots (3 images) → Task 2 (template) + Task 3 (screenshots array)
- ✅ Tab system callout → Task 3, Section 2 body
- ✅ Schema-aware edit form callout → Task 3, Section 2 body
- ✅ Announcement bar → Task 4 + Task 5
- ✅ Blog outline → Task 6
- ✅ "bash 3.2–5.2, tested in CI" → Task 3, features + Section 4 body
- ✅ "most systems" not "virtually every" → Task 3, Section 4 body

**Type consistency check:**
- `installNote?: string` added in Task 1, used in Task 3, rendered in Task 2 ✅
- `screenshots?: string[]` added in Task 1, used in Task 3, rendered in Task 2 ✅
- `MarketingSection.body` used in all 4 sections in Task 3 ✅
- Section 2 and 3 have no `code` field — valid (field is optional) ✅
