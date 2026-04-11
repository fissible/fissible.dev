---
title: Reference
description: Technical reference for Station's platform core, module system, and admin panels.
---

## Platform Core

### Module Registry

Every module implements the `PlatformModule` contract and registers itself with the platform:

```php
interface PlatformModule
{
    public function id(): string;
    public function name(): string;
    public function navigationItems(): array;
    public function searchProviders(): array;
    public function panel(): Panel;
}
```

Registered modules automatically receive sidebar navigation, global search integration, and role-based access control.

### Roles and permissions

Station uses [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) for role-based access. See [Roles & Permissions](/station/roles-permissions/) for the full hierarchy and authorization model.

| Role | Scope | Description |
|------|-------|-------------|
| Platform Admin | Global | Manages all tenants, system settings, backups |
| Admin | Tenant | Full control within a single tenant |
| Editor | Tenant | Create and edit content, submit for review |
| Viewer | Tenant | Read-only access to content |

### Admin panels

Station provides two Filament panels:

- **Platform Panel** (`/platform`) — system-wide management for platform administrators. Tenant management, backup/restore, system health, notification channels.
- **Admin Panel** (`/admin`) — per-tenant management. Content, media, menus, site settings, user management.

## CMS Module

### Content types

Content types define the schema for entries — field definitions, routing, templates, and workflow assignment. See [Content Types](/station/content-types/) for the full reference including all field types and configuration options.

### Entries

Entries are instances of a content type. Features include:

- Full version history with diff view — see [Entry Versioning](/station/entry-versioning/)
- Draft / published / archived status
- Slug generation and SEO metadata
- Media attachments via Spatie Media Library
- Embargo and expiration scheduling — see [Content Scheduling](/station/content-scheduling/)

### Menus

Menus are managed through a drag-and-drop builder with four item type drivers (External, Entry, Listing, Placeholder). See [Menus](/station/menus/) for the full reference.

### SEO and sitemaps

Each entry supports meta title, description, and Open Graph fields. Sitemaps are generated automatically based on published entries. See [Frontend & Theming](/station/frontend-theming/) for SEO resolution details.

## Flow Module

The Flow module provides a workflow engine for content lifecycle management. Flows are defined as step sequences with conditions, approvals, and automated actions. See [Workflows](/station/workflows/) for the full reference.

## Multi-tenancy

Station uses a tenant-per-row model with automatic query scoping via global scopes. See [Multi-Tenancy](/station/multi-tenancy/) for tenant resolution, domain mapping, and membership details.

## Maintenance mode

Each tenant can toggle maintenance mode independently, returning 503 to unauthenticated visitors. See [Maintenance Mode](/station/maintenance-mode/).

## Account self-service

Users manage passwords, 2FA, avatars, notification preferences, and account deletion from the profile page. See [Account Self-Service](/station/account-self-service/).

## Editorial context bar

Authenticated users see a compact navigation bar at the top of every page with environment indicator, role-based navigation links, and extension stacks. See [Frontend & Theming](/station/frontend-theming/) for details.

## Media

Station uses [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary) for all file management. Media files are:

- Tracked in the `media` database table
- Stored on configurable Laravel filesystem disks (local, S3, etc.)
- Hashed on upload (`content_hash` in `custom_properties`) for integrity verification
