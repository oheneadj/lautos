# Changelog

All notable changes to this project are documented in this file.

## [Unreleased]

### Security
- Admin Settings page no longer leaks the real bank account number, MoMo number, or exchange rate to a `staff_admin` (Orders-only role) — `disabled()` form fields only block editing in Filament, they still render the current value, so any panel user who could open `/admin/settings` could previously read those fields even though only `super_admin`/exchange-rate-permission holders could edit them. The values are now stripped out of the form state entirely before it reaches a user who isn't allowed to see them.
- Support ticket attachments now live on the private disk and are only reachable through a signed URL (viewable by the ticket's own customer or an admin) — previously both the customer-side upload and the admin reply upload stored attachments on the public disk with no access control at all, an enumerable/guessable path to potentially sensitive screenshots or documents.
- KYC document endpoint (`admin.kyc-documents.show`) now requires the requester to actually be an admin, not just authenticated — previously any logged-in customer holding a valid signed URL (e.g. one that leaked via browser history or a screenshot) could view another customer's Ghana Card/TIN.
- Phone OTP send is now rate-limited (1 per minute per account) — previously a customer could trigger unlimited real SMS sends to any phone number, since `phone` has no uniqueness constraint either.
- Phone OTP codes now expire after 10 minutes and verification attempts are rate-limited (5 per 10 minutes) — previously a code never expired and guesses were unlimited, making brute force feasible.
- `OrderService::confirmPayment()` now locks the order row inside a transaction — previously two near-simultaneous confirmations on the same order (a double-click, or two admin sessions) could both pass the status check before either wrote, double-logging history and double-firing `PaymentConfirmed`.
- `OrderDetail::uploadPaymentProof()` now re-locks and re-checks the order's status against the database instead of trusting the in-memory property — previously a status change from a concurrent admin action in the moment around a customer's upload could be silently overridden.
- Settings page now re-checks role server-side before persisting `bank_*`/`momo_*`/exchange-rate fields — previously these were only `disabled()` client-side, which Filament's own docs warn is bypassable, meaning a non-super-admin could have redirected where customer payments go.
- Added `SmsLogPolicy` restricting SMS Logs (which store OTP codes in plaintext) to `super_admin` only — previously any panel role, including `staff_admin`, could read live OTP codes straight out of the log.
- Admin-side phone OTP (`ProfilePhoneInfo`) now has the same rate-limit/expiry protections as the customer side — it had been missed when that was fixed previously.
- Support ticket attachments now require `mimes:jpg,jpeg,png,pdf`, closing a stored-XSS path via `.svg`/`.html` uploads that was open while every other upload in the app already restricted this.
- Contact form and registration are now rate-limited — previously the only two of CLAUDE.md's three named public forms (alongside login) with no throttle at all.
- Order admin actions (`confirmPayment`, `rejectPayment`, `advanceStage`, `fillLogistics`, `cancelOrder`, `addNote`) now call `->authorize('update', $order)` — previously only the resource's built-in actions were policy-gated, not these custom ones.
- `KycService::verify()` now refuses to verify a customer with no KYC documents on file.
- Review approve/reject and Car status-change actions (single and bulk) in the admin panel now call `->authorize()`/`->authorizeIndividualRecords()` — previously, like the Order actions above, only the resources' built-in actions were policy-gated.
- Google sign-in no longer silently links to an existing password account just because the email matches — previously, since registration never verifies email ownership, an attacker could pre-register a victim's email and inherit the victim's Google identity the moment they signed in. Customers now connect Google to an existing account explicitly from Security settings instead.
- Resetting a forgotten password now logs out every other active session for that account — previously a compromised session stayed valid through a password reset meant to shut it out.

### Added
- Security settings now has a "Connected Accounts" section to connect/disconnect Google, with an "Add Password" option for Google-only accounts that have never set a real password (sends the same reset-link email used by "Forgot password", without requiring logout first).
- Security settings now has an "Active Sessions" list — see every device signed in (IP, browser, last active), log out any single session, or log out all other sessions at once.
- Customers are now notified (mail + database + SMS if they have a phone) when an admin verifies their KYC — previously they only ever heard from us on a rejection, never an approval.
- Public pages now have canonical URLs (filtered/paginated catalogue and blog listings all canonicalize back to their base URL), Twitter Card tags, and structured data: site-wide `Organization` schema, `BreadcrumbList` on car/blog detail pages, `Article` schema on blog posts (real author/dates from the post itself), and `FAQPage` schema on `/faqs` built from the same `Faq` rows the page renders. All 9 previously-generic static pages (How It Works, Shipping, Customs Clearance, Quality Guarantee, FAQs, Refund Policy, Terms, Privacy, Fraud Awareness) now have real per-page titles/descriptions instead of a generic fallback.
- `DatabaseSeeder` is now environment-aware — `php artisan db:seed` in production seeds only roles/permissions, the admin account, settings, and make/model/FAQ reference data, automatically skipping the fake demo seeders (cars, orders, sample blog post). `deploy.sh` now runs it on every deploy instead of requiring a remembered list of individual `--class=` commands.

### Fixed
- The bank account name/number shown to a customer on their order detail page, and in the order-placed email, are no longer blank — `OrderDetail::paymentInfo()` and `OrderPlacedNotification` were reading `Setting::get('account_name'/'account_number')`, but the Settings page actually stores them as `bank_account_name`/`bank_account_number`, so the wrong key always fell back to the '—' placeholder regardless of what the admin had configured. This blocked any customer trying to pay by bank transfer (MoMo details were unaffected, since those key names did match).
- `sitemap:generate` now includes Reserved cars (using the same `visibleOnCatalogue()` scope the public catalogue itself uses) instead of only Available ones, so the sitemap no longer drifts from what's actually browsable on the site.
- Verifying a customer's KYC with no documents on file, or any other admin order action hitting one of `OrderService`'s state guards, now shows a Filament notification instead of a raw 500 — the guards added in earlier rounds were correctly blocking the action but had nowhere to surface the message.
- `KycService::verify()` no longer requires both Ghana Card and TIN to be on file — a customer only ever needs to provide one of the two (per `ProfileEditRequest`'s own validation rules), so an admin verifying a customer who'd only uploaded one document was incorrectly blocked.
- Payment proofs are now viewable by admins again — the Filament infolist was building a `Storage::disk('public')` URL for files that are actually stored on the `private` disk, so every proof 404'd. They're now served through a signed admin-only route, the same pattern already used for KYC documents.
- Blog posts and the car list in the admin panel now eager-load their `category`/`author` and `make`/`carModel`/`images` relations — previously every page of either table triggered an extra query per row.
- GiantSMS OTP delivery no longer silently fails — `GiantSmsService` now sets a 15s timeout and catches connection failures (previously a non-responding gateway would hang indefinitely and bypass error handling, leaving the customer with no feedback even though the SMS had already sent).
- An order's displayed car details (year, make, model, thumbnail) no longer break or silently change when its car is edited, soft-deleted, or force-deleted — `orders` now snapshots these fields at creation time, the same way price was already snapshotted. Existing orders were backfilled from their current car relation.
- Admin phone number is now correctly stored without spaces (`+233551234567`), consistent with the Ghana phone validation pattern, which requires no spaces.
- Removed an incorrect `phone` validation rule from `ProfileEditRequest` that was blocking the customer profile-info save (name/address/KYC) entirely, since that form never submits a phone field.
- Fixed the homepage hero search and the public car catalogue's body-type filter chips returning zero results whenever a `<select>`'s default/"Any" option was left unselected — Livewire was hydrating that into a blank array entry which made `whereIn()` match nothing, blanking the entire result set regardless of any other filter selected (e.g. picking only "Manual" transmission would show no cars at all).
- Fixed an uncaught `ValueError` (500 error) on the public car catalogue when an invalid `body_type` value appeared in the URL.

### Removed
- "Delete Account" section from the customer-facing profile page (`/dashboard/profile`). The underlying feature/component is untouched, just no longer shown there.
- "Recently Added Cars" widget from the admin dashboard.

### Added
- `sms_logs` table and admin "SMS Logs" page — every GiantSMS request/response (OTP and queued notifications alike) is now recorded for troubleshooting.
- Admin ability to rename/remove a make's car models and trims from the make's edit page, via a new `CarModelService`.
- `DEPLOYMENT.md` — a production deployment checklist for Hostinger Premium/Business shared hosting (env vars, cron jobs, seeding, smoke tests).
- `spatie/laravel-backup` — nightly database + storage backups (`backup:clean`/`backup:run`/`backup:monitor`, scheduled in `routes/console.php`), since this app has no server-level backup tooling on shared hosting.
- `sentry/sentry-laravel` — real-time error reporting, wired into `bootstrap/app.php`'s exception handler.

### Removed
- `laravel/horizon` — required Redis and a persistent worker process, neither available on the shared hosting this app deploys to. The queue now runs on the plain `database` driver via a cron-driven `queue:work --stop-when-empty`.
