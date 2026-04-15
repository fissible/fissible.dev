# Phase 2: Station-Shaped App Structure

**Goal:** Make fissible.dev a working Station instance where tenants have real
public sites, the admin panel works, and new Station features can land without
restructuring the repo.

**Exit criteria:** A tenant provisioned through the admin panel gets a working
subdomain site that renders its pages. The Filament admin panel manages tenants,
pages, menus, and memberships. Auth protects the admin. The service provider
structure supports future module registration.

---

## Existing Groundwork

Already in place (do not rewrite):

- **Models:** Tenant, TenantMembership, TenantPage, TenantMenu, User
- **Migrations:** tenants, tenant_memberships, tenant_pages, tenant_menus
- **Services:** TenantProvisioner, TenantLifecycleService, TenantDomainService, TenantSeeder
- **Config:** config/station.php (platform toggle, domain config, demo settings)
- **Middleware:** EnsurePlatformEnabled
- **Marketing site:** All public routes, MarketingController, Blade components

---

## Component 1: Auth + Filament Admin

### What

Install Filament v5 and Laravel Sanctum. Filament serves the admin panel at
`/admin`. Users authenticate via Filament's built-in login. Sanctum provides
API token auth for future API routes.

### How

- `composer require filament/filament laravel/sanctum`
- `php artisan filament:install --panels`
- Configure the default Filament panel (AdminPanelProvider)
- Make the User model implement `FilamentUser` with `canAccessPanel()`
- Create Filament resources for: Tenant, TenantPage, TenantMenu, TenantMembership
- Each resource gets list, create, edit, view pages via Filament's standard CRUD

### What replaces what

The current `app/Http/Controllers/Platform/TenantController.php` and its 3 Blade
views (`platform/tenants/index.blade.php`, `create.blade.php`, `show.blade.php`)
are replaced by Filament resources. The platform layout component
(`components/layouts/platform.blade.php`) is also removed.

The `/platform/*` routes in `routes/web.php` are removed. Filament handles its
own routing at `/admin`.

### What stays

The 4 service classes stay — Filament resources call them for provisioning,
lifecycle transitions, and demo seeding. The EnsurePlatformEnabled middleware
is no longer needed (Filament has its own auth gate) and can be removed.

---

## Component 2: Tenant Resolution Middleware

### What

Middleware that resolves the current tenant from the subdomain on every request
to `*.fissible.dev`. Binds the tenant to the request so downstream controllers
can access it.

### How

- `ResolveTenant` middleware:
  - Extract subdomain from `Host` header
  - Skip if the host is the root domain (`fissible.dev`), `www.fissible.dev`,
    `docs.fissible.dev`, or `platform.fissible.dev`
  - Look up tenant by slug (subdomain = slug)
  - Abort 404 if no tenant found or tenant is not active
  - Bind tenant to `app('tenant')` and `$request->attributes->set('tenant', $tenant)`
- Register in `bootstrap/app.php` as a global web middleware (runs on every
  request, but short-circuits for non-tenant hosts)

### Forge/DNS requirements

- Wildcard DNS: `*.fissible.dev` A record pointing to the Forge server
- Forge site configured to accept wildcard subdomains (Forge supports this
  natively — no nginx customization needed, just set the site domain to
  `fissible.dev` and enable "Wildcard Sub-Domains" in the site settings)

---

## Component 3: Tenant Site Rendering

### What

A controller and views that render a tenant's public-facing site. When a visitor
hits `acme.fissible.dev`, they see that tenant's pages rendered in a tenant-scoped
layout.

### Routes

All tenant site routes are defined in a separate route group that only activates
when the tenant resolution middleware has bound a tenant:

- `GET /` — tenant homepage (TenantPage where `is_homepage = true`)
- `GET /{slug}` — tenant page by slug

### Controller

`TenantSiteController`:

- `home()` — find the homepage TenantPage for the current tenant, render it
- `page(string $slug)` — find TenantPage by slug for the current tenant, 404 if
  not found or not published

### Views

