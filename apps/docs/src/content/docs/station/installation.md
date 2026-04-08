---
title: Installation
description: How to install and configure Station for local development or production deployment.
---

## Requirements

- PHP 8.2+
- MySQL 8.0+ or PostgreSQL
- Composer
- Node.js 18+ (for frontend assets)
- Docker (recommended for local development via Laravel Sail)

## Development setup

```bash
git clone https://github.com/fissible/station.git
cd station
composer install
cp .env.example .env
php artisan key:generate
```

### Using Laravel Sail (Docker)

```bash
./vendor/bin/sail up -d
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```

### Without Docker

```bash
php artisan migrate
php artisan db:seed
php artisan serve
```

## Running tests

```bash
# With Sail
./vendor/bin/sail artisan test

# Without Sail
php artisan test
```

Station uses [Pest](https://pestphp.com/) for testing.

## Configuration

Key environment variables:

| Variable | Description | Default |
|----------|-------------|---------|
| `APP_URL` | Base URL for the application | `http://localhost` |
| `DB_CONNECTION` | Database driver (`mysql` or `pgsql`) | `mysql` |
| `FILESYSTEM_DISK` | Default storage disk | `local` |
| `QUEUE_CONNECTION` | Queue driver (`sync`, `redis`, `database`) | `sync` |
| `BACKUP_DISK` | Storage disk for backups | `local` |
| `BACKUP_INTERVAL_HOURS` | Hours between scheduled backups | `6` |

## Production deployment

Station is designed for self-hosting. A typical production setup includes:

1. A PHP 8.2+ server (Nginx + PHP-FPM or similar)
2. MySQL 8.0+ or PostgreSQL database
3. Redis for caching and queues
4. A queue worker (`php artisan queue:work`)
5. A scheduler (`php artisan schedule:run` via cron)
6. Object storage (S3-compatible) recommended for media and backups
