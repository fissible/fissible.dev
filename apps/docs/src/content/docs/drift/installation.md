---
title: Installation
description: Install drift via Composer. Requires fissible/accord (installed automatically).
---

## Requirements

- PHP ^8.2
- fissible/accord ^1.0 (installed automatically as a dependency)

## Install

```bash
composer require fissible/drift
```

`fissible/accord` is installed automatically as a Composer dependency — you do not need to require it separately.

## Auto-discovery

drift registers its service provider via Laravel's package auto-discovery. No manual registration in `config/app.php` is needed.

After installation, three Artisan commands are available:

```bash
php artisan accord:validate    # Check for drift (CI gate)
php artisan accord:version     # Full drift → version bump → changelog pipeline
php artisan drift:coverage     # Check controller implementation coverage
```

## Dev-only install

drift is typically installed as a dev dependency when used only for CI checks and local development. Use `--dev` if you don't need it in production:

```bash
composer require --dev fissible/drift
```

Note: if you also use [watch](../watch/) in production, install drift without `--dev` since watch depends on drift at runtime.
