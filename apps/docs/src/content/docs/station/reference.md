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

Station uses [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) for role-based access.

| Role | Scope | Description |
|------|-------|-------------|
| Platform Admin | Global | Manages all tenants, system settings, backups |
| Admin | Tenant | Full control within a single tenant |
| Editor | Tenant | Create and edit content, submit for review |
| Viewer | Tenant | Read-only access to content |

Custom roles can be created per tenant with granular permissions.

### Admin panels

Station provides two Filament panels:

- **Platform Panel** (`/platform`) — system-wide management for platform administrators. Tenant management, backup/restore, system health, notification channels.
- **Admin Panel** (`/admin`) — per-tenant management. Content, media, menus, site settings, user management.

## CMS Module

### Content types

Content types define the schema for entries. Each content type specifies:

- Fields (text, rich text, media, relationships, etc.)
- Validation rules
- Display configuration

### Entries

Entries are instances of a content type. Features include:

- Full version history with diff view
- Draft / published / archived status
- Slug generation and SEO metadata
- Media attachments via Spatie Media Library

#### Entry versioning

When a published entry is edited, Station creates a **draft fork** rather than modifying the live entry directly. The fork references the canonical entry via `version_of_id`, and a unique constraint ensures only one draft fork exists per entry at a time.

Draft forks go through the workflow (review or hotfix) before being merged back into the canonical entry. Each merge creates an **entry commit** — a snapshot of the entry's data, slug, and hierarchy for audit and rollback purposes.

Key operations:

- **Fork for editing** — race-safe via database transactions and unique constraint
- **Commit history** — browse prior versions and rollback to any commit
- **Rollback** — restores entry data, handles slug collisions, and deletes any active fork

#### Hotfix workflow

For urgent changes, editors can submit a draft fork as a **hotfix**. Hotfix entries bypass the standard review queue and route directly to super admins for approval. Only super admins can approve (publish) or reject (return to draft) a hotfix.

#### Content scheduling

Entries support two scheduling fields:

- **Embargo (`embargo_at`)** — the entry is published but hidden from delivery until the embargo date passes
- **Expiration (`expire_at`)** — the entry is automatically hidden after the expiration date

Scheduling is opt-in per content type (`scheduling_enabled`). Dates respect the site timezone configured in Site Settings. The `deliverable` scope on the Entry model filters out embargoed and expired entries automatically.

### Menus

Menus are managed through a drag-and-drop builder powered by a Livewire `DraggableTree` component. Each menu has a `handle` (used in templates) and a configurable `max_depth` (default 4).

#### Menu item types

Menu items use a driver pattern. Each type implements the `MenuItemTypeDriver` contract:

| Type | Description |
|------|-------------|
| External | Link to an external URL (configurable target: self or blank) |
| Entry | Link to a specific published entry |
| Listing | Expands to recent entries of a content type |
| Placeholder | Dynamic expansion — child pages, pages under a root slug, or content type listings |

Listing and placeholder items are **expanding** drivers — they resolve to multiple nodes at render time. The `MenuRenderer` service handles expansion, preloading entries and pages efficiently.

#### Role-based visibility

Each menu item can set a `required_role_level` (1–5). Items are filtered at render time based on the viewer's role level:

| Level | Role |
|-------|------|
| 1 | Reviewer |
| 2 | Author |
| 3 | Editor |
| 4 | Admin |
| 5 | Super Admin |

Items with no required level are visible to all visitors.

#### Protected menus

The `primary` and `secondary` menu handles are protected and cannot be deleted.

### SEO and sitemaps

Each entry supports meta title, description, and Open Graph fields. Sitemaps are generated automatically based on published entries.

## Flow Module

The Flow module provides a workflow engine for content lifecycle management. Flows are defined as step sequences with conditions, approvals, and automated actions.

## Maintenance mode

Each tenant can toggle maintenance mode from Site Settings. When enabled, the `EnforceMaintenanceMode` middleware returns a 503 response with a custom HTML message to unauthenticated visitors. Authenticated users bypass maintenance mode entirely.

Maintenance mode is per-tenant — each tenant's `SiteSettings` stores its own `maintenance_mode` flag and `maintenance_html` content.

## Account self-service

Users manage their own accounts from the profile settings page.

### Password and authentication

- Password changes via the standard Filament profile form
- **TOTP two-factor authentication** — users can enable app-based 2FA through Filament's built-in MFA UI. The TOTP secret is stored on the user model.

### Avatar

User avatars are stored via Spatie Media Library in the `avatar` collection on the `public` disk.

### Notification preferences

Users can opt out of specific notification types (e.g., `submitted_for_review`, `entry_published`). Preferences are stored as a JSON array on the user model. All notifications default to enabled.

### Account deletion (anonymization)

Users can delete their own account from the profile page. This requires password confirmation and triggers the `AnonymizeUser` service, which:

- Replaces the email with `anonymized+{uuid}@deleted.invalid` and randomizes the password
- Revokes all tenant memberships and role assignments
- Invalidates all active sessions
- Sets an `anonymized_at` timestamp
- Retains the user's name and authored content for audit purposes

A safety guard prevents deletion if the user is the only super admin in any tenant.

## Editorial context bar

Authenticated users see a compact navigation bar at the top of every page. The context bar provides:

- Quick navigation between frontend, admin panel, and platform panel (based on the user's role)
- Environment indicator — color-coded by environment (red for production, orange for staging, green for local)
- Extension points via Blade stacks (`context-bar-status` and `context-bar-actions`) for status indicators and quick actions

The bar is only visible to authenticated users and adapts its links based on role: editors see a link to the admin panel, platform admins see links to both admin and platform panels.

## Media

Station uses [Spatie Media Library](https://spatie.be/docs/laravel-medialibrary) for all file management. Media files are:

- Tracked in the `media` database table
- Stored on configurable Laravel filesystem disks (local, S3, etc.)
- Hashed on upload (`content_hash` in `custom_properties`) for integrity verification
