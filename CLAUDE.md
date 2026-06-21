# Livingston Autos — AI Coding Agent Instructions
> These instructions govern every line of code you write for this project.
> You must read and follow them completely before writing any code.
> You must re-read them at the start of every new session and every time you start a new feature.
> If you are ever unsure whether something violates these instructions, stop and re-read them.

---

## 0. How to Never Forget These Instructions

At the start of every session, every new feature, and every new file:

1. Read this entire file before writing a single line of code.
2. Before you create any file, ask yourself: *"Does this follow every rule in this document?"*
3. Before you finish any feature, ask yourself: *"Have I written the test, run it, and does it pass?"*
4. If you are continuing work from a previous session, re-read this file first. Do not assume you remember it.
5. Keep a running mental checklist: DRY · KISS · YAGNI · Comments · Tests · Enums · Reusable Components · UUIDs · Tenant Scope.
6. If a user asks you to skip any of these rules, politely decline and explain why the rule exists.

---

## 1. Project Context

**Project:** Livingston Autos — Car Import & Sales Web Application
**Client:** Mr. Seth
**Stack:** Laravel 13 · Livewire 4 · AlpineJS · Tailwind CSS · FilamentPHP · MySQL 8

**Reference Documents (read before starting any feature):**
- `docs.md` — Full system requirements, epics, user stories, acceptance criteria, tasks
- `livingston_autos_srs_v2.docx` — Software Requirements Specification

**Architecture:**
- Admin panel → FilamentPHP (route: `/admin`)
- Public website → Blade + Livewire + AlpineJS + Tailwind
- Customer dashboard → Blade + Livewire + AlpineJS + Tailwind (custom-built, NOT Filament)
- Single-tenant → Built exclusively for Livingston Autos. No multi-vendor isolation needed.
- Route IDs → UUIDs only. Raw integer IDs are never exposed in URLs or API responses.

---

## 2. Core Coding Principles

These three principles apply to every single file you create or edit. No exceptions.

### 2.1 KISS — Keep It Simple, Stupid
- Write the simplest code that correctly solves the problem.
- If you can do it in 5 lines, do not write 15.
- Avoid clever tricks. Prefer boring, obvious code over elegant but hard-to-read code.
- If a junior developer would have to Google what your code does, rewrite it to be simpler.

### 2.2 DRY — Don't Repeat Yourself
- If the same logic appears in more than one place, extract it.
- Use Laravel service classes, traits, helper methods, Blade components, and Livewire components to avoid duplication.
- If you copy and paste code, stop. That is a sign you need to abstract it.
- Shared UI pieces must be Blade components or Livewire components, never duplicated HTML.

### 2.3 YAGNI — You Aren't Gonna Need It
- Only build what is required by the current user story you are working on.
- Do not add extra fields, methods, or features "just in case" they might be needed later.
- Do not over-engineer. Build exactly what is needed, no more.
- If a feature is not in `docs.md`, do not build it unless explicitly asked.

---

## 3. Code Style & Readability

- Write code as if another developer will maintain it tomorrow.
- Use clear, descriptive names for variables, methods, and classes. No abbreviations unless universally understood (e.g. `$id`, `$url`).
- Keep methods short. A method should do one thing. If it does more than one thing, split it.
- Keep controllers thin. Business logic belongs in Service classes, not controllers.
- Keep Livewire components focused. One component = one responsibility.
- Prefer explicit over implicit. Do not rely on magic unless it is standard Laravel convention.

**Naming conventions:**
```
Models          → PascalCase singular       → Car, Order, BlogPost
Controllers     → PascalCase + Controller   → CarController
Livewire        → PascalCase                → CarCatalogue, OrderTimeline
Services        → PascalCase + Service      → OrderService, NotificationService
Jobs            → PascalCase verb phrase    → SendOrderConfirmationEmail, ArchiveSoldCars
Events          → PascalCase past tense     → OrderPlaced, PaymentConfirmed
Listeners       → PascalCase verb phrase    → SendOrderConfirmationNotification
Migrations      → snake_case description    → create_cars_table
Blade views     → kebab-case               → car-card.blade.php
Variables       → camelCase                → $carPrice, $orderId
```

