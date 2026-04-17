---
title: Theme Development
description: Build custom Station themes. Covers theme structure, the theme.json contract, CSS variables, templates, distribution, and the theme API.
sidebar:
  order: 61
---

Station themes drive frontend rendering. Each tenant picks its own theme from the available set, and can override the theme's CSS variables without touching the theme files. This guide shows how to build a new theme from scratch.

## Quick start

Scaffold a theme with the built-in artisan command:

```bash
php artisan station:make-theme acme
```

This creates `themes/acme/` with a valid `theme.json`, the required Blade views, and a starter `assets/app.css`. Station runs a reconciliation pass, so the theme is immediately available in the Site Settings theme selector.

## Theme structure

Every theme lives in a single directory with this layout:

```
themes/acme/
  theme.json          # Required manifest
  views/
    layout.blade.php      # Required — base layout
    homepage.blade.php    # Required — homepage template
    list.blade.php        # Required — content type listing
    detail.blade.php      # Required — entry detail
    page.blade.php        # Optional — page template
    partials/             # Optional — reusable partials
  assets/
    app.css               # Theme stylesheet (served at /themes/{slug}/app.css)
  preview.jpg            # Optional — preview image
```

**Required views:** `layout.blade.php`, `homepage.blade.php`, `list.blade.php`, `detail.blade.php`. Missing any of these causes validation to fail and the theme is flagged as `invalid`.

## The `theme.json` contract

The manifest declares the theme's identity, CSS variables, and available templates.

```json
{
  "name": "Acme",
  "slug": "acme",
  "version": "1.0.0",
  "theme_api": 1,
  "variant": "default",
  "tier": "free",
  "description": "A clean, modern theme for Station.",
  "author": "Acme Studios",
  "station_requires": ">=0.5.0",
  "preview": "preview.jpg",
  "variables": {
    "--color-accent":     { "type": "color",     "default": "#2563EB" },
    "--color-text":       { "type": "color",     "default": "#1A1A2E" },
    "--color-background": { "type": "color",     "default": "#FFFFFF" },
    "--font-body":        { "type": "font",      "default": "system-ui, sans-serif" },
    "--max-width":        { "type": "dimension", "default": "1100px" }
  },
  "templates": ["layout", "homepage", "list", "detail", "page"]
}
```

| Field | Required | Description |
|-------|----------|-------------|
| `name` | Yes | Human-readable name, 1–100 chars. |
| `slug` | Yes | Lowercase letters, digits, hyphens. Must match the directory name. |
| `version` | Yes | Semver. |
| `theme_api` | Yes | Integer ≥ 1. The Station theme contract version the theme targets. |
| `tier` | Yes | `free` or `premium`. |
| `author` | Yes | String. |
| `station_requires` | Yes | Composer-style constraint (e.g., `>=0.5.0`). |
| `variables` | Yes | CSS variable declarations — see below. |
| `templates` | Yes | Non-empty list of template names. Each should map to `views/{name}.blade.php`. |
| `variant` | No | Optional sub-identifier (e.g., `warm`, `bold`). |
| `preview` | No | Path to a preview image relative to the theme root. |
| `description` | No | Long-form description. |

## CSS variables

Variables declared in `theme.json` can be overridden per-tenant from the Site Settings admin page.

### Declaring variables

```json
"variables": {
  "--color-accent": { "type": "color",     "default": "#2563EB" },
  "--font-body":    { "type": "font",      "default": "Georgia, serif" },
  "--max-width":    { "type": "dimension", "default": "1100px" }
}
```

Supported types:

| Type | Admin UI | Validation |
|------|---------|------------|
| `color` | Color picker | Any valid CSS color (hex, rgb, hsl, oklch, named colors). |
| `font` | Text input (200 char max) | Rejects `;`, `{`, `}`, `(`, `)`, `url(`, `@`, `expression(`. |
| `dimension` | Text input | `px`, `rem`, `em`, `%`, `vh`, `vw`, `vmin`, `vmax`, `ch`, `ex`. |

Keys must start with `--`. Invalid keys or types fail validation.

### Defaults in the stylesheet

Declare the defaults in `assets/app.css` so the theme works without any overrides:

```css
:root {
  --color-accent: #2563EB;
  --color-text: #1A1A2E;
  --color-background: #FFFFFF;
  --font-body: system-ui, sans-serif;
  --max-width: 1100px;
}

body {
  color: var(--color-text);
  background: var(--color-background);
  font-family: var(--font-body);
}
```

When a tenant overrides a variable, Station injects a `<style>` tag containing only the overrides. Theme defaults come from the theme's own CSS file — Station does not duplicate them inline.

### Override precedence

```
:root { --color-accent: #e63946; }   <-- tenant override (inline <style>, highest)
:root { --color-accent: #2563EB; }   <-- theme default (in app.css)
                                       browser default (lowest)
```

