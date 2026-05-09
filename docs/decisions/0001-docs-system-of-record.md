# ADR 0001 — Docs system of record + dogfooding boundary

- Status: Accepted
- Date: 2026-05-08
- Issue: [fissible/fissible.dev#19](https://github.com/fissible/fissible.dev/issues/19)

## Context

`fissible.dev` is the public face of the Station product and the first-party
Station tenant. Until now there has been no explicit rule for what content
lives where, or for how feature needs surfaced while dogfooding should be
routed back into the platform.

Three viable options were on the table:

1. **Astro `apps/docs` only** — keep the current Starlight setup as the single
   home for all public content (marketing + docs).
2. **Station CMS only** — port marketing *and* docs into Station entries on the
   first-party `fissible.dev` tenant; pure dogfooding.
3. **Hybrid** — marketing in Station CMS, technical docs in Starlight.

Phase 3 of `PLAN.md` already records that the marketing site moved into
Laravel/Station while docs remained Starlight, but that decision was about
runtime hosting (both deploy on Forge), not about long-term system of record
or the dogfooding boundary. This ADR makes the rule explicit.

## Decision

### Docs system of record — hybrid

- **Marketing content** (positioning pages, pricing, ICP messaging,
  multi-tenant pitch, "same DB, no Zapier", etc.) lives in **Station CMS as
  entries on the first-party Fissible tenant**. This dogfoods the platform on
  the customer-facing surface where authoring ergonomics matter most.

- **Technical docs** (Station guides, API references, theme/module dev,
  install/Forge runbooks, "Add to Home Screen", etc.) live in
  **`apps/docs/` (Starlight)** and ship at `docs.fissible.dev`. Starlight is
  better suited to versioned, code-heavy, deeply cross-linked technical
  content than the current Station CMS surface.

The split is content-type driven, not author-team driven. When in doubt about
where a new page belongs: if it teaches the reader *how to use the software*,
it goes to Starlight; if it sells the reader *on the software*, it goes to
Station CMS.

### Dogfooding boundary rule

- Marketing pages = **Station CMS entries** on the first-party tenant.
  Authoring happens through the same admin UI customers use.
- Site-specific code = private `App\Site` namespace **inside the canonical
  `fissible/station` repo**, or a `fissible-site` module distributed
  separately. Either way, it does **not** live in `fissible/fissible.dev`.
- **Product changes stay in `fissible/station` and `fissible/crm`.**
  `fissible/fissible.dev` only ADDS via modules and content — it never
  competes with the platform repos as a source of product code.
- When a feature need surfaces during dogfooding:
  - If any other Station customer would benefit → land it in the platform as
    a generic feature.
  - If only useful to a docs site or Fissible's own marketing → land it as a
    `fissible.dev`-side module/overlay.
  - **When in doubt: generic.**

### Runtime vs. distribution

These boundaries are about runtime architecture. They do **not** preclude a
separate sanitized customer-distribution starter repo. "One Station app
serving the first-party tenant" is the runtime rule; how customers receive a
copy of Station to self-host is a separate distribution problem. See
`docs/superpowers/plans/2026-04-15-station-dogfood-handoff.md` for the full
articulation.

## Migration plan

Most of the migration is already done:

- Phase 1 (Astro marketing site → Laravel) — shipped (PR #6).
- Phase 2 (Station-shaped app structure, Filament admin, tenant subdomains) —
  shipped (PR #7).
- Phase 3 (docs coexistence) — Starlight retained at `apps/docs/`,
  marketing on Station — confirmed by this ADR.
- Phase 4 (Astro marketing decommission) — done.

Remaining migration work is **content migration**, tracked by the Station-side
plan `docs/superpowers/plans/2026-04-15-first-party-tenant.md` in
`fissible/station`. This ADR does not replan it; it just confirms the
destination.

## Consequences

- The 7 open D-series roadmap tickets in this repo (`#13`–`#19`) get explicit
  destinations:
  - **Station CMS (first-party tenant)** — `#13` (Pricing), `#14` (Multi-tenant
    pitch), `#15` (ICP repositioning), `#17` (Same DB / no Zapier).
  - **Starlight `apps/docs/`** — `#16` (CMS authors are CRM users),
    `#18` (Add to Home Screen).
  - **Meta** — `#19` (this ADR).
- A new `App\Site` (or `fissible-site` module) namespace becomes the
  legitimate home for site-specific helpers; PRs adding such helpers to
  `fissible/station` proper should be redirected.
- Reviewers can cite this ADR to push back on PRs that try to rebuild Station
  capabilities in `fissible/fissible.dev`.
