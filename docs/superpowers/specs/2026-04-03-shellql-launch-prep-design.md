# ShellQL Launch Prep — Design Spec

**Date:** 2026-04-03  
**Scope:** fissible.dev website updates to support the ShellQL HN launch  
**Approach:** C — Full ShellQL tool page + homepage announcement bar + dev.to blog outline

---

## Goal

Prepare fissible.dev to receive HN traffic for a "Show HN: ShellQL — a bash TUI for SQLite, zero dependencies" launch. The ShellQL tool page should be the primary landing destination. The homepage should signal the launch without disrupting the Station-first narrative.

---

## 1. ShellQL Tool Page (`/tools/shellql`)

**File:** `apps/site/src/data/packages.ts` — update the `shellql` entry

No template changes required. The `MarketingPage.astro` component already renders `description` and `marketingSections` when present; shellql just lacks them today.

### Description (new field)

> ShellQL is a full SQLite workbench that runs in your terminal. Open a database on any server you can SSH into — no GUI, no port forwarding, no setup. Browse schemas, run queries, and edit records with a keyboard-driven TUI that also supports mouse input.

### Marketing Sections (4 new entries)

**Section 1 — Works where your data lives**

Title: `Works where your data lives`

Body: Remote servers, Docker containers, CI environments. Every machine with bash and sqlite3 can run ShellQL. There's no GUI to install, no port to forward, no config file to write. Connect over SSH and open a database with one command: `shql path/to/db.sqlite`. Named connections via sigil are available if you want them.

Code:
```bash
# Open a database directly
shql myapp.db

# Over SSH — just works
ssh user@server
shql /var/app/production.db

# Named connection from sigil
shql --connection production
```

---

**Section 2 — Browse, query, and mutate**

Title: `Browse, query, and mutate`

Body: ShellQL has four screens: a schema browser for exploring tables and columns, a table view with filtering for browsing rows, a query tab for running arbitrary SQL, and a record inspector for viewing and editing individual rows. Insert, update, and delete are first-class — not read-only like most TUI database tools. Table creation uses a SQL query tab preloaded with a `CREATE TABLE` template, giving you full DDL control without a rigid GUI wizard.

No code block for this section — the prose is the content.

---

**Section 3 — Keyboard-driven, mouse-friendly**

Title: `Keyboard-driven, mouse-friendly`

Body: Most terminal tools pick one input model. ShellQL supports both. Navigate screens and records with keyboard shortcuts for speed, or use the mouse when that's faster. The interface is intuitive enough that you don't need to read the docs to get started.

No code block.

---

**Section 4 — Zero dependencies, real architecture**

Title: `Zero dependencies, real architecture`

Body: ShellQL has no runtime dependencies beyond bash and sqlite3 — both present on virtually every Linux and macOS system. It's built on shellframe, a TUI framework for bash that provides screen management, keyboard routing, and component lifecycle. This isn't a wrapper around `dialog` or a pile of escape codes — it's a structured application that works on bash 3.2–5.x, including the macOS default shell.

Code:
```bash
# Install via Homebrew
brew install fissible/tap/shellql
```

---

### Features list (update existing)

Replace the current 4 bullets with:

- Schema browser, table view, query tab, and record inspector
- Full CRUD — insert, update, and delete rows with an intuitive UI
- Works over SSH — no GUI, no port forwarding, just bash and sqlite3
- Mouse and keyboard support; bash 3.2–5.x compatible

---

## 2. Homepage Announcement Bar

**New file:** `apps/site/src/components/AnnouncementBar.astro`  
**Modified file:** `apps/site/src/pages/index.astro`

### Component

A slim, full-width banner rendered above the Hero. Accent-colored background, single line of text with a link to `/tools/shellql`. No dismiss button (no localStorage/cookie complexity).

Content:
> **New:** ShellQL — a full SQLite workbench for the terminal, works over SSH. [Check it out →](/tools/shellql)

Styling: thin strip (~40px height), centered text, accent background color, white/light text, small font size (~0.8rem).

### index.astro change

Add `<AnnouncementBar />` as the first element inside `<main>`, above `<Hero />`.

---

## 3. Blog Post Outline (dev.to — not a site change)

A draft outline is saved to `docs/shellql-blog-post-outline.md` for reference. Nothing is deployed to fissible.dev.

**Title:** "Why I built a SQLite workbench in bash"

**Structure:**

1. **Hook** — You SSH into a server. The SQLite database is right there. Every GUI tool you own is useless.
2. **The build** — What shellframe gave me, what I had to build on top of it, the surprising parts.
3. **The SSH use case** — Why terminal-native matters for database tools specifically. Remote servers, Docker, CI.
4. **Full CRUD** — Most TUI database tools are read-only. ShellQL isn't. Walk through insert/update/delete.
5. **Mouse support** — Unexpected in a bash tool. Why it matters for adoption.
6. **Install and try it** — `brew install fissible/tap/shellql`, link to `/tools/shellql`, link to GitHub.

**Post on:** dev.to (gets indexed, has its own developer audience, backlinks to fissible.dev)

---

## Out of scope

- Building a `/blog` section on fissible.dev
- Changes to any other tool pages
- Changes to the Station hero or WorkflowAnimation
- ShellQL docs site updates

---

## Files touched

| File | Change |
|------|--------|
| `apps/site/src/data/packages.ts` | Add `description` + `marketingSections` + update `features` for shellql entry |
| `apps/site/src/components/AnnouncementBar.astro` | New component |
| `apps/site/src/pages/index.astro` | Add `<AnnouncementBar />` above `<Hero />` |
| `docs/shellql-blog-post-outline.md` | New file — blog draft outline (not deployed) |