---

## 4. Comments

Every file you write must be commented. Follow these rules exactly.

### 4.1 Voice & Tone
- Write all comments in **first person singular** as the developer, not as an AI.
- Sound like a human developer explaining their own code.
- Use **simple, plain English**. If a comment needs a dictionary, rewrite it.
- Be **concise**. Do not state the obvious. Comments explain *why*, not *what* (the code shows *what*).

**Good:**
```php
// I use eager loading here to avoid N+1 queries on the catalogue page.
$cars = Car::with('images')->available()->paginate(12);
```

**Bad:**
```php
// This code retrieves all available cars from the database with their associated images
// using eager loading to prevent N+1 query problems and then paginates the results
// to show 12 items per page on the catalogue page of the application.
$cars = Car::with('images')->available()->paginate(12);
```

**Bad (AI voice):**
```php
// Here we retrieve cars with their images to optimize database queries
$cars = Car::with('images')->available()->paginate(12);
```

### 4.2 File-Level DocBlocks
Every PHP class file must have a file-level docblock:

```php
<?php

/**
 * Handles the public car catalogue — filtering, searching, and pagination.
 *
 * @author Ohene Adjei
 */
```

### 4.3 Class DocBlocks
```php
/**
 * Manages the lifecycle of a customer order from placement to delivery.
 */
class OrderService
```

### 4.4 Method DocBlocks
Use docblocks on all public methods. Keep descriptions to one clear sentence.

```php
/**
 * Advances an order to the next stage in the shipment pipeline.
 *
 * @param  Order  $order
 * @param  string $stage
 * @param  string|null $note
 * @return void
 */
public function advanceStage(Order $order, string $stage, ?string $note = null): void
```

### 4.5 Inline Comments
Use inline comments sparingly — only where the logic is not immediately obvious.

```php
// I check for sold_at rather than status so restored cars don't get re-archived.
if ($car->sold_at && $car->sold_at->diffInDays(now()) >= 7) {
    $car->delete();
}
```

### 4.6 No Noise Comments
Do not write comments that just repeat the code:

```php
// Bad — this just repeats what the code says
$order->save(); // save the order

// Good — explains why
$order->touch(); // I force a timestamp update so the dashboard sorts this order to the top.
```

---

## 5. Enums

- **Never use MySQL enum types in migrations.** Always use `string` in the migration column definition.
- **Always create a PHP Enum class** for any field that has a fixed set of values.
- **Always use the Enum class** everywhere that field is referenced — models, services, Filament, Livewire, Blade views.
- Enum classes live in `app/Enums/`.

**Migration (correct):**
```php
// I use a string here and enforce values via the CarStatus enum class.
$table->string('status')->default('available');
```

**Enum class:**
```php
<?php

namespace App\Enums;

/**
 * Defines the possible availability states of a car listing.
 */
enum CarStatus: string
{
    case Available = 'available';
    case Reserved  = 'reserved';
    case Sold      = 'sold';

    /** Returns a human-readable label for display. */
    public function label(): string
    {
        return match($this) {
            self::Available => 'Available',
            self::Reserved  => 'Reserved',
            self::Sold      => 'Sold',
        };
    }

    /** Returns the Tailwind colour class for the status badge. */
    public function colour(): string
    {
        return match($this) {
            self::Available => 'green',
            self::Reserved  => 'amber',
            self::Sold      => 'red',
        };
    }
}
```

**Model (correct):**
```php
protected $casts = [
    'status' => CarStatus::class,
];
```

**All enums for this project:**

