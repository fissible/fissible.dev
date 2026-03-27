---
title: watch
description: Browser-based dev cockpit for Laravel. Dashboard, route browser, drift detector, spec manager, version tracker, and API explorer at /watch. No build step. HTMX + Alpine.js.
---

watch is a browser-based dev cockpit for Laravel. It mounts a dashboard, route browser, drift detector, spec manager, version tracker, and API explorer at `/watch` — all powered by HTMX and Alpine.js with no build step required.

```bash
composer require fissible/watch
```

## Features

| Feature | Path | Always shown |
|---------|------|-------------|
| Dashboard | `/watch` | Yes |
| Route browser | `/watch/routes` | Yes |
| OpenAPI spec viewer | `/watch/spec` | Yes |
| Drift detector | `/watch/drift` | Yes |
| Forge (spec generation) | `/watch/forge` | Yes |
| Version manager | `/watch/versions` | Yes |
| Trace (API explorer) | `/watch/trace` | When route is registered |
| Testing | `/watch/testing` | When route is registered |
| Faults (exception tracking) | `/watch/faults` | When fissible/fault is installed |

The Faults page appears automatically when [fissible/fault](../fault/) is installed — no additional configuration needed.

## No build step

watch uses HTMX for server-driven round trips and Alpine.js for client-side interactivity. Both are loaded from CDN. There is no `npm install`, no webpack config, and no asset compilation step.

## Modular nav

Navigation links appear automatically based on what is installed and what routes are registered. watch uses `Route::has()` to detect optional features. Installing fault adds the Faults link; registering a Trace route adds the Trace link.

## UI themes

Set a UI theme via the environment variable when deploying watch to non-local environments:

```dotenv
WATCH_ENV_THEME=staging     # staging banner
WATCH_ENV_THEME=production  # production banner
```

See the [Reference](reference) page for all configuration options and extension points.
