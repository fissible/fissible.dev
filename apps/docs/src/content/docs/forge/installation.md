---
title: Installation
description: Install forge via Composer. Requires fissible/accord and fissible/drift.
---

## Requirements

- PHP ^8.2
- fissible/accord ^1.0
- fissible/drift ^1.0

Both accord and drift are installed automatically as Composer dependencies.

## Install

```bash
composer require fissible/forge
```

## Auto-discovery

forge registers its service provider via Laravel's package auto-discovery. No manual registration is needed.

After installation, the `accord:generate` command is available:

```bash
php artisan accord:generate --help
```

## Dependency chain

forge depends on both accord and drift. Requiring forge gives you the full generation → validation → drift detection pipeline in one `composer require`.

| Package | Role |
|---------|------|
| `fissible/accord` | Runtime contract validation |
| `fissible/drift` | Drift detection and version analysis |
| `fissible/forge` | Spec generation from routes |
