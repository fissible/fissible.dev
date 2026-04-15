# Issue #5 Plan: Move `fissible.dev` from Astro pages to a Laravel/Station web app

## Default decisions

These are the working assumptions for implementation unless the user overrides them.

### 1. Directory structure

Use a standard Laravel application at the **repo root**.

Rationale:
- This repo is being repurposed from a static site into the actual Station web app.
- Laravel tooling assumes the project root contains `artisan`, `composer.json`, `app/`, `config/`, `resources/`, `routes/`, and `public/`.
- Putting Laravel in `app/` or another nested folder adds friction for deployment, CI, docs, and day-to-day development.
- The current Astro apps are transitional assets, not the long-term center of the repo.

Transitional structure:
- `apps/site` remains temporarily as the source of truth while marketing pages are ported.
- `apps/docs` remains temporarily if docs continue as a separate static deploy in phase 1.
- After parity, remove or archive Astro apps.

### 2. Deployment target

Target **Laravel Forge on a VPS** for the first production deployment.

Rationale:
- Station is explicitly positioned as self-hosted Laravel software.
- Forge matches a conventional Laravel app with queues, scheduler, Redis, storage, backups, and long-lived workers.
- Vapor adds AWS/serverless complexity too early.
- Railway is viable for prototypes, but Forge is a better fit for a durable self-hosted product story.

### 3. Marketing site fate

Migrate the **main marketing site into Station/Laravel**.

Keep **docs separate temporarily** during the first migration phase.

Rationale:
- The issue is asking how to handle the Astro sites going forward; the clean answer is that the marketing site should stop being a separate static app.
- Marketing pages are part of the product surface and should live in the same application that is being sold.
- Docs are lower-risk to keep separate initially because `docs.fissible.dev` already maps cleanly to a dedicated docs app and content set.
- This reduces scope while still resolving the architectural mismatch for the main site.

Default end state:
- `fissible.dev` served by Laravel/Station.
- `docs.fissible.dev` can stay static for phase 1, then optionally move into Station later.

### 4. Database

Use **MySQL in production** and allow **SQLite only for local/dev convenience**.

Rationale:
- Existing Station docs already describe MySQL as the primary foundation.
- This aligns with Laravel defaults, Forge workflows, and broad hosting support.
- It avoids inventing a new production posture while the app itself is still being established.

## Product boundary for this ticket

This ticket should be treated as an **application bootstrap and migration plan**, not as a full Station feature build.

The goal is to:
- establish Laravel/Station as the runtime for `fissible.dev`
- preserve or migrate current Station marketing content
- define how docs coexist during the transition
- leave the repo in a state where ongoing Station work lands in the real app, not in temporary Astro pages

The goal is **not** to finish every Station capability described in the docs.

## Phases

### Phase 0: Bootstrap the Laravel app ✅

Deliverables:
- Initialize Laravel at repo root.
- Add baseline deployment/runtime config:
  - MySQL
  - Redis
  - queue worker
  - scheduler
  - filesystem config
- Add an app shell with:
  - homepage route
  - `/station` route
  - basic shared layout

Exit criteria:
- `php artisan serve` works locally.
- A simple Blade-based marketing shell renders.

### Phase 1: Port current marketing content out of Astro ✅

Deliverables:
- Port the current Station-led homepage from `apps/site`.
- Port the `/station` product page.
- Port tools index and any still-relevant tool landing pages that should remain public.
- Recreate redirects currently expressed in `vercel.json`.

Implementation notes:
- Preserve messaging, information architecture, and assets where practical.
- Treat Astro as the reference implementation, not the long-term home.
- Keep the public pages content-first; do not block on full CMS-driven editing yet.

Exit criteria:
- Main public routes on Laravel have parity with the current Astro site.
- Existing inbound links continue to resolve via redirects.

### Phase 2: Establish Station-shaped app structure

Deliverables:
- Create the initial app architecture for Station-oriented development:
  - tenant-aware domain concepts
  - admin route namespace placeholders
  - module/service provider boundaries
  - theme-aware public rendering hooks
- Add configuration scaffolding that matches the existing docs posture.

Important constraint:
- This phase should create the **frame** for Station, not every implementation described in docs.

Exit criteria:
- New Station work can land in Laravel without rethinking the repo structure again.

### Phase 3: Decide docs coexistence path ✅

Decision: Both the Laravel marketing site and the Starlight docs site are deployed
on Forge. Docs remain as a separate Starlight build at `docs.fissible.dev`, served
from `apps/docs/`. This is the intentional long-term split — docs stay as static
Starlight, marketing/platform is Laravel.

### Phase 4: Decommission Astro marketing app ✅

Deliverables:
- Remove `apps/site` once public page parity is confirmed.
- Remove Vercel-specific deployment assumptions for the main site.
- Update root tooling and docs to reflect Laravel-first development.

Exit criteria:
- The repo no longer presents Astro as the primary implementation for `fissible.dev`.

## Dependency graph

1. Laravel bootstrap
2. Basic public routes/layout
3. Asset migration from Astro
4. Homepage + `/station` parity
5. Redirect preservation
6. Station-oriented internal structure
7. Docs coexistence decision documented
8. Astro marketing app removal

## Work split for Claude

Recommended immediate next steps:

1. Bootstrap Laravel at repo root in a way that does not destroy `apps/site` or `apps/docs`.
2. Create a minimal Blade layout and public routes for `/` and `/station`.
3. Inventory which assets/components from `apps/site` must be ported first.
4. Port the homepage and Station page before worrying about full CMS behavior.
5. Leave `apps/docs` untouched except for cross-linking unless the user explicitly expands scope.

## Risks

- A root-level Laravel install will replace the repo's current Node-first shape; that is intentional, but it will create broad file churn.
- If the user actually wants Station as a separate product repo and `fissible.dev` to remain mostly marketing/docs, this plan is too aggressive.
- The existing Station docs describe more product surface than this ticket should implement in one pass.

## Explicit assumptions to confirm later

If the user responds with preferences, revisit these first:
- Forge/VPS vs another host
- whether docs should stay static permanently
- whether tools pages remain hand-authored or become Station-managed content
