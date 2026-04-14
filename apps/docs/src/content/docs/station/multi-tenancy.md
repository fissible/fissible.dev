---
title: Multi-Tenancy
description: Tenant-per-row architecture with automatic query scoping, domain resolution, and per-tenant permissions.
sidebar:
  order: 50
---

Station supports running multiple independent sites from a single installation. Each tenant gets its own content, users, menus, and configuration — completely isolated from other tenants while sharing the same codebase and database.

For single-site installations, the default tenant is created during setup and you don't need to think about tenancy at all.

Station uses a **tenant-per-row** model where all tenants share the same database. Tenant isolation is enforced through global query scopes and middleware-driven context resolution.

## Tenant model

Each tenant represents an independent site:

| Field | Type | Description |
|-------|------|-------------|
| `name` | string | Display name |
| `slug` | string | Subdomain identifier (unique) |
| `domain` | string (nullable) | Custom domain override |
| `uuid` | string | Public-facing unique identifier |

## TenantContext

`TenantContext` is a request-scoped singleton that tracks the current tenant. It is set by the `ResolveTenant` middleware at the start of each request and consumed by:

- **Global scopes** on tenant-scoped models (automatic query filtering)
- **Model boot hooks** (automatic `tenant_id` assignment on creation)
- **Service classes** that need tenant awareness

## HasTenant trait

Applied to all tenant-scoped models (entries, content types, menus, workflows, etc.). The trait:

1. Registers a `TenantScope` global scope that adds `WHERE tenant_id = ?` to all queries
2. Hooks into the model's `creating` event to auto-assign `tenant_id` from `TenantContext`

This means tenant filtering is automatic — application code does not need to manually filter by tenant.

## Tenant resolution

The `ResolveTenant` middleware resolves the current tenant on every request using this priority:

1. **Custom domain match** — if the request host matches a tenant's `domain` field, that tenant is selected
2. **Subdomain match** — the first subdomain segment is matched against tenant `slug`
3. **Single-tenant fallback** — if neither matches and only one tenant exists, it is used (`Tenant::first()`)

The middleware skips tenant resolution for `/platform` and `/setup` routes (these operate outside tenant context).

Resolution is wrapped in a try-catch to handle fresh installs where no tenants exist yet.

### `STATION_TENANCY_MODE`

Tenant fallback behavior is controlled by `STATION_TENANCY_MODE`:

| Mode | Behavior |
|------|----------|
| `single` | Unknown hosts fall back to the first tenant. This is the default and is suitable for single-site self-hosted installs. |
| `multi` | Unknown hosts do **not** fall back. The request returns `404` unless the host matches a tenant domain or subdomain. |

For multi-tenant production installs, set:

```dotenv
STATION_TENANCY_MODE=multi
```

This makes host routing fail closed, which avoids accidentally serving the first tenant when DNS, proxy, or domain mapping is misconfigured.

## Tenant membership

The `TenantMembership` model links users to tenants:

| Field | Type | Description |
|-------|------|-------------|
| `user_id` | FK | The user |
| `tenant_id` | FK | The tenant |
| `status` | enum | `active` or `suspended` |
| `added_via` | string | How the user joined: `invitation` or `installer` |

A unique constraint on `(user_id, tenant_id)` prevents duplicate memberships. Users can belong to multiple tenants.

## Domain mapping

Tenants support two routing modes:

- **Subdomain** — the `slug` field maps to a subdomain (e.g., `acme` resolves `acme.yourdomain.com`)
- **Custom domain** — the `domain` field maps a fully qualified domain (e.g., `www.acme.com`)

Custom domains are checked first during resolution. This allows a tenant to operate under its own domain while still being part of the shared Station installation.

## Tenant creation

Tenants are created through two paths:

- **Web installer** — `php artisan station:install` (or the `/setup` wizard) creates the default tenant during initial setup
- **Platform admin panel** — platform administrators can create additional tenants at `/platform`

## Cross-tenant access

Users with the `is_platform_admin` flag on their user record bypass tenant membership checks. Platform admins can:

- Access all tenants regardless of membership
- Manage tenants, users, and system settings via the `/platform` panel
- View cross-tenant reports and backups

## Queued jobs

Asynchronous jobs that operate on tenant-scoped data must run in the correct tenant context. The `SetTenantContext` job middleware ensures this:

```php
// Applied to queued jobs automatically
class SetTenantContext
{
    public function handle($job, $next)
    {
        // Sets TenantContext from the job's tenant_id
        // before the job executes
    }
}
```

Jobs that are dispatched within a tenant context automatically capture the `tenant_id` and restore it when the job runs.

## Permission integration

Station uses Spatie Permission with **teams mode** enabled. The `team_id` in Spatie's permission tables maps to `tenant_id`, which means:

- Roles and permissions are scoped per tenant
- A user can have different roles in different tenants
- Role checks automatically use the current tenant from `TenantContext`

This integrates with the [Roles & Permissions](/station/roles-permissions/) system.
