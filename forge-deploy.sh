#!/bin/bash
# Forge deployment script for fissible.dev / staging.fissible.dev
# Copy this into Forge > Site > Deploy Script
#
# Requirements:
#   PHP 8.3+
#   MySQL 8+
#   Node 22+ (set in Forge > Server > PHP/Node versions)
#   Writable: storage/, bootstrap/cache/
#
# First deploy only:
#   1. Set environment variables in Forge UI
#   2. Generate app key: php artisan key:generate
#   3. Create storage symlink: php artisan storage:link
#   4. Run initial migration: php artisan migrate --force
#   5. Create admin user: php artisan station:make-admin admin@fissible.dev
#   6. For staging, point APP_URL at https://staging.fissible.dev and use a
#      separate database + branch
#   7. Enable wildcard subdomains in Forge site settings only if the site will
#      host tenant subdomains
#   8. Add any required DNS records before requesting Let's Encrypt

set -e

cd "$FORGE_SITE_PATH"

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
