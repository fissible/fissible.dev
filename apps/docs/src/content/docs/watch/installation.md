---
title: Installation
description: Install watch via Composer and configure access control, write mode, and URL prefix.
---

## Requirements

- Laravel
- fissible/accord, fissible/drift, and fissible/forge (installed automatically as dependencies)

## Install

```bash
composer require fissible/watch
```

Dependencies (accord, drift, forge) are installed automatically.

## Auto-discovery

watch registers its service provider via Laravel's package auto-discovery. Visit `/watch` in your browser — no additional registration is needed.

## Publish config

```bash
php artisan vendor:publish --tag=watch-config
```

This creates `config/watch.php`.

## Configuration

All configuration is driven by environment variables:

```dotenv
WATCH_WRITABLE=true         # Enable write actions (spec generation, version bumps)
WATCH_ENV_THEME=staging     # Force UI theme: staging | production
WATCH_PREFIX=watch          # URL prefix (default: watch → /watch)
WATCH_BRAND=watch           # Nav brand name
```

### WATCH_WRITABLE

When set to `true`, write actions are enabled: spec generation via forge, version bumps via drift, and any other actions that modify files. Set to `false` (or omit) to make the cockpit fully read-only — safe for shared or production environments.

## Access control

watch does not enforce authentication by default. Restrict access by wrapping the watch routes in an auth middleware in your application:

```php
// In a service provider or routes file:
Route::middleware(['auth', 'can:view-watch-cockpit'])
    ->prefix(config('watch.prefix', 'watch'))
    ->group(function () {
        // watch routes are registered here by the service provider
    });
```

Alternatively, use the `watch.local` middleware alias to restrict access to local/development/testing environments only. See the [Reference](reference) page.
