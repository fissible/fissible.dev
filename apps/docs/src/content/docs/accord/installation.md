---
title: Installation
description: Install accord and integrate it in 5 steps. Covers Laravel middleware registration, failure mode selection, and CI setup.
---

## Requirements

- PHP ^8.2
- An OpenAPI 3.0.x spec (YAML or JSON)
- Laravel, Slim, or Mezzio

## 5-step integration

### Step 1 — Install

```bash
composer require fissible/accord
```

### Step 2 — Get a spec

Scaffold one from your existing routes using [forge](../forge/):

```bash
php artisan accord:generate
```

Or drop an existing spec at `resources/openapi/v1.yaml`.

### Step 3 — Register the middleware

**Laravel 11+ (using `bootstrap/app.php`):**

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->appendToGroup('api', [
        \Fissible\Accord\Drivers\Laravel\Middleware\ValidateApiContract::class,
    ]);
})
```

**Scoped route group:**

```php
Route::middleware([
    'api',
    \Fissible\Accord\Drivers\Laravel\Middleware\ValidateApiContract::class,
])->group(function () {
    // your API routes
});
```

### Step 4 — Set failure mode for adoption

Start with `log` mode so violations appear in your logs without breaking requests:

```dotenv
ACCORD_FAILURE_MODE=log
```

Once your spec covers all your routes, switch to `exception`:

```dotenv
ACCORD_FAILURE_MODE=exception
```

### Step 5 — Add drift detection to CI

```bash
composer require --dev fissible/drift
```

In your GitHub Actions workflow:

```yaml
- name: Check API contract (drift)
  run: php artisan accord:validate

- name: Check implementation coverage
  run: php artisan drift:coverage
```

---

## Publish config

```bash
php artisan vendor:publish --tag=accord-config
```

This creates `config/accord.php` with all available options. See the [Reference](reference) page for the full config reference.

---

## Slim driver

```php
$app->add(new \Fissible\Accord\Drivers\Slim\ValidateApiContract($specSource));
```

## Mezzio driver

```php
$pipeline->pipe(\Fissible\Accord\Drivers\Mezzio\ValidateApiContract::class);
```
