# Marketing Site Redesign — Design Spec

**Issue:** fissible/fissible.dev#3
**Date:** 2026-03-28
**Status:** Approved

## Overview

Rewrite fissible.dev to position Station (self-hosted Laravel CMS with approval workflows) as the primary product. The current site presents 12+ tools equally, creating confusion. The redesign focuses messaging on a single value prop — "nothing goes live without approval" — and sequences content for two audiences: decision-makers first, developers second.

## Constraints

- Station is pre-release. CTAs drive waitlist signups, not installs.
- In-place refactor on a branch (Approach A), not a rewrite or framework migration.
- Incremental checkpoints — each slice is independently shippable.
- Dark theme retained. Visual identity evolves (hierarchy, spacing, intent), not replaces.

## Site Architecture

### URL Structure

```
/                    → Station-led homepage (6 sections)
/station/            → Station product page + waitlist
/guit/               → guit product page (coming soon)
/sigil/              → sigil product page (purchase pending)
/conduit/            → conduit product page (coming soon)
/tools/              → Ecosystem index (grouped: API tools, CLI/TUI tools)
/tools/accord/       → Individual tool page
/tools/drift/        → ...
/tools/forge/
/tools/seed/
/tools/shellframe/
/tools/ptyunit/
/tools/shellql/
/tools/watch/
/tools/fault/
```

### Redirects

