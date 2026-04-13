---
title: Frontend & Theming
description: Theme-based frontend rendering with pluggable templates, editorial tools, SEO resolution, and sitemap generation.
sidebar:
  order: 60
---

Station's frontend uses a theme-based rendering system with pluggable templates, an editorial context bar for authenticated users, SEO resolution, and automatic sitemap generation.

## Theme structure

Each theme lives in a `themes/{slug}/` directory with the following layout:

```
themes/heartland/
  theme.json          # Theme manifest
  views/
    layout.blade.php      # Base layout (required)
    homepage.blade.php    # Homepage template (required)
    list.blade.php        # Content type listing (required)
    detail.blade.php      # Entry detail (required)
    page.blade.php        # Page template
    partials/             # Reusable partials
  assets/
    styles.css            # Theme CSS
```

**Required views:** `layout.blade.php`, `homepage.blade.php`, `list.blade.php`, `detail.blade.php`

## Theme manifest

The `theme.json` file describes the theme:

```json
{
  "name": "Heartland",
  "slug": "heartland",
  "version": "1.0.0",
  "variant": "default",
  "tier": "free",
  "description": "A warm, earthy theme for content-driven sites.",
  "author": "fissible",
  "station_requires": ">=0.3.0",
  "preview": "preview.png",
  "logo": {
    "path": "assets/logo.svg",
    "height": 40
  }
}
```

| Field | Description |
|-------|-------------|
| **name** | Human-readable theme name |
| **slug** | URL-safe identifier, must match directory name |
| **version** | Semver version of the theme |
| **variant** | Theme variant (e.g., `default`, `bold`) |
| **tier** | Availability tier (`free`, `pro`) |
| **station_requires** | Minimum Station version |
| **preview** | Path to preview screenshot |
| **logo** | Logo configuration with path and height |

## Theme selection

The active theme is set via the `STATION_THEME` env var (default: `heartland`).

### Built-in themes

| Theme | Slug | Description |
|-------|------|-------------|
| **Heartland** | `heartland` | Warm, earthy tones |
| **Light** | `light` | Clean white background |
| **Dark** | `dark` | Dark background |
| **Minimal** | `minimal` | Monochrome, typography-focused |

## Theme commands

```bash
# List all available themes
php artisan theme:list

# Set the default theme
php artisan theme:set-default {slug}

# Install a theme from a path
php artisan theme:install {path}
```

## Template resolution

When rendering an entry, Station resolves the template in this order:

1. **Content type `template` field** — if set, converted to the `theme::` namespace (e.g., `events-detail` becomes `theme::events-detail`)
2. **Fallback** — if the resolved template does not exist, falls back to `theme::detail`

Pages always use `theme::page` regardless of the template field.

### Custom templates

To register a custom template in the admin dropdown, annotate the first line of the Blade file:

```blade
{{-- @station action: show --}}
```

Optionally provide a display name:

```blade
{{-- @station name: 'Featured Event' --}}
```

Templates with the `@station action: show` annotation appear in the template selector when editing content types.

## Display helpers

The `Entry` model provides helper methods for frontend rendering:

| Method | Description |
|--------|-------------|
| `displayTitle()` | Returns the first text field value, or the slug if no text fields exist |
| `displayBody()` | Returns all fields except the title field |
| `displayExcerpt(160)` | Returns a plain-text truncation of the body (default 160 characters) |
| `renderFields(exclude)` | Renders all fields by type, optionally excluding named fields |
| `renderContentBlocks(blocks)` | Renders content blocks via the block registry |

## Editorial context bar

Authenticated users see a top bar on the frontend providing editorial tools and navigation.

### Components

| Component | Description |
|-----------|-------------|
| **Environment indicator** | Color-coded badge showing the current environment (local, staging, production) |
| **Navigation links** | Links to frontend, admin panel, and platform panel (based on user role) |
| **Extension stacks** | Blade stacks `context-bar-status` and `context-bar-actions` for plugins |
| **Maintenance banner** | Displayed when maintenance mode is active for the current tenant |

## SEO

The `SeoResolver` service resolves meta tags for each entry with fallback logic:

| Field | Resolution |
|-------|------------|
| **meta_title** | Entry `meta_title` field, or `displayTitle()` |
| **meta_description** | Entry `meta_description` field, or `displayExcerpt()` |
| **ogImageUrl** | Entry Open Graph image, or site default |

The base layout includes:

- Canonical URL for each page
- Open Graph tags (`og:title`, `og:description`, `og:image`)
- Link to `/sitemap.xml`

## Sitemap

Station generates a sitemap at `/sitemap.xml` from all deliverable (published, non-expired) entries.

| Content | Priority |
|---------|----------|
| Top-level pages | `1.0` |
| Child pages | `0.8` |
| All other content types | `0.6` |

The sitemap is cached per tenant with a **1-hour TTL**.

## Frontend routes

| Route | Description |
|-------|-------------|
| `/` | Homepage |
| `/{contentType}` | Content type listing (paginated) |
| `/{contentType}/{slug}` | Entry detail |
| `/{path}` | Hierarchical page (supports nested paths) |
| `/preview/{uuid}` | Draft preview (authentication required) |
| `/page/{entry}/preview` | Page draft preview (authentication required) |
