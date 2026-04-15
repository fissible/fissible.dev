# Phase 2: Station-Shaped App Structure

**Goal:** Make fissible.dev a working Station instance where tenants have real
public sites, the admin panel works, and new Station features can land without
restructuring the repo.

**Exit criteria:** A tenant provisioned through the admin panel gets a working
subdomain site that renders its pages. The Filament admin panel manages tenants,
pages, menus, and memberships. Auth protects the admin. The service provider
structure supports future module registration.

---

## Prerequisites

The tenant scaffold (models, migrations, services, config) exists in the working
tree but some files may not be committed to main. Before starting Phase 2
implementation, verify all of these are committed:

- `app/Models/Tenant.php`, `TenantMembership.php`, `TenantPage.php`, `TenantMenu.php`
- `database/migrations/2026_04_14_17*.php` (4 migrations)
- `app/Services/TenantProvisioner.php`, `TenantLifecycleService.php`,
  `TenantDomainService.php`, `TenantSeeder.php`
- `config/station.php`

If any are missing from main, commit them as a prerequisite PR before starting
Phase 2 work.

### Existing groundwork (do not rewrite)

- **Models:** Tenant, TenantMembership, TenantPage, TenantMenu, User
- **Migrations:** tenants, tenant_memberships, tenant_pages, tenant_menus
- **Services:** TenantProvisioner, TenantLifecycleService, TenantDomainService, TenantSeeder
- **Config:** config/station.php (platform toggle, domain config, demo settings)
- **Marketing site:** All public routes, MarketingController, Blade components

---

## Component 1: Auth + Filament Admin

### What

Install Filament v5. Filament serves the admin panel at `/admin` on the root
domain only. Users authenticate via Filament's built-in login.

Sanctum is **not** installed in this phase — there are no API routes or token
consumers yet. Add it when the API deliverable arrives.

### Admin authorization

Add a `is_platform_admin` boolean column to the users table (migration). Default
is `false`. Only users with `is_platform_admin = true` can access the Filament
panel.

The User model implements `FilamentUser` with:

```php
public function canAccessPanel(Panel $panel): bool
{
    return $this->is_platform_admin;
}
```

**First-admin bootstrap:** An artisan command `station:make-admin` that accepts
an email address and either promotes an existing user or creates one with a
prompted password. This runs once on first deploy:

```bash
php artisan station:make-admin admin@fissible.dev
```

Add this to the first-deploy checklist in `forge-deploy.sh` comments.

### How

- `composer require filament/filament`
- `php artisan filament:install --panels`
- Add `is_platform_admin` migration
- Configure AdminPanelProvider:
  - Path: `/admin`
  - Domain: `config('app.url')` host only (explicit `->domain()` constraint)
  - Auth guard: web (default)
- Create Filament resources for: Tenant, TenantPage, TenantMenu, TenantMembership
- Each resource gets list, create, edit, view pages via Filament's standard CRUD
- Create `station:make-admin` artisan command

### What replaces what

The current `app/Http/Controllers/Platform/TenantController.php` and its 3 Blade
views (`platform/tenants/index.blade.php`, `create.blade.php`, `show.blade.php`)
are replaced by Filament resources. The platform layout component
(`components/layouts/platform.blade.php`) is also removed.

The `/platform/*` routes in `routes/web.php` are removed. Filament handles its
own routing at `/admin`.

The `EnsurePlatformEnabled` middleware is removed — Filament's `canAccessPanel()`
gate replaces it.

### What stays

The 4 service classes stay — Filament resources call them for provisioning,
lifecycle transitions, and demo seeding.

---

## Component 2: Tenant Resolution Middleware

### What

Middleware that resolves the current tenant from the subdomain on every request
to `*.fissible.dev`. Binds the tenant to the request so downstream controllers
can access it.

### How

`ResolveTenant` middleware:

1. Extract subdomain from `Host` header
2. Compare against a configurable skip-list from `config('station.domains')`:
   the root domain, reserved slugs, and any explicitly listed app hostnames.
   Do not hardcode hostnames in the middleware.
3. If the host is on the skip-list, call `$next($request)` — no tenant context
4. Look up tenant by slug (subdomain = slug)
5. Abort 404 if no tenant found or tenant status is not `active`
6. Bind tenant to `app()->instance('tenant', $tenant)` and
   `$request->attributes->set('tenant', $tenant)`

Register in `bootstrap/app.php` as a web middleware alias (`tenant.resolve`).
**Do not** register as global middleware — it is applied only to the tenant
route group.

### Config additions to station.php

```php
'domains' => [
    'managed_root' => env('STATION_MANAGED_ROOT_DOMAIN', 'fissible.dev'),
    'app_hosts' => [
        // Hosts that are NOT tenant subdomains (skip resolution)
        // Automatically includes managed_root and www.managed_root
        env('STATION_PLATFORM_HOST', 'platform.fissible.dev'),
    ],
    'reserved_slugs' => [
        'www', 'platform', 'station-demo', 'api', 'admin',
        'mail', 'staging', 'app', 'support',
    ],
],
```

The middleware reads `app_hosts` + auto-generates root and www entries from
`managed_root`. Local dev (`localhost`, `127.0.0.1`) is always skipped.

### Forge/DNS requirements

- Wildcard DNS: `*.fissible.dev` A record pointing to the Forge server
- Forge site: enable "Wildcard Sub-Domains" in site settings
- Wildcard SSL: Let's Encrypt wildcard cert via Forge (requires DNS validation)

---

## Component 3: Tenant Site Rendering

### What

A controller and views that render a tenant's public-facing site. When a visitor
hits `acme.fissible.dev`, they see that tenant's pages rendered in a tenant-scoped
layout.

