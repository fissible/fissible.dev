# ShellQL Page Redesign — Design Spec

**Date:** 2026-04-04 (revised after SME review)
**Goal:** Replace the generic MarketingPage template for /tools/shellql with a dedicated custom layout that leads with screenshots, surfaces the SSH differentiator faster, and converts HN scanners in ~10 seconds.

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

### 1. Hero with inline logo

Logo and headline are on the same row. HN users care about capability, not branding — logo doesn't lead.

```
[ShellQL logo, ~40px height, right-aligned]  SQLite workbench that runs over SSH
                                              Browse tables, run queries, and edit rows
                                              directly on the server. No GUI. No port forwarding.
```

- Logo: `/shellql/SHellQL_SQLite_in_your_terminal.png`, `height: 40px`, floated or flex right
- Headline: `SQLite workbench that runs over SSH`
- Subhead: `Browse tables, run queries, and edit rows directly on the server. No GUI. No port forwarding.`

### 2. Quick Install

Stacked — two separate lines, copy-pasteable:

```
brew install fissible/tap/shellql
shql my.db
```

Two lines of context below, given visual weight (not just footnote styling):

```
Runs on bash 3.2–5.2 (macOS + Linux)
No dependencies beyond sqlite3
```

Button row: **GitHub ↗** (from `pkg.githubUrl`, `target="_blank"`) and **View Docs** (from `pkg.docsUrl`)

### 3. Proof Strip

Three screenshots side by side on desktop, stacked on mobile (≤768px):

| File | Caption |
|------|---------|
| `/screenshots/shellql/fission1.png` | Jump between databases instantly |
| `/screenshots/shellql/fission4.png` | Filter and scan large tables fast |
| `/screenshots/shellql/fission5.png` | Edit rows with schema-aware fields |

- Images: `width: 100%`, `border-radius: var(--radius)`, `border: 1px solid var(--border)`, `loading="lazy"`
- Captions: `font-size: 0.75rem`, `color: var(--text-muted)`, centered below each image
- Layout: CSS grid, `grid-template-columns: repeat(3, 1fr)` on desktop, `1fr` on mobile

### 4. Terminal Preview Block

Immediately after the proof strip. A styled `<pre>` block showing a realistic terminal session — reinforces "terminal-native" and gives an instant mental model.

```
> shql production.db

[products] [orders] [+SQL]

id  name                price
1   Classic Bouquet     46.32
2   Wildflower Mix      33.12

[Enter] Inspect   [f] Filter   [x] Export
```

Styling: dark background (`var(--bg-elevated)`), monospace font, accent-colored prompt (`>`), muted keybind hints at the bottom. Border: `1px solid var(--border)`. No syntax highlighting library needed — plain `<pre><code>`.

### 5. Why This Exists

Short pain statement, no section heading:

> You SSH into a server. The SQLite database is right there — and every GUI tool you own stops working.
> ShellQL lets you browse and edit it directly, without leaving the terminal.

Styled: body text, `color: var(--text-muted)`, `max-width: 600px`, `line-height: 1.7`.

### 6. Feature Blocks (2×2 grid)

Four outcome-focused blocks in a 2-column grid (1 column on mobile):

| Title | Body |
|-------|------|
| Do real work, not just read | Insert, update, delete. Most terminal tools stop at read-only. |
| Built for remote environments | Run over SSH with zero setup. No tunnels, no file syncing. |
| Multi-tab workflow | Open tables and queries side by side. Switch instantly with tabs and shortcuts. |
| Mouse or keyboard | Use it like vim or like a GUI. Both paths work. |

Each block: title in `var(--text)`, `font-weight: 600`, body in `var(--text-muted)`, `font-size: 0.9rem`. Background: `var(--bg-elevated)`, border, padding, border-radius.

### 7. Deep-Dive Sections

Render `pkg.marketingSections` — the existing 4 entries from packages.ts unchanged:
- Works where your data lives (with code block)
- Browse, query, and mutate
- Keyboard-driven, mouse-friendly
- Zero dependencies, real architecture (with code block)

Same rendering pattern as MarketingPage: `<h2>` title, `<p>` body, optional `<pre><code>` block.

### 8. Bottom Install

Repeat for users who scrolled to the bottom before deciding.

```
brew install fissible/tap/shellql
```

Button row: GitHub ↗, View Docs (same as top).

---

## Assets

| Asset | Path | Usage |
|-------|------|-------|
| Logo | `/shellql/SHellQL_SQLite_in_your_terminal.png` | Section 1 (inline with headline, small) |
| Screenshot 1 | `/screenshots/shellql/fission1.png` | Proof strip |
| Screenshot 2 | `/screenshots/shellql/fission4.png` | Proof strip |
| Screenshot 3 | `/screenshots/shellql/fission5.png` | Proof strip |

All assets already committed to `apps/site/public/`.

---

## Out of Scope

- Changes to any other tool pages
- Changes to `packages.ts`
- Changes to `MarketingPage.astro`
- Logo asset redesign (SME feedback: current logo is "20% toward startup branding"; recommended direction is ultra-minimal — `shql` in monospace with green `ql`, or a `>_` terminal badge. This is a separate asset design task, not a site change.)
- The old screenshots (row-editor.png, schema-browser.png, table-data-view.png) remain in public/ but are no longer referenced
- The old shellql_logo.png remains in public/ but is not used
