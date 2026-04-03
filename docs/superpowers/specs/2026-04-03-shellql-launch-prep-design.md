# ShellQL Launch Prep — Design Spec

**Date:** 2026-04-03 (revised after SME review)
**Scope:** fissible.dev website updates to support the ShellQL HN launch  
**Approach:** C — Full ShellQL tool page + homepage announcement bar + dev.to blog outline

---

## Goal

Prepare fissible.dev to receive HN traffic for a "Show HN: ShellQL — a bash TUI for SQLite, zero dependencies" launch. The ShellQL tool page is the primary landing destination. HN users decide in ~10 seconds — the page must answer "what is it, why is it different, how do I try it" above the fold.

---

## 1. ShellQL Tool Page (`/tools/shellql`)

**File:** `apps/site/src/data/packages.ts` — update the `shellql` entry  
**File:** `apps/site/src/components/MarketingPage.astro` — add `image` support

### Tagline (existing field, update)

`"SQLite workbench that runs where your data lives"`

### Description (new field — pain-first hook)

> You SSH into a server. The SQLite database is right there. Every GUI tool you own stops working.
>
> ShellQL is a full SQLite workbench that runs in your terminal. Open a database on any server you can SSH into. Browse schemas, run queries, and edit records without leaving the shell. Runs on bash 3.2–5.2 across macOS and Linux, tested in CI.

### Quick start block (new — rendered immediately after description)

This appears before the features list. Requires either a new `quickStart` field on `OssPackage`, or inline in `MarketingPage` if the install block is promoted. Implementation note: ensure the GitHub link is rendered prominently here too (it already exists in the CTA row — verify it's visible without scrolling).

```bash
brew install fissible/tap/shellql
shql my.db
```

Sub-label under the block: `Works over SSH. No GUI. No port forwarding.`

### Features list (update existing 4 bullets)

- Schema browser, table view, query tab, and record inspector
- Full CRUD — insert, update, and delete rows with an intuitive UI
- Works over SSH — browse and edit data directly on the machine
- Mouse and keyboard support; bash 3.2–5.2, tested in CI across macOS and Linux

### Visual proof section (new — between Section 1 and Section 2)

Three screenshots displayed in sequence (or a single asciinema embed if a recording is produced later). User will provide screenshots staged as follows:

1. **Schema browser** — database with 3–4 real-looking tables (users, orders, products, etc.)
2. **Table view** — paginated rows with visible filtering UI
3. **Record inspector/edit** — single record open in form-style edit view

Implementation: add an optional `screenshots: string[]` field to `OssPackage` (relative paths from `public/`). `MarketingPage` renders them in a row or stacked block with a plain label: "Schema browser · Table view · Record inspector."

No marketing copy around the screenshots. Let the UI speak.

### Marketing Sections (4 entries)

**Section 1 — Works where your data lives**

Title: `Works where your data lives`

Body: Remote servers. Docker containers. CI jobs. If the machine has bash and sqlite3, you can open the database directly. No GUI install. No port forwarding. No syncing files back to your laptop. SSH in and run it.

Code:
```bash
# Local
shql myapp.db

# Remote — SSH and run directly
ssh user@server
shql /var/app/production.db

# Named connection from sigil
shql --connection production
```

---

**Section 2 — Browse, query, and mutate**

Title: `Browse, query, and mutate`

Body: ShellQL has four screens:
- **Schema browser** — tree-style navigation through tables and columns
- **Table view** — paginated rows with filtering
- **Query tab** — raw SQL execution with results inline
- **Record inspector** — form-style editing; insert, update, and delete are first-class

Table creation uses a SQL query tab preloaded with a `CREATE TABLE` template, giving you full DDL control.

No code block.

---

**Section 3 — Keyboard-driven, mouse-friendly**

Title: `Keyboard-driven, mouse-friendly`

Body: Most terminal tools pick one input model. ShellQL supports both. Navigate screens and records with keyboard shortcuts for speed, or use the mouse when that's faster. The interface is intuitive enough that you don't need to read the docs to get started.

No code block.

---

**Section 4 — Zero dependencies, real architecture**

Title: `Zero dependencies, real architecture`

Body: No runtime dependencies beyond bash and sqlite3 — both available on most systems. Built on shellframe, a TUI framework for bash that provides screen management, keyboard routing, and component lifecycle. This isn't a wrapper around `dialog` — it's a structured application tested on bash 3.2–5.2 across macOS and Linux in CI.

Code:
```bash
brew install fissible/tap/shellql
```

---

### OssPackage type changes

```ts
screenshots?: string[];   // optional, paths relative to /public
```

---

## 2. Homepage Announcement Bar

**New file:** `apps/site/src/components/AnnouncementBar.astro`  
**Modified file:** `apps/site/src/pages/index.astro`

### Copy (sharper, blunter)

> **New:** ShellQL — SQLite workbench that runs over SSH (bash, no GUI) &nbsp;[→ /tools/shellql](/tools/shellql)

### Component

Slim full-width strip above `<Hero />`. No dismiss button. Accent background, light text, ~0.8rem font, ~40px height. Internal link only (no external navigation).

---

## 3. Blog Post Outline (dev.to — not a site change)

Saved to `docs/shellql-blog-post-outline.md`. Nothing deployed to fissible.dev.

**Title:** "Why I built a SQLite workbench in bash"

**Structure:**

1. **Hook** — You SSH into a server. The SQLite database is right there. Every GUI tool you own is useless.
2. **The build** — What shellframe gave me, what I had to build on top of it.
3. **The SSH use case** — Why terminal-native matters for database tools specifically.
4. **Full CRUD** — Most TUI database tools are read-only. ShellQL isn't.
5. **Mouse support** — Unexpected in a bash tool. Why it matters for adoption.
6. **Install** — `brew install fissible/tap/shellql`, link to `/tools/shellql`, GitHub.

**Post on:** dev.to

---

## Out of scope

- Building a `/blog` section on fissible.dev
- Changes to any other tool pages
- Changes to the Station hero or WorkflowAnimation
- ShellQL docs site updates
- Strategic repositioning of fissible.dev (tools ecosystem vs. Station flagship — deferred)

---

## Files touched

| File | Change |
|------|--------|
| `apps/site/src/data/packages.ts` | Update `shellql` entry: tagline, description, quickStart concept, features, marketingSections, screenshots array |
| `apps/site/src/components/MarketingPage.astro` | Add screenshots rendering; promote install/GitHub to above-fold |
| `apps/site/src/components/AnnouncementBar.astro` | New component |
| `apps/site/src/pages/index.astro` | Add `<AnnouncementBar />` above `<Hero />` |
| `docs/shellql-blog-post-outline.md` | New file — blog draft outline (not deployed) |

---

## Screenshot staging guide (for user)

Capture these 3 terminal screenshots before implementation begins. Terminal: ~80 columns, dark background.

1. **Schema browser** — 3–4 tables with realistic names (users, orders, products, sessions)
2. **Table view** — 5–8 rows visible, mix of column types, filter UI visible if possible
3. **Record inspector/edit** — single record open, form-style layout, ideally mid-edit
