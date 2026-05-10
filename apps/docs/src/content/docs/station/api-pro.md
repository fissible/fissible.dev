---
title: API Pro
description: Premium API operations module for route inspection, Request Lab, OpenAPI sync, version scaffolding, and fault triage.
sidebar:
  order: 58
---

API Pro adds API lifecycle tooling to Station's tenant admin panel. It combines route inspection, OpenAPI coverage, drift detection, live request testing, version scaffolding, and fault triage.

API Pro is a premium module. Its pages are visible only when the module is installed for the tenant and the current user has the required `api-pro.*` permission.

## What v1 includes

| Page | Purpose |
|------|---------|
| Dashboard | Route count, OpenAPI coverage, and version overview |
| Routes | Searchable route browser with flat/tree views and coverage status |
| Request Lab | Build and send requests against relative application API routes |
| Spec | View current OpenAPI spec, run drift detection, generate and sync specs |
| Versions | Track API versions and scaffold the next version |
| Faults | Triage captured fault groups with filters, trends, and bulk actions |

API Pro builds on the Fissible API toolchain:

| Package | Role |
|---------|------|
| `fissible/accord` | OpenAPI spec loading and validation |
| `fissible/drift` | Route inspection, coverage analysis, and drift detection |
| `fissible/forge` | OpenAPI generation from Laravel routes |

## Install and enable

Install the package and run the module lifecycle:

```bash
composer require fissible/api-pro
php artisan station:module:install api-pro
```

Platform admins can also install the module from **Platform > Modules** when the package is available to the Station installation.

## Writable mode

API Pro defaults to read-only behavior for operations that write files or send live requests.

Enable write actions with:

```ini
API_PRO_WRITABLE=true
```

Writable mode controls:

- Route stub generation
- Request Lab live request sending
- OpenAPI spec writes to `openapi/{version}.json`
- Version scaffolding

Keep writable mode off in environments where admins should inspect API state without changing files or sending requests.

## Dashboard

The dashboard summarizes the API surface:

- Total inspected routes
- OpenAPI coverage status
- Known API versions

Coverage is based on routes discovered from the running Laravel application and the configured OpenAPI spec source.

## Routes

The Routes page shows application routes with:

- Method and path
- Route name and action
- Coverage status
- Flat and tree views
- Search by path, method, action, or name

Users with `api-pro.routes.generate` can generate stubs when writable mode is enabled.

## Request Lab

Request Lab is an admin-facing API workbench.

Use it to:

- Search and select an application API route
- Fill path parameters, query parameters, body fields, and headers
- Preview a curl command
- Send a request to the current application
- Inspect status, duration, and response body

Safety constraints:

- Requests are limited to relative application routes.
- Sending requests requires `api-pro.request-lab.send`.
- Sending requests also requires `API_PRO_WRITABLE=true`.

## OpenAPI Spec

The Spec page can:

- Load and display the selected OpenAPI version
- Run drift detection between the spec and current Laravel routes
- Generate an OpenAPI spec from inspected routes
- Preview generated changes before writing
- Write specs to `openapi/{version}.json` when writable mode is enabled

Use drift detection before releases to identify routes that were added, removed, or changed without corresponding spec updates.

## Versions

The Versions page tracks API versions and supports scaffolding the next version from an existing one.

Version statuses:

| Status | Meaning |
|--------|---------|
| Draft | Work in progress |
| Latest | Current recommended version |
| Live | Version currently served |
| Retired | No longer active |

Live versions cannot be deleted until they are retired.

## Fault triage

The Faults page shows tenant-scoped fault groups.

Use it to:

- Filter open, resolved, and ignored faults
- Search by message, class, or file
- Sort by first seen, last seen, count, or class
- Review 14-day trend data
- Resolve, ignore, or reopen faults
- Bulk resolve or ignore selected faults

Fault triage requires `api-pro.faults.manage`.

## Permissions

| Permission | Default roles |
|------------|---------------|
| `api-pro.dashboard.view` | Reviewer and above |
| `api-pro.routes.view` | Reviewer and above |
| `api-pro.routes.generate` | Admin, Super Admin |
| `api-pro.request-lab.view` | Reviewer and above |
| `api-pro.request-lab.send` | Admin, Super Admin |
| `api-pro.spec.view` | Reviewer and above |
| `api-pro.spec.sync` | Admin, Super Admin |
| `api-pro.versions.view` | Editor and above |
| `api-pro.versions.manage` | Admin, Super Admin |
| `api-pro.faults.manage` | Editor and above |

## Operational notes

- API Pro reads the running application's routes, so run it in the environment whose API surface you want to inspect.
- Keep writable mode off unless the environment is intended for live request sending or file generation.
- Generated specs are scaffolds; review them before treating them as public contracts.
- Request Lab sends real application requests. Avoid destructive endpoints unless you intend to perform the action.
