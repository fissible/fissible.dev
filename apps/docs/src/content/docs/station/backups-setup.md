---
title: Setting Up Backups
description: How to enable, configure, and verify Station's automated backup system.
sidebar:
  order: 71
---

This guide walks through enabling backups, tuning the schedule and retention settings, running backups manually, and restoring from a backup. By the end you should have automated backups running on a schedule with no further intervention.

## Prerequisites

- A working Station installation with `php artisan` available
- Laravel's task scheduler running via cron (see [Step 2](#2-enable-the-laravel-scheduler))
- Enough disk space for at least 48 hours of backups (database dumps + metadata — media files are **not** duplicated)

## 1. Enable backups

Open your `.env` file and set:

```dotenv
STATION_BACKUPS_ENABLED=true
```

This is the master switch. When `false`, no scheduled backups run and the backup commands exit early.

## 2. Enable the Laravel scheduler

Station's backups rely on Laravel's built-in task scheduler. The scheduler needs a single system cron entry that runs every minute:

```bash
* * * * * cd /path-to-your-station && php artisan schedule:run >> /dev/null 2>&1
```

Replace `/path-to-your-station` with the absolute path to your Station installation.

**If you're using Laravel Sail**, the scheduler is already configured inside the container — no extra cron entry is needed.

To verify the scheduler sees the backup commands:

```bash
php artisan schedule:list
```

You should see `backup:run --full` and `backup:prune` in the output. If they don't appear, double-check that `STATION_BACKUPS_ENABLED=true` in your `.env` and run `php artisan config:clear`.

## 3. Configure the schedule and retention

All settings are controlled through environment variables. The defaults work well for most sites — only change them if you have a reason to.

### Backup frequency

```dotenv
BACKUP_FREQUENCY_HOURS=6
```

How often a full backup runs. The default (every 6 hours) produces 4 backups per day. Set to `1` for hourly backups on high-traffic sites, or `24` for once-daily on low-traffic sites.

### Storage disk

```dotenv
BACKUP_DISK=local
```

The Laravel filesystem disk where backups are stored. The default `local` disk writes to `storage/app/`. You can point this at any disk defined in `config/filesystems.php` — for example, an S3 bucket or a mounted network volume.

### Retention tiers

Backups age through three tiers. As they get older, Station keeps fewer of them to save space:

| Variable | Default | What it controls |
|----------|---------|-----------------|
| `BACKUP_HOT_HOURS` | `48` | How long every backup is kept (hours) |
| `BACKUP_WARM_DAYS` | `14` | After hot, keep one backup per day for this many days |
| `BACKUP_COLD_MONTHS` | `6` | After warm, keep one backup per week for this many months |

```dotenv
BACKUP_HOT_HOURS=48
BACKUP_WARM_DAYS=14
BACKUP_COLD_MONTHS=6
```

**Example with defaults:** A backup created now stays in the hot tier for 48 hours (kept alongside every other backup from that window). After 48 hours it moves to warm, where only one backup per calendar day is kept. After 14 days it moves to cold, where only one per week is kept. After 6 months it is deleted.

Pruning runs automatically every day at 03:00.

## 4. Run a backup manually

You don't have to wait for the schedule. To run a backup right now:

```bash
# Full-system backup (all tenants)
php artisan backup:run --full

# Single-tenant backup
php artisan backup:run --tenant=my-site-slug
```

The command prints the backup size when it finishes. If something goes wrong, the error is printed to the console and the backup is marked as `failed` in the database.

## 5. Verify backups are working

After your first scheduled backup has run, check that everything looks right:

```bash
# See recent backup records
php artisan tinker --execute="
  \App\Backup\Models\BackupManifest::latest()->take(5)->get(['id','type','status','tier','total_size_bytes','completed_at'])->each(fn(\$b) => dump(\$b->toArray()));
"
```

You should see records with `status: completed`. If any show `status: failed`, check the `failure_reason` column for details.

Platform administrators can also view backups in the **Platform panel** under the Backup Manager page, which shows status, sizes, and tier information at a glance.

## 6. Restore from a backup

### Tenant self-service (hot tier only)

Tenant administrators can restore their own site from any backup in the hot tier (last 48 hours) through the admin panel. The restore flow is:

1. **Preview** — see what will be replaced (row counts, media files, timestamps)
2. **Confirm** — type the site name to confirm
3. **Execute** — restore runs with real-time progress

The system automatically creates a safety snapshot before restoring, so you can always undo a restore.

### Platform admin restore

Platform administrators can restore from any tier (hot, warm, or cold) through the Platform panel.

### Emergency CLI restore

If the admin panel is inaccessible, you can restore from the command line using the backup's sidecar metadata file:

```bash
php artisan backup:manual-restore storage/app/backups/full/2026-04-08/120000/backup.meta.json
```

Each backup writes a `.meta.json` sidecar file next to its database dump. This file contains everything needed to locate and verify the backup.

## What gets backed up

| Artifact | Included | Notes |
|----------|----------|-------|
| Database | Yes | Full SQL dump with SHA-256 checksum |
| Media manifest | Yes | JSON inventory of all media files with paths, hashes, and sizes |
| Media files | No | Files stay in place on their storage disk — not duplicated |
| Application code | No | Managed by your deployment process / git |

Because media files are tracked by manifest (not copied), backups are fast and small. During a restore, the system verifies every media file still exists and matches its recorded hash. If any file is missing, the restore fails safely and rolls back.

## Troubleshooting

**Backups not running on schedule**
- Confirm `STATION_BACKUPS_ENABLED=true` in `.env`
- Confirm the Laravel scheduler cron is running (`php artisan schedule:list` should show the backup commands)
- Run `php artisan config:clear` after changing `.env` values

**Backup marked as failed**
- Check the `failure_reason` in the backup record (visible in the Platform panel or via tinker)
- Common causes: disk full, database connection timeout, file permission issues

**Restore fails with "missing media"**
- A media file referenced in the backup manifest no longer exists on disk
- The restore automatically rolls back to the pre-restore snapshot
- Investigate why the file is missing (deleted outside of Station, storage disk issue) before retrying

**Running out of disk space**
- Lower `BACKUP_HOT_HOURS` to keep fewer recent backups
- Lower `BACKUP_WARM_DAYS` and `BACKUP_COLD_MONTHS` to shorten retention
- Run `php artisan backup:prune` to apply new retention settings immediately
- Consider pointing `BACKUP_DISK` at a larger volume or external storage
