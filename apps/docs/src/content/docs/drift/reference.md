---
title: Reference
description: drift reference — accord:validate, accord:version, drift:coverage, DriftDetector, VersionAnalyser, and ChangelogGenerator.
---

## Artisan commands

### accord:validate

Checks for drift between your live routes and the OpenAPI spec. Prints a table of results and exits non-zero if any drift is found — suitable as a CI gate.

```bash
php artisan accord:validate
php artisan accord:validate --api-version=v2
```

**Output table statuses:**

| Status | Meaning |
|--------|---------|
| PASS | Route and spec path match |
| WARN | Route exists but has no spec path (undocumented) |
| FAIL | Spec path exists but has no matching route (removed) |

Exits with a non-zero code on any WARN or FAIL, making it suitable for use as a CI gate.

---

### accord:version

The full drift → changelog pipeline. Detects drift, reads the current `info.version` from the spec, recommends a semver bump, prompts for confirmation, updates the spec, and prepends a changelog entry.

```bash
php artisan accord:version
php artisan accord:version --api-version=v1 --dry-run
php artisan accord:version --yes   # skip confirmation prompt
```

Steps performed:
1. Detect drift
2. Read current `info.version` from the spec
3. Recommend a semver bump
4. Prompt for confirmation (skipped with `--yes`)
5. Update the spec version
6. Prepend a changelog entry

---

### drift:coverage

Checks that your controllers implement the routes declared in your spec.

```bash
php artisan drift:coverage
```

**Output statuses:**

| Status | Meaning |
|--------|---------|
| IMPLEMENTED | Controller method exists and is callable |
| MISSING | No controller method found — would return 500 |
| UNKNOWN | Route uses a closure — cannot be statically analyzed |

---

## Core API

### DriftDetector

```php
use Fissible\Drift\DriftDetector;

$detector = new DriftDetector($source);
$report   = $detector->detect($routes, 'v1');

$report->isClean();              // bool — true if no drift
$report->hasBreakingChanges();   // bool — true if any FAIL entries
$report->hasAdditiveChanges();   // bool — true if any WARN entries
$report->summary();              // string description of the report
```

`$source` is a `SpecSourceInterface` instance (see [accord reference](../accord/reference)).

---

### VersionAnalyser

Analyzes a `DriftReport` and recommends a semver bump.

```php
use Fissible\Drift\VersionAnalyser;

$recommendation = (new VersionAnalyser($source))->analyse($report);

$recommendation->bumpType;               // 'major' | 'minor' | 'patch' | 'none'
$recommendation->recommendedVersion;     // e.g. '1.2.0'
$recommendation->requiresNewUriVersion;  // bool — true if a new /v2 is needed
```

---

### ChangelogGenerator

Generates a changelog entry from a report and recommendation, and prepends it to a changelog file.

```php
use Fissible\Drift\ChangelogGenerator;

$generator = new ChangelogGenerator();
$entry     = $generator->generate($report, $recommendation);

// Prepend the entry to CHANGELOG.md
$generator->prepend($entry, base_path('CHANGELOG.md'));
```
