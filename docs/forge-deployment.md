# Forge Deployment

This repo is deployed to one Forge-managed server with two sites:

- `fissible.dev` — Laravel marketing application
- `docs.fissible.dev` — static Astro/Starlight documentation site built from `apps/docs`

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

git pull origin $FORGE_SITE_BRANCH

composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

npm ci
npm run build

php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Do not run `php artisan db:seed --force` as part of normal deploys. Seed manually only when specific seed data is required.

Production environment notes:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL=https://fissible.dev`
- `LOG_LEVEL=info`
- `STATION_PLATFORM_ENABLED=false` until platform auth/admin is production-ready
- `MAIL_MAILER=log` is acceptable until real transactional email is needed

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
