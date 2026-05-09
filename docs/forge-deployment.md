# Forge Deployment

This repo is deployed to one Forge-managed server with two production sites:

- `fissible.dev` — Laravel marketing application
- `docs.fissible.dev` — static Astro/Starlight documentation site built from `apps/docs`

Staging for the Laravel app is a third Forge site on the same server:

- `staging.fissible.dev` — pre-production Laravel site deployed from the `staging` branch

## Server

Current baseline:

- Type: app server
- OS: Ubuntu 24.04
- PHP: 8.4
- Database: MySQL 8.4 LTS
- Size: 2 GB RAM / 1 vCPU is sufficient for the initial marketing site, docs, and early Station work

## DNS

Name.com records:

| Type | Host | Value |
| --- | --- | --- |
| A | `fissible.dev` | Forge server IP |
| CNAME | `www.fissible.dev` | `fissible.dev` |
| A | `staging.fissible.dev` | Forge server IP |
| A | `staging-app.fissible.dev` | Forge server IP |
| A | `docs.fissible.dev` | Forge server IP |
| MX | `fissible.dev` | Google Workspace records |

Do not add `www.docs.fissible.dev` unless that URL becomes intentionally supported.

## `fissible.dev` Laravel Site

Forge site settings:

- Domain: `fissible.dev`
- Repository: `fissible/fissible.dev`
- Branch: `main`
- Web directory: `/public`
- Database: connected to the local `forge` MySQL database
- SSL: Let's Encrypt certificate for `fissible.dev`; include `www.fissible.dev` if Forge is configured to redirect www traffic

Deploy script:

```bash
cd $FORGE_SITE_PATH

# Maintenance mode — auto-recover if anything fails
php artisan down --refresh=15
trap 'php artisan up' ERR

git pull origin $FORGE_SITE_BRANCH

composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

npm ci --ignore-scripts
npm run build

php artisan filament:assets
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan up
```

Do not run `php artisan db:seed --force` as part of normal deploys. Seed manually only when specific seed data is required.

Production environment notes:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://fissible.dev`
- `LOG_LEVEL=info`
- `STATION_PLATFORM_ENABLED=false` until platform auth/admin is production-ready
- `MAIL_MAILER=log` is acceptable until real transactional email is needed

## `staging.fissible.dev` Laravel Site

Purpose: validate deploys, migrations, tenant provisioning, module installs,
and theme changes without touching production data.

Recommended Forge site settings:

- Domain: `staging.fissible.dev`
- Aliases: add `staging-app.fissible.dev` if you want a separate platform/app host
- Repository: `fissible/fissible.dev`
- Branch: `staging`
- Web directory: `/public`
- Database: separate MySQL database and user, e.g. `fissible_staging`
- SSL: Let's Encrypt certificate covering `staging.fissible.dev` and `staging-app.fissible.dev` if both are used

Git workflow:

```bash
git checkout main
git pull origin main
git checkout -b staging
git push -u origin staging
```

If the `staging` branch already exists, keep it as the integration branch for
pre-production deploys and merge or rebase from `main` intentionally.

Staging environment overrides:

- `APP_ENV=staging`
- `APP_DEBUG=true`
- `APP_URL=https://staging.fissible.dev`
- `LOG_LEVEL=debug`
- `STATION_PLATFORM_ENABLED=true`
- `STATION_PLATFORM_HOST=staging-app.fissible.dev`
- `STATION_MANAGED_ROOT_DOMAIN=staging.fissible.dev`
- `DB_DATABASE=fissible_staging`
- staging-specific DB username/password
- safe mailer choice such as `MAIL_MAILER=log` until you intentionally test outbound email

Use the same deploy script as production. The script uses `$FORGE_SITE_PATH`, so
it works for both `fissible.dev` and `staging.fissible.dev` without path edits.

First staging deploy checklist:

1. Create the Forge site and connect it to the `staging` branch.
2. Create the staging database and user.
3. Paste the deploy script from [forge-deploy.sh](../forge-deploy.sh).
4. Set the staging `.env` values in Forge.
5. Deploy once.
6. Run `php artisan key:generate` if the app key is still blank.
7. Run `php artisan storage:link`.
8. Run `php artisan station:make-admin you@fissible.dev`.

Verification:

```bash
php artisan about
php artisan migrate:status
php artisan route:list | rg admin
```

Browser checks:

- `https://staging.fissible.dev` loads the marketing site
- `https://staging-app.fissible.dev/admin` loads the admin login if a separate app host is configured
- production content and staging content do not share database state
- pushing to `staging` deploys only the staging site

Rollback / safety notes:

- Do not point staging at the production database, even temporarily.
- Keep `APP_DEBUG=true` only on staging, never production.
- If you are not using `staging-app.fissible.dev`, leave `STATION_PLATFORM_HOST` set to the single host you actually serve.

## `docs.fissible.dev` Static Docs Site

Forge site settings:

- Domain: `docs.fissible.dev`
- Repository: `fissible/fissible.dev`
- Branch: `main`
- Web directory: `/apps/docs/dist`
- Database: none
- Composer install: not needed
- SSL: Let's Encrypt certificate for `docs.fissible.dev` only

Deploy script:

```bash
cd $FORGE_RELEASE_DIRECTORY

npm ci --prefix apps/docs
npm run build --prefix apps/docs

ln -sfn $FORGE_RELEASE_DIRECTORY /home/forge/docs.fissible.dev/current
```

Notes:

- The docs site uses Forge zero-downtime releases.
- Nginx should serve `root /home/forge/docs.fissible.dev/current/apps/docs/dist;`.
- If the site 404s after deploy, verify that `current` points at a release containing `apps/docs/dist/index.html`.

Useful checks:

```bash
ls -la /home/forge/docs.fissible.dev/current
ls -la /home/forge/docs.fissible.dev/current/apps/docs/dist/index.html
grep -R "root " /etc/nginx/sites-available/docs.fissible.dev
```

## Seeding

Production seeders must not rely on factories, because Forge production installs use `composer install --no-dev` and Faker is a dev dependency.

Use explicit model creation in seeders:

```php
User::query()->firstOrCreate(
    ['email' => 'platform@fissible.dev'],
    [
        'name' => 'Platform Admin',
        'password' => Str::random(32),
    ]
);
```

Avoid this in production seeders:

```php
User::factory()->create();
```

## Vercel

Vercel is no longer required for:

- `fissible.dev`
- `www.fissible.dev`
- `docs.fissible.dev`

After verifying both Forge sites and SSL certificates, remove those custom domains from Vercel projects and cancel/downgrade Vercel billing if no other projects require it.
