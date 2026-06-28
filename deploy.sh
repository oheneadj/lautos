#!/usr/bin/env bash
set -euo pipefail

# Run after pulling new code in production — installs dependencies, runs
# migrations, then rebuilds the route/config/event/view caches so Laravel
# isn't parsing routes and Blade files from scratch on every request.

composer install --no-dev --optimize-autoloader
npm ci
npm run build

php artisan migrate --force

# DatabaseSeeder checks app()->environment('production') itself and only
# runs the seeders that are safe on a live database (roles/permissions,
# the real admin account, settings, and make/model/FAQ reference data) —
# the fake demo seeders (cars, orders, sample blog post) are skipped
# automatically, so this is safe to run on every deploy.
php artisan db:seed --force

php artisan optimize:clear
php artisan optimize

php artisan queue:restart
