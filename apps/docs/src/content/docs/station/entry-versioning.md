---
title: Entry Versioning
description: Draft fork model, commit history, rollback, and hotfix workflow for safe content editing.
sidebar:
  order: 14
---

Station uses a **draft fork model** for versioning. Editing a published entry creates a separate draft record rather than modifying the live entry. This keeps the published version stable while changes are prepared, reviewed, and merged.

## Draft forks

When a user edits a published entry, the system creates a **fork** — a new `Entry` row that references the original (canonical) entry via `version_of_id`.

**Constraints:**
- A unique database constraint ensures at most one fork per canonical entry
- Fork creation is wrapped in a database transaction for race safety
- The fork copies the canonical entry's current data, slug, and hierarchy

The canonical entry remains live and unchanged throughout the editing process.

## Fork lifecycle

```
Create fork --> Edit draft --> Submit for review (if workflow) --> Merge to canonical --> Delete fork
```

1. **Create** — triggered when a user opens a published entry for editing. If a fork already exists, the existing fork is loaded instead.
2. **Edit** — all changes are made to the fork. The canonical entry is untouched.
3. **Review** — if the content type has a workflow, the fork enters the pipeline. Reviewers see a diff between fork and canonical.
4. **Merge** — on approval (or direct publish if no workflow), the fork's data replaces the canonical entry's data.
5. **Cleanup** — the fork row is deleted after a successful merge.

### Discarding a fork

If edits are abandoned, the fork is deleted. The canonical entry remains exactly as it was. No data is lost.

## Publishing and merging

When a fork is published (either directly or after workflow approval), the merge process:

1. Copies `data`, `slug`, and `parent_id` from the fork to the canonical entry
2. Transfers media attachments (Spatie MediaLibrary) from fork to canonical
3. Creates a commit snapshot (see below)
4. Deletes the fork

**Slug collision detection:** if the fork's slug would collide with another entry's slug at the same hierarchy level, the merge fails with a validation error.

## Commit history

Every publish creates an append-only **commit** record in the `entry_commits` table:

| Column | Description |
|--------|-------------|
| `entry_id` | The canonical entry |
| `data` | JSON snapshot of entry data at publish time |
| `slug` | Slug at publish time |
| `parent_id` | Parent entry at publish time (for pages) |
| `committed_by` | User who published |
| `committed_at` | Timestamp |

Commits form a chronological history of every published version of an entry. They are never modified or deleted (append-only).

## Rollback

Admins can restore any previous commit from the entry's history panel:

1. Select a commit from the history list
2. Confirm the rollback
3. The commit's `data` and `slug` are written to the canonical entry
4. If an active fork exists, it is deleted (the rollback supersedes in-progress edits)

**Slug collision handling:** if the restored slug conflicts with another entry, the system appends a numeric suffix.

## Hotfix workflow

For urgent changes that cannot wait for the standard review pipeline:

1. An editor creates a fork and marks it as a **hotfix**
2. The hotfix bypasses the normal workflow queue
3. Only **super admins** can approve or reject hotfix submissions
4. On approval, the fork merges to canonical immediately

Hotfix entries display a distinct **Hotfix** status badge in the admin panel.

## Soft deletes

Entries use Laravel's soft delete trait. When an entry is deleted:

- The canonical entry is soft-deleted
- Any active fork is also soft-deleted (cascading)
- Trashed entries can be restored from the admin panel, which also restores the fork if one existed

## Imported commits

Commits created by `station:content:import` carry a `provenance` record on the commit row with the source package checksum, source environment, and source git commit/tag when available. Imports always **append** — the pre-import commit remains recoverable through the history panel. See [Code vs. Content Deploy](/station/content-promotion/) for when to use content packages.
