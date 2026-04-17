---
title: Theme Reference
description: Reference for the theme.json schema, CSS variable types, artisan commands, and configuration keys used by the Station theme system.
sidebar:
  order: 62
---

Reference material for building and operating Station themes.

## `theme.json` schema

| Field | Type | Required | Validation |
|-------|------|----------|------------|
| `name` | string | Yes | 1–100 chars. |
| `slug` | string | Yes | Lowercase alphanumeric + hyphens. Must match the directory name. |
| `version` | string | Yes | Valid semver (e.g., `1.0.0`). |
| `theme_api` | integer | Yes | ≥ 1. |
| `tier` | string | Yes | One of: `free`, `premium`. |
| `author` | string | Yes | Any string. |
| `station_requires` | string | Yes | Composer-style constraint (e.g., `>=0.5.0`). |
| `variables` | object | Yes | Keys start with `--`. Each value is `{ type, default }`. |
| `templates` | array<string> | Yes | Non-empty. Each entry must correspond to `views/{name}.blade.php`. |
| `variant` | string | No | Sub-identifier (e.g., `warm`, `bold`). |
| `suite` | string | No | Theme family identifier. |
| `description` | string | No | Long-form description. |
| `preview` | string | No | Path to preview image relative to theme root. |
| `logo` | object | No | `{ max_height?, max_width?, hint? }` — displayed in Site Settings to guide logo uploads. |

## Variable types

| Type | Admin UI | Accepted values |
|------|---------|-----------------|
| `color` | Color picker | Hex (`#RGB`, `#RRGGBB`, `#RRGGBBAA`), `rgb()`, `rgba()`, `hsl()`, `hsla()`, `oklch()`, named colors. |
| `font` | Text input (max 200 chars) | Font-family stack. Rejects `;`, `{`, `}`, `(`, `)`, `url(`, `@`, `expression(`. |
| `dimension` | Text input | Number with unit: `px`, `rem`, `em`, `%`, `vh`, `vw`, `vmin`, `vmax`, `ch`, `ex`. |

Any override value that fails validation is silently dropped from the rendered CSS.

## Required views

These are required in every theme — validation fails without them:

- `views/layout.blade.php`
- `views/homepage.blade.php`
- `views/list.blade.php`
- `views/detail.blade.php`

Declared in `templates` but missing the view file is a **warning**, not an error.

## Reconciliation rules

| Disk state | DB state | Action |
|------------|---------|--------|
| Valid theme exists | No row | Create row, `status=available`. |
| Valid theme exists | Row, any status | Set `status=available`, refresh `cached_metadata`, touch `last_seen_at`. |
| No theme on disk | Row, `status=available` | Set `status=missing`, log warning. |
| No theme on disk | Row, `removed_at` set | No change (intentional removal). |
| Theme exists but invalid | Any | Set `status=invalid`, populate `validation_errors`. |
| Theme returns to disk | Row, `status=missing` | Set `status=available`, clear `validation_errors`. |
| Same slug in multiple paths | — | Both flagged; slug marked `invalid`. |

## Resolution chain

When the frontend asks for a tenant's active theme, Station resolves it in this order:

1. `site_settings.frontend_theme` for the tenant.
2. `STATION_THEME` environment variable.
3. `'heartland'` system default.
4. First available theme (last resort).

Each fallback emits a `FallbackReason`:

| Reason | Cause |
|--------|-------|
| `none` | Tenant setting resolved successfully. |
| `missing` | Tenant setting points at a slug that does not exist on disk. |
| `invalid` | Tenant setting points at an invalid theme. |
| `removed` | Tenant setting points at a soft-removed theme. |
| `env_default` | No tenant setting; fell back to env var. |
| `system_default` | No tenant setting, no env; fell back to `heartland`. |

## Artisan commands

### `station:make-theme {slug}`

Scaffold a new theme in the primary writable theme path.

```bash
php artisan station:make-theme acme
```

Creates `themes/acme/` with a valid `theme.json`, required views, a starter `assets/app.css`, and runs reconciliation. Fails if the directory already exists or the slug is invalid.

### `station:theme:reconcile [--slug=]`

Reconcile theme registry with filesystem state.

```bash
php artisan station:theme:reconcile                # full sweep
php artisan station:theme:reconcile --slug=acme    # single theme
```

Outputs a `ReconcileReport` with counts of created, updated, missing, invalid, and recovered themes.

### `station:theme:list`

List all installed themes with source, status, version, and tenant usage.

```bash
php artisan station:theme:list
```

### `station:packages:refresh`

Sync the package registry from the remote feed (for Composer-installable themes).

```bash
php artisan station:packages:refresh
```

Requires `station.themes.registry_url` to be configured.

### `theme:set-default {slug}`

Legacy command. Writes `STATION_THEME` to `.env`. Per-tenant themes from Site Settings take precedence.

## Configuration keys

From `config/station.php`:

```php
'theme' => env('STATION_THEME', 'heartland'),

'themes' => [
    'paths' => [
        base_path('themes'),  // primary writable path (first) + any additional paths
    ],
    'max_upload_size' => 10 * 1024 * 1024,  // 10MB
    // 'registry_url' => 'https://registry.example.com/themes.json',
],
```

| Key | Purpose |
|-----|---------|
| `station.theme` | Env-default theme slug used when a tenant has no `frontend_theme`. |
| `station.themes.paths` | Array of directories scanned for themes. First path is the primary writable target. |
| `station.themes.max_upload_size` | Max bytes for zip uploads. |
| `station.themes.registry_url` | Optional remote feed URL for the package registry. |

## Zip upload security

| Check | Rule |
|-------|------|
| File size | ≤ `max_upload_size` (default 10MB). |
| Path traversal | Reject any entry containing `../`. |
| Allowed extensions | `.blade.php`, `.css`, `.js`, `.json`, `.jpg`, `.jpeg`, `.png`, `.svg`, `.webp`, `.woff`, `.woff2`, `.txt`, `.md`. |
| PHP files | Only in `views/`, only `.blade.php`. |
| Executable files | Rejected. |
| Symlinks | Rejected. |
| Structure | `theme.json`, `views/`, `assets/` required. |
| Slug match | `theme.json.slug` must match archive root directory name. |

## Scheduled tasks

Station registers two scheduled commands automatically:

| Command | Cadence |
|---------|---------|
| `station:theme:reconcile` | Daily at 03:30 |
| `station:packages:refresh` | Daily at 04:30 |

## Data model

| Table | Scope | Purpose |
|-------|-------|---------|
| `installed_themes` | Platform | Authoritative list of known themes with source, status, and staleness info. |
| `theme_overrides` | Tenant | CSS variable overrides per `(tenant_id, theme_slug)`. |
| `available_packages` | Platform | Curated Composer packages available for install. |
| `site_settings.frontend_theme` | Tenant | Tenant's selected theme slug (nullable, not an FK). |

## Related guides

- [Theme development](/station/themes/) — building a theme from scratch.
- [Frontend & theming](/station/frontend-theming/) — template rendering, editorial bar, SEO.
- [Configuration](/station/configuration/) — environment variables and config keys.
