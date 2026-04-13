---
title: Reference
description: Technical reference for Station's platform core, module system, and admin panels.
sidebar:
  order: 99
---

Quick-reference page linking to detailed documentation for each area of Station.

## Platform Core

### Roles and permissions

Station uses [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) for role-based access with **teams mode** (tenant-scoped). See [Roles & Permissions](/station/roles-permissions/) for the full hierarchy and authorization model.

| Role | Level | Scope | Description |
|------|-------|-------|-------------|
| Super Admin | 5 | Tenant | Full control including user management and site settings |
| Admin | 4 | Tenant | Manage users (below admin), content types, menus, settings |
| Editor | 3 | Tenant | Create and edit content, manage media, submit for review |
| Author | 2 | Tenant | Create content, submit for review |
| Reviewer | 1 | Tenant | Review submitted content, approve or reject |

**Platform Admins** (`is_platform_admin = true`) operate above the role hierarchy — they can access any tenant without a membership and are treated as super admin everywhere.

### Admin panels

| Panel | URL | Access | Purpose |
|-------|-----|--------|---------|
| **Admin** | `/admin` | Tenant members | Content, media, menus, forms, workflows, users, site settings |
| **Platform** | `/platform` | Platform admins | Tenant management, backups, system health, notification channels, modules |

### Module system

**Built-in modules** implement the `PlatformModule` contract:

```php
interface PlatformModule
{
    public function id(): string;
    public function name(): string;
    public function navigationItems(): array;
    public function searchProviders(): array;
    public function routes(): void;
    public function permissions(): array;
    public function permissionRoleMap(): array;
}
```

**External modules** implement `StationModule` with lifecycle methods (`install`, `uninstall`, `onUpgrade`), dependency declarations, and Composer package integration. See [Modules](/station/modules/) for the full reference.

## CMS Module

### Content types

Content types define the schema for entries — field definitions, routing, templates, and workflow assignment. See [Content Types](/station/content-types/) for all field types and configuration options.

### Entries

Entries are instances of a content type:

- Full version history with commit snapshots — see [Entry Versioning](/station/entry-versioning/)
- Draft fork model for safe editing of published content — see [Content Publishing](/station/content-publishing/)
- Embargo and expiration scheduling — see [Content Scheduling](/station/content-scheduling/)
- Block-based content editing — see [Content Blocks](/station/content-blocks/)
- Slug generation and SEO metadata
- Media attachments via Spatie Media Library

### Menus

Named, hierarchical menus with four item type drivers (External, Entry, Listing, Placeholder), drag-and-drop ordering, and role-based visibility. See [Menus](/station/menus/).

### SEO and sitemaps

Each entry supports meta title, description, and Open Graph fields. Sitemaps are generated automatically at `/sitemap.xml` from deliverable entries. See [Frontend & Theming](/station/frontend-theming/) for SEO resolution details.

## Forms Module

Form builder for collecting visitor submissions with honeypot, IP blocking, and rate limiting. Forms can trigger automations on submission. See [Forms](/station/forms/).

## Flow Module

### Workflows

Step-based content lifecycle pipelines with five handler types: Gate, Review, Publish, Notify, and Hold. See [Workflows](/station/workflows/).

### Automations

Event-driven rules that fire actions when triggers occur (e.g., form submission creates a CMS entry). See [Automations](/station/automations/).

## Multi-tenancy

Tenant-per-row model with automatic query scoping via global scopes. Subdomain and custom domain resolution. See [Multi-Tenancy](/station/multi-tenancy/).

## Users and access

- **Users & Invitations** — email-based invitation flow, team management, suspension. See [Users & Invitations](/station/users/).
- **Account Self-Service** — password changes, TOTP 2FA, avatars, notification preferences, account deletion. See [Account Self-Service](/station/account-self-service/).

## Frontend

- **Theming** — pluggable theme system with templates, editorial context bar, and SEO. See [Frontend & Theming](/station/frontend-theming/).
- **Maintenance Mode** — per-tenant 503 for unauthenticated visitors. See [Maintenance Mode](/station/maintenance-mode/).
- **Editorial Context Bar** — environment indicator, role-based navigation, extension stacks. See [Frontend & Theming](/station/frontend-theming/).

## Operations

- **Backup & Restore** — tiered retention, media manifests, self-service restore. See [Backup & Restore](/station/backup-restore/).
- **Setting Up Backups** — step-by-step guide. See [Setting Up Backups](/station/backups-setup/).
- **Seeder Suites** — industry-specific content presets for demos. See [Seeder Suites](/station/seeder-suites/).

## Media

Station uses [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary) for all file management. Media files are:

- Tracked in the `media` database table
- Stored on configurable Laravel filesystem disks (local, S3, etc.)
- Hashed on upload (`content_hash` in `custom_properties`) for integrity verification
- Inventoried in backup media manifests for restore verification
