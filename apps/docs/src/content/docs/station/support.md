---
title: Support
description: Bundled help center, knowledge base, inline help text, and exception feedback for Station tenants.
sidebar:
  order: 59
---

The Support module is bundled with Station. It provides tenant-facing help surfaces and support feedback without requiring a separate package install.

Support depends on the CMS module because knowledge base and help content are stored as Station content types.

## What v1 includes

| Area | Capability |
|------|------------|
| Public knowledge base | `/help` and `/help/{slug}` routes for public and shared articles |
| Admin help center | **Support > Help Center** page inside the tenant admin panel |
| Inline help text | Platform-managed help text records addressable by key |
| Exception feedback | Browser-side soft-error toast and `/support/feedback` endpoint |
| Installer seed | Creates the support content types during installation |

The module is bundled, default-installed, and cannot be removed from the module UI.

## Content types

Support seeds three content types.

| Content type | Purpose | Managed by |
|--------------|---------|------------|
| `kb-article` | Tenant-authored knowledge base articles | Tenant editors/admins |
| `station-help` | Station product help shown in the admin Help Center | Platform-managed |
| `help-text` | Inline help snippets resolved by key | Platform-managed |

### Knowledge base articles

`kb-article` fields:

| Field | Description |
|-------|-------------|
| Title | Article title |
| Body | Rich text article body |
| Category | Optional grouping label |
| Audience | `public`, `internal`, or `both` |
| Helpful counts | Yes/no feedback counters |

The public `/help` routes show articles whose audience is `public` or `both`.

### Station Help

The admin Help Center reads `station-help` entries and groups them by section. It is intended for product help about Station itself.

Fields:

| Field | Description |
|-------|-------------|
| Title | Article title |
| Body | Rich text help content |
| Section | Group shown in the Help Center |

### Inline help text

`help-text` entries support reusable snippets. The platform resolves them by key and renders the body where a feature asks for contextual help.

Fields:

| Field | Description |
|-------|-------------|
| Key | Stable lookup key |
| Body | Rich text help body |

## Public knowledge base

Support registers:

```text
/help
/help/{slug}
```

These routes are guarded by the knowledge-base public setting and only expose articles intended for public audiences.

Use public articles for customer-facing help, setup instructions, and common troubleshooting pages. Use internal articles for tenant staff notes that should not appear on public help routes.

## Admin Help Center

Users with `support.kb.view` see **Support > Help Center** in the admin navigation.

The page provides searchable Station help content grouped by section. Users with `support.station-help.edit` can edit platform-managed Station help content.

## Exception feedback

Support registers a throttled feedback endpoint:

```text
POST /support/feedback
```

When browser-side support feedback is enabled, Station can capture user comments associated with a frontend exception or soft error. Feedback records store tenant, user, optional Sentry event ID, name, email, comments, IP address, user agent, and forwarding timestamp.

To connect browser feedback to Sentry, configure:

```ini
SENTRY_BROWSER_DSN=...
```

If the browser DSN is not set, Station can still show a generic support message without a Sentry event ID.

## Permissions

| Permission | Default roles |
|------------|---------------|
| `support.kb.view` | Reviewer, Author, Editor, Admin, Super Admin |
| `support.kb.edit` | Editor, Admin, Super Admin |
| `support.feedback.view` | Admin, Super Admin |
| `support.station-help.edit` | Super Admin |

## Operational notes

- Support content is tenant-scoped.
- Public knowledge base routes should be reviewed before launch because `public` and `both` audience articles are visible outside the admin panel.
- Station Help and inline help text are platform-managed so product help can stay consistent across tenants.
- The feedback endpoint is throttled to reduce abuse.
