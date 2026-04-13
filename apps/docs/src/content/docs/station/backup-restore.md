---
title: Backup & Restore
description: Tiered backup system with self-service restore, manifest-based media verification, and logarithmic retention.
sidebar:
  order: 70
---

Station includes a built-in backup and restore system. Backups run on a schedule, age through retention tiers (keeping recent backups dense and old ones sparse), and can be restored from the admin panel or CLI. Tenant admins can self-service restore from the last 48 hours; platform admins can restore from any point.

If you just want to get backups running, start with [Setting Up Backups](/station/backups-setup/). This page covers the full system architecture.

Backups are self-contained snapshots — each one is independently restorable with no chaining or dependencies on other backups.

## Backup types

| Type | Scope | Description |
|------|-------|-------------|
| Full system | All tenants | Complete database dump + media manifest for every tenant |
| Per-tenant | Single tenant | Scoped database dump + media manifest for one tenant |

Both types produce the same artifacts: a database dump and a media manifest (not a media archive — see [Media manifests](#media-manifests) below).

## Retention tiers

Backups follow a logarithmic retention policy — dense recent coverage, sparse historical snapshots:

| Tier | Age | Keeps | Visible to |
|------|-----|-------|------------|
| Hot | 0 -- 48 hours | All backups | Tenant admins + Platform admins |
| Warm | 2 -- 14 days | 1 per day | Platform admins only |
| Cold | 14 days -- 6 months | 1/week (first month), then 1/month | Platform admins only |

A daily pruning job promotes, compresses, or deletes backups as they age through tiers. Cold-tier backups older than one month are compressed into a single archive with a sidecar metadata file.

### Scheduling

Backup frequency is configurable by the platform admin:

- Default: every 6 hours
- Minimum: every 1 hour
- Maximum: every 24 hours

Adjust based on storage capacity. The storage dashboard shows current usage, trends, and projected fill dates.

## Media manifests

Backups do **not** archive media files into tarballs. Instead, each backup records a **media manifest** — a JSON inventory of every tracked file with its path, content hash, and size.

```json
{
  "backup_id": "01jt...",
  "backup_version": 1,
  "captured_at": "2026-04-07T12:05:37Z",
  "files": [
    { "path": "tenant-1/media/photo.jpg", "hash": "sha256:abc...", "size": 204800 }
  ],
  "total_files": 1204,
  "total_bytes": 5368709120
}
```

This approach avoids duplicating gigabytes of media on every backup. The database (Spatie Media Library's `media` table) is the source of truth. Content hashes are computed at upload time, not during backup.

Manifests over 1 MB are gzip-compressed (`.json.gz`).

## Restore pipeline

Restores follow a strict 9-step pipeline. Every restore creates a mandatory pre-restore snapshot before making any changes.

### 1. Tenant maintenance mode

Queues are paused, uploads are rejected, and a maintenance page is shown to users.

### 2. Mandatory pre-restore snapshot

A standalone full backup is created automatically. If this snapshot fails, the restore aborts and maintenance mode is exited. This snapshot is **not optional** — it ensures every restore is recoverable.

### 3. Validate target backup

The system verifies the target backup has `status = completed` and that checksums match for both the database dump and media manifest. If any check fails, the restore aborts.

### 4. Stage restore

The database dump and media manifest are extracted to an isolated staging area. Disk space is checked before staging begins. No in-place overwrites occur at this stage.

### 5. Apply database restore

The staged database dump is imported, using drop + recreate within a transaction where the database engine supports it.

### 6. Apply media restore

Current media is diffed against the manifest:

- Files not in the manifest are **deleted**
- All manifest files are verified to exist on storage and match their `content_hash`
- Mode is always **replace** — the restored state exactly matches the backup snapshot

If any manifest file is missing from storage, the restore **fails** and the system auto-restores from the pre-restore snapshot.

### 7. Verification

- Row counts are checked against manifest metadata
- A sample of media files are spot-checked for accessibility
- Key database tables are verified to exist

### 8. Unlock tenant

Maintenance mode is exited, queues resume, and the restore is marked complete.

### 9. Failure handling

Any failure during steps 5 -- 7 triggers:

- Automatic restore from the pre-restore snapshot
- Notification to the platform admin

The tenant is never left in an inconsistent state.

## Tenant self-service

Tenant administrators can restore from **hot-tier backups only** (last 48 hours). Older backups require a platform administrator.

The restore confirmation flow is a 3-step guided process:

1. **Preview** — shows what will be replaced (row counts, media file counts, timestamps)
2. **Confirm** — type the site name to confirm (placeholder shows the expected value)
3. **Execute** — restore runs with real-time progress

This flow is implemented as a core (non-editable) workflow using Station's Flow engine.

## Storage dashboard

The platform admin panel includes a storage overview:

- Total backup size and breakdown (database vs. media)
- Storage trend over time
- Projected storage fill date at current rate
- Per-tenant usage breakdown
- Largest tenant highlighted

## Backup data model

Each backup is tracked in the `backup_manifests` table with:

- **Identification**: ULID primary key, backup schema version, type (full system / per-tenant), tenant reference
- **Storage**: separate paths and disks for database dump and media manifest, with SHA-256 checksums on both
- **Sizing**: total size, database size, media size, tenant count, total row count
- **Lifecycle**: status (`running` / `verifying` / `completed` / `failed`), failure reason, timestamps for started/verified/completed
- **Retention**: current tier (hot/warm/cold), `restorable_until` (tenant self-service window), `expires_at` (pruner deletion date)
- **Restore tracking**: `is_pre_restore` flag, `restored_backup_id` linking pre-restore snapshots to their trigger
- **Metadata**: JSON blob for per-table row counts, app version, and debug info. Sidecar `.meta.json` file mirrors critical fields for bare-metal recovery.
- **Policy**: `media_restore_mode` (always `replace`), `missing_media_policy` (default `fail`)

## Notification channels

Backup events (success, failure, restore triggered) are dispatched through Station's notification channel system. Channels are configured in the platform admin panel:

| Channel type | Configuration |
|-------------|---------------|
| Database | In-app notification (always enabled) |
| Email | Recipient address |
| Slack | Webhook URL |

Channels are general-purpose — backup is the first consumer, but the same channels serve future notification types (review submitted, workflow completed, etc.).
