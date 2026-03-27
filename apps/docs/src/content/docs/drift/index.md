---
title: drift
description: OpenAPI drift detection and version analysis for PHP. Compares live routes against your spec, recommends semver bumps, and generates changelogs. Depends on fissible/accord.
---

drift detects when your Laravel routes have diverged from your OpenAPI spec. It recommends the appropriate semver bump (major, minor, or patch), updates the spec version, and prepends a changelog entry — all in one command.

```bash
composer require fissible/drift
```

## How it works

drift compares your live Laravel routes against the paths declared in your OpenAPI spec. It categorizes each difference:

- **Removed** — a path exists in the spec but has no matching route (breaking change → major)
- **Undocumented** — a route exists but has no matching spec path (additive change → minor)
- **Passing** — route and spec path match

The result is a `DriftReport` with clean/warning/failing entries. drift uses the report to recommend a semver bump and can write the new version and changelog entry back to your spec file.

## Quick start

```bash
# Install
composer require fissible/drift

# Check for drift in CI
php artisan accord:validate

# Run the full drift → changelog pipeline interactively
php artisan accord:version

# Check controller implementation coverage
php artisan drift:coverage
```

drift registers three Artisan commands. See the [Reference](reference) page for full documentation on each.
