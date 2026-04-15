# Station Dogfood Handoff: fissible.dev vs station

> **Read this first when working in `fissible/fissible.dev`.** This repo contains older plans that describe turning `fissible.dev` into a Station-shaped Laravel shell. That direction is now superseded.

## Current Decision

The canonical Station product lives in:

`/Users/allenmccabe/lib/fissible/station`

The production dogfood target is:

- `fissible.dev` is a first-party Station tenant served by the canonical Station app.
- `app.fissible.dev` is the Station control plane for login, admin, and platform management.
- The current `fissible/fissible.dev` Laravel app is a temporary/reference shell. Do not add new Station product features here.

The current Station-side implementation plan is:

`/Users/allenmccabe/lib/fissible/station/docs/superpowers/plans/2026-04-15-first-party-tenant.md`

That plan adds first-party tenant support, app-host routing, installer provisioning, a Fissible seeder suite, and a Fissible frontend theme scaffold.

## Superseded Local Plans

These local plans are historical context, not the current implementation direction:

- `docs/superpowers/plans/2026-04-14-station-webapp-scaffold.md`
- `docs/superpowers/plans/2026-04-14-tenant-provisioning-and-domains.md`
- `docs/superpowers/plans/2026-04-15-phase2-station-app-structure.md`
- `docs/superpowers/specs/2026-04-14-phase2-station-app-structure.md`

They explain how the temporary Laravel shell got here, but they should not be used as the next-step plan for Station dogfooding.

## What Not To Do

- Do not port Station modules into this repo.
- Do not keep building tenant CMS, forms, lead management, subscriptions, content blocks, or platform management in this repo.
- Do not treat this repo's simple `Tenant`, `TenantPage`, `TenantMenu`, or `is_platform_admin` implementation as the future Station architecture.
- Do not split the product into a custom `fissible.dev` Laravel app plus a separate Station app unless the user explicitly reverses the dogfood decision.

## What To Do Next

Work in `/Users/allenmccabe/lib/fissible/station`.

1. Verify the first-party tenant plan is fully implemented:
   - `php artisan test --filter=FirstPartyTenantTest`
   - `php artisan theme:list`
   - check `config/station.php` for `app_host` and `first_party`
   - check `StationInstall` for configured first-party slug lookups instead of hard-coded `default`

2. Add or update a Station project handoff after verification:
   - update `/Users/allenmccabe/lib/fissible/station/PROJECT.md`
   - note that `2026-04-15-first-party-tenant.md` is the canonical dogfood plan
   - include remaining blockers and exact test results

3. Migrate Fissible public content from this repo into Station:
   - use this repo's Blade views/config/assets as source material
   - move real content into Station entries, blocks, menus, and the Fissible theme
   - keep this repo as reference until Station serves `fissible.dev`

4. Prepare production routing:
   - `fissible.dev` -> Station public tenant frontend
   - `www.fissible.dev` -> same first-party tenant or redirect to apex
   - `app.fissible.dev` -> Station control plane
   - optional wildcard `*.fissible.dev` -> Station for demo/client tenants

5. After Station serves the public site, archive or decommission this temporary app.

## Open Product Work After Dogfood Bootstrapping

These are Station features to build in the canonical Station repo as dogfooding exposes the need:

- Real Fissible theme polish and content migration
- Blog module
- Forms/contact lead capture wiring
- Lead management
- Per-tenant theme selection before external clients use the same install with non-Fissible themes
- Subscription/customer portal features
- Public demo tenant such as `station-demo.fissible.dev`

## Mental Model

There should be one product implementation:

`Station app -> many tenants -> Fissible is first-party tenant`

There should not be two competing implementations:

`custom fissible.dev app + separate Station app`