- `resources/views/tenant/layout.blade.php` — base layout for tenant sites.
  Includes the tenant's menus (from TenantMenu), tenant name in title, basic
  navigation. Uses the same CSS design system as the marketing site for now.
- `resources/views/tenant/page.blade.php` — renders a TenantPage (title, body,
  excerpt). Body is rendered as HTML (TenantPage.body is longText, assumed to
  contain safe HTML from the admin).

### Menu rendering

TenantMenu has a `location` field (e.g., "primary", "secondary") and an `items`
JSON array. The tenant layout reads menus by location and renders them as nav
links.

---

## Component 4: StationServiceProvider

### What

A dedicated service provider that registers Station-specific bindings and
configuration. Keeps Station concerns out of AppServiceProvider so future
module work has a clear integration point.

### Responsibilities

- Register the `ResolveTenant` middleware alias
- Bind `TenantProvisioner`, `TenantLifecycleService`, `TenantDomainService`,
  `TenantSeeder` as singletons (they're stateless, no reason to re-instantiate)
- Register a `tenant()` helper or facade that returns the current tenant
  (shorthand for `app('tenant')`)
- Publish config if needed (future: module registration hooks go here)

### Registration

Add to `bootstrap/providers.php` alongside AppServiceProvider.

---

## Routing Strategy

Three route groups, each with different middleware:

1. **Marketing routes** (existing) — `fissible.dev` root domain only. No tenant
   context. Served by MarketingController.

2. **Admin routes** — `/admin/*` on the root domain. Filament handles routing
   and auth internally.

3. **Tenant routes** — `*.fissible.dev` subdomains. ResolveTenant middleware
   binds the tenant, TenantSiteController renders pages.

The marketing routes and tenant routes never conflict because they're on
different hosts. The admin routes are path-based on the root domain.

---

## File Structure (new/modified files)

```
app/
  Filament/
    Resources/
      TenantResource.php
      TenantResource/Pages/
      TenantPageResource.php         (Filament resource for TenantPage model)
      TenantPageResource/Pages/
      TenantMenuResource.php
      TenantMenuResource/Pages/
      TenantMembershipResource.php
      TenantMembershipResource/Pages/
  Http/
    Controllers/
      TenantSiteController.php       (new: public tenant page rendering)
    Middleware/
      ResolveTenant.php              (new: subdomain → tenant binding)
  Providers/
    Filament/
      AdminPanelProvider.php         (new: Filament panel config)
    StationServiceProvider.php       (new: Station bindings and registration)
config/
  station.php                        (existing, unchanged)
resources/
  views/
    tenant/
      layout.blade.php              (new: tenant site base layout)
      page.blade.php                (new: tenant page template)
routes/
  web.php                           (modified: remove /platform routes, add tenant routes)
  tenant.php                        (new: tenant subdomain routes)
bootstrap/
  app.php                           (modified: register ResolveTenant middleware)
  providers.php                     (modified: add StationServiceProvider)
```

### Files removed

```
app/Http/Controllers/Platform/TenantController.php
app/Http/Middleware/EnsurePlatformEnabled.php
resources/views/platform/tenants/index.blade.php
resources/views/platform/tenants/create.blade.php
resources/views/platform/tenants/show.blade.php
resources/views/components/layouts/platform.blade.php
```

---

## What This Does NOT Include

- CMS content editing UI beyond Filament's default form fields (rich text
  editor, media library, etc. are Phase 2+ or separate issues)
- Approval workflows / Flow module (Station's core differentiator, but a
  separate feature build)
- API routes or Sanctum token management UI
- Custom tenant themes (tenants share the base layout for now)
- Custom domain support (tenants use `{slug}.fissible.dev` only; custom domains
  are a future config/nginx concern)
- SSL for wildcard subdomains (requires wildcard cert via Forge — a Forge config
  step, not code)

---

## Dependencies

Must be done in this order:

1. Auth + Filament install (everything else needs auth working)
2. StationServiceProvider (registers bindings used by other components)
3. Filament resources (replaces Platform controller)
4. ResolveTenant middleware (needed before tenant rendering)
5. Tenant site rendering (depends on middleware)

Within step 3, the Filament resources are independent of each other and can
be built in any order.
