#!/usr/bin/env bash
set -euo pipefail

# Run after pulling new code in production — installs dependencies, runs
# migrations, then rebuilds the route/config/event/view caches so Laravel
# isn't parsing routes and Blade files from scratch on every request.

composer install --no-dev --optimize-autoloader
npm ci
npm run build

php artisan migrate --force

php artisan optimize:clear
php artisan optimize

php artisan queue:restart