### Routes (routes/tenant.php)

Host-constrained route group. Only matches `{tenant}.{managed_root}` subdomains:

```php
Route::domain('{tenantSlug}.' . config('station.domains.managed_root'))
    ->middleware(['web', 'tenant.resolve'])
    ->group(function () {
        Route::get('/', [TenantSiteController::class, 'home']);
        Route::get('/{slug}', [TenantSiteController::class, 'page']);
    });
```

This file is loaded from `bootstrap/app.php` alongside `routes/web.php`.

### Controller

`TenantSiteController`:

- `home()` — find the homepage TenantPage for the current tenant, render it
- `page(string $slug)` — find TenantPage by slug for the current tenant, 404 if
  not found or not published (`published_at` must be non-null and in the past)

### Views

- `resources/views/tenant/layout.blade.php` — base layout for tenant sites.
  Includes the tenant's menus (from TenantMenu), tenant name in title, basic
  navigation. Uses the same CSS design system as the marketing site for now.
- `resources/views/tenant/page.blade.php` — renders a TenantPage (title, excerpt,
  body).

### Body rendering and sanitization

TenantPage.body is longText. It is **not** rendered as raw HTML. Instead:

- Body content is stored as HTML (produced by Filament's rich text editor)
- On render, body is passed through `Str::sanitizeHtml()` (Laravel's built-in
  HTML sanitizer, which strips script tags, event handlers, and dangerous
  attributes) before output
- The Blade template uses `{!! Str::sanitizeHtml($page->body) !!}`

This allows safe formatted content (headings, lists, links, images) while
preventing XSS from a compromised admin account.

### Menu rendering

TenantMenu has a `location` field (e.g., "primary", "secondary") and an `items`
JSON array. The tenant layout reads menus by location and renders them as nav
links. Menu item URLs are escaped normally via Blade's `{{ }}` syntax.

---

## Component 4: StationServiceProvider

### What

A dedicated service provider that registers Station-specific bindings and
configuration. Keeps Station concerns out of AppServiceProvider so future
module work has a clear integration point.

### Responsibilities

- Bind `TenantProvisioner`, `TenantLifecycleService`, `TenantDomainService`,
  `TenantSeeder` as singletons (they're stateless)
- Register a `tenant()` helper function that returns `app('tenant')` or null
- Merge `config/station.php` if needed (future: module registration hooks)

### What it does NOT do

Middleware registration stays in `bootstrap/app.php`. Route loading stays in
`bootstrap/app.php`. The provider handles service bindings and helpers only.

### Registration

Add to `bootstrap/providers.php` alongside AppServiceProvider.

---

## Routing Strategy

Three route groups, each host-constrained:

1. **Marketing + admin routes** (`routes/web.php`) — bound to the root domain
   via `Route::domain(config('station.domains.managed_root'))`. Marketing pages
   served by MarketingController. Filament admin at `/admin` with its own domain
   constraint in AdminPanelProvider (set to root domain host).

2. **Tenant routes** (`routes/tenant.php`) — bound to
   `{tenantSlug}.{managed_root}` via `Route::domain()`. ResolveTenant middleware
   applied. TenantSiteController renders pages.

The host constraints mean:
- `/admin` is unreachable on tenant subdomains (Filament's domain constraint)
- Marketing routes don't match on tenant subdomains (web.php domain constraint)
- Tenant routes don't match on the root domain (tenant.php domain constraint)
- `docs.fissible.dev` is a separate Forge site entirely and never hits this app

---

## File Structure (new/modified files)

```
app/
  Console/
    Commands/
      MakeAdminCommand.php             (new: station:make-admin)
  Filament/
    Resources/
      TenantResource.php
      TenantResource/Pages/
      TenantPageResource.php
      TenantPageResource/Pages/
      TenantMenuResource.php
      TenantMenuResource/Pages/
      TenantMembershipResource.php
      TenantMembershipResource/Pages/
  Http/
    Controllers/
      TenantSiteController.php         (new: public tenant page rendering)
    Middleware/
      ResolveTenant.php                (new: subdomain → tenant binding)
  Providers/
    Filament/
      AdminPanelProvider.php           (new: Filament panel config)
    StationServiceProvider.php         (new: Station bindings and helpers)
config/
  station.php                          (modified: add app_hosts to domains)
database/
  migrations/
    xxxx_add_is_platform_admin_to_users.php  (new)
resources/
  views/
    tenant/
      layout.blade.php                (new: tenant site base layout)
      page.blade.php                  (new: tenant page template)
routes/
  web.php                             (modified: remove /platform, add domain constraint)
  tenant.php                          (new: tenant subdomain routes)
bootstrap/
  app.php                             (modified: load tenant.php, register middleware alias)
  providers.php                       (modified: add StationServiceProvider)
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

- Rich text editor customization beyond Filament's default
- Approval workflows / Flow module (separate feature build)
- API routes or Sanctum (deferred until there is a consumer)
- Custom tenant themes (tenants share the base layout for now)
- Custom domain support (tenants use `{slug}.fissible.dev` only)
- SSL wildcard provisioning (Forge config step, not code)

---

## Dependencies

Must be done in this order:

1. Prerequisite: verify tenant scaffold is committed to main
2. Auth + Filament install + `is_platform_admin` migration + `station:make-admin`
3. StationServiceProvider (registers service bindings)
4. Filament resources (replaces Platform controller) + remove old platform code
5. ResolveTenant middleware + config updates + tenant route file
6. Tenant site rendering (controller, views, sanitized body output)

Within step 4, the Filament resources are independent of each other.
