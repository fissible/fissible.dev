---
title: Reference
description: fault reference — FaultGroup model API, fault_groups table, test generation workflow, and fingerprinting details.
---

## FaultGroup model API

```php
use Fissible\Fault\Models\FaultGroup;

$group = FaultGroup::find($id);

// Status checks
$group->isOpen();
$group->isResolved();
$group->isIgnored();

// Status transitions
$group->markResolved('Fixed in PR #123');
$group->markIgnored('Expected during maintenance');
$group->reopen();

// Convenience accessors
$group->shortClass();       // 'QueryException'
$group->relativeFile();     // 'app/Services/UserService.php'
```

### Status workflow

```
open  →  markResolved()  →  resolved
open  →  markIgnored()   →  ignored
resolved / ignored  →  reopen()  →  open
```

When `FAULT_REOPEN_ON_RECURRENCE=true`, fault calls `reopen()` automatically when a resolved group's exception is captured again.

---

## fault_groups table schema

| Column | Type | Description |
|--------|------|-------------|
| `id` | bigint | Primary key |
| `fingerprint` | string(64) | SHA-256 hash of `class\|file\|line` |
| `exception_class` | string | Fully-qualified exception class name |
| `file` | string | Absolute file path |
| `line` | integer | Line number |
| `status` | enum | `open`, `resolved`, `ignored` |
| `occurrence_count` | integer | Total number of captures |
| `first_seen_at` | timestamp | When the group was first created |
| `last_seen_at` | timestamp | When the most recent occurrence was captured |
| `resolved_at` | timestamp | When it was last resolved |
| `resolution_note` | text | Optional note from `markResolved()` |
| `stack_trace` | text | Sample stack trace from the most recent occurrence |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

## Test generation workflow

From the fault triage UI at `/watch/faults`, click **Generate Test** on any fault group. fault produces a PHPUnit skeleton annotated with `@group fault-{hash}`:

```php
/**
 * @group fault-deadbeef
 */
class FaultDeadbeefTest extends TestCase
{
    public function test_reproduces_query_exception_in_user_service(): void
    {
        // Arrange: reproduce the conditions that triggered the exception
        // File: app/Services/UserService.php, line 42

        // Act
        // ...

        // Assert
        // ...
    }
}
```

Run the generated test:

```bash
php artisan test --filter fault-deadbeef
```

When the test passes (the bug is fixed), mark the fault group as resolved in the UI.

---

## Fingerprinting

SHA-256 is computed from:

```
{exception_class}|{relative_file}|{line}
```

The exception message is intentionally excluded so that exceptions with variable-content messages (containing IDs, timestamps, query strings) map to the same group rather than creating separate groups per message variant.
