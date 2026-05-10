---
title: Modules
description: Install, manage, and build modules that extend Station with new features, permissions, and admin UI.
sidebar:
  order: 55
---

Station's module system lets you extend the platform with optional features. Modules are self-contained packages that integrate into the admin sidebar, permission system, search, installer hooks, and Filament pages without modifying core code.

## Quick overview

Station has three practical module types:

- **Built-in modules** (CMS, Flow, Forms) - always available, ship with Station
- **Bundled modules** (Support) - ship with Station and are registered through the module lifecycle, but cannot be removed
- **External modules** (AI, CRM, API Pro) - installed via Composer, managed through the admin UI or CLI

External modules follow a full lifecycle: install, configure, upgrade, disable, and remove. Each module can provide its own database migrations, permissions, Filament UI, menu contributions, installer hooks, and configuration.

## First-party modules

| Key | Module | Type | Notes |
|-----|--------|------|-------|
| `cms` | CMS | Built-in | Content, media, menus, publishing, scheduling, SEO |
| `support` | [Support](/station/support/) | Bundled | Knowledge base, help center, inline help text, support feedback |
| `ai` | [AI Module](/station/ai-module/) | External, free | Bring-your-own-key AI agents, usage audit, AI automation action |
| `crm` | [CRM Module](/station/crm-module/) | External, premium | Contacts, companies, deals, activities, CRM workflow triggers |
| `api-pro` | [API Pro](/station/api-pro/) | External, premium | API route browser, Request Lab, OpenAPI sync, versions, faults |

## Managing modules in the admin panel

Navigate to **Platform > Modules** to see all available modules organized into sections.

### Installed

Modules that are installed and active. Shows the installed version and lifecycle actions. Modules that other modules depend on cannot be removed until dependents are removed or disabled.

### Available

Modules that are registered but not yet installed. Shows the Composer package version, required dependencies, and an **Install** button.

If a module's dependencies are not yet installed, it appears in the **Blocked** section with instructions to install the dependencies first.

### Actions

| Action | Description |
|--------|-------------|
| **Install** | Runs migrations, publishes config, provisions permissions |
| **Disable** | Stops the module from booting while preserving data and permissions |
| **Enable** | Re-enables a disabled module from existing data |
| **Remove** | Disables the module and removes the Composer package when supported |
| **Retry** | Re-attempt a failed installation from where it left off |
| **Reset** | Clear a stuck installation state |
| **Check for Updates** | Runs `composer update` and detects version changes |
| **Refresh** | Regenerates the Composer autoload map |

## Managing modules from the CLI

### List modules

```bash
php artisan station:module:list
```

Shows a table of all registered modules with key, name, version, status, and dependencies.

### Install a module

```bash
php artisan station:module:install {key}
```

Resolves dependencies (installs them first if needed), runs compatibility checks, then executes the installation lifecycle:

1. **Migrations** — runs the module's database migrations
2. **Config** — publishes the module's configuration file
3. **Permissions** — creates permissions and assigns them to roles
4. **Complete** — marks the module as installed

If the Composer package isn't installed yet, the command prompts you to run `composer require`.

### Disable a module

```bash
php artisan station:module:disable {key}
```

Disabling preserves module data and permissions, but prevents the module provider from booting on the next request.

### Enable a module

```bash
php artisan station:module:enable {key}
```

Re-enables a disabled module without rerunning first-time installation.

### Remove a module

```bash
php artisan station:module:remove {key}
```

Blocks if other installed modules depend on this one. Otherwise, disables the module and removes the Composer package when removal is supported.

Database tables created by the module's migrations are **preserved** on removal. This is intentional - data is not destroyed when a module is removed.

`station:module:uninstall` remains as a deprecated alias for `station:module:remove`. Prefer `remove` in new scripts.

### Scaffold a new module

```bash
php artisan station:module:make
```

Interactive wizard that creates a module adapter class and registers it in the configuration. Prompts for name, package, description, and dependencies.

### Sync Docker and Composer config

```bash
php artisan station:module:sync
```

Updates `compose.yaml` with volume mounts and `composer.json` with path repositories for local fissible packages. Useful during development.

## Module lifecycle

### Installation

Modules install in dependency order — dependencies are resolved and installed first automatically. Each step is tracked in the database, so a failed installation can be retried from where it left off.

### Upgrades

When a module's Composer package is updated, `station:module:list` (or the admin UI's **Check for Updates** action) detects the version change and marks the module as `needs_upgrade`. The upgrade handler runs any version-specific migrations or data transformations.

### Disable and remove

Modules are disabled or removed in reverse dependency order. The system blocks removal if other installed modules still require the target module.

Removal is intentionally non-destructive: database tables and tenant data remain in place unless a future destructive cleanup tool explicitly purges them.

### Status values

| Status | Meaning |
|--------|---------|
| `available` | Registered but not installed |
| `installed` | Installed and active |
| `installing` | Installation in progress |
| `needs_upgrade` | Composer version is newer than installed version |
| `failed` | Installation failed (can retry or reset) |
| `disabled` | Manually disabled |
| `missing` | Adapter registered but Composer package not found |

## Permissions

Each module owns a permission namespace matching its key. For example, `ai.configure`, `crm.contacts.view`, and `api-pro.routes.view` all belong to their module namespaces. Permissions are provisioned during installation and assigned to roles according to the module's default mapping.

Permission assignments are only set on first creation. If a tenant admin later customizes role-permission assignments, module upgrades will not overwrite those changes.

## Building a custom module

### 1. Create the adapter

A module adapter implements the `StationModule` interface. The `AbstractStationModule` base class handles most of the lifecycle:

```php
namespace App\Modules;

use App\Platform\Support\AbstractStationModule;

class MyModule extends AbstractStationModule
{
    public function key(): string { return 'mymodule'; }
    public function name(): string { return 'My Module'; }
    public function description(): string { return 'Description of what it does.'; }
    public function provider(): string { return 'Vendor\\MyModule\\MyModuleServiceProvider'; }

    protected function composerPackage(): string { return 'vendor/my-module'; }

    public function requires(): array { return []; }  // dependency keys

    public function permissions(): array
    {
        return ['mymodule.view', 'mymodule.manage'];
    }

    public function permissionRoleMap(): array
    {
        return [
            'mymodule.view'   => ['reviewer', 'author', 'editor', 'admin', 'super_admin'],
            'mymodule.manage' => ['admin', 'super_admin'],
        ];
    }

    public function hasMigrations(): bool { return true; }

    public function migrationPath(): ?string
    {
        return base_path('vendor/vendor/my-module/database/migrations');
    }
}
```

### 2. Register the adapter

Add it to `config/station-modules.php`:

```php
return [
    'mymodule' => ['adapter' => \App\Modules\MyModule::class],
    // ...existing modules
];
```

### 3. Create the Composer package

The module's code lives in a separate Composer package with its own service provider, migrations, and Filament resources. The service provider should extend `StationModuleServiceProvider` so it only boots when the module is enabled:

```php
use App\Platform\Support\StationModuleServiceProvider;

class MyModuleServiceProvider extends StationModuleServiceProvider
{
    protected function moduleKey(): string { return 'mymodule'; }

    protected function registerModule(): void
    {
        // Bind services
    }

    protected function bootModule(): void
    {
        // Register routes, views, Filament plugins
    }
}
```

### 4. Install

```bash
composer require vendor/my-module
php artisan station:module:install mymodule
```

Or use `php artisan station:module:make` to scaffold the adapter interactively.
