# fissible.dev

Public site overlay and content source for the first-party Fissible Station
tenant.

The canonical Station product lives in
[`fissible/station`](https://github.com/fissible/station). This repo holds
Fissible-only assets, marketing source material, the Starlight technical docs
under `apps/docs/`, and the now-historical Astro/Laravel scaffolding from the
migration phases. **Product code does not live here.**

## Where things live

| Content type | Home | Surfaces at |
| --- | --- | --- |
| Marketing pages (pricing, ICP, positioning) | Station CMS entries on the first-party tenant | `fissible.dev` |
| Technical docs (guides, API reference, runbooks) | Starlight in `apps/docs/` | `docs.fissible.dev` |
| Station control plane (login, admin) | `fissible/station` runtime | `app.fissible.dev` |
| Site-specific helpers / overlays | `App\Site` namespace in `fissible/station`, or `fissible-site` module | n/a |

## Decisions of record

- [ADR 0001 — Docs system of record + dogfooding boundary](docs/decisions/0001-docs-system-of-record.md)
- [Dogfood handoff plan](docs/superpowers/plans/2026-04-15-station-dogfood-handoff.md)
- [`PLAN.md`](PLAN.md) — historical phase-by-phase migration record (phases 0–4 shipped)

## Dogfooding rule (short version)

Marketing copy → Station CMS entries.
Technical docs → Starlight at `apps/docs/`.
Product changes → `fissible/station` or `fissible/crm`, not here.

When a feature need surfaces while dogfooding: if any other Station customer
would benefit, it lands in the platform as a generic feature. Only ship it as
a `fissible.dev`-side overlay/module if it is genuinely site-specific.
**When in doubt: generic.** Full text in [ADR 0001](docs/decisions/0001-docs-system-of-record.md).
