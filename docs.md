# Livingston Autos — System Requirements
### Agile Development Specification · v1.0 · May 2026

---

## Table of Contents

1. [Project Overview](#project-overview)
2. [Tech Stack](#tech-stack)
3. [Agile Conventions](#agile-conventions)
4. [Phase 1 Delivery Plan](#phase-1-delivery-plan)
5. [PART A — Admin Panel (FilamentPHP)](#part-a--admin-panel-filamentphp)
   - [Epic 1 — Project Setup & Base Configuration](#epic-1--project-setup--base-configuration)
   - [Epic 2 — Authentication & Roles/Permissions](#epic-2--authentication--rolespermissions)
   - [Epic 3 — Car Management](#epic-3--car-management)
   - [Epic 4 — Order Management](#epic-4--order-management)
   - [Epic 5 — User & KYC Management](#epic-5--user--kyc-management)
   - [Epic 6 — Blog Management](#epic-6--blog-management)
   - [Epic 7 — System Settings](#epic-7--system-settings)
   - [Epic 8 — Admin Dashboard Widgets](#epic-8--admin-dashboard-widgets)
6. [PART B — Public Frontend](#part-b--public-frontend)
   - [Epic 9 — Homepage](#epic-9--homepage)
   - [Epic 10 — Car Catalogue](#epic-10--car-catalogue)
   - [Epic 11 — Car Detail Page](#epic-11--car-detail-page)
   - [Epic 12 — Blog (Public)](#epic-12--blog-public)
   - [Epic 13 — Static Pages](#epic-13--static-pages)
   - [Epic 14 — SEO & Performance](#epic-14--seo--performance)
7. [PART C — Customer Dashboard](#part-c--customer-dashboard)
   - [Epic 15 — Customer Authentication & KYC Registration](#epic-15--customer-authentication--kyc-registration)
   - [Epic 16 — Customer Dashboard Home](#epic-16--customer-dashboard-home)
   - [Epic 17 — Order Placement](#epic-17--order-placement)
   - [Epic 18 — Payment Proof Upload](#epic-18--payment-proof-upload)
   - [Epic 19 — Shipment Tracking](#epic-19--shipment-tracking)
   - [Epic 20 — Profile & KYC Management](#epic-20--profile--kyc-management)
   - [Epic 21 — Notifications](#epic-21--notifications)
8. [Database Schema Overview](#database-schema-overview)
9. [Background Jobs & Scheduler](#background-jobs--scheduler)
10. [Phase 2 — Import Duty Calculator](#phase-2--import-duty-calculator)

---

## Project Overview

**Client:** Mr. Seth — Livingston Autos
**Product:** Car Import & Sales Web Application
**Purpose:** A digital storefront and order management system for importing and selling vehicles sourced from South Korea and Japan into Ghana.

**Core Flow:**
> Customer browses cars → places order → pays offline (bank/MoMo) → uploads proof → admin confirms → car ships → customer tracks → car delivered → customer clears at port

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | Laravel 13 |
| Frontend Reactivity | Livewire 4 |
| JavaScript | AlpineJS |
| CSS | Tailwind CSS |
| Admin Panel | FilamentPHP |
| Roles & Permissions | FilamentShield + Spatie Laravel Permission |
| Database | MySQL 8 |
| File Storage | Laravel Storage / AWS S3 |
| Queue & Scheduler | Laravel Horizon + Redis |
| Auth | Laravel Fortify (with 2FA for admin) |
| SMS | Arkesel or Hubtel (Ghana) |
| Email | Laravel Mail + SMTP (e.g. Mailgun / Resend) |
| SEO | Artesaos/SEOTools |

---

## Agile Conventions

```
EPIC       → Major feature area
US-XX      → User Story
AC         → Acceptance Criteria (what "done" looks like)
TASK       → Individual developer task
Priority   → Must Have | Should Have | Nice to Have
Points     → Story point estimate (Fibonacci: 1 2 3 5 8 13)
Sprint     → Suggested sprint assignment (2-week sprints)
```

**Definition of Done (DoD):**
- Feature works as described in all ACs
- Mobile responsive (tested at 375px, 768px, 1280px)
- No console errors
- Migrations committed
- Feature branch merged to `develop` via PR
- Reviewed by at least one other developer or self-reviewed against AC checklist

---

## Phase 1 Delivery Plan

| Sprint | Focus | Epics |
|---|---|---|
| Sprint 1 | Foundation | Epic 1 (Setup), Epic 2 (Auth & Roles) |
| Sprint 2 | Car Management | Epic 3 (Cars) |
| Sprint 3 | Orders | Epic 4 (Orders), Epic 5 (Users & KYC) |
| Sprint 4 | Admin Completion | Epic 6 (Blog), Epic 7 (Settings), Epic 8 (Widgets) |
| Sprint 5 | Public Frontend | Epic 9 (Homepage), Epic 10 (Catalogue), Epic 11 (Car Detail) |
| Sprint 6 | Public Frontend | Epic 12 (Blog), Epic 13 (Static Pages), Epic 14 (SEO) |
| Sprint 7 | Customer Auth | Epic 15 (Customer Auth & KYC) |
| Sprint 8 | Customer Dashboard | Epic 16, 17, 18 (Dashboard, Orders, Payment Proof) |
| Sprint 9 | Customer Dashboard | Epic 19, 20, 21 (Tracking, Profile, Notifications) |
| Sprint 10 | QA & Launch | Testing, bug fixes, performance, deployment |

---

## PART A — Admin Panel (FilamentPHP)

> The admin panel is built entirely with FilamentPHP. All resources, pages, and widgets live under the `/admin` route. Only authenticated users with admin roles can access it.

---

### Epic 1 — Project Setup & Base Configuration

**Goal:** Initialise the Laravel project with all required packages, database, and folder structure so all subsequent epics build on a solid, consistent foundation.

---

#### US-01 · Laravel Project Initialisation
**As a** developer
**I want** a clean Laravel 13 project with all core packages installed
**So that** development can begin consistently across the team

**Priority:** Must Have | **Points:** 3 | **Sprint:** 1

**Acceptance Criteria:**
- [x] Laravel 13 project created and committed to version control
- [x] `.env.example` configured with all required keys (DB, mail, SMS, S3, Redis)
- [x] MySQL database created and connected
- [x] Tailwind CSS configured via Vite
- [x] AlpineJS installed and available globally
- [x] Livewire 4 installed and configured
- [x] FilamentPHP installed and accessible at `/admin`
- [x] FilamentShield installed and configured
- [x] Spatie Laravel Permission installed
- [x] Laravel Horizon installed and configured
- [x] Artesaos SEOTools installed
- [x] `develop` and `main` branches created in Git

**Tasks:**
- [x] `T-01-1` Run `laravel new livingston-autos`
- [x] `T-01-2` Install and configure Tailwind CSS + Vite
- [x] `T-01-3` Install Livewire 4 (`composer require livewire/livewire`)
- [x] `T-01-4` Install FilamentPHP (`composer require filament/filament`)
- [x] `T-01-5` Install FilamentShield (`composer require bezhansalleh/filament-shield`)
- [x] `T-01-6` Install Spatie Permission (`composer require spatie/laravel-permission`)
- [x] `T-01-7` Install Laravel Horizon (`composer require laravel/horizon`)
- [x] `T-01-8` Install SEOTools (`composer require artesaos/seotools`)
- [x] `T-01-9` Configure `.env.example` and `.env` locally
- [x] `T-01-10` Run initial migrations, commit scaffold

---

#### US-02 · Database Schema — Core Tables
**As a** developer
**I want** all core database tables created via migrations
**So that** all features have a consistent data structure from day one

**Priority:** Must Have | **Points:** 5 | **Sprint:** 1

**Acceptance Criteria:**
- [x] All migrations run without error on a fresh database
- [x] Foreign key constraints are in place
- [x] Soft deletes enabled on `cars`, `orders`, `users`
- [x] Timestamps on all tables

**Tasks:**
- [x] `T-02-1` Create `users` migration (extend default with KYC fields)
- [x] `T-02-2` Create `cars` migration
- [x] `T-02-3` Create `car_images` migration
- [x] `T-02-4` Create `orders` migration
- [x] `T-02-5` Create `order_status_histories` migration
- [x] `T-02-6` Create `payment_proofs` migration
- [x] `T-02-7` Create `settings` migration (key-value store)
- [x] `T-02-8` Create `blog_posts` migration
- [x] `T-02-9` Create `blog_categories` migration
- [x] `T-02-10` Create `notifications` migration (Laravel default)
- [x] `T-02-11` Run all Spatie Permission migrations
- [x] `T-02-12` Write seeders: roles, permissions, default settings, demo cars

---

### Epic 2 — Authentication & Roles/Permissions

**Goal:** Secure the admin panel with login, 2FA, and a full roles & permissions system so Mr. Seth can control exactly what each staff member can do.

---

#### US-03 · Admin Login & 2FA
**As an** admin user
**I want** to log in securely with 2FA
**So that** only authorised people can access the admin panel

**Priority:** Must Have | **Points:** 3 | **Sprint:** 1

**Acceptance Criteria:**
- [x] Admin login page at `/admin/login`
- [x] Incorrect credentials show a clear error message
- [x] 2FA is enforced for all admin accounts (TOTP via Google Authenticator)
- [x] 2FA setup prompt shown on first login
- [x] Admin session expires after 60 minutes of inactivity
- [x] Brute-force protection: account locked after 5 failed attempts
- [x] Password reset via email works correctly

**Tasks:**
- [x] `T-03-1` Configure Fortify for admin authentication
- [x] `T-03-2` Enable TOTP 2FA via Fortify
- [x] `T-03-3` Customise Filament login page with Livingston Autos branding
- [x] `T-03-4` Configure session timeout (60 min)
- [x] `T-03-5` Add rate limiting to login route
- [x] `T-03-6` Test password reset flow end-to-end

---

#### US-04 · Roles & Permissions Management
**As a** Super Admin
**I want** to create roles, assign permissions, and manage staff access
**So that** each staff member only has access to what they need

**Priority:** Must Have | **Points:** 5 | **Sprint:** 1

**Acceptance Criteria:**
- [x] Two default roles exist on fresh install: `super_admin` and `staff_admin`
- [x] Super Admin can create new roles from the admin panel
- [x] Super Admin can assign/revoke permissions per role
- [x] Super Admin can assign roles to admin users
- [x] Staff Admin cannot access the Roles & Permissions section
- [x] All Filament resources respect permission gates (view, create, edit, delete)
- [x] Permission changes take effect immediately without re-login

**Default Permission Matrix:**

| Permission | Super Admin | Staff Admin |
|---|---|---|
| Manage Cars (CRUD) | ✅ | Configurable |
| Manage Orders | ✅ | ✅ |
| Confirm Payments | ✅ | Configurable |
| Manage Users / KYC | ✅ | Configurable |
| Manage Blog | ✅ | Configurable |
| Update Exchange Rate | ✅ | Configurable |
| Manage Roles & Permissions | ✅ | ❌ |
| View Dashboard Widgets | ✅ | ✅ |
| System Settings | ✅ | ❌ |

**Tasks:**
- [x] `T-04-1` Run FilamentShield setup command to generate policies
- [x] `T-04-2` Define all permissions in `ShieldSeeder`
- [x] `T-04-3` Seed default `super_admin` and `staff_admin` roles
- [x] `T-04-4` Apply `->authorize()` gates to all Filament resources
- [x] `T-04-5` Build Roles management page in FilamentShield
- [x] `T-04-6` Build Admin Users resource (create/edit staff accounts)
- [x] `T-04-7` Test permission matrix for both roles

---

### Epic 3 — Car Management

**Goal:** Give admin the ability to fully manage the car inventory — from adding new listings with photos to updating availability status — powering the public catalogue.

---

#### US-05 · Create a Car Listing
**As an** admin
**I want** to add a new car to the system with all its details and photos
**So that** it appears immediately on the public catalogue

**Priority:** Must Have | **Points:** 5 | **Sprint:** 2

**Acceptance Criteria:**
- [x] Admin can fill in all required car fields (see SRS Section 5.1.1)
- [x] At least 3 photos must be uploaded before a listing can be published
- [x] Photos can be reordered via drag-and-drop
- [x] Price is entered in USD; GHS equivalent is shown in a read-only preview using the current exchange rate
- [x] Availability status defaults to `Available` on creation
- [x] Listing appears on the public catalogue immediately upon save
- [x] Validation errors are shown inline per field
- [x] Admin receives a success notification on save

**Tasks:**
- [x] `T-05-1` Create `Car` model with all fillable fields and `SoftDeletes`
- [x] `T-05-2` Create `CarImage` model and relationship (`Car` hasMany `CarImage`)
- [x] `T-05-3` Build Filament `CarResource` with full form schema
- [x] `T-05-4` Add `SpatieMediaLibraryFileUpload` or custom multi-image uploader
- [x] `T-05-5` Add drag-and-drop image reordering (Filament `SortableList` or custom)
- [x] `T-05-6` Add GHS preview field (computed from price × exchange rate)
- [x] `T-05-7` Add all form validations (required fields, min photos)
- [ ] `T-05-8` Configure image storage to S3 public bucket (car photos are public)
- [x] `T-05-9` Write `CarFactory` and seeders for demo data

---

#### US-06 · Edit & Update a Car Listing
**As an** admin
**I want** to edit any field of an existing car listing
**So that** I can correct details or update the price

**Priority:** Must Have | **Points:** 3 | **Sprint:** 2

**Acceptance Criteria:**
- [x] All fields from the create form are editable
- [x] Photos can be added, removed, or reordered
- [x] Changes reflect immediately on the public catalogue
- [x] An edit log entry is written to `activity_log` (Spatie Activity Log)
- [x] GHS price preview updates live when USD price is changed

**Tasks:**
- [x] `T-06-1` Reuse `CarResource` form for edit (Filament handles this by default)
- [x] `T-06-2` Install and configure `spatie/laravel-activitylog`
- [x] `T-06-3` Add `LogsActivity` trait to `Car` model
- [x] `T-06-4` Verify live GHS preview on price change (AlpineJS reactivity)

---

#### US-07 · Manage Car Availability Status
**As an** admin
**I want** to change a car's status between Available, Reserved, and Sold
**So that** the catalogue always reflects real inventory

**Priority:** Must Have | **Points:** 2 | **Sprint:** 2

**Acceptance Criteria:**
- [x] Admin can toggle status from the car list table (inline action)
- [x] Admin can toggle status from the car detail/edit page
- [x] Status changes are reflected immediately on the public catalogue
- [x] When marked `Sold`, a `sold_at` timestamp is recorded
- [x] When marked `Available`, `sold_at` is cleared
- [x] Status badge is colour-coded in the admin table: green (Available), amber (Reserved), red (Sold)

**Tasks:**
- [x] `T-07-1` Add `status` and `sold_at` columns to `cars` table
- [x] `T-07-2` Create `CarStatus` enum: `Available`, `Reserved`, `Sold`
- [x] `T-07-3` Add `ToggleStatus` table action in `CarResource`
- [x] `T-07-4` Add colour-coded `BadgeColumn` for status in table

---

#### US-08 · Auto-Archive Sold Cars
**As an** admin
**I want** sold cars to automatically disappear from the catalogue after 7 days
**So that** I don't have to manually remove them

**Priority:** Must Have | **Points:** 3 | **Sprint:** 2

**Acceptance Criteria:**
- [x] Cars with status `Sold` and `sold_at` older than 7 days are automatically archived (soft deleted)
- [x] Archived cars do NOT appear on the public catalogue
- [x] Archived cars ARE visible in the admin panel under an `Archived` filter tab
- [x] Admin can manually restore an archived car
- [x] The scheduler runs daily at midnight Ghana time (GMT+0)

**Tasks:**
- [x] `T-08-1` Create `ArchiveSoldCars` artisan command
- [x] `T-08-2` Register command in `Console/Kernel.php` to run daily
- [x] `T-08-3` Add `Archived` filter tab to `CarResource` table (query scope on `trashed()`)
- [x] `T-08-4` Add `Restore` action on archived cars in admin
- [x] `T-08-5` Write unit test for archive command logic

---

#### US-09 · Car Listing Table — Admin View
**As an** admin
**I want** a clean, filterable table of all cars
**So that** I can quickly find and manage any listing

**Priority:** Must Have | **Points:** 2 | **Sprint:** 2

**Acceptance Criteria:**
- [x] Table shows: thumbnail, make, model, year, price (USD), status, date added
- [x] Table is searchable by make, model, colour
- [x] Table is filterable by: status, make, fuel type, transmission, country of origin
- [x] Table is sortable by: price, year, date added
- [x] Bulk actions available: Delete, Change Status
- [x] Pagination: 15 rows per page default

**Tasks:**
- [x] `T-09-1` Define all table columns in `CarResource::table()`
- [x] `T-09-2` Add `SelectFilter` for status, make, fuel type, etc.
- [x] `T-09-3` Add `TextInputFilter` for keyword search
- [x] `T-09-4` Add bulk actions
- [x] `T-09-5` Add image thumbnail using `ImageColumn`

---

### Epic 4 — Order Management

**Goal:** Give admin full visibility and control over all customer orders — from reviewing payment proofs to updating shipment stages through the full 9-step pipeline.

---

#### US-10 · View All Orders
**As an** admin
**I want** to see all orders in a filterable table
**So that** I can manage them efficiently

**Priority:** Must Have | **Points:** 3 | **Sprint:** 3

**Acceptance Criteria:**
- [x] Table shows: order ID, customer name, car (make + model), order status, payment status, date placed, date updated
- [x] Table is filterable by: order status, payment status, date range
- [x] Table is searchable by: customer name, car name, order ID
- [x] Table is sortable by: date placed, status
- [x] Colour-coded status badges
- [x] Orders requiring action (e.g. `Payment Uploaded`) are visually highlighted
- [x] Count of "action required" orders shown in admin navigation badge

**Tasks:**
- [x] `T-10-1` Create `Order` model with all relationships
- [x] `T-10-2` Build `OrderResource` table in Filament
- [x] `T-10-3` Add status filters and search
- [x] `T-10-4` Add navigation badge for pending action orders
- [x] `T-10-5` Highlight `Payment Uploaded` rows with a visual indicator

---

#### US-11 · View Order Detail
**As an** admin
**I want** to view the full detail of an order on one screen
**So that** I have all the context I need to act on it

**Priority:** Must Have | **Points:** 5 | **Sprint:** 3

**Acceptance Criteria:**
- [x] Order detail page shows: customer info, KYC summary, car details, order status, full status history timeline, all uploaded payment proofs
- [x] Payment proofs are displayed inline (images shown, PDFs with a preview/download link)
- [x] A visual shipment stage timeline shows all 9 stages with timestamps for completed stages
- [x] Internal admin notes are visible on this page
- [x] All available actions for the current status are shown as buttons

**Tasks:**
- [x] `T-11-1` Build custom Filament `ViewOrder` page (or use `Infolist`)
- [x] `T-11-2` Build visual shipment timeline as a custom Filament `Infolist` component
- [x] `T-11-3` Build payment proof viewer (inline image + PDF download)
- [x] `T-11-4` Build order status history section (all past statuses with timestamps)
- [x] `T-11-5` Add internal admin notes section (textarea + save)

---

#### US-12 · Confirm or Reject Payment
**As an** admin
**I want** to confirm or reject a customer's uploaded payment proof
**So that** the order can proceed or the customer is informed to re-submit

**Priority:** Must Have | **Points:** 3 | **Sprint:** 3

**Acceptance Criteria:**
- [x] `Confirm Payment` button is visible when order status is `Payment Uploaded`
- [x] `Reject Payment` button is visible when order status is `Payment Uploaded`
- [ ] Confirming payment: status moves to `Payment Confirmed`, car status moves to `Reserved`, customer receives email + SMS
- [ ] Rejecting payment: admin must enter a reason; status moves back to `Pending Payment`, customer receives email + SMS with reason
- [x] Action requires confirmation modal before executing
- [x] Both actions are logged in `order_status_histories`

**Tasks:**
- [x] `T-12-1` Create `ConfirmPayment` Filament action
- [x] `T-12-2` Create `RejectPayment` Filament action with reason input
- [x] `T-12-3` Wire both actions to update `orders.status` and `cars.status`
- [x] `T-12-4` Dispatch `PaymentConfirmed` / `PaymentRejected` events
- [ ] `T-12-5` Write listeners to send email + SMS notifications
- [x] `T-12-6` Log status change to `order_status_histories`

---

#### US-13 · Update Shipment Stage
**As an** admin
**I want** to update the shipment stage of an order as the car progresses
**So that** the customer sees live tracking on their dashboard

**Priority:** Must Have | **Points:** 3 | **Sprint:** 3

**Acceptance Criteria:**
- [x] Admin can advance the order to the next stage using an `Update Stage` action
- [x] Stages must advance in order — cannot skip stages
- [x] Each stage update records a timestamp in `order_status_histories`
- [ ] Customer receives email + SMS on each stage change
- [x] For the `Shipped` stage, admin must enter an estimated arrival date
- [x] For the `Delivered` stage, a confirmation modal is shown; this triggers the 7-day sold countdown

**Order Stage Sequence:**
```
Pending Payment
  → Payment Uploaded
    → Payment Confirmed
      → Purchased
        → In Transit to Port
          → Shipped  [requires estimated arrival date]
            → Arrived in Ghana
              → Cleared
                → Delivered  [triggers sold_at timestamp on Car]
```

**Tasks:**
- [x] `T-13-1` Create `UpdateShipmentStage` Filament action
- [x] `T-13-2` Enforce sequential stage progression in `OrderService`
- [x] `T-13-3` Add `estimated_arrival_date` field on `Shipped` action modal
- [x] `T-13-4` Trigger `Car::markSold()` method on `Delivered` stage
- [x] `T-13-5` Dispatch stage-change events and wire notification listeners
- [x] `T-13-6` Write unit tests for stage progression rules

---

#### US-14 · Add Internal Admin Notes to Orders
**As an** admin
**I want** to add private notes to any order
**So that** I can record context that only staff can see

**Priority:** Should Have | **Points:** 2 | **Sprint:** 3

**Acceptance Criteria:**
- [x] Notes input is visible on the order detail page
- [x] Multiple notes can be added over time (append, not overwrite)
- [x] Each note shows the author (admin name) and timestamp
- [x] Notes are never visible to customers

**Tasks:**
- [x] `T-14-1` Create `order_notes` table (order_id, admin_id, body, created_at)
- [x] `T-14-2` Build notes section as a Filament `Repeater` or custom component
- [x] `T-14-3` Ensure notes are excluded from all customer-facing queries

---

### Epic 5 — User & KYC Management

**Goal:** Give admin visibility over all registered customers and their KYC documents so they can verify identities and manage accounts.

---

#### US-15 · View & Search Customers
**As an** admin
**I want** to view a list of all registered customers
**So that** I can find and manage any customer account

**Priority:** Must Have | **Points:** 2 | **Sprint:** 3

**Acceptance Criteria:**
- [x] Table shows: name, email, phone, registration date, KYC status, number of orders
- [x] Searchable by name, email, phone
- [x] Filterable by KYC status (Verified / Pending / Incomplete)
- [x] Clicking a customer opens their detail view

**Tasks:**
- [x] `T-15-1` Build `CustomerResource` in Filament
- [x] `T-15-2` Add KYC status column and filter
- [x] `T-15-3` Link to customer detail page

---

#### US-16 · Review Customer KYC Documents
**As an** admin
**I want** to review a customer's uploaded Ghana Card and TIN documents
**So that** I can verify their identity before clearing their car

**Priority:** Must Have | **Points:** 3 | **Sprint:** 3

**Acceptance Criteria:**
- [x] Customer detail page shows all profile fields and KYC document uploads
- [x] Ghana Card image / PDF can be previewed inline
- [x] TIN document can be previewed inline
- [x] Admin can mark KYC as `Verified` or `Needs Resubmission`
- [ ] If `Needs Resubmission`, admin enters a reason; customer receives email + SMS notification
- [x] KYC status is shown on the customer list table

**Tasks:**
- [x] `T-16-1` Add `kyc_status` and `kyc_notes` to `users` table
- [x] `T-16-2` Build KYC document viewer in customer detail page
- [x] `T-16-3` Add `VerifyKYC` and `RequestResubmission` Filament actions
- [ ] `T-16-4` Wire KYC resubmission notification (email + SMS)

---

### Epic 6 — Blog Management

**Goal:** Give admin a full-featured blog editor to publish content that builds SEO authority and positions Livingston Autos as the go-to resource for car import information in Ghana.

---

#### US-17 · Create & Publish Blog Posts
**As an** admin
**I want** to write and publish blog posts with rich text and images
**So that** I can drive organic traffic to the site

**Priority:** Must Have | **Points:** 3 | **Sprint:** 4

**Acceptance Criteria:**
- [ ] Admin can create a blog post with: title, body (rich text), featured image, category, tags, status (Draft / Published), published date
- [ ] Rich text editor supports: headings, bold, italic, bullet lists, numbered lists, links, inline images, blockquotes
- [ ] SEO fields available per post: meta title, meta description, Open Graph image
- [ ] Slug auto-generated from title but editable
- [ ] Draft posts are not visible on the public blog
- [ ] Scheduled publishing: admin can set a future publish date
- [ ] Published posts appear immediately (or at scheduled time) on the public blog

**Tasks:**
- [ ] `T-17-1` Create `BlogPost` model with all fields
- [ ] `T-17-2` Create `BlogCategory` model with relationship
- [ ] `T-17-3` Build `BlogPostResource` in Filament
- [ ] `T-17-4` Integrate TipTap rich text editor (Filament default)
- [ ] `T-17-5` Add SEO fields section to blog post form
- [ ] `T-17-6` Add slug auto-generation with AlpineJS (live from title)
- [ ] `T-17-7` Add scheduled publishing logic (check `published_at` <= now)

---

#### US-18 · Manage Blog Categories
**As an** admin
**I want** to create and manage blog categories
**So that** posts are organised and the blog is easy to navigate

**Priority:** Should Have | **Points:** 1 | **Sprint:** 4

**Acceptance Criteria:**
- [ ] Admin can create, edit, and delete categories
- [ ] Categories have: name, slug, description
- [ ] Deleting a category does not delete associated posts (posts become uncategorised)

**Tasks:**
- [ ] `T-18-1` Build `BlogCategoryResource` in Filament
- [ ] `T-18-2` Handle category deletion gracefully (null out post category_id)

---

### Epic 7 — System Settings

**Goal:** Give admin a central settings panel to manage the exchange rate, payment details, and system-wide configuration without needing a developer.

---

#### US-19 · Manage USD/GHS Exchange Rate
**As an** authorised admin
**I want** to update the USD to GHS exchange rate
**So that** all prices and cost summaries are always accurate

**Priority:** Must Have | **Points:** 2 | **Sprint:** 4

**Acceptance Criteria:**
- [ ] Exchange rate setting is accessible in the admin Settings page
- [ ] Only users with the `update_exchange_rate` permission can change it
- [ ] Changing the rate immediately updates all GHS prices shown on the public catalogue and customer dashboard
- [ ] Previous rate and timestamp of last update are shown for reference
- [ ] Change is logged in `activity_log` with the admin's name and both old/new values

**Tasks:**
- [ ] `T-19-1` Create `settings` table with key-value structure (or use `spatie/laravel-settings`)
- [ ] `T-19-2` Build Settings page in Filament
- [ ] `T-19-3` Add permission gate for exchange rate field
- [ ] `T-19-4` Cache exchange rate and bust cache on update
- [ ] `T-19-5` Log exchange rate changes via activity log

---

#### US-20 · Manage Payment Details
**As an** admin
**I want** to update the bank account and MoMo number shown to customers
**So that** payments always go to the correct account

**Priority:** Must Have | **Points:** 2 | **Sprint:** 4

**Acceptance Criteria:**
- [ ] Settings page includes: bank name, account name, account number, MoMo number, MoMo name
- [ ] Only Super Admin can update payment details
- [ ] Updated details immediately reflect on customer order pages and the Payment Info public page
- [ ] Changes are logged in `activity_log`

**Tasks:**
- [ ] `T-20-1` Add payment details keys to settings table seeder
- [ ] `T-20-2` Add payment details fields to Filament Settings page
- [ ] `T-20-3` Apply Super Admin only gate to these fields

---

#### US-21 · Manage Demurrage Warning Message
**As an** admin
**I want** to edit the demurrage/clearing delay warning message
**So that** I can keep customers properly informed

**Priority:** Should Have | **Points:** 1 | **Sprint:** 4

**Acceptance Criteria:**
- [ ] Admin can edit the demurrage warning text shown on order pages and the calculator (Phase 2)
- [ ] Changes reflect immediately without a deployment

**Tasks:**
- [ ] `T-21-1` Add `demurrage_warning` key to settings table
- [ ] `T-21-2` Add textarea field to Filament Settings page

---

### Epic 8 — Admin Dashboard Widgets

**Goal:** Give admin a clear, at-a-glance overview of the business when they first log into the panel.

---

#### US-22 · Admin Dashboard Overview
**As an** admin
**I want** to see key business metrics on my dashboard home screen
**So that** I know what needs my attention without navigating through menus

**Priority:** Should Have | **Points:** 3 | **Sprint:** 4

**Acceptance Criteria:**
- [ ] Dashboard shows stat cards: Total Available Cars, Total Reserved Cars, Total Sold Cars (all time)
- [ ] Dashboard shows: Orders Requiring Action (Payment Uploaded — awaiting confirmation)
- [ ] Dashboard shows: Orders by Stage (count per shipment stage)
- [ ] Dashboard shows: Recently Added Cars (last 5, with thumbnail and status)
- [ ] Dashboard shows: Recent Orders (last 10, with customer, car, status)
- [ ] All stats respect the logged-in user's permissions (staff see only what they manage)

**Tasks:**
- [ ] `T-22-1` Build `CarStatsWidget` (available / reserved / sold counts)
- [ ] `T-22-2` Build `ActionRequiredWidget` (orders pending payment confirmation)
- [ ] `T-22-3` Build `OrdersByStageWidget` (counts per stage)
- [ ] `T-22-4` Build `RecentOrdersTable` widget
- [ ] `T-22-5` Build `RecentCarsTable` widget
- [ ] `T-22-6` Arrange widgets in a responsive grid layout

---

## PART B — Public Frontend

> The public-facing website is built with Blade, Livewire, AlpineJS, and Tailwind CSS. It is fully responsive, SEO-optimised, and accessible to guests without registration.

---

### Epic 9 — Homepage

**Goal:** Create a compelling, conversion-focused homepage that immediately communicates the Livingston Autos brand, shows available cars, and prompts visitors to explore or get in touch.

---

#### US-23 · Homepage Layout & Hero
**As a** visitor
**I want** to land on a professional, visually strong homepage
**So that** I immediately understand what Livingston Autos offers and feel confident

**Priority:** Must Have | **Points:** 5 | **Sprint:** 5

**Acceptance Criteria:**
- [ ] Hero section: brand logo, headline, sub-headline, CTA button (`Browse Cars`)
- [ ] Hero background is a high-quality car image (admin-uploadable or hardcoded initially)
- [ ] Navigation: Logo, Browse Cars, Blog, About Us, Contact — with WhatsApp icon
- [ ] Navigation is sticky on scroll
- [ ] Navigation collapses to hamburger menu on mobile
- [ ] Page is fully responsive at 375px, 768px, 1280px

**Tasks:**
- [ ] `T-23-1` Create base Blade layout (`layouts/app.blade.php`) with Tailwind
- [ ] `T-23-2` Build sticky navigation component
- [ ] `T-23-3` Build mobile hamburger menu with AlpineJS `x-show` toggle
- [ ] `T-23-4` Build hero section component

---

#### US-24 · Homepage — Featured Cars
**As a** visitor
**I want** to see a selection of available cars on the homepage
**So that** I can immediately browse without going to the full catalogue

**Priority:** Must Have | **Points:** 3 | **Sprint:** 5

**Acceptance Criteria:**
- [ ] Homepage shows the 6 most recently added `Available` cars as cards
- [ ] Each card shows: primary photo, make, model, year, price (USD + GHS), status badge
- [ ] Cards link to the car detail page
- [ ] A `View All Cars` button links to the full catalogue
- [ ] Cards are displayed in a responsive 3-column grid (desktop), 2-column (tablet), 1-column (mobile)

**Tasks:**
- [ ] `T-24-1` Create `CarCard` Blade component
- [ ] `T-24-2` Build featured cars section (query 6 latest available)
- [ ] `T-24-3` Build responsive grid layout with Tailwind

---

#### US-25 · Homepage — Trust & Process Section
**As a** visitor
**I want** to understand how the buying process works
**So that** I feel confident enough to enquire or place an order

**Priority:** Must Have | **Points:** 2 | **Sprint:** 5

**Acceptance Criteria:**
- [ ] Section titled `How It Works` or similar
- [ ] Shows 4–5 steps: Browse → Order → Pay → Track → Receive
- [ ] Each step has an icon, short title, and 1–2 sentence description
- [ ] Section is visually distinct from the car listing area

**Tasks:**
- [ ] `T-25-1` Build `HowItWorks` Blade component
- [ ] `T-25-2` Design with Tailwind using brand colours

---

#### US-26 · Homepage — WhatsApp Floating Button
**As a** visitor
**I want** a WhatsApp button always visible
**So that** I can contact the business at any point

**Priority:** Must Have | **Points:** 1 | **Sprint:** 5

**Acceptance Criteria:**
- [ ] Floating WhatsApp button visible on all pages (fixed bottom-right)
- [ ] Links to `https://wa.me/{business_number}` with a default message: `Hello, I am interested in your cars.`
- [ ] Button is accessible (aria-label set)
- [ ] Button does not obscure important content on mobile

**Tasks:**
- [ ] `T-26-1` Build `WhatsAppButton` Blade component included in `layouts/app.blade.php`
- [ ] `T-26-2` Load WhatsApp number from settings

---

### Epic 10 — Car Catalogue

**Goal:** A fast, filterable catalogue page where visitors can browse all available cars and find exactly what they're looking for.

---

#### US-27 · Browse All Cars
**As a** visitor
**I want** to browse all available cars with filtering and search
**So that** I can find a car that matches my needs and budget

**Priority:** Must Have | **Points:** 5 | **Sprint:** 5

**Acceptance Criteria:**
- [ ] Catalogue shows all `Available` and `Reserved` cars (with badges)
- [ ] `Sold` cars are shown with a SOLD badge while within 7-day window
- [ ] Each car card shows: primary photo, make, model, year, price (USD + GHS), fuel type, transmission, status badge
- [ ] Filter panel (sidebar on desktop, collapsible drawer on mobile): Make, Price Range (GHS), Year Range, Fuel Type, Transmission, Country of Origin
- [ ] Search bar: filters results live as user types (Livewire)
- [ ] Sort options: Newest First, Price Low→High, Price High→Low
- [ ] Pagination: 12 cars per page
- [ ] Filters persist in URL query string (shareable links)
- [ ] Empty state shown if no results match filters

**Tasks:**
- [ ] `T-27-1` Build `CarCatalogue` Livewire component
- [ ] `T-27-2` Implement filter logic in Livewire `updatedX()` methods
- [ ] `T-27-3` Implement search with `LIKE` query
- [ ] `T-27-4` Implement sort logic
- [ ] `T-27-5` Persist filters to URL query string using `#[Url]` attribute
- [ ] `T-27-6` Build mobile filter drawer with AlpineJS
- [ ] `T-27-7` Build empty state component
- [ ] `T-27-8` Implement pagination

---

### Epic 11 — Car Detail Page

**Goal:** A rich, persuasive single car page that gives the visitor everything they need to make a buying decision — specs, photos, cost summary, and a clear call to action.

---

#### US-28 · View Car Details
**As a** visitor
**I want** to see the full details of a car on its own page
**So that** I can evaluate whether it's the right car for me

**Priority:** Must Have | **Points:** 5 | **Sprint:** 5

**Acceptance Criteria:**
- [ ] Page shows: all car attributes, photo gallery, status badge, price in USD and GHS
- [ ] Photo gallery: main image with thumbnail strip, click to enlarge (lightbox), swipeable on mobile
- [ ] Special features shown as a tag/badge list
- [ ] Basic cost summary section: Car Price + Shipping = Total Before Clearing (both USD and GHS)
- [ ] Demurrage warning shown below cost summary
- [ ] `Order This Car` CTA button — visible only if car status is `Available`
- [ ] `Reserved` and `Sold` cars show a status message instead of the CTA
- [ ] WhatsApp button with pre-filled message: `I am interested in the [Year] [Make] [Model] listed on Livingston Autos.`
- [ ] SEO: unique meta title and description per car (Make + Model + Year + price)
- [ ] Page URL is SEO-friendly: `/cars/{year}-{make}-{model}-{id}`

**Tasks:**
- [ ] `T-28-1` Build `CarDetail` Blade view
- [ ] `T-28-2` Build photo gallery with lightbox (use `GLightbox` or similar JS lib from CDN)
- [ ] `T-28-3` Add touch/swipe support for mobile gallery
- [ ] `T-28-4` Build basic cost summary component
- [ ] `T-28-5` Build demurrage warning component (text from settings)
- [ ] `T-28-6` Add pre-filled WhatsApp deeplink
- [ ] `T-28-7` Generate SEO-friendly slug for car URL
- [ ] `T-28-8` Set per-car meta title and description via SEOTools

---

### Epic 12 — Blog (Public)

**Goal:** A clean, readable public blog that ranks for Ghana car import search terms, drives organic traffic, and builds trust.

---

#### US-29 · Browse Blog Posts
**As a** visitor
**I want** to browse blog articles about car imports
**So that** I can learn and feel confident about the process

**Priority:** Must Have | **Points:** 3 | **Sprint:** 6

**Acceptance Criteria:**
- [ ] Blog listing page at `/blog`
- [ ] Shows all published posts ordered by most recent
- [ ] Each card shows: featured image, title, excerpt (first 150 chars), category, publish date, read time estimate
- [ ] Filterable by category
- [ ] Pagination: 9 posts per page

**Tasks:**
- [ ] `T-29-1` Build `BlogIndex` Blade view
- [ ] `T-29-2` Build `BlogCard` component
- [ ] `T-29-3` Add category filter (Livewire)
- [ ] `T-29-4` Add read time estimate helper (`word_count / 200` minutes)

---

#### US-30 · Read a Blog Post
**As a** visitor
**I want** to read a full blog article
**So that** I can get valuable information about car imports

**Priority:** Must Have | **Points:** 2 | **Sprint:** 6

**Acceptance Criteria:**
- [ ] Individual post page at `/blog/{slug}`
- [ ] Shows: featured image, title, author, date, category, body content, tags
- [ ] Body renders rich text (headings, lists, images, links) correctly
- [ ] SEO: unique meta title, description, and Open Graph image per post
- [ ] Related posts section at the bottom (same category, max 3)
- [ ] Social share buttons: WhatsApp, Twitter/X, Facebook, copy link

**Tasks:**
- [ ] `T-30-1` Build `BlogPost` Blade view
- [ ] `T-30-2` Render TipTap HTML content safely (`{!! $post->body !!}` with sanitisation)
- [ ] `T-30-3` Build related posts query (same category, exclude current)
- [ ] `T-30-4` Add social share buttons component
- [ ] `T-30-5` Set per-post Open Graph meta via SEOTools

---

### Epic 13 — Static Pages

**Goal:** Build the supporting pages that complete the site experience and provide customers with all the information they need to trust and contact the business.

---

#### US-31 · About Us Page
**As a** visitor
**I want** to learn about Livingston Autos
**So that** I can trust the business before buying

**Priority:** Must Have | **Points:** 1 | **Sprint:** 6

**Acceptance Criteria:**
- [ ] Page at `/about`
- [ ] Content: company story, sourcing (Korea + Japan), how long in business
- [ ] Can be updated from the admin settings or hardcoded initially

**Tasks:**
- [ ] `T-31-1` Build `About` Blade view
- [ ] `T-31-2` (Optional) Add editable content fields to admin Settings

---

#### US-32 · Contact Page
**As a** visitor
**I want** to find Livingston Autos' contact details
**So that** I can reach out before or after buying

**Priority:** Must Have | **Points:** 1 | **Sprint:** 6

**Acceptance Criteria:**
- [ ] Page at `/contact`
- [ ] Shows: phone number, email, WhatsApp link, physical address
- [ ] Simple contact enquiry form: name, email, phone, message
- [ ] Form submissions are emailed to Mr. Seth and stored in the database
- [ ] Success message shown after submission

**Tasks:**
- [ ] `T-32-1` Build `Contact` Blade view with Livewire form component
- [ ] `T-32-2` Create `contact_enquiries` table
- [ ] `T-32-3` Send contact form submission email to admin

---

#### US-33 · Payment Information Page
**As a** customer
**I want** a dedicated page with payment instructions
**So that** I know exactly how to pay for my car

**Priority:** Must Have | **Points:** 1 | **Sprint:** 6

**Acceptance Criteria:**
- [ ] Page at `/payment-info`
- [ ] Shows bank account details and MoMo number (from settings)
- [ ] Shows clear step-by-step payment instructions
- [ ] Includes demurrage/clearing warning
- [ ] Content updates automatically when admin changes payment details in settings

**Tasks:**
- [ ] `T-33-1` Build `PaymentInfo` Blade view
- [ ] `T-33-2` Load bank and MoMo details from `settings` table

---

### Epic 14 — SEO & Performance

**Goal:** Ensure the site is discoverable, fast, and well-structured so it ranks for Ghana car import search terms and provides a great user experience.

---

#### US-34 · SEO Foundations
**As a** site owner
**I want** the site to be properly structured for search engines
**So that** customers can find Livingston Autos on Google

**Priority:** Must Have | **Points:** 3 | **Sprint:** 6

**Acceptance Criteria:**
- [ ] All pages have unique meta title and description
- [ ] Open Graph tags set on all public pages (title, description, image, URL)
- [ ] `sitemap.xml` generated and updated automatically (include: cars, blog posts, static pages)
- [ ] `robots.txt` configured (block `/admin`, allow everything else)
- [ ] Canonical URLs set on all pages
- [ ] Structured data (JSON-LD) on car detail pages (`Product` schema with price)
- [ ] All images have descriptive `alt` attributes
- [ ] H1 tag on every page, logical heading hierarchy (H1 → H2 → H3)
- [ ] Page URLs are lowercase, hyphenated, no special characters

**Tasks:**
- [ ] `T-34-1` Configure SEOTools defaults in `seotools.php` config
- [ ] `T-34-2` Set per-page meta in all controllers/Livewire components
- [ ] `T-34-3` Generate `sitemap.xml` using `spatie/laravel-sitemap`
- [ ] `T-34-4` Write `robots.txt`
- [ ] `T-34-5` Add JSON-LD `Product` schema to car detail page
- [ ] `T-34-6` Audit all images for alt text

---

#### US-35 · Performance Optimisation
**As a** visitor on a Ghanaian mobile network
**I want** pages to load quickly
**So that** I don't give up and leave the site

**Priority:** Must Have | **Points:** 3 | **Sprint:** 6

**Acceptance Criteria:**
- [ ] Car listing images served in WebP format with lazy loading
- [ ] Images resized to appropriate dimensions on upload (max 1200px wide for gallery, 600px for thumbnails) using Laravel's image processing
- [ ] Route caching and config caching enabled on production
- [ ] Blade views cached on production
- [ ] Database queries optimised: no N+1 queries (use `with()` eager loading throughout)
- [ ] Homepage and catalogue load under 3 seconds on simulated 4G

**Tasks:**
- [ ] `T-35-1` Install `intervention/image` for server-side image resizing on upload
- [ ] `T-35-2` Convert uploaded images to WebP on save
- [ ] `T-35-3` Add `loading="lazy"` to all non-above-the-fold images
- [ ] `T-35-4` Add `php artisan optimize` to deployment script
- [ ] `T-35-5` Audit all Eloquent queries with Laravel Debugbar in development
- [ ] `T-35-6` Add eager loading (`with()`) everywhere cars + images are queried

---

## PART C — Customer Dashboard

> The customer dashboard is a fully custom-built interface using Livewire 4, AlpineJS, and Tailwind CSS. It is intentionally separate from FilamentPHP and designed to match the Livingston Autos brand experience.

---

### Epic 15 — Customer Authentication & KYC Registration

**Goal:** Allow customers to create an account with all required KYC information so they can place orders and have their car cleared through Ghana customs.

---

#### US-36 · Customer Registration
**As a** new customer
**I want** to create an account with my personal and KYC details
**So that** I can place an order for a car

**Priority:** Must Have | **Points:** 5 | **Sprint:** 7

**Acceptance Criteria:**
- [ ] Registration form accessible at `/register`
- [ ] Fields: Full Name, Email, Phone Number, Password, Confirm Password
- [ ] After basic registration, customer is taken to a KYC completion step
- [ ] KYC step fields: Residential Address, Ghana Card Number (required if no TIN), TIN (required if no Ghana Card)
- [ ] System validates: at least one of Ghana Card Number or TIN is provided
- [ ] Customer can upload Ghana Card scan (image or PDF, max 5MB)
- [ ] Customer can upload TIN document (image or PDF, max 5MB, optional if Ghana Card uploaded)
- [ ] KYC documents stored in private S3 storage (not publicly accessible by URL)
- [ ] Email verification required before placing a first order
- [ ] KYC step can be skipped and completed later from the dashboard, but a banner reminds the customer

**Tasks:**
- [ ] `T-36-1` Build `Register` Livewire component (step 1: basic info)
- [ ] `T-36-2` Build `CompleteKYC` Livewire component (step 2: KYC info + uploads)
- [ ] `T-36-3` Add GhanaCard + TIN validation rule (at least one required)
- [ ] `T-36-4` Configure private S3 disk for KYC documents
- [ ] `T-36-5` Configure email verification (Laravel Fortify)
- [ ] `T-36-6` Build `KYCReminder` banner component for dashboard
- [ ] `T-36-7` Encrypt Ghana Card and TIN fields at rest (cast with encryption)

---

#### US-37 · Customer Login & Password Reset
**As a** returning customer
**I want** to log in to my account and reset my password if forgotten
**So that** I can access my orders and dashboard

**Priority:** Must Have | **Points:** 2 | **Sprint:** 7

**Acceptance Criteria:**
- [ ] Login form at `/login`
- [ ] Failed login shows clear error (do not reveal if email exists — show generic message)
- [ ] `Remember Me` option available
- [ ] Password reset via email link works correctly
- [ ] After login, customer is redirected to their dashboard
- [ ] After registration + email verification, customer is redirected to dashboard

**Tasks:**
- [ ] `T-37-1` Build `Login` Livewire component with branded styling
- [ ] `T-37-2` Configure Fortify password reset
- [ ] `T-37-3` Set post-login redirect to `/dashboard`

---

### Epic 16 — Customer Dashboard Home

**Goal:** Give customers a clear, branded overview of their account activity the moment they log in.

---

#### US-38 · Dashboard Overview Page
**As a** customer
**I want** to see a summary of my orders and account status when I log in
**So that** I know what's happening with my cars at a glance

**Priority:** Must Have | **Points:** 3 | **Sprint:** 8

**Acceptance Criteria:**
- [ ] Dashboard home at `/dashboard`
- [ ] Welcome message with customer's first name
- [ ] KYC status banner: `Complete` (green) or `Incomplete — action needed` (amber, links to profile)
- [ ] Summary cards: Total Orders, Orders in Progress, Cars Delivered
- [ ] Recent orders section: last 3 orders with status and stage progress indicator
- [ ] `Browse More Cars` CTA button
- [ ] Page is mobile-responsive

**Tasks:**
- [ ] `T-38-1` Build `Dashboard` Livewire page component
- [ ] `T-38-2` Build `KYCStatusBanner` component
- [ ] `T-38-3` Build summary stat cards
- [ ] `T-38-4` Build recent orders mini-list component

---

### Epic 17 — Order Placement

**Goal:** Allow customers to place an order for a car directly from the car detail page, with clear payment instructions shown immediately.

---

#### US-39 · Place an Order
**As a** customer
**I want** to place an order for a car I want to buy
**So that** I can proceed with payment and have the car shipped to me

**Priority:** Must Have | **Points:** 5 | **Sprint:** 8

**Acceptance Criteria:**
- [ ] `Order This Car` button on car detail page redirects guests to login/register
- [ ] Authenticated customers see an order confirmation modal
- [ ] Modal shows: car details, price (USD + GHS), shipping cost, basic total
- [ ] Customer confirms order with a single click
- [ ] Order is created in `Pending Payment` status
- [ ] Car status immediately changes to `Reserved`
- [ ] Customer is redirected to the order detail page
- [ ] Payment instructions (bank account + MoMo) shown prominently on order page
- [ ] Customer receives email + SMS confirmation with payment details
- [ ] If KYC is incomplete, a warning is shown but order can still be placed (KYC must be completed before delivery)

**Tasks:**
- [ ] `T-39-1` Build `PlaceOrder` Livewire component/action (modal)
- [ ] `T-39-2` Create `OrderService::createOrder()` method
- [ ] `T-39-3` Wire order creation to car status update (Reserved)
- [ ] `T-39-4` Dispatch `OrderPlaced` event
- [ ] `T-39-5` Send email + SMS on order placement
- [ ] `T-39-6` Show payment instructions on order detail page
- [ ] `T-39-7` Add KYC incomplete warning to order modal

---

#### US-40 · View All My Orders
**As a** customer
**I want** to see a list of all my orders
**So that** I can track each car I have ordered

**Priority:** Must Have | **Points:** 2 | **Sprint:** 8

**Acceptance Criteria:**
- [ ] Orders page at `/dashboard/orders`
- [ ] Shows all orders with: car photo, make/model, order date, current status, action button
- [ ] Orders sorted by most recent first
- [ ] Status shown with colour-coded badges
- [ ] Each order links to its detail page

**Tasks:**
- [ ] `T-40-1` Build `OrderList` Livewire component
- [ ] `T-40-2` Scope query to authenticated customer only (`where('user_id', auth()->id())`)

---

### Epic 18 — Payment Proof Upload

**Goal:** Allow customers to submit evidence of their offline payment so admin can confirm and advance their order.

---

#### US-41 · Upload Payment Proof
**As a** customer
**I want** to upload my bank transfer receipt or MoMo screenshot
**So that** the admin can confirm my payment and process my order

**Priority:** Must Have | **Points:** 3 | **Sprint:** 8

**Acceptance Criteria:**
- [ ] Upload option visible on order detail page when status is `Pending Payment`
- [ ] Customer can upload image (JPG, PNG) or PDF, max 10MB
- [ ] Customer can add a short note (e.g. transaction reference number)
- [ ] Multiple proofs can be uploaded for the same order
- [ ] After upload, order status changes to `Payment Uploaded`
- [ ] Admin receives email + SMS notification that a proof has been uploaded
- [ ] Uploaded proofs stored in private S3 storage
- [ ] Upload button is hidden once status moves past `Payment Uploaded`

**Tasks:**
- [ ] `T-41-1` Build `UploadPaymentProof` Livewire component
- [ ] `T-41-2` Create `payment_proofs` table (order_id, file_path, note, uploaded_at)
- [ ] `T-41-3` Store proofs in private S3 disk
- [ ] `T-41-4` Update order status to `Payment Uploaded` on first successful upload
- [ ] `T-41-5` Dispatch `PaymentProofUploaded` event → notify admin via email + SMS

---

### Epic 19 — Shipment Tracking

**Goal:** Give customers a clear, real-time visual of exactly where their car is in the shipping and delivery pipeline.

---

#### US-42 · View Shipment Tracking Timeline
**As a** customer
**I want** to see a visual timeline of my car's journey
**So that** I know exactly where it is and what comes next

**Priority:** Must Have | **Points:** 5 | **Sprint:** 9

**Acceptance Criteria:**
- [ ] Order detail page at `/dashboard/orders/{id}` shows a vertical timeline
- [ ] All 9 stages are shown in sequence
- [ ] Completed stages: shown with a filled check icon and the date/time completed
- [ ] Current stage: highlighted with a pulsing/active indicator
- [ ] Upcoming stages: shown greyed out
- [ ] Estimated arrival date shown prominently once car is `Shipped`
- [ ] Clearing fee warning banner shown from `Arrived in Ghana` stage onwards
- [ ] Timeline updates in real-time without page refresh (Livewire polling or push)

**Tasks:**
- [ ] `T-42-1` Build `ShipmentTimeline` Livewire component
- [ ] `T-42-2` Query `order_status_histories` to render completed stages with timestamps
- [ ] `T-42-3` Style timeline with Tailwind (vertical line, icons, states)
- [ ] `T-42-4` Add Livewire polling (`wire:poll.30s`) to refresh timeline
- [ ] `T-42-5` Show `estimated_arrival_date` after `Shipped` stage
- [ ] `T-42-6` Build demurrage warning banner component (shows from `Arrived in Ghana`)

---

### Epic 20 — Profile & KYC Management

**Goal:** Allow customers to view and update their personal information and KYC documents from their dashboard.

---

#### US-43 · Edit Profile
**As a** customer
**I want** to update my profile details
**So that** my information stays current

**Priority:** Must Have | **Points:** 2 | **Sprint:** 9

**Acceptance Criteria:**
- [ ] Profile page at `/dashboard/profile`
- [ ] Customer can edit: Full Name, Phone Number, Residential Address
- [ ] Customer can change their password (current password required)
- [ ] Email address cannot be changed (sensitive — requires separate verification flow)
- [ ] Success message shown on save

**Tasks:**
- [ ] `T-43-1` Build `EditProfile` Livewire component
- [ ] `T-43-2` Build `ChangePassword` Livewire component
- [ ] `T-43-3` Add form validation

---

#### US-44 · Update KYC Documents
**As a** customer
**I want** to upload or replace my KYC documents
**So that** my identity is verified and my car can be cleared

**Priority:** Must Have | **Points:** 3 | **Sprint:** 9

**Acceptance Criteria:**
- [ ] KYC section on profile page shows current document status
- [ ] Customer can upload/replace Ghana Card document
- [ ] Customer can upload/replace TIN document
- [ ] After upload, KYC status resets to `Pending Review`
- [ ] Admin is notified of new KYC submission
- [ ] If KYC was `Needs Resubmission`, the admin's reason is shown so customer knows what to fix

**Tasks:**
- [ ] `T-44-1` Build `KYCDocuments` Livewire component
- [ ] `T-44-2` Show current KYC status and admin feedback if resubmission requested
- [ ] `T-44-3` Handle file replacement (delete old, store new in private S3)
- [ ] `T-44-4` Reset `kyc_status` to `pending` on new upload
- [ ] `T-44-5` Notify admin of new KYC submission

---

### Epic 21 — Notifications

**Goal:** Keep customers informed at every step of their order with timely email and SMS notifications.

---

#### US-45 · Email Notifications
**As a** customer
**I want** to receive email updates on my order
**So that** I never miss an important update

**Priority:** Must Have | **Points:** 5 | **Sprint:** 9

**Acceptance Criteria:**
- [ ] Emails sent for all events in the notification matrix (see SRS Section 5.6)
- [ ] Emails use a branded Livingston Autos HTML template
- [ ] Emails include: order reference, car details, current status, next steps
- [ ] Payment instruction email includes full bank and MoMo details
- [ ] Clearing warning email includes the demurrage warning text
- [ ] All emails have a plain-text fallback

**Notification Events:**
| Event | Template |
|---|---|
| Order Placed | Order confirmation + payment instructions |
| Payment Proof Received | Acknowledgement, we are reviewing |
| Payment Confirmed | Confirmed, car is reserved |
| Car Shipped | Shipped notification + estimated arrival |
| Car Arrived in Ghana | Arrival notice + clearing instructions + demurrage warning |
| Car Delivered | Delivery confirmation + thank you |
| Payment Rejected | Rejection reason + instructions to resubmit |
| KYC Resubmission Requested | Reason + link to profile |

**Tasks:**
- [ ] `T-45-1` Build branded HTML email layout template
- [ ] `T-45-2` Create Mailable class for each notification event (8 mailables)
- [ ] `T-45-3` Create and register Listeners for each event
- [ ] `T-45-4` Queue all mail jobs on the `notifications` queue
- [ ] `T-45-5` Test each email with Mailtrap in development

---

#### US-46 · SMS Notifications
**As a** customer
**I want** to receive SMS updates on my order
**So that** I'm informed even when I'm not checking my email

**Priority:** Must Have | **Points:** 3 | **Sprint:** 9

**Acceptance Criteria:**
- [ ] SMS sent for high-priority events (see notification matrix — SMS column)
- [ ] SMS messages are concise and under 160 characters where possible
- [ ] SMS sending is queued and does not block the main application
- [ ] Failed SMS deliveries are logged and can be retried

**Tasks:**
- [ ] `T-46-1` Create `SmsService` wrapper class for Arkesel / Hubtel API
- [ ] `T-46-2` Create `SendSms` queued job
- [ ] `T-46-3` Add SMS dispatch to each relevant event listener
- [ ] `T-46-4` Add SMS failure logging
- [ ] `T-46-5` Store SMS provider credentials in `.env`

---

## Database Schema Overview

```sql
-- USERS (customers + admin)
users
  id, name, email, phone, password,
  address, ghana_card_number (encrypted), tin_number (encrypted),
  ghana_card_path, tin_path,
  kyc_status (enum: pending|verified|needs_resubmission),
  kyc_notes, email_verified_at,
  deleted_at, timestamps

-- CARS
cars
  id, make, model, year, engine_capacity, transmission,
  fuel_type, mileage, colour, country_of_origin,
  price_usd, shipping_cost_usd, special_features (json),
  status (enum: available|reserved|sold),
  sold_at, deleted_at, timestamps

-- CAR IMAGES
car_images
  id, car_id (FK), path, sort_order, timestamps

-- ORDERS
orders
  id, user_id (FK), car_id (FK),
  status (enum: 9 stages),
  estimated_arrival_date, delivered_at,
  deleted_at, timestamps

-- ORDER STATUS HISTORY
order_status_histories
  id, order_id (FK), status, notes, changed_by (admin_id nullable),
  created_at

-- PAYMENT PROOFS
payment_proofs
  id, order_id (FK), file_path, note, timestamps

-- ORDER NOTES (admin internal)
order_notes
  id, order_id (FK), admin_id (FK), body, timestamps

-- SETTINGS (key-value)
settings
  id, key (unique), value, timestamps

-- BLOG POSTS
blog_posts
  id, title, slug (unique), body (longtext), excerpt,
  featured_image_path, category_id (FK nullable),
  status (enum: draft|published), published_at,
  meta_title, meta_description, og_image_path,
  timestamps

-- BLOG CATEGORIES
blog_categories
  id, name, slug, description, timestamps

-- CONTACT ENQUIRIES
contact_enquiries
  id, name, email, phone, message, timestamps
```

---

## Background Jobs & Scheduler

| Job / Command | Schedule | Purpose |
|---|---|---|
| `ArchiveSoldCars` | Daily 00:00 GMT | Soft-delete cars with `sold_at` > 7 days ago |
| `SendShipmentReminder` | Weekly | Remind customers with cars in `Arrived in Ghana` to clear |
| `PruneNotifications` | Weekly | Clean old database notifications |
| `SendSms` | Queue (immediate) | SMS dispatch — queued to avoid blocking |
| All email Mailables | Queue (immediate) | Queued email dispatch |

---

## Phase 2 — Import Duty Calculator

> Deferred. Not part of Sprint 1–10. Starts after Phase 1 go-live.

**Trigger:** Mr. Seth provides 3–5 sample Auto Tax calculations to validate the GRA formula.

**Scope:**
- Guest-accessible at `/calculator`
- Pre-fills from car detail page
- Inputs: Car Price (USD), Shipping, Year, Engine Capacity
- Outputs: Import Duty, VAT, NHIL, GETFL, ECOWAS Levy, EXIM Levy, Total Clearing Estimate, Grand Total (USD + GHS)
- GRA formula based on CIF value and vehicle age depreciation schedule
- Disclaimer on all estimates
- Results shareable via URL

---

*End of Document — Livingston Autos System Requirements v1.0*
*Prepared by Ohene Adjei · May 2026*
