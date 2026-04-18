---
title: Content Package CLI
description: Reference for station:content:export and station:content:import — flags, examples, failure modes, and remedies.
sidebar:
  order: 17
---

Station ships two Artisan commands for content promotion: `station:content:export` (on the source environment) and `station:content:import` (on the target). Both operate on a single `.zip` package file.

## Package format overview

A package is a zip archive with this layout:

```
package.zip
├── manifest.json           # provenance, entry index, media index, package checksum
├── entries/
│   └── <content-type>/
│       └── <slug>.json     # one file per included entry
└── media/
    └── <content-hash>.<ext>  # binary assets, keyed by sha256
```

The manifest records:

- **Required:** `package_format_version`, `created_at`, `created_by`, `source_environment`, `source_tenant_slug`, `target_tenant_slug`, `package_checksum`
- **When available:** `source_git_commit`, `source_git_branch`, `source_git_tag`, `source_git_clean`, `note`

Every entry file has its own sha256 listed in the manifest; every media file is stored under its content hash. The `package_checksum` is computed over the canonicalized manifest (sorted keys, UTF-8, no whitespace) and is re-verified at import time.

## Export

### Synopsis

```
php artisan station:content:export --tenant=<slug> --out=<path.zip> [options]
```

### Options

| Option | Description |
|--------|-------------|
| `--tenant=<slug>` | **Required.** Source tenant slug. |
| `--target-tenant=<slug>` | Target tenant slug. Defaults to the source. |
| `--content-type=<slug>` | Content type to include (repeatable). Omit for all. |
| `--entry=<content-type/slug>` | Specific entry to include (repeatable). |
| `--include-deps` | Include parent chain, directly-referenced taxonomy terms, and media. |
| `--out=<path>` | Output path for the zip. Required unless `--dry-run`. |
| `--dry-run` | Print what would be exported, without writing. |
| `--note="..."` | Freeform provenance note embedded in the manifest. |
| `--source-env=<label>` | Override the source environment label (defaults to `APP_ENV`). |

### Examples

Promote everything under the `pages` content type from the `acme` tenant, with their parent chain and media:

```
php artisan station:content:export \
  --tenant=acme \
  --content-type=pages \
  --include-deps \
  --out=releases/acme/2026-04-18.zip \
  --note="Sprint 42 content refresh"
```

Promote a single entry, resolving its parents automatically:

```
php artisan station:content:export \
  --tenant=acme \
  --entry=pages/about \
  --include-deps \
  --out=/tmp/about.zip
```

Dry-run to preview what would go in a package:

```
php artisan station:content:export \
  --tenant=acme \
  --content-type=pages \
  --out=/tmp/ignored.zip \
  --dry-run
```

### What gets exported

Export only picks up **published** commits — in-flight drafts and review forks are left behind.

With `--include-deps`, the resolver pulls in:

- parent entries (entire parent chain)
- taxonomy terms directly referenced by included entries
- media directly attached to included entries (by content hash, deduped)

Menus are **not** pulled as dependencies of entries in v1 — menus reference entries, not the other way around. If you want menus promoted, select them explicitly. (Menu promotion is on the Phase 3 roadmap.)

## Import

### Synopsis

```
php artisan station:content:import <package.zip> [--dry-run|--yes] [--as=<email>]
```

### Options

| Option | Description |
|--------|-------------|
| `package` | **Required positional.** Path to the zip to import. |
| `--dry-run` | Run full validation, print a change summary, write nothing. |
| `--yes` | Skip the confirmation prompt and apply. |
| `--as=<email>` | Email of the user the audit record is attributable to. |

### Examples

Dry-run to validate a package and see what would land:

```
php artisan station:content:import releases/acme/2026-04-18.zip --dry-run
```

Output:

```
Package: releases/acme/2026-04-18.zip
  Checksum: a8b7f4e1c9d2...
  Format:   1.0
  Source:   staging / acme
  Target:   acme
  Note:     Sprint 42 content refresh
Validation passed.
  Entries: 23
    - pages: 18
    - news:  5
  Media:   12
Dry run — no changes applied.
```

Apply non-interactively (CI):

```
php artisan station:content:import releases/acme/2026-04-18.zip --yes --as=deploybot@example.com
```

### What happens on apply

Import is wrapped in a single DB transaction per tenant — either everything lands, or nothing does. Binary media is staged on storage first and reaped on rollback.

For each entry in the package:

1. The target canonical is located (or created) by `(content type slug, entry slug)`.
2. A new commit is appended to the entry's history with provenance (package checksum, source environment, source git refs) — the pre-import commit is always recoverable.
3. **If the content type has no production workflow:** the canonical is updated in place. Embargo / expire timestamps are preserved as absolute UTC; the scheduler handles delivery visibility.
4. **If the content type has a production workflow:** the canonical stays live; a draft fork carrying the imported data enters the production review pipeline in a pending state. Approval state from the source environment is captured in provenance but not authoritative on the target.
5. **Workflow + schedule combined:** the pending commit enters *scheduled-pending-approval*. Schedule retained as metadata; approval must complete before publish fires.

Every import writes an audit record attributable to the user (`--as`) and the package checksum.

## Failure modes

All import failures are fail-closed: the command exits non-zero with a tagged reason, and no changes land.

| Tag | Meaning | Remedy |
|-----|---------|--------|
| `package_unreadable` | Zip file missing or corrupt | Re-export, re-transfer, or recompute permissions |
| `manifest_missing` | `manifest.json` not in the archive | Package built with a tool other than `station:content:export`; re-export |
| `manifest_malformed` | Required manifest fields missing or manifest not parseable | Re-export; the package format expected is v1.0 |
| `unsupported_format_version` | `package_format_version` not supported by the current Station build | Upgrade the target Station version or re-export on a compatible source |
| `checksum_mismatch` | `package_checksum` does not match the manifest contents | Package was tampered with or corrupted in transit; re-export and re-transfer |
| `entry_checksum_mismatch` | An entry file's bytes do not match its manifest checksum | Archive was modified after export; re-export |
| `media_checksum_mismatch` | A media file's bytes do not match its content hash | Archive was modified after export; re-export |
| `entry_missing` / `media_missing` | Manifest references a file absent from the archive | Archive corruption; re-export |
| `target_tenant_missing` | `target_tenant_slug` does not exist on the target | Create the tenant on the target **first**, or re-export with a corrected target slug |
| `content_type_missing` | A referenced content type does not exist on the target tenant | Deploy the code that defines the content type, then retry |
| `parent_missing` | An imported entry references a parent that is neither in the package nor on the target | Re-export with `--include-deps`, or promote the parent separately first |

## Scripting notes

- `--dry-run` makes both commands safe to wire into CI pre-flight checks.
- The exit code is the primary contract; fail-closed tags appear on stderr prefixed with `[<tag>]` for log-grepping.
- `--yes` is intentionally separate from `--dry-run`; you must pass `--yes` to apply a package non-interactively.
- Imports require at least one user on the target environment (to attribute new entries to). Pass `--as=<email>` to be explicit; otherwise the importer falls back to an active tenant member.

## Related

- [Code vs. Content Deploy](/station/content-promotion/) — the broader ownership story
- [Controlled vs. Live Editing](/station/controlled-vs-live-editing/) — editing modes and the override permission
- [Entry Versioning](/station/entry-versioning/) — how imported commits appear in history
- [Content Scheduling](/station/content-scheduling/) — embargo and expiry semantics preserved across environments