| Enum Class | Values | Used On |
|---|---|---|
| `App\Enums\CarStatus` | `available`, `reserved`, `sold` | `cars.status` |
| `App\Enums\OrderStatus` | `pending_payment`, `payment_uploaded`, `payment_confirmed`, `purchased`, `in_transit_to_port`, `shipped`, `arrived_in_ghana`, `cleared`, `delivered` | `orders.status` |
| `App\Enums\KycStatus` | `pending`, `verified`, `needs_resubmission` | `users.kyc_status` |
| `App\Enums\BlogStatus` | `draft`, `published` | `blog_posts.status` |

---

## 6. Reusable Components

Always check if a component already exists before creating a new one. Never duplicate UI or logic.

### 6.1 Blade Components
Reusable UI pieces live in `resources/views/components/`.
Create a Blade component any time a UI element is used in more than one place.

```
resources/views/components/
  car-card.blade.php          ← used on homepage, catalogue, dashboard
  status-badge.blade.php      ← used everywhere a status is shown
  page-header.blade.php       ← page title + breadcrumb
  alert.blade.php             ← success / error / warning messages
  whatsapp-button.blade.php   ← floating button on all pages
  demurrage-warning.blade.php ← clearing delay warning
  section-title.blade.php     ← consistent section headings
```

**Usage:**
```blade
<x-car-card :car="$car" />
<x-status-badge :status="$car->status" />
<x-alert type="success" message="Order placed successfully." />
```

### 6.2 Livewire Components
Create a Livewire component any time a piece of UI needs reactivity (filtering, search, forms, real-time updates).

```
app/Livewire/
  Public/
    CarCatalogue.php        ← filterable car listing
    CarSearch.php           ← search bar
  Customer/
    PlaceOrder.php          ← order confirmation modal
    UploadPaymentProof.php  ← proof upload
    ShipmentTimeline.php    ← order tracking
    EditProfile.php         ← profile form
    KycDocuments.php        ← KYC upload
  Auth/
    Register.php
    CompleteKyc.php
```

### 6.3 Service Classes
Extract all business logic into service classes in `app/Services/`.
Controllers and Livewire components only call services — they never contain business logic themselves.

```
app/Services/
  OrderService.php       ← create orders, advance stages, confirm payments
  CarService.php         ← create/update cars, manage status, archive
  NotificationService.php← dispatch email + SMS notifications
  KycService.php         ← handle KYC status updates
  SettingsService.php    ← read/write settings with caching
```

**Example — thin controller:**
```php
public function store(PlaceOrderRequest $request, Car $car): RedirectResponse
{
    // I delegate all order logic to the service to keep this controller clean.
    $order = $this->orderService->createOrder(auth()->user(), $car);

    return redirect()->route('dashboard.orders.show', $order);
}
```

### 6.4 Laravel Components to Always Use
- **Form Requests** for all validation — never validate in controllers or Livewire `rules()` for complex rules.
- **Policies** for all authorisation — never `if ($user->role === 'admin')` in controllers.
- **Events & Listeners** for all side effects (emails, SMS, status logging) — never call these directly in services.
- **Jobs** for all queued work — never send email synchronously.
- **Scopes** on models for common queries — never repeat `where()` clauses.

```php
// I use a local scope so I never have to repeat this filter across the codebase.
public function scopeAvailable(Builder $query): Builder
{
    return $query->where('status', CarStatus::Available);
}
```

---

## 7. Migrations

- Always use `string` for enum-like columns (see Section 5).
- Every migration must be reversible — write the `down()` method properly.
- Never modify an existing migration that has been committed. Create a new migration instead.
- Add database-level indexes on all foreign keys and any column used in `WHERE` or `ORDER BY` clauses.
- Always add comments to migrations explaining non-obvious columns.
- **Every table must have a `uuid` column.** The `id` column still exists as the internal primary key but `uuid` is what gets used in routes, APIs, and all external-facing references.

