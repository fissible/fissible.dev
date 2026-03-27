---
title: Installation
description: Install fault, run the migration, wire up the exception handler, and configure the default ignore list.
---

## Requirements

- fissible/watch installed (fault displays inside the watch cockpit)

## Install

```bash
composer require fissible/fault
```

## Run the migration

fault stores fault groups in the database. Run the migration after installing:

```bash
php artisan migrate
```

This creates the `fault_groups` and `fault_occurrences` tables.

## Handler integration

Register the fault reporter in your Laravel exception handler (`bootstrap/app.php`):

```php
->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->report(function (Throwable $e): void {
        app(\Fissible\Fault\Services\FaultReporter::class)->capture($e);
    });
})
```

Every unhandled exception will now be captured, fingerprinted, and stored. Visit `/watch/faults` to see them.

## Configuration

```dotenv
FAULT_ENABLED=true
FAULT_MAX_GROUPS=500
FAULT_REOPEN_ON_RECURRENCE=true
FAULT_CONTEXT_DEPTH=10
```

| Variable | Default | Description |
|----------|---------|-------------|
| `FAULT_ENABLED` | `true` | Enable or disable exception capture |
| `FAULT_MAX_GROUPS` | `500` | Maximum number of fault groups to retain |
| `FAULT_REOPEN_ON_RECURRENCE` | `true` | Reopen a resolved group when the exception recurs |
| `FAULT_CONTEXT_DEPTH` | `10` | Number of stack frames to store per occurrence |

## Default ignore list

fault skips exceptions that are expected and not actionable. The default ignore list includes:

- `Symfony\Component\HttpKernel\Exception\NotFoundHttpException` (404s)
- `Illuminate\Auth\AuthenticationException` (unauthenticated requests)
- `Illuminate\Validation\ValidationException` (form validation errors)

To customize the ignore list, publish the config:

```bash
php artisan vendor:publish --tag=fault-config
```

Then edit `config/fault.php` to add or remove exception classes from the `ignore` array.
