---
title: Roles & Permissions
description: Tenant-scoped role hierarchy, authorization rules, admin minting, and Spatie Permission integration.
sidebar:
  order: 41
---

Station has five built-in roles arranged in a strict hierarchy. Each role inherits the capabilities of those below it. Roles are scoped per-tenant — a user can be an `editor` in one tenant and a `super_admin` in another.

The role system is built on [Spatie Laravel Permission](https://spatie.be/docs/laravel-permission) with **teams mode** enabled, where each tenant maps to a Spatie team.

## Role hierarchy

Roles follow a strict hierarchy that governs all user management actions:

```
super_admin > admin > editor > author > reviewer
```

| Role | Level | Capabilities |
|------|-------|-------------|
| Super Admin | 5 | Full control — manage all users, roles, site settings, workflows. Can toggle admin minting. |
| Admin | 4 | Manage editors, authors, and reviewers. Manage content types, menus, and site settings (except admin minting). Can manage other admins only if admin minting is enabled. |
| Editor | 3 | Create and edit content, submit for review, manage media |
| Author | 2 | Create content, submit for review |
| Reviewer | 1 | Review submitted content, approve or reject |

### Hierarchy enforcement

An actor can only manage users whose role is **strictly below** their own:

- **Super admin** can act on all roles including admin
- **Admin** can act on editor, author, reviewer (and admin only if admin minting is enabled)
- **Editor, author, reviewer** have no access to user management

This applies to all actions: role assignment, suspension, removal, and invitation.

## Authorization model

Authorization is enforced at three layers:

### 1. Panel access

Checked by `User::canAccessPanel()`:

- Platform admin (`is_platform_admin = true`) — access granted to any tenant, no membership required
- Otherwise — requires an **active** tenant membership (`status = active`) and **any Spatie role** scoped to that tenant

### 2. Resource visibility

Each Filament resource defines which roles can see it. For example, `UserResource` is visible only to `admin`, `super_admin`, and platform admins.

### 3. Action authorization

Individual actions (edit role, suspend, approve content) check both the required role and the hierarchy rules. Platform admins are treated as super admin equivalent at every authorization point.

## Tenant scoping

The `ResolveTenant` middleware sets the Spatie team context on every request:

```php
app(\Spatie\Permission\PermissionRegistrar::class)
    ->setPermissionsTeamId($tenant->id);
```

After this, all `$user->assignRole()` and `$user->hasRole()` calls automatically scope to the current tenant. Roles assigned in one tenant are invisible in another.

## Admin minting

The `allow_admin_minting` setting (per-tenant, default `false`) controls whether admins can assign or invite other admins:

| Setting | Who can assign `admin` role |
|---------|----------------------------|
| `false` (default) | Only super admins |
| `true` | Super admins and admins |

Only super admins can toggle this setting in Site Settings.

## Self-modification prevention

Users cannot:

- Change their own role
- Suspend themselves
- Remove themselves from a tenant

These guards prevent accidental lockout.

## Last super admin guard

The system refuses any action that would leave a tenant with zero super admins:

- Changing the last super admin's role to something lower
- Suspending the last super admin
- Removing the last super admin from the tenant

The check counts active super admin memberships in the tenant. If only one remains and it's the target user, the action is blocked.

## Suspended user behavior

A suspended user:

- Cannot access the admin panel for that tenant
- Retains their account and role assignment (reactivation restores access instantly)
- Can still access other tenants where they have active memberships
- Is blocked from API access and background jobs via the `EnsureActiveMembership` trait/middleware

## Role-based UI features

### Menu visibility

Menu items can set a `required_role_level` (1-5). Items are filtered at render time based on the viewer's role:

| Level | Role |
|-------|------|
| 1 | Reviewer |
| 2 | Author |
| 3 | Editor |
| 4 | Admin |
| 5 | Super Admin |

Items with no required level are visible to all visitors (including unauthenticated).

### Context bar

Authenticated users see an editorial context bar at the top of every page. Links adapt based on role — editors see a link to the admin panel, platform admins see links to both admin and platform panels. The bar is color-coded by environment (red for production, orange for staging, green for local).

### Workflow enforcement

The `enforce_workflow_for_admins` site setting (default `true`) controls whether admins must go through workflow steps or can bypass them. When disabled, admins and super admins can approve their own content and skip review gates.
