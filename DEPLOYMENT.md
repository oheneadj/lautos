# Deployment Checklist — Hostinger Shared Hosting

This checklist is written specifically for **Hostinger Premium/Business shared hosting**, not a VPS or cloud server. That matters: this tier gives you SSH access and cron jobs via hPanel, but **no Redis and no persistent background processes** — you cannot run a long-lived daemon. `laravel/horizon` has been removed from this project for that reason — it requires Redis plus a persistent process, and its `ext-pcntl`/`ext-posix` requirement could have broken `composer install` on hosts that disable those extensions. The queue runs via the plain `database` driver instead, processed by a cron-driven `queue:work --stop-when-empty` (see §7). If you later move to a VPS/cloud server, Redis + Horizon (`composer require laravel/horizon`) become a real option again.

---

## 1. Hostinger-specific setup

- [ ] Use hPanel's **"Setup Laravel App"** / Git deploy tool if your plan offers it — it handles pointing the domain at this app's `public/` folder for you. Otherwise, manually set the domain's document root to this app's `public/` directory rather than moving Laravel's files into `public_html`.
- [ ] In hPanel, set the domain's **PHP version to 8.3 or newer** (`composer.json` requires `"php": "^8.3"`). Shared hosting plans often default to an older version — check this before anything else, since a wrong PHP version causes confusing unrelated errors.
- [ ] Confirm SSH access is enabled for this plan/domain (needed for `composer`, `php artisan`, and ideally `npm`).

## 2. PHP extensions to confirm in hPanel before deploying

hPanel → Advanced → PHP Configuration lets you toggle extensions per domain. Most shared plans have these on by default, but it's worth checking — a missing one fails silently or with a confusing unrelated error, not a clear "extension missing" message.

- [ ] **`gd`** — required. `app/Services/ImageOptimizer.php` explicitly uses `Intervention\Image\Drivers\Gd\Driver` to resize/re-encode every uploaded car photo to WebP. Without GD, every image upload (cars, blog covers, KYC docs, site logo) fails.
- [ ] `pdo_mysql` — required, for the MySQL connection.
- [ ] `mbstring`, `openssl`, `tokenizer`, `ctype`, `filter`, `hash`, `session` — required by Laravel's framework core itself (confirmed directly against `vendor/laravel/framework/composer.json`'s own `require` block, not guessed).
- [ ] `curl` — required for outbound HTTP: the GiantSMS API client and Google OAuth token exchange both go through it.
- [ ] `fileinfo` — required for upload MIME-type detection (car photos, KYC documents, payment proofs).
- [ ] `intl` — recommended; several underlying packages (Carbon, validation) use it when present and degrade silently without it.
- [ ] `zip` — recommended if you'll use Filament's export features anywhere in the admin panel.

You do **not** need `imagick` (the app uses GD, not Imagick), `redis`, or `pcntl`/`posix` (those were only needed for `laravel/horizon`, which has been removed — see the note at the top of this document).

## 3. Environment variables (`.env` on the server — never commit this file)

**Required:**
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL=https://yourdomain.com` (the real domain)
- [ ] `DB_CONNECTION=mysql` + `DB_HOST`/`DB_DATABASE`/`DB_USERNAME`/`DB_PASSWORD` from hPanel's **MySQL Databases** page — do not leave this as the `sqlite` some local `.env` files use.
- [ ] `GIANTSMS_API_KEY` and `GIANTSMS_SENDER_ID` — without these, OTP/SMS notifications fail.

**Override these from `.env.example`'s defaults — they assume Redis, which isn't available here:**
- [ ] `QUEUE_CONNECTION=database` (not `redis`)
- [ ] `CACHE_STORE=file` or `database` (not `redis`)

**Effectively required, not optional:** the "Continue with Google" button on login/register renders unconditionally with no fallback — if `GOOGLE_CLIENT_ID`/`GOOGLE_CLIENT_SECRET`/`GOOGLE_REDIRECT_URI` are empty, clicking it throws a raw 500 error instead of failing gracefully.
- [ ] `GOOGLE_CLIENT_ID`, `GOOGLE_CLIENT_SECRET`, `GOOGLE_REDIRECT_URI`

**Recommended:**
- [ ] `MAIL_MAILER=smtp` + `MAIL_HOST`/`MAIL_PORT`/`MAIL_USERNAME`/`MAIL_PASSWORD` — without these, emails silently write to `storage/logs/laravel.log` instead of sending. Easy to miss in testing because nothing errors.
- [ ] `AWS_*` (S3) — optional on shared hosting; local disk storage works fine at small scale. Only set these up if you want uploads off the shared filesystem.

**Security-critical — set before you ever seed the admin account:**
- [ ] `ADMIN_EMAIL` and `ADMIN_PASSWORD` — set to real values. If left unset, the admin seeder creates `admin@livingstonautos.com` / `password` — a publicly guessable login.

**Operational visibility (new — see §4 below):**
- [ ] `BACKUP_DISK` — defaults to `local`, but should point to an off-server disk (e.g. `s3`) in production so a backup survives even if the hosting account itself is lost.
- [ ] `BACKUP_NOTIFICATION_EMAIL` — where backup success/failure notifications go. Leave empty and you won't be told if a backup silently stops working.
- [ ] `SENTRY_LARAVEL_DSN` — from your Sentry project settings. Without it the package is installed but inert (no error tracking happens).

