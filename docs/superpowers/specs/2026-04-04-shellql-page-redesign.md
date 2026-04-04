# ShellQL Page Redesign — Design Spec

**Date:** 2026-04-04
**Goal:** Replace the generic MarketingPage template for /tools/shellql with a dedicated custom layout that leads with screenshots, surface the SSH differentiator faster, and converts HN scanners more effectively.

---

## Architecture

**Approach:** Hybrid — custom layout component, same data layer (packages.ts unchanged).

| File | Action |
|------|--------|
| `apps/site/src/pages/tools/[slug].astro` | Modify: if `pkg.slug === 'shellql'`, render `<ShellQLPage pkg={pkg} />` instead of `<MarketingPage pkg={pkg} />` |
| `apps/site/src/components/ShellQLPage.astro` | Create: custom layout, all shellql-specific content hardcoded, reads `pkg` for install/githubUrl/docsUrl/marketingSections |
| `apps/site/src/data/packages.ts` | No changes |
| `apps/site/src/components/MarketingPage.astro` | No changes |

The slug detection is one conditional in `[slug].astro`. If the custom page is ever removed, delete one file and one line.

---

## Page Layout (top to bottom)

### 1. Logo
- Element: `<img>` 
- Source: `/shellql/SHellQL_SQLite_in_your_terminal.png`
- Height: ~100px, left-aligned
- No border, no card — just the image above the headline

### 2. Hero
- Headline: `SQLite workbench that runs over SSH`
- Subhead: `Browse tables, run queries, and edit rows directly on the server. No GUI. No port forwarding.`

### 3. Quick Install
- Code block: `brew install fissible/tap/shellql  ·  shql my.db` (monospace, accent color)
- Badge line below: `bash 3.2–5.2 · macOS + Linux · no deps beyond sqlite3` (small, muted)
- Button row: **GitHub ↗** (from `pkg.githubUrl`, opens new tab) and **View Docs** (from `pkg.docsUrl`)

### 4. Proof Strip
Three screenshots side by side on desktop, stacked on mobile:

| File | Caption |
|------|---------|
| `/screenshots/shellql/fission1.png` | Recent connections |
| `/screenshots/shellql/fission4.png` | Filter and scan tables |
| `/screenshots/shellql/fission5.png` | Edit rows with schema-aware fields |

- Images: `width: 100%`, `border-radius`, `border: 1px solid var(--border)`, `loading="lazy"`
- Captions: small, muted, centered below each image
- Layout: CSS grid, `grid-template-columns: repeat(3, 1fr)` on desktop, 1 column on mobile (≤768px)

### 5. Why This Exists
Short pain statement, no heading:

> You SSH into a server. The SQLite database is right there — and every GUI tool you own stops working.
> ShellQL lets you work with the database where it actually lives.

Styled as body text, muted color, max-width ~600px.

### 6. Feature Blocks (2×2 grid)
Four outcome-focused blocks in a 2-column grid (1 column on mobile):

| Title | Body |
|-------|------|
| Do real work, not just read | Insert, update, delete. Most terminal tools stop at read-only. |
| Built for remote environments | Run over SSH with zero setup. No tunnels, no file syncing. |
| Fast navigation | Tabs and keyboard shortcuts to jump between schema, tables, and queries. |
| Mouse or keyboard | Use it like vim or like a GUI. Both paths work. |

Each block: title in `--text` (normal weight, slightly larger), body in `--text-muted`.

### 7. Deep-Dive Sections
Render `pkg.marketingSections` — the existing 4 entries from packages.ts:
- Works where your data lives
- Browse, query, and mutate
- Keyboard-driven, mouse-friendly
- Zero dependencies, real architecture

Same rendering as current MarketingPage: section title + body + optional code block.

### 8. Bottom Install
Repeat the install command with GitHub and Docs buttons — for users who scrolled to the bottom before deciding.

- Code: `brew install fissible/tap/shellql`
- Buttons: same as top (GitHub ↗, View Docs)

---

## Assets

| Asset | Path | Usage |
|-------|------|-------|
| Logo | `/shellql/SHellQL_SQLite_in_your_terminal.png` | Section 1 |
| Screenshot 1 | `/screenshots/shellql/fission1.png` | Proof strip |
| Screenshot 2 | `/screenshots/shellql/fission4.png` | Proof strip |
| Screenshot 3 | `/screenshots/shellql/fission5.png` | Proof strip |

All assets already committed to `apps/site/public/`.

---

## Out of Scope

- Changes to any other tool pages
- Changes to packages.ts
- Changes to MarketingPage.astro
- The old screenshots (row-editor.png, schema-browser.png, table-data-view.png) remain in public/ but are no longer referenced
- The old shellql_logo.png remains in public/ but is not used
- Blog post or HN submission changes
