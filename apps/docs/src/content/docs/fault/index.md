---
title: fault
description: Exception tracking for the watch cockpit. Captures, deduplicates, and triages unhandled exceptions at /watch/faults. Depends on fissible/watch.
---

fault captures every unhandled exception in your Laravel application, deduplicates them by fingerprint, and surfaces them in a triage UI at `/watch/faults`.

```bash
composer require fissible/fault
```

## Features

| Feature | Description |
|---------|-------------|
| Capture | Hooks into `withExceptions()` to record every unhandled exception |
| Deduplication | Groups exceptions by SHA-256 fingerprint: `class\|file\|line` |
| Triage UI | Filterable, paginated list at `/watch/faults` with open/resolved/ignored status |
| Detail view | Full metadata, sample stack trace, occurrence count, first/last seen timestamps |
| Notes | Auto-saving textarea for root-cause analysis and AI evaluations |
| Status workflow | Mark resolved or ignored; optional reopen on recurrence |
| Test generation | One-click PHPUnit skeleton annotated with `@group fault-{hash}` |
| Ignore list | Skip expected exceptions (404s, auth errors, validation errors) |

## How fingerprinting works

Each exception is fingerprinted using a SHA-256 hash of three fields:

```
class | relative_file | line
```

The exception message is intentionally excluded. This means exceptions that carry variable content — user IDs, timestamps, record counts — map to the same fault group instead of creating noise. One group per root cause, not one group per occurrence.

For example, all occurrences of `QueryException` thrown at `app/Services/UserService.php:42` will group together regardless of the SQL message content.

## Triage flow

Exceptions arrive as open fault groups. From the `/watch/faults` UI you can:

- **Mark resolved** — with an optional note (e.g. "Fixed in PR #123")
- **Mark ignored** — for expected exceptions that don't need action
- **Reopen** — when `FAULT_REOPEN_ON_RECURRENCE=true`, fault reopens a resolved group automatically if the exception recurs

The triage UI is part of the [watch](../watch/) cockpit. Installing fault automatically adds the Faults link to the watch nav.