## 4. Backups & error tracking

Two packages were added specifically because shared hosting gives you no built-in visibility into either of these once the app is live — no Horizon dashboard, no live log tailing (`laravel/pail` is dev-only), and an SFTP trip to read `storage/logs/laravel.log` is the only way to notice something's wrong otherwise.

**`spatie/laravel-backup`** — backs up the database and the full app directory (including `storage/`, where KYC documents and payment proofs live) on a nightly schedule (`routes/console.php`): `backup:clean` at 01:00, `backup:run` at 01:30, `backup:monitor` at 02:00, all driven by the same cron `schedule:run` entry from §6 — no extra cron line needed.
- [ ] Set `BACKUP_DISK` to something other than `local` once you have an off-server disk configured (S3 or similar) — a backup stored on the same shared-hosting account it's protecting against doesn't help if that account is lost or suspended.
- [ ] Set `BACKUP_NOTIFICATION_EMAIL` so backup failures actually reach someone.
- [ ] Run `php artisan backup:run` once by hand after first deploying, and confirm a zip actually lands in `storage/app/Laravel/` (or your configured disk) before trusting the cron schedule.

**`sentry/sentry-laravel`** — reports unhandled exceptions in real time (wired into `bootstrap/app.php`'s `withExceptions()`).
- [ ] Create a free Sentry project and set `SENTRY_LARAVEL_DSN` in production `.env`.
- [ ] Leave it unset in local dev — with no DSN, the package silently does nothing, which is the desired behaviour.

## 5. Database seeding

`database/seeders/DatabaseSeeder.php` is environment-aware — it checks `app()->environment('production')` itself, so you don't need to remember a list of individual `--class=` flags.

- [ ] Set `APP_ENV=production` in `.env` (you should already have this from §3).
- [ ] Run plain `php artisan db:seed --force` after the first `migrate --force`. In production this seeds only: `ShieldPermissionsSeeder`, `RolesAndPermissionsSeeder`, `AdminUserSeeder` (idempotent — only set `ADMIN_EMAIL`/`ADMIN_PASSWORD` for real before this first run), `SettingsSeeder`, `MakesSeeder`, `MakeLogosSeeder`, `CarModelsSeeder`, `FaqSeeder`.
- [ ] `CarSeeder` (50 fake cars), `OrderSeeder` (fake orders), and `BlogPostSeeder` (a starter sample post) are skipped automatically outside local/testing — `db:seed`/`migrate:fresh --seed` are now safe to run in production without manually excluding them.
- [ ] It's still safe to re-run `php artisan db:seed --force` again later (e.g. after a redeploy) — every seeder in the production set uses `firstOrCreate`/`updateOrCreate`-style idempotency, so it won't duplicate rows.

## 6. Build & deploy steps

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build          # or build locally and upload public/build/ if Node isn't usable over SSH
php artisan key:generate         # once only — never re-run after going live, it invalidates existing sessions/encrypted data
php artisan migrate --force
php artisan storage:link         # if this doesn't create a working symlink on Hostinger's filesystem, create it manually via hPanel File Manager
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 7. Replacing Horizon: cron-driven processing (hPanel → Cron Jobs)

Since there's no persistent worker on this tier, add **two** cron entries:

- [ ] **Scheduler** (drives `PublishScheduledPosts` every minute, `ArchiveSoldCars` daily at 00:00 Africa/Accra, and `GenerateSitemap` hourly):
  ```
  * * * * * php /home/USERNAME/path-to-app/artisan schedule:run >> /dev/null 2>&1
  ```
- [ ] **Queue worker** (processes queued jobs — notably `SendGiantSms` — then exits, since nothing can stay running):
  ```
  * * * * * php /home/USERNAME/path-to-app/artisan queue:work --stop-when-empty >> /dev/null 2>&1
  ```

Replace the path with the real absolute path to this app's `artisan` file on the server.

## 8. Filament admin access

- [ ] Admin panel is at `/admin`. Shield (permissions) and Breezy (2FA) are both active.
- [ ] After seeding, log in with the real `ADMIN_EMAIL`/`ADMIN_PASSWORD` and set up 2FA before relying on the account.

## 9. Final smoke test

- [ ] Homepage loads.
- [ ] `/cars` catalogue and its filters work.
- [ ] Admin can log into `/admin`.
- [ ] A test OTP/SMS actually arrives (confirms `GIANTSMS_API_KEY` **and** that the queue cron is really running — check it doesn't just sit queued forever).
- [ ] A test order's email notification arrives (confirms `MAIL_*`).
- [ ] An uploaded image (e.g. a car photo) actually renders on the page (confirms storage disk + `storage:link` took effect).
- [ ] `php artisan backup:run` produces a real backup file, and `BACKUP_NOTIFICATION_EMAIL` receives the success notification.
- [ ] Trigger a test exception (e.g. visit a route that throws) and confirm it shows up in the Sentry dashboard.
