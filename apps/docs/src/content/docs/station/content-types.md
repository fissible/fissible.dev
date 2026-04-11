---
title: Content Types
description: Schema definitions for entries — field types, route patterns, templates, and content type configuration.
---

Content types define the schema and behavior for entries in Station. Each content type describes what fields an entry has, how it routes on the frontend, which template renders it, and what features (scheduling, workflow, API) are enabled.

## Content type fields

A content type record includes:

| Field | Type | Description |
|-------|------|-------------|
| `name` | string | Plural display name (e.g., "Blog Posts") |
| `singular_name` | string | Singular display name (e.g., "Blog Post") |
| `slug` | string | URL-safe identifier, unique per tenant |
| `fields` | JSON | Array of field definitions (see below) |
| `route_pattern` | string | URL prefix for frontend routing |
| `template` | string | Blade view from the active theme |
| `icon` | string | Heroicon name for admin navigation |
| `sort_order` | integer | Display order in admin sidebar |
| `api_enabled` | boolean | Expose entries via REST API |
| `scheduling_enabled` | boolean | Enable embargo/expire dates |
| `workflow_id` | FK (nullable) | Workflow pipeline for approval |

## Creating content types

Content types are managed in the admin panel under **Content > Content Types**. The create form collects the name, slug, icon, route pattern, template, and feature toggles. Fields are added via a repeater after creation.

The **pages** content type is created during installation and cannot be deleted. It uses hierarchical routing and is the only content type with parent-child URL nesting.

## Field types

Each field in the `fields` JSON array defines a form input and storage behavior. Supported types:

### text

Single-line text input. Stored as a string in the `data` JSON column.

### textarea

Multi-line plain text. Stored as a string. Renders as a `<textarea>` in the admin form and outputs as plain text (or pre-formatted) on the frontend.

### rich_text

WYSIWYG editor (Filament RichEditor). Stored as HTML. Renders as raw HTML on the frontend.

### content_blocks

Block-based editor using Filament Builder. Stored as a JSON array of typed blocks. See [Content Blocks](/station/content-blocks/) for full documentation.

**Configuration options:**
- `allowed_blocks` — array of block type keys to restrict available blocks (omit for all)
- `max_blocks` — maximum number of blocks (default: 50)

**Constraint:** maximum one `content_blocks` field per content type.

### toggle

Boolean switch. Stored as `true`/`false`. Renders as a toggle in the admin form.

### select

Dropdown with predefined options. Stored as the selected option's value string.

**Configuration:**
- `options` — array of `{ label, value }` objects

### date

Date picker (no time component). Stored as a date string (`YYYY-MM-DD`).

### datetime

Date and time picker. Stored as an ISO 8601 datetime string.

## Field configuration schema

Each field in the `fields` array follows this JSON structure:

```json
{
  "name": "subtitle",
  "label": "Subtitle",
  "type": "text",
  "required": false
}
```

For fields with additional configuration:

```json
{
  "name": "category",
  "label": "Category",
  "type": "select",
  "required": true,
  "options": [
    { "label": "News", "value": "news" },
    { "label": "Opinion", "value": "opinion" }
  ]
}
```

```json
{
  "name": "body",
  "label": "Body Content",
  "type": "content_blocks",
  "required": false,
  "allowed_blocks": ["text", "image", "callout"],
  "max_blocks": 20
}
```

Field name and type cannot be changed after creation. Changing `required` triggers a confirmation dialog showing affected entries.

## Route patterns

The `route_pattern` field determines how entries appear on the frontend.

### Pages (hierarchical routing)

The **pages** content type uses hierarchical routing based on `url_path`. A page's URL is built from its parent chain:

```
/about
/about/team
/about/team/leadership
```

The `url_path` column stores the full path. It is recomputed when a page's slug or parent changes.

### All other content types (flat routing)

Non-page content types use flat routing: `/{route_pattern}/{slug}`. For example, a "Blog Posts" content type with `route_pattern = blog` produces:

```
/blog/my-first-post
/blog/another-article
```

Listing pages are available at `/{route_pattern}` (e.g., `/blog`).

## Template assignment

The `template` field specifies which Blade view from the active theme renders entries of this content type. Themes register templates by including an annotation comment in the Blade file:

```php
{{-- @station action: show --}}
```

Templates registered this way appear in the template dropdown when editing a content type.

If the assigned template is not set or cannot be found in the active theme, the engine falls back to the theme's default `detail` view (`theme::detail`).

## API exposure

When `api_enabled` is true, entries of that content type are accessible via the REST API:

- **Single entry:** `GET /api/{route_pattern}/{slug}`
- **Listing:** `GET /api/{route_pattern}`

Only published, deliverable entries are returned. The API respects scheduling constraints (embargo/expire).

## Scheduling

When `scheduling_enabled` is true (the default), entries gain embargo and expiration date fields. See [Content Scheduling](/station/content-scheduling/) for details.

## Workflow assignment

Setting `workflow_id` routes entries through a review pipeline before publishing. If null and the tenant has a default workflow, the default is used. If no workflow applies, entries publish directly. See [Workflows](/station/workflows/) for details.

## Protected content types

The content type with slug `pages` is protected — it cannot be deleted through the admin UI or API. It is created during installation and serves as the foundation for hierarchical page routing.