Tenant overrides apply as long as the variable is still declared in the current theme's `theme.json`. If a theme update removes or renames a variable, stale overrides are silently dropped from render output — no runtime error, no data loss.

## Templates

The `templates` array declares which views the theme provides. The admin-side Content Type template selector reads from this list.

```json
"templates": ["layout", "homepage", "list", "detail", "page"]
```

Each entry must correspond to `views/{name}.blade.php`. Missing files produce a warning at validation time.

### Custom templates

Content Types can point at a custom template in the theme. Mark templates available to the dropdown by adding an annotation on line 1:

```blade
{{-- @station action: show --}}
```

Optionally add a display name:

```blade
{{-- @station name: 'Featured Event' --}}
```

Templates with `@station action: show` appear in the Content Type template selector. Missing templates fall back to `detail` at render time.

### View namespace

Blade views in the active theme are registered under the `theme::` namespace:

```blade
@extends('theme::layout')

@section('content')
  <article>…</article>
@endsection
```

`FrontendController` registers the namespace on every request using the resolved theme's view path.

### Shared view data

Every frontend view receives these variables automatically:

| Variable | Description |
|----------|-------------|
| `$siteName` | Tenant's site name. |
| `$logoUrl` | URL of the tenant's logo, or `null`. |
| `$activeTheme` | Slug of the resolved theme. |
| `$theme` | `Theme` value object with `slug`, `name`, `version`, `viewPath`, `assetsPath`, etc. |
| `$themeOverrideCss` | Rendered CSS string containing tenant overrides. Empty if none. |
| `$primaryColor` | Legacy — the `primary_color` setting (still injected). |

The layout should include the theme stylesheet and inject overrides:

```blade
<link rel="stylesheet" href="{{ $theme->publicCssUrl() }}">
@if ($themeOverrideCss)
<style>{!! $themeOverrideCss !!}</style>
@endif
```

## Distribution

### As a Composer package

Publish a theme as a standalone Composer package. The package installer places the theme files in the Station `themes/` directory during install and triggers reconciliation.

Your package should include:

- `theme.json` at the package root.
- `views/` and `assets/` folders.
- A `composer.json` with appropriate metadata — see existing bundled themes for the pattern.

Once published, add it to the Fissible package registry (or your own self-hosted registry) so platform admins can install it from the Themes page.

### As a zip upload

Non-technical admins can upload a theme as a `.zip` archive from the platform Themes page. The archive must:

- Contain exactly one top-level directory named `{slug}/`, matching the `theme.json` slug.
- Pass the security scan:
  - No path traversal (`../`).
  - Allowed extensions only: `.blade.php`, `.css`, `.js`, `.json`, `.jpg`, `.jpeg`, `.png`, `.svg`, `.webp`, `.woff`, `.woff2`, `.txt`, `.md`.
  - No `.php` files outside `views/`.
  - No executable files, no symlinks.
- Be under the upload size limit (default 10MB, see `config/station.php`).

On upload, Station extracts to a temp directory, validates the structure and manifest, then copies to the primary writable theme path.

## Theme API versioning

`theme_api` in the manifest declares which version of the Station theme contract the theme targets. Station 0.5 ships `theme_api: 1`. Future contract changes will bump this number.

- **Forward compatibility:** A theme targeting `theme_api: 1` continues to work on any Station version that supports API 1.
- **Breaking changes:** When Station introduces `theme_api: 2`, themes must opt in by bumping their declared API. API 1 themes continue to work alongside API 2 themes.

## Failure modes

Station's theme system degrades gracefully. Common failures and their runtime behavior:

| Failure | Behavior |
|---------|----------|
| Tenant's `frontend_theme` points at a non-existent slug | Falls back through the resolution chain. Admin sees a warning banner in Site Settings. |
| Theme was soft-removed by a platform admin | Same as above — tenants referring to the removed slug see the fallback. Overrides are preserved for recovery. |
| Theme exists but `theme.json` is invalid | Theme is marked `invalid` at reconciliation. Tenants referring to it fall back; admin sees the warning. |
| Content Type points at a template not declared by the current theme | Falls back to `detail`. A warning is logged. |
| Tenant override references a variable no longer declared by the theme | Silently dropped from render output. Stored value preserved in case the variable returns. |

## Reconciliation

Station discovers themes on disk and syncs with DB metadata via **reconciliation**:

- Reconciliation runs daily via the scheduler.
- It also runs after any install/uninstall/upload.
- Manual: `php artisan station:theme:reconcile [--slug=]`.

The reconciliation report shows what changed (created, updated, missing, invalid, recovered). Run it after a manual file change if you want Station to pick up the update immediately.

## Next steps

- [Theme reference](/station/theme-reference/) — command and schema reference.
- [Frontend & theming](/station/frontend-theming/) — template rendering, editorial bar, SEO.
