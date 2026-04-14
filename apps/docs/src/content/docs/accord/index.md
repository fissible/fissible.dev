---
title: accord
description: OpenAPI contract validator for PHP. PSR-7/15 core with drivers for Laravel, Slim, and Mezzio. The foundation of the Fissible PHP suite.
---

accord is an OpenAPI contract validator for PHP. It validates HTTP requests and responses against an OpenAPI 3.0 spec at runtime using PSR-7/15 middleware. Drivers are available for Laravel, Slim, and Mezzio.

accord is the foundation of the Fissible PHP suite. forge and drift build on it directly, and Station wraps the same contract-validation workflow into its integrated admin experience.

## The Fissible PHP suite

```
[forge]  ──────────────────────────────►  [accord]
generate / update spec                   validate at runtime
    ▲                                    │
    │                                    ▼
    └──────────────────────────────────  [drift]
                                         detect drift, bump version
```

- **forge** — generate an OpenAPI spec from your Laravel routes
- **accord** — validate requests and responses against the spec at runtime
- **drift** — detect spec drift, recommend semver bumps, generate changelogs
- **station** — wraps the same API tooling and exception triage into its built-in admin platform

## How it works

accord extracts the API version from the request URI (`/v1/` → `v1`), loads the matching spec file (`resources/openapi/v1.yaml`), and validates the request body and response body against the schemas defined in that spec.

When no schema is defined for a route, accord passes silently. This makes incremental adoption safe — you can add accord to an existing API and enable validation route by route.

## Quick start

```bash
composer require fissible/accord
```

Register the middleware on your `api` route group in Laravel:

```php
// routes/api.php (Laravel 11+, using ->middleware())
Route::middleware(['api', \Fissible\Accord\Drivers\Laravel\Middleware\ValidateApiContract::class])
    ->group(function () {
        // your API routes
    });
```

Place your spec at `resources/openapi/v1.yaml`. Set `ACCORD_FAILURE_MODE=log` while adopting, then switch to `exception` when your spec is complete.

See the [Installation](installation) page for the full 5-step integration guide.
