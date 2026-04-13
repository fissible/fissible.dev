---
title: Maintenance Mode
description: Per-tenant maintenance mode that returns 503 to unauthenticated visitors while allowing admins to continue working.
sidebar:
  order: 75
---

Station provides a per-tenant maintenance mode that displays a 503 page to unauthenticated visitors while authenticated users continue to access the site normally. Each tenant controls its own maintenance state independently.

## Toggling maintenance mode

Maintenance mode is controlled from the **Site Settings** page in the admin panel. Two fields are available:

| Field | Type | Purpose |
|-------|------|---------|
| **maintenance_mode** | Boolean toggle | Enables or disables maintenance mode for the current tenant |
| **maintenance_html** | Textarea (optional) | Custom HTML to display to visitors during maintenance |

When `maintenance_mode` is enabled, unauthenticated visitors receive a **503 Service Unavailable** response on all CMS frontend routes.

## Middleware

The `EnforceMaintenanceMode` middleware runs on CMS frontend routes and performs two checks:

1. **Is maintenance mode active?** — reads `maintenance_mode` from the tenant's `SiteSettings`
2. **Is the user authenticated?** — authenticated users always bypass maintenance mode

If both conditions are met (maintenance active, user unauthenticated), the middleware returns a 503 response.

## What visitors see

The 503 response renders one of two views:

- **Custom HTML** — if `maintenance_html` is set in Site Settings, that HTML is returned directly
- **Default message** — a simple "Under Maintenance" page when no custom HTML is configured

## Routes affected

Maintenance mode applies to all CMS frontend routes:

| Route | Affected |
|-------|----------|
| `/` (homepage) | Yes |
| `/{contentType}` (listing) | Yes |
| `/{contentType}/{slug}` (detail) | Yes |
| `/{path}` (hierarchical pages) | Yes |
| `/admin/*` (admin panel) | No |
| `/platform/*` (platform panel) | No |
| API routes | No |
| `/sitemap.xml` | No |
| Auth and invite routes | No |

## Per-tenant isolation

Each tenant has its own `SiteSettings` record with independent `maintenance_mode` and `maintenance_html` values. Enabling maintenance on one tenant has no effect on other tenants.