```php
// I use uuid for all external references — the integer id never leaves the database.
$table->uuid('uuid')->unique();

// I store the price in USD cents (integer) to avoid floating point issues.
$table->unsignedInteger('price_usd_cents');

// I keep sold_at separate from status so I can query the archive window accurately.
$table->timestamp('sold_at')->nullable();
```

---

## 8. Testing

**This is non-negotiable. Every feature must have tests that pass before you move on.**

### 8.1 Rules
- Write tests **before or alongside** the feature, never after.
- Run the tests after writing them. If they fail, fix the code — not the tests.
- Do not move to the next task until all tests for the current task pass.
- Use **Feature tests** for HTTP/Livewire flows (routes, forms, pages).
- Use **Unit tests** for isolated logic (service methods, enum methods, helpers).

### 8.2 What to Test — Minimum Requirements

For every feature, write tests that cover:
1. **Happy path** — the feature works correctly with valid input.
2. **Auth guard** — unauthenticated users are redirected; unauthorised users get 403.
3. **Validation** — invalid input returns the correct validation errors.
4. **Edge cases** — at least one scenario where something could go wrong.

### 8.3 Test Structure

```
tests/
  Feature/
    Admin/
      CarManagementTest.php
      OrderManagementTest.php
      RolesPermissionsTest.php
      BlogManagementTest.php
    Customer/
      RegistrationTest.php
      OrderPlacementTest.php
      PaymentProofUploadTest.php
      ShipmentTrackingTest.php
    Public/
      CarCatalogueTest.php
      CarDetailPageTest.php
      BlogTest.php
  Unit/
    Services/
      OrderServiceTest.php
      CarServiceTest.php
    Enums/
      CarStatusTest.php
      OrderStatusTest.php
    Jobs/
      ArchiveSoldCarsTest.php
```

### 8.4 Test Examples

**Feature test:**
```php
<?php

namespace Tests\Feature\Admin;

use App\Enums\CarStatus;
use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests the admin car management workflows.
 */
class CarManagementTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_create_a_car_listing(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->post('/admin/cars', [
                'make'  => 'Toyota',
                'model' => 'Corolla',
                'year'  => 2020,
                // ... other required fields
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('cars', ['make' => 'Toyota', 'model' => 'Corolla']);
    }

    /** @test */
    public function guest_cannot_access_admin_car_management(): void
    {
        $this->get('/admin/cars')->assertRedirect('/admin/login');
    }

    /** @test */
    public function car_requires_at_least_three_photos(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->post('/admin/cars', ['make' => 'Toyota']) // no photos
            ->assertSessionHasErrors('images');
    }
}
```

**Unit test:**
```php
<?php

namespace Tests\Unit\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Services\OrderService;
use Tests\TestCase;

/**
 * Tests the order stage progression rules.
 */
class OrderServiceTest extends TestCase
{
    /** @test */
    public function order_stages_must_advance_in_sequence(): void
    {
        $order = Order::factory()->create(['status' => OrderStatus::PaymentConfirmed]);
        $service = new OrderService();

        // I expect an exception if we try to skip a stage.
        $this->expectException(\InvalidArgumentException::class);

        $service->advanceStage($order, OrderStatus::Shipped);
    }
}
```

### 8.5 Running Tests

After writing tests for any feature, run:

```bash
php artisan test --filter=FeatureNameTest
```

If any test fails, **fix the application code** until they all pass. Then run the full suite:

```bash
php artisan test
```

Do not proceed to the next feature until the full suite is green.

---

## 9. Filament-Specific Rules

- Every Filament Resource must have a corresponding Policy registered.
- Use `->authorize()` on all actions that modify data.
- Build complex order/car views as custom Filament `Infolist` pages, not generic edit forms.
- The shipment timeline on the order detail page must be a custom `Infolist` component — not a dropdown.
- Navigation badge for "Orders Requiring Action" must query `Payment Uploaded` status count.
- All Filament forms must have grouped sections with clear labels for readability.
- Use `->columns(2)` on form layouts — never put everything in a single column.