301 redirects from old routes (`/accord/` → `/tools/accord/`, etc.) to preserve SEO and existing links. Implemented via Vercel `vercel.json` redirects config (since the site is static output, Astro middleware won't run). Update all internal links to use new paths. Canonical URLs point to `/tools/*`.

### Navigation

```
fissible    |    Station    Tools    Docs    GitHub
```

- **Station** → `/station/`
- **Tools** → `/tools/`
- **Docs** → `https://docs.fissible.dev`
- **GitHub** → `https://github.com/fissible`

No dropdowns. Flat, 4 items.

## Homepage Design

### Audience Sequencing

Sections 1–3 speak to the **decision-maker**. Sections 4–5 speak to the **developer**. Section 6 converges **both**.

Never blend both audiences in the same sentence.

**Litmus test:** If a non-technical person can't understand the hero, it's wrong. If a developer doesn't trust the mid-page, it's wrong.

### Section 1: Hero

- **Headline:** "Stop accidental publishing. Ship APIs that don't break."
- **Clarifying line:** "Approval workflows and API contract enforcement that prevent mistakes before they go live."
- **Descriptor:** "Self-hosted CMS and API platform. One-time license."
- **Primary CTA:** "Join the Waitlist" (cyan, solid)
- **Secondary CTA:** "See How It Works ↓" (outline, scroll anchor to workflow section)

Typography: headline is the largest, heaviest element on the page. Clarifying line is muted but readable. Descriptor is secondary. Max-width on clarifying line (~420px equivalent) to force balanced two-line wrap.

### Section 2: Workflow (scroll-triggered animation)

- **Heading:** "Nothing goes live without approval"
- **Subheading:** "Every step is enforced. Every action is logged."
- **Steps:** Draft → Submit → **Approve** → Publish → Audit Log
- Each step has a label + one-line description:
  - Draft: "Write safely"
  - Submit: "Request review"
  - Approve: "Must sign off"
  - Publish: "Goes live"
  - Audit Log: "Everything tracked"

**Approve step** is visually dominant: larger, thicker cyan border, ✓ icon, subtle glow. If someone remembers one thing, it's "approval is required."

**Animation:** Scroll-triggered. Steps appear sequentially with fade + slide, 150–250ms each. No heavy motion. This is the only animated section — everything else stays calm.

**3-second glance test:** A viewer should understand "it forces approval before publishing."

### Transition

Thin spacer/divider between workflow and use case. Mental shift from "how it works" to "who it's for."

### Section 3: Use Case

- **Heading:** "Built for teams that can't afford mistakes"
- **Scenario** (in a cyan-left-bordered callout, 4 scannable lines):
  ```
  Financial advisor publishes content
  → must be approved by compliance
  → nothing goes live early
  → every change is tracked
  ```
- **Metadata row** (3 columns):
  - **Who:** Teams with approval requirements
  - **Why:** Compliance, legal, editorial control
  - **How:** Self-hosted, your infrastructure

### Section 4: Technical Credibility

- **Heading:** "Built for developers who ship carefully"
- **3 cards** (benefit-first, not label-first):
  - **Self-Hosted** — "Your data stays under your control"
  - **Laravel** — "Built on a framework your team already knows"
  - **API Contracts** — "Prevent breaking changes before deployment"

No feature dump. Each card answers "why should I trust this?"

### Section 5: Ecosystem

- **Heading:** "Built on open-source tools"
- **Subheading:** "Station's API layer is powered by tools you can use independently."
- **3 tool cards** (minimal):
  - **accord** — OpenAPI contract validator
  - **drift** — API drift detection
  - **forge** — OpenAPI spec scaffolding
- **Link:** "Explore all tools →" → `/tools/`

Support section, not a second product pitch. Each tool card links to its GitHub repo page (e.g., `github.com/fissible/accord`), not the org profile.

### Section 6: CTA (Convergence)

- **Headline:** "Put approval between your team and production."
- **Subtext:** "Station is launching soon. Be the first to try it."
- **Primary CTA:** "Join the Waitlist" (cyan, solid) — Formspree integration (existing `formspreeId` for Station)
- **Secondary CTA:** "Explore Tools" (outline) → `/tools/`

## Visual Evolution

### What changes

1. **Typography hierarchy** — much larger/bolder hero headline, stronger contrast between headline → subheadline → body → metadata. Currently everything feels similar weight.
2. **Intentional color** — cyan (`#7dd3fc`) used for actions (CTAs, active states) and control points (Approve step), not decoratively on every card link.
3. **"Control" feel** — slightly stronger borders/focus states, clearer step progression, more deliberate spacing. Not heavy-handed, just intentional.
4. **Layout flow** — break the grid early. Hero (full width) → workflow (linear) → use case (content block) → then grid (tools). Same components, different order = different perception.
5. **Spacing** — increased vertical spacing between sections. Hero + workflow breathe. Tools section compressed (secondary).
6. **Motion** — workflow animation is the only moving part. Everything else stays calm.

### What stays

- Dark theme (`#0a0a0a` background)
- Cyan accent (`#7dd3fc`)
- Overall aesthetic ("quiet confidence")
- No gradients, no SaaS marketing template feel

### Rule

Same visual language, different sentence structure.

## /tools/ Index Page

Simple, not marketing-heavy. Grouped sections:

**API Tools**
- accord — OpenAPI contract validator for Laravel
- drift — API drift detection and changelog generation
- forge — OpenAPI spec scaffolding from Laravel routes
- watch — Browser-based dev cockpit for Laravel
- fault — Exception tracking and triage for Laravel

**CLI / TUI Tools**
- shellframe — TUI framework for bash
- seed — Bash fake data generator
- ptyunit — PTY test framework for bash
- shellql — Terminal SQLite workbench

Each entry: name, one-line description, link to individual page + GitHub repo link.

## Individual Tool Pages (under /tools/*)

Keep existing MarketingPage structure but ensure each page answers:
- What it does (fast)
- Why it matters
- How to install
- Quick example

No long marketing fluff. Deep link to the tool's GitHub repo (e.g., `github.com/fissible/seed`), not the org profile page.

## Paid Product Pages (top-level)

Station, guit, sigil, conduit keep their existing ComingSoonPage structure at top-level URLs. These should be minimal and intentional — not half-built product pages. Clearly marked as coming soon.

## Data Layer Changes

`packages.ts` needs updates:
- Add a `route` or `basePath` field to support `/tools/` prefix for OSS packages
- Paid products keep top-level routes
- Add `suite` grouping field if not already present (for /tools/ index grouping)
- Ensure `githubUrl` points to individual repo (already does — verify all are correct)

## Implementation Order

Incremental on a branch, each checkpoint independently shippable:

1. **New nav + homepage structure** — rewrite Nav.astro (flat, 4 items) + index.astro (6 sections, static content first)
2. **Typography/spacing system** — evolve global.css type scale, spacing variables, intentional color usage
3. **/tools/ index + route migration** — create /tools/index.astro, move tool pages to /tools/*, add 301 redirects, update internal links
4. **Workflow animation** — add IntersectionObserver + CSS keyframes to workflow section
5. **Cleanup and polish** — verify redirects, test mobile, check all deep links, remove "coming soon" clutter from homepage

## Out of Scope

- Interactive demo (tracked in fissible/fissible.dev#4)
- Pricing page
- Light theme
- New framework or dependencies
- Docs site changes (separate concern)

## Success Criteria

- User understands product in <5 seconds
- User sees clear workflow value
- User has a clear next action (join waitlist / explore tools)
- At any scroll position, the user can answer "what does this prevent?"
