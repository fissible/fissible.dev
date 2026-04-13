---
title: Installation
description: How to install and configure Station for local development or production deployment.
sidebar:
  order: 1
---

Station is a self-hosted CMS built on Laravel 12 and PHP 8.2. This guide covers everything from cloning the repo to verifying your first login.

## Requirements

| Dependency | Minimum version | Notes |
|-----------|----------------|-------|
| PHP | 8.2+ | With `pdo_mysql` or `pdo_pgsql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json` |
| Composer | 2.x | PHP dependency manager |
| MySQL | 8.0+ | Or PostgreSQL (set `DB_CONNECTION=pgsql`) |
| Node.js | 18+ | For building frontend assets with Vite |
| npm | 9+ | Included with Node.js |

**Optional but recommended:**

- **Docker** — for local development via [Laravel Sail](https://laravel.com/docs/sail)
- **Redis** — for caching and queues in production

## Quick start (local development)

### Option A: Using Laravel Sail (Docker)

This is the recommended approach for local development. Sail provides a pre-configured Docker environment with PHP, MySQL, and Redis.

```bash
git clone https://github.com/fissible/station.git
cd station
composer install
cp .env.example .env
php artisan key:generate
```

Start the containers:

```bash
./vendor/bin/sail up -d
```

Run migrations and seed the database:

```bash
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```

Build the frontend assets:

```bash
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

For live-reloading during development, use `npm run dev` instead of `npm run build`.

Station is now available at [http://localhost](http://localhost).

### Option B: Without Docker

Make sure PHP, Composer, Node.js, and a database server are installed on your machine.

```bash
git clone https://github.com/fissible/station.git
cd station
composer install
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set your database credentials:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=station
DB_USERNAME=root
DB_PASSWORD=your_password
```

Create the database if it doesn't exist:

```bash
mysql -u root -p -e "CREATE DATABASE station;"
```

Run migrations, seed, and build assets:

```bash
php artisan migrate
php artisan db:seed
npm install
npm run build
php artisan serve
```

Station is now available at [http://localhost:8000](http://localhost:8000).

## Web-based installer

For production deployments (or if you prefer a guided setup), Station includes a web-based installer wizard. Instead of editing `.env` by hand:

1. Deploy the Station files to your server
2. Run `composer install` and `cp .env.example .env && php artisan key:generate`
3. Visit `/setup` in your browser

The wizard walks you through:
- Database connection details (host, port, name, username, password)
- Site name and URL
- Timezone
- Admin account creation (name, email, password)

After the wizard completes, the `/setup` route is permanently disabled (a lock file is created at `storage/app/installed`).

**If you need to re-run the installer**, delete the lock file first:

```bash
rm storage/app/installed
```

### What the installer does

Behind the scenes, the wizard writes your answers to `storage/app/install-input.json` and then runs `php artisan station:install`, which:

1. Writes your database and site settings to `.env`
2. Runs all migrations
3. Creates the default roles (`admin`, `editor`, `author`, `reviewer`)
4. Creates the default tenant
5. Creates your admin account and assigns the `admin` role
6. Seeds starter content (a homepage and privacy policy page)
7. Installs any configured modules
8. Creates the `storage/app/public` symlink
9. Writes the lock file to prevent re-running

Each step is tracked in `storage/app/install-progress.json`, so if the process is interrupted (server timeout, database error), you can re-run `php artisan station:install` and it picks up where it left off.

## Configuration reference

After installation, these are the key environment variables you may want to adjust. All of them have sensible defaults — you only need to change what applies to your situation.

### Application

| Variable | Default | Description |
|----------|---------|-------------|
| `APP_NAME` | `Laravel` | Your site name, shown in the browser tab and emails |
| `APP_URL` | `http://localhost` | The public URL of your site (important for links in emails and the API) |
| `APP_ENV` | `local` | Set to `production` on live servers |
| `APP_DEBUG` | `true` | Set to `false` in production (prevents error details from leaking) |

### Database

| Variable | Default | Description |
|----------|---------|-------------|
| `DB_CONNECTION` | `sqlite` | Database driver: `mysql`, `pgsql`, or `sqlite` |
| `DB_HOST` | `127.0.0.1` | Database server hostname |
| `DB_PORT` | `3306` | Database server port (3306 for MySQL, 5432 for PostgreSQL) |
| `DB_DATABASE` | — | Database name |
| `DB_USERNAME` | — | Database username |
| `DB_PASSWORD` | — | Database password |

### Station features

| Variable | Default | Description |
|----------|---------|-------------|
| `STATION_PLATFORM_ENABLED` | `false` | Enables the `/platform` admin panel for managing tenants, backups, and system settings |
| `STATION_THEME` | `heartland` | Active frontend theme (themes live in the `themes/` directory) |
| `STATION_PRIMARY_COLOR` | `#C17B3A` | Hex color used as the accent/brand color in the active theme |
| `MFA_ENABLED` | `false` | Enables TOTP multi-factor authentication for user accounts |

### Backups

| Variable | Default | Description |
|----------|---------|-------------|
| `STATION_BACKUPS_ENABLED` | `false` | Master switch for automated backups |
| `BACKUP_DISK` | `local` | Laravel filesystem disk for backup storage |
| `BACKUP_FREQUENCY_HOURS` | `6` | Hours between scheduled backups |

See [Setting Up Backups](/station/backups-setup/) for the full backup configuration guide.

### Queues and caching

| Variable | Default | Description |
|----------|---------|-------------|
| `QUEUE_CONNECTION` | `sync` | Queue driver. Use `sync` for development (jobs run immediately). Use `redis` or `database` in production |
| `CACHE_STORE` | `file` | Cache backend. `file` works fine for small sites; `redis` is better for production |
| `SESSION_DRIVER` | `file` | Where sessions are stored. `file` or `database` for most setups |

### Mail

| Variable | Default | Description |
|----------|---------|-------------|
| `MAIL_MAILER` | `log` | Mail driver. `log` writes emails to the log file (for development). Use `smtp`, `ses`, `mailgun`, etc. in production |
| `MAIL_HOST` | `127.0.0.1` | SMTP server hostname |
| `MAIL_PORT` | `2525` | SMTP server port |
| `MAIL_USERNAME` | — | SMTP username |
| `MAIL_PASSWORD` | — | SMTP password |
| `MAIL_FROM_ADDRESS` | `hello@example.com` | The "from" address on outgoing emails |
| `MAIL_FROM_NAME` | `${APP_NAME}` | The "from" name on outgoing emails |

## Production deployment

A typical production setup includes:

1. A PHP 8.2+ server (Nginx + PHP-FPM is the most common)
2. MySQL 8.0+ or PostgreSQL
3. Redis for caching and queues
4. A queue worker process
5. The Laravel scheduler running via cron
6. HTTPS via a reverse proxy or load balancer

### Step-by-step

**1. Deploy the code**

Clone (or upload) Station to your server, for example to `/var/www/station`:

```bash
cd /var/www
git clone https://github.com/fissible/station.git
cd station
composer install --no-dev --optimize-autoloader
```

The `--no-dev` flag skips development dependencies (testing tools, debug bars) and `--optimize-autoloader` improves class loading performance.

**2. Configure the environment**

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your production values. At a minimum, set:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=your-db-host
DB_DATABASE=station
DB_USERNAME=station_user
DB_PASSWORD=a_strong_password

QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=your-smtp-user
MAIL_PASSWORD=your-smtp-password
MAIL_FROM_ADDRESS=noreply@your-domain.com
```

**3. Run the installer**

Either visit `/setup` in your browser for the guided wizard, or run the CLI installer:

```bash
php artisan migrate --force
php artisan db:seed --force
```

Or, if you've set up `install-input.json`:

```bash
php artisan station:install
```

**4. Build frontend assets**

```bash
npm install
npm run build
```

**5. Set file permissions**

The web server needs to write to `storage/` and `bootstrap/cache/`:

```bash
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

Replace `www-data` with your web server's user if different.

**6. Create the storage symlink**

```bash
php artisan storage:link
```

This makes uploaded files in `storage/app/public` accessible at `your-domain.com/storage`.

**7. Cache configuration for performance**

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Run these again any time you change `.env` or route files.

**8. Set up the Laravel scheduler**

Add this cron entry on your server (run `crontab -e` to edit):

```
* * * * * cd /var/www/station && php artisan schedule:run >> /dev/null 2>&1
```

This runs every minute and lets Laravel decide which scheduled tasks are due. Station uses the scheduler for:
- Automated backups (if enabled)
- Backup pruning
- Processing scheduled content publishes

**9. Start the queue worker**

If you're using `redis` or `database` for `QUEUE_CONNECTION`, you need a worker process:

```bash
php artisan queue:work --sleep=3 --tries=3 --max-time=3600
```

In production, use a process manager like **systemd** or **Supervisor** to keep the worker running. Here's an example Supervisor config:

```ini
[program:station-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/station/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/station/storage/logs/worker.log
stopwaitsecs=3600
```

After creating the config file (e.g., `/etc/supervisor/conf.d/station-worker.conf`):

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start station-worker:*
```

**10. Verify the installation**

Open your site in a browser. You should see the frontend homepage. Log in to the admin panel at `/admin` with the credentials you set during installation.

Check that the scheduler and queue are working:

```bash
# Scheduler should list backup and content-processing commands
php artisan schedule:list

# Queue should be processing (if using redis/database driver)
php artisan queue:monitor redis:default
```

## Running tests

Station uses [Pest](https://pestphp.com/) for testing.

```bash
# With Sail
./vendor/bin/sail artisan test

# Without Sail
php artisan test

# Run a specific test file
php artisan test tests/Feature/Cms/EntrySchedulingTest.php

# Run with coverage (requires Xdebug or PCOV)
php artisan test --coverage
```

## Troubleshooting

**"500 Server Error" after deployment**
- Check `storage/logs/laravel.log` for the actual error
- Verify file permissions on `storage/` and `bootstrap/cache/`
- Run `php artisan config:clear` if you recently changed `.env`

**"Class not found" errors**
- Run `composer dump-autoload` to regenerate the autoload files
- If using `config:cache`, run `php artisan config:clear` and re-cache

**Assets look broken or unstyled**
- Make sure you ran `npm run build`
- Check that the `APP_URL` in `.env` matches the actual URL you're using
- Verify the `public/build/` directory exists and contains compiled assets

**Database connection refused**
- Verify `DB_HOST`, `DB_PORT`, `DB_USERNAME`, and `DB_PASSWORD` in `.env`
- Make sure the database server is running and the database exists
- If using Sail, use `DB_HOST=mysql` (the Docker service name), not `127.0.0.1`

**Scheduled tasks not running**
- Verify the cron entry is installed (`crontab -l`)
- Check that `php artisan schedule:list` shows the expected commands
- Look in `storage/logs/laravel.log` for scheduler errors

**Queue jobs not processing**
- Check that `QUEUE_CONNECTION` is not set to `sync` (sync runs jobs immediately, not via the worker)
- Verify the queue worker is running (`ps aux | grep queue:work`)
- Check `failed_jobs` table for failed jobs: `php artisan queue:failed`

**"/setup" returns 404**
- The installer lock file exists, meaning installation already completed
- If you need to re-run it: `rm storage/app/installed`