---

## 10. Security Rules

- Never trust user input. Validate everything with Form Requests.
- Never expose KYC document URLs directly. Always serve them through a signed temporary URL.
- Never put business logic in Blade views or migrations.
- Always use Laravel's `$casts` for sensitive fields (encryption on Ghana Card, TIN).
- Never hardcode credentials. All keys, passwords, API tokens go in `.env`.
- Always use Policies for authorisation checks — never manual role string comparisons in controllers.
- Rate-limit all public-facing forms (contact, login, registration).

---

## 11. Git Commit Rules

- One commit per completed task from `docs.md`.
- Commit message format: `[TASK-ID] Short description of what was done`
- Example: `[T-05-1] Add Car model with fillable fields and SoftDeletes`
- Never commit directly to `main`. All work goes to `develop` via feature branches.
- Branch naming: `feature/epic-name` or `fix/short-description`

---

## 12. Before You Mark Any Task Done

Run through this checklist mentally before saying a task is complete:

- [ ] Does the code follow KISS, DRY, and YAGNI?
- [ ] Is every method doing only one thing?
- [ ] Are there docblocks on the class and all public methods?
- [ ] Are inline comments in first person, simple English, explaining *why* not *what*?
- [ ] If there is an enum-like field — is it a `string` in the migration and an Enum class in the code?
- [ ] Is any repeated UI extracted into a Blade component?
- [ ] Is any business logic in a Service class, not in the controller or Livewire component?
- [ ] Are tests written for this feature?
- [ ] Did I run the tests and do they all pass?
- [ ] Is the full test suite still green?
- [ ] Are all routes using UUIDs? Is the integer `id` hidden from every URL and response?
- [ ] Is customer data excluded from all public (unauthenticated) views and queries?
- [ ] Are internal admin notes excluded from all customer-facing queries?
- [ ] Are the auth and authorisation boundaries covered by tests?

If the answer to any of these is **no**, fix it before moving on.

---

## 13. ID Security — Never Expose Raw Integer IDs

Raw auto-increment integer IDs tell an attacker exactly how many records exist, make enumeration attacks trivial (try `/orders/1`, `/orders/2`...), and leak business information. We never expose them.

### 13.1 The Rule
- Every table has both an `id` (integer, internal primary key) and a `uuid` (string, public identifier).
- The `id` column **never appears** in any URL, API response, Blade view, Livewire component, or JavaScript.
- The `uuid` is the only identifier used in all external-facing contexts.
- In Filament (admin panel), use `uuid` as the route key as well — admin users should not see integer IDs either.

### 13.2 Migration Setup
Every table migration must include a `uuid` column:

```php
Schema::create('cars', function (Blueprint $table) {
    $table->id();                        // internal only — never used in routes
    $table->uuid('uuid')->unique();      // I use this everywhere externally
    // ... other columns
    $table->timestamps();
});
```

### 13.3 Model Setup
Use the `HasUuids` trait on every model so UUIDs are auto-generated, and override `getRouteKeyName()` so Laravel's route model binding uses `uuid` automatically:

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

/**
 * Represents a car listing in the system.
 */
class Car extends Model
{
    // I use HasUuids so the uuid is auto-generated on creation — no manual work needed.
    use HasUuids;

