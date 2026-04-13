---
title: Station
description: Self-hosted Laravel CMS and workflow platform with multi-tenancy, modular architecture, and admin UI powered by Filament.
sidebar:
  order: 0
---

Station is a self-hosted CMS and workflow platform built on Laravel 12. Create content types, build forms, define editorial workflows, and manage multiple sites — all from a single installation with a Filament-powered admin panel.

## What you get

- **Content management** — define content types with custom fields, create entries with versioning, schedule publishes with embargo/expiration dates
- **Form builder** — collect visitor submissions with spam defense and automation triggers
- **Editorial workflows** — review gates, field-level diffs, hotfix approval, and automated publishing
- **Multi-tenancy** — isolated tenants on a shared database, each with its own content, users, and configuration
- **Module system** — extend Station with external packages that plug into navigation, search, and permissions
- **Self-hosted** — deploy on your own server, keep your data

## Built-in modules

| Module | Description |
|--------|-------------|
| **CMS** | Content types, entries, versioning, media library, menus, scheduling, SEO, sitemaps, REST API |
| **Flow** | Workflow engine for content review, hotfix approval, notifications, and holds |
| **Forms** | Form builder with submissions, spam defense, and automation integration |

See [Modules](/station/modules/) for extending Station with custom modules.

## Architecture

Station is organized into layers:

- **Laravel Foundation** — Laravel 12, PHP 8.2+, MySQL, Redis, Queues, Sanctum, Spatie Media Library, Spatie Permissions
- **Platform Core** — Module Registry, Nav Registry, Search Registry, Auth/Roles, and tenant context. A Filament Adapter translates platform contracts into Filament v5 resources.
- **Modules** — Built-in modules (CMS, Flow, Forms) implement the `PlatformModule` contract. External modules implement `StationModule` and follow a managed lifecycle (install, upgrade, uninstall).
- **Admin UI** — Filament v5 + Livewire for all admin panels.
- **Content Delivery** — Blade templates, theme system, and REST API for the public-facing site.

### Admin panels

| Panel | URL | Purpose |
|-------|-----|---------|
| **Admin** | `/admin` | Per-tenant content, media, menus, forms, workflows, users, and site settings |
| **Platform** | `/platform` | Cross-tenant management, backups, system health, notification channels |

## Multi-tenancy

Station supports multiple tenants on a single installation. Each tenant gets isolated content, media, users, and configuration. Tenant resolution works via subdomain or custom domain mapping.

Platform administrators manage all tenants from the `/platform` panel. See [Multi-Tenancy](/station/multi-tenancy/) for details.

## Key concepts

- **[Content Types](/station/content-types/)** — define the shape of your content (fields, routing, templates)
- **[Content Blocks](/station/content-blocks/)** — block-based editor with text, image, callout, quote, embed, CTA, and form blocks
- **[Entry Versioning](/station/entry-versioning/)** — draft forks, commit history, and rollback
- **[Content Scheduling](/station/content-scheduling/)** — embargo and expiration dates for timed delivery
- **[Forms](/station/forms/)** — collect visitor submissions with spam defense
- **[Automations](/station/automations/)** — trigger actions (like creating entries) when events happen
- **[Workflows](/station/workflows/)** — review pipelines that govern the content lifecycle
- **[Menus](/station/menus/)** — drag-and-drop builder with nested items, dynamic expansion, and role-based visibility
- **[Roles & Permissions](/station/roles-permissions/)** — five-level hierarchy with tenant-scoped RBAC
- **[Modules](/station/modules/)** — extend Station with external packages
- **[Maintenance Mode](/station/maintenance-mode/)** — per-tenant 503 page for unauthenticated visitors
- **[Backups](/station/backup-restore/)** — tiered retention with self-service restore
