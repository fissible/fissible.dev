---
title: Configuration
description: Comprehensive reference for all environment variables and configuration options in Station.
sidebar:
  order: 2
---

Station is configured primarily through environment variables in the `.env` file. This page documents every supported variable organized by category.

## Application

| Variable | Default | Description |
|----------|---------|-------------|
| `APP_NAME` | `Station` | Application name, used in page titles and emails |
| `APP_ENV` | `production` | Environment (`local`, `staging`, `production`) |
| `APP_KEY` | — | Encryption key, generated via `php artisan key:generate` |
| `APP_DEBUG` | `false` | Enable debug mode (stack traces, detailed errors) |
| `APP_URL` | `http://localhost` | Base URL for route and asset generation |

## Database

| Variable | Default | Description |
|----------|---------|-------------|
| `DB_CONNECTION` | `mysql` | Database driver (`mysql`, `pgsql`, `sqlite`) |
| `DB_HOST` | `127.0.0.1` | Database server hostname |
| `DB_PORT` | `3306` | Database server port |
| `DB_DATABASE` | `station` | Database name |
| `DB_USERNAME` | `root` | Database username |
| `DB_PASSWORD` | — | Database password |

## Station features

| Variable | Default | Description |
|----------|---------|-------------|
| `STATION_THEME` | `heartland` | Active frontend theme slug (see [Frontend Theming](./frontend-theming)) |
| `STATION_PRIMARY_COLOR` | `#C17B3A` | Accent color injected as a CSS custom property |
| `STATION_PLATFORM_ENABLED` | `true` | Enable the `/platform` super-admin panel |
| `STATION_BACKUPS_ENABLED` | `false` | Enable the backup system (see [Backup Setup](./backups-setup)) |
| `MFA_ENABLED` | `false` | Enable TOTP two-factor authentication on the profile page |

## Backups

These variables are only relevant when `STATION_BACKUPS_ENABLED=true`.

| Variable | Default | Description |
|----------|---------|-------------|
| `STATION_BACKUP_FREQUENCY_HOURS` | `6` | Hours between scheduled backups |
| `STATION_BACKUP_DISK` | `local` | Filesystem disk for backup storage |
| `STATION_BACKUP_RETENTION_HOT_HOURS` | `48` | Hours to retain hot-tier (full) backups |
| `STATION_BACKUP_RETENTION_WARM_DAYS` | `14` | Days to retain warm-tier backups |
| `STATION_BACKUP_RETENTION_COLD_MONTHS` | `6` | Months to retain cold-tier (archived) backups |

## Queues and caching

| Variable | Default | Description |
|----------|---------|-------------|
| `QUEUE_CONNECTION` | `database` | Queue driver (`sync`, `database`, `redis`, `sqs`) |
| `CACHE_STORE` | `database` | Cache driver (`file`, `database`, `redis`, `memcached`) |
| `SESSION_DRIVER` | `database` | Session storage driver (`file`, `database`, `redis`, `cookie`) |

## Mail

| Variable | Default | Description |
|----------|---------|-------------|
| `MAIL_MAILER` | `log` | Mail transport (`smtp`, `ses`, `mailgun`, `postmark`, `log`) |
| `MAIL_HOST` | `127.0.0.1` | SMTP server hostname |
| `MAIL_PORT` | `2525` | SMTP server port |
| `MAIL_USERNAME` | — | SMTP authentication username |
| `MAIL_PASSWORD` | — | SMTP authentication password |
| `MAIL_FROM_ADDRESS` | `hello@example.com` | Default sender email address |
| `MAIL_FROM_NAME` | `${APP_NAME}` | Default sender display name |

## Dusk testing

These variables are used when running Laravel Dusk browser tests.

| Variable | Default | Description |
|----------|---------|-------------|
| `INSTALLER_LOCK_FILE` | `storage/app/installed` | Path to the installer lock file that signals Station is installed |
| `DUSK_DRIVER_URL` | — | Selenium or ChromeDriver URL for Dusk tests (e.g., `http://localhost:9515`) |