    /**
     * I route by uuid so integer ids never appear in URLs.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
```

### 13.4 Routes
Routes always use the model variable, never a raw id:

```php
// Correct — uuid is resolved automatically by route model binding
Route::get('/cars/{car}', [CarController::class, 'show']);

// The URL will look like: /cars/550e8400-e29b-41d4-a716-446655440000
// Never like: /cars/42
```

### 13.5 Blade & Livewire — Never Output an Integer ID
```blade
{{-- Correct --}}
<a href="{{ route('cars.show', $car) }}">View Car</a>

{{-- Wrong — exposes integer id --}}
<a href="/cars/{{ $car->id }}">View Car</a>
```

```php
// Correct — pass uuid to any place a reference is needed externally
$this->dispatch('car-selected', uuid: $car->uuid);

// Wrong
$this->dispatch('car-selected', id: $car->id);
```

### 13.6 API Responses & JSON
Never include `id` in any JSON response. Always use `uuid` and name it `id` in the response so consumers don't need to change their code:

```php
// I rename uuid to id in the response so the integer id is never exposed.
return response()->json([
    'id'    => $car->uuid,
    'make'  => $car->make,
    'model' => $car->model,
]);
```

### 13.7 Tests — Always Assert on UUID in URLs
```php
/** @test */
public function car_detail_url_uses_uuid_not_integer_id(): void
{
    $car = Car::factory()->create();

    // I confirm the route uses the uuid, not the integer id.
    $this->get(route('cars.show', $car))
         ->assertOk()
         ->assertDontSee($car->id)   // integer id must not appear on the page
         ->assertSee($car->uuid);    // uuid should be in the canonical URL
}
```

---

## 14. Separation of Concerns

This is a **single-tenant application** built exclusively for Livingston Autos. There is no multi-vendor or multi-tenant infrastructure. The separation of concerns here refers to keeping each layer of the application responsible for exactly one thing — not data isolation between businesses.

### 14.1 The Three Application Zones

The application has three distinct zones. Code must never cross zone boundaries directly — always go through the defined interface (service, event, policy).

| Zone | Route prefix | Built with | Responsibility |
|---|---|---|---|
| **Admin Panel** | `/admin` | FilamentPHP | CRUD, order management, KYC review, settings |
| **Public Website** | `/` | Blade + Livewire | Catalogue, blog, static pages — no auth required |
| **Customer Dashboard** | `/dashboard` | Blade + Livewire | Orders, payment proof, profile, tracking — auth required |

### 14.2 Layer Rules

**Controllers & Livewire components** — handle HTTP/UI only. They receive input, call a service, and return a response or update the view. They contain zero business logic.

**Service classes** (`app/Services/`) — own all business logic. A service method does one job: create an order, advance a shipment stage, verify KYC. Services never touch the request or response directly.

**Models** — own data relationships, casts, scopes, and simple computed attributes. No business logic beyond local query scopes.

**Events & Listeners** — all side effects (emails, SMS, status history logging) are triggered by events, never called inline inside services or controllers.

**Policies** (`app/Policies/`) — own all authorisation decisions. No `if ($user->role === 'admin')` checks anywhere outside a Policy.

### 14.3 Customer Data Must Never Reach the Public Zone

- Customer orders, KYC documents, and personal data are never queried or rendered on public (unauthenticated) pages.
- The `auth` middleware must be on every `/dashboard/*` route.
- Admin Filament resources query customer data only within the authenticated `/admin` context.

### 14.4 Admin Data Must Never Reach the Customer Zone

- Internal admin notes (`order_notes`) are excluded from every customer-facing query — never load them in a Livewire component the customer can see.
- KYC document signed URLs are only generated inside authenticated admin or customer contexts, never cached or exposed publicly.

### 14.5 Authorisation Boundary Tests

Write a test for each protected zone that confirms an unauthenticated user and a wrong-role user are both rejected:

```php
/** @test */
public function guest_cannot_access_customer_dashboard(): void
{
    $this->get('/dashboard')->assertRedirect('/login');
}

/** @test */
public function customer_cannot_access_admin_panel(): void
{
    $customer = User::factory()->customer()->create();

    $this->actingAs($customer)->get('/admin')->assertForbidden();
}
```

---

*These instructions are the law for this project. When in doubt, re-read them.*
*Last updated: May 2026 — Livingston Autos*
