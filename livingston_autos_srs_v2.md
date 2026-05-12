**LIVINGSTON AUTOS**

**Software Requirements Specification**

Car Import & Sales Web Application

| Project | Livingston Autos Web Application |
| :---- | :---- |
| **Version** | 2.0 — Updated Requirements |
| **Date** | May 2026 |
| **Prepared By** | Ohene Adjei |
| **Client** | Mr. Seth — Livingston Autos |
| **Tech Stack** | Laravel 13, Livewire 4, AlpineJS, Tailwind CSS, FilamentPHP |
| **Status** | Draft v2.0 — Pending Client Sign-off |

# **Revision History**

| Version | Date | Changes | Author |
| :---- | :---- | :---- | :---- |
| 1.0 | 03 May 2026 | Initial requirements extracted from discovery conversation. | Ohene Adjei |
| **2.0** | 08 May 2026 | Business name confirmed (Livingston Autos). All open questions resolved. Calculator moved to Phase 2\. FilamentPHP adopted for admin. Roles & permissions added. Phased delivery plan added. | Ohene Adjei |

# **1\. Executive Summary**

Livingston Autos is a Ghana-based car import business that sources vehicles primarily from South Korea and Japan. This document specifies the requirements for a web application that serves as the company's digital storefront and order management system.

The application enables customers to browse available vehicles, place orders, upload payment proof, and track their shipment — with payments handled offline via bank transfer or Mobile Money. A full import duty and cost calculator will be delivered in Phase 2 as a standalone value-add tool to drive traffic and convert visitors into buyers.

# **2\. Business Context**

## **2.1 Business Model**

* Cars are sourced from South Korea (primary) and Japan (secondary).

* Vehicles are shipped to Ghana — typical lead time is 45 to 60 days.

* Multiple cars may share a single shipping container.

* The container cannot be opened until all customers in that shipment have paid their clearing fees.

* Clearing fees are paid separately by each customer directly to customs/terminal — not through the app.

## **2.2 Payment Flow**

* Car price \+ shipping is paid by customers via bank transfer or Mobile Money (MoMo).

* The app displays bank account details and MoMo number for payment.

* Admin confirms payment manually after reviewing uploaded proof.

* Clearing fees are paid offline. The app warns customers about demurrage and storage penalties for delays.

## **2.3 Key Business Rules**

* Sold cars remain visible on the catalogue with a SOLD badge for 7 days after confirmed delivery, then auto-archive.

* Demurrage and port penalty fees are variable and set by external authorities — not calculated in the app.

* Car prices are displayed in both USD and GHS.

* USD/GHS exchange rate is managed by authorised admin staff.

# **3\. Stakeholders & User Roles**

| Role | User | Description |
| :---- | :---- | :---- |
| **Super Admin** | Mr. Seth | Full platform access including roles & permissions management, all CRUD operations, financial settings. |
| **Staff Admin** | Authorised Staff | Scoped access based on assigned permissions e.g. update exchange rate, manage orders, update shipment status. Cannot manage roles or delete cars. |
| **Customer** | Registered Buyer | Browses cars, places orders, uploads payment proof, tracks shipments, manages their profile and KYC documents. |
| **Guest** | Unauthenticated Visitor | Full access to car catalogue, blog, and contact pages. Prompted to register only when attempting to place an order or save data. |

# **4\. Delivery Phases**

| Phase | Name | Scope |
| :---- | :---- | :---- |
| **Phase 1** | **Core Application** | Car listings, customer registration & KYC, order management, shipment tracking, admin panel (FilamentPHP), roles & permissions, blog, WhatsApp integration, basic cost summary (car \+ shipping in USD & GHS). |
| **Phase 2** | **Import Duty Calculator** | Full in-app Ghana import duty calculator using GRA rate formula. Validated against sample calculations. Guest-accessible. Drives organic SEO traffic and lead generation. |

*⚠ Phase 2 is deferred pending receipt of sample Auto Tax calculations from Mr. Seth to validate the GRA duty formula.*

# **5\. Functional Requirements**

## **5.1 Car Listing & Catalogue**

**5.1.1 Car Attributes**

| Field | Description | Required? |
| :---- | :---- | :---- |
| **Make** | Manufacturer e.g. Toyota, Honda, Hyundai | **Yes** |
| **Model** | e.g. Corolla, Civic, Sonata | **Yes** |
| **Year / Age** | Year of manufacture | **Yes** |
| **Engine Capacity** | e.g. 1800cc, 2000cc | **Yes** |
| **Transmission** | Automatic / Manual | **Yes** |
| **Fuel Type** | Petrol / Diesel / Hybrid | **Yes** |
| **Mileage** | Current odometer reading | **Yes** |
| **Colour** | Exterior colour | **Yes** |
| **Country of Origin** | Korea / Japan | **Yes** |
| **Price (USD)** | Car price in US Dollars | **Yes** |
| **Price (GHS)** | Auto-converted using current exchange rate | **Yes** |
| **Shipping Cost (USD \+ GHS)** | Cost to ship to Ghana, shown in both currencies | **Yes** |
| **Special Features** | e.g. Sunroof, Reverse camera, Leather seats | **Optional** |
| **Photos** | Multiple high-quality images | **Yes (min. 3\)** |
| **Availability Status** | Available / Reserved / Sold | **Yes** |

**5.1.2 Listing Behaviour**

* Prices displayed in both USD and GHS on all listing cards and detail pages.

* GHS price auto-calculated using the current admin-configured exchange rate.

* Available cars shown prominently; Reserved cars shown with a badge; Sold cars shown with SOLD badge.

* Sold cars auto-archive 7 days after delivery confirmation (Laravel Scheduler).

* Filtering by: Make, Model, Year Range, Price Range, Fuel Type, Transmission, Country of Origin.

* Keyword search across make, model, and colour.

## **5.2 Admin Panel — FilamentPHP**

The admin panel is built with FilamentPHP, providing a robust back-office interface for Mr. Seth and authorised staff. FilamentPHP runs on the same Laravel \+ Livewire \+ AlpineJS \+ Tailwind stack as the main application.

**5.2.1 Car Management**

* Add, edit, archive, and restore car listings.

* Upload and reorder multiple images per listing.

* Toggle availability status: Available / Reserved / Sold.

* View archive of sold cars.

**5.2.2 Order Management**

* View all orders with filtering by status, customer, date range, and car.

* Review uploaded payment proofs (images/PDFs displayed inline in a modal).

* Confirm or reject payments with an optional note to the customer.

* Update shipment stage through the 9-stage pipeline.

* Custom order detail view with a visual shipment timeline (custom Filament infolist component).

* Add internal admin notes to orders.

**5.2.3 User & KYC Management**

* View all registered customers.

* Review uploaded Ghana Card and TIN documents.

* Flag or verify customer KYC status.

**5.2.4 Roles & Permissions**

* Built using FilamentShield (Spatie Laravel Permission).

* Super Admin — full access.

* Staff Admin — configurable permissions e.g. manage orders, update exchange rate, manage blog.

* Super Admin can create, edit, and assign roles from within the admin panel.

**5.2.5 System Settings**

* Update USD/GHS exchange rate (permission-controlled).

* Update bank account details and MoMo payment number shown to customers.

* Configure demurrage warning message text.

**5.2.6 Blog Management**

* Create, edit, publish, and unpublish blog posts.

* Rich text editor (TipTap via Filament).

* SEO fields per post: meta title, meta description, Open Graph image.

* Categories and tags.

* Featured image upload.

**5.2.7 Dashboard Widgets**

* Total cars available vs. reserved vs. sold.

* Pending payment confirmations (action required).

* Orders by shipment stage.

* Recent orders feed.

## **5.3 Customer Registration & KYC**

Customers must register before placing an order. KYC information is collected as required for Ghana customs clearance. Guests may browse and use the calculator freely — registration is prompted only when placing an order.

**5.3.1 Registration Fields**

* Full Name

* Phone Number (Ghana)

* Email Address

* Residential Address

* Ghana Card Number — required if no TIN provided

* TIN — Tax Identification Number — required if no Ghana Card provided

*⚠ At least one of Ghana Card Number or TIN is mandatory. Both can be provided and is preferred.*

**5.3.2 Document Uploads**

* Ghana Card scan or photo (PDF or image)

* TIN document (PDF or image) — optional if Ghana Card provided

* Documents stored in private, non-public storage (S3 or equivalent).

## **5.4 Order Placement & Management**

**5.4.1 Customer Order Flow**

1. Customer browses catalogue and selects a car.

2. Customer views car detail page with price in USD and GHS.

3. Guest is prompted to register/login when clicking Order This Car.

4. Customer confirms order — receives bank and MoMo payment details on screen and via email/SMS.

5. Customer makes payment externally (bank transfer or MoMo).

6. Customer uploads proof of payment via their dashboard.

7. Admin reviews proof and confirms payment — order moves to Payment Confirmed.

8. Car status changes to Reserved on the catalogue.

9. Admin updates shipment stages as the car progresses.

10. Customer receives email and SMS notification at each stage change.

11. Upon delivery, admin marks order Delivered. Car moves to Sold.

12. Car remains visible as Sold for 7 days then auto-archives.

**5.4.2 Order Status Pipeline**

| Status | Description |
| :---- | :---- |
| **Pending Payment** | Order placed. Awaiting customer payment proof upload. |
| **Payment Uploaded** | Customer has uploaded proof. Awaiting admin confirmation. |
| **Payment Confirmed** | Admin verified payment. Car is Reserved. |
| **Purchased** | Car has been purchased from source (Korea / Japan). |
| **In Transit to Port** | Car being transported to the shipping port. |
| **Shipped** | Car loaded on vessel, en route to Ghana. (\~45–60 days) |
| **Arrived in Ghana** | Car has arrived at Tema Port. |
| **Cleared** | Customs clearance completed by customer. |
| **Delivered** | Car handed to customer. Triggers 7-day sold countdown. |

## **5.5 Customer Dashboard (Custom-Built)**

The customer dashboard is a bespoke Livewire \+ AlpineJS \+ Tailwind interface — separate from the FilamentPHP admin panel — designed for a premium consumer experience consistent with the Livingston Autos brand.

* View all placed orders with current status.

* Visual shipment timeline per order showing all 9 stages.

* Estimated arrival date displayed per order (based on 45–60 day window from Shipped date).

* Upload payment proof for pending orders.

* View payment instructions (bank details / MoMo) per order.

* Prominent clearing fee disclaimer and demurrage warning on relevant orders.

* Edit personal profile, address, and KYC documents.

* Email and SMS notification history per order.

## **5.6 Notifications**

* Both email and SMS notifications are sent on key order events.

* SMS delivery via a Ghanaian SMS gateway (e.g. Arkesel or Hubtel).

| Event | Email | SMS | Notes |
| :---- | :---- | :---- | :---- |
| Order placed successfully | **Yes** | **Yes** | Includes payment instructions |
| Payment proof received | **Yes** | **Yes** | Acknowledgement to customer |
| Payment confirmed by admin | **Yes** | **Yes** | Car now reserved |
| Car purchased from source | **Yes** | **No** | Email only — low urgency |
| Car shipped | **Yes** | **Yes** | Estimated arrival date included |
| Car arrived in Ghana | **Yes** | **Yes** | Prompt to arrange clearing |
| Car delivered | **Yes** | **Yes** | Thank you message |
| Payment proof rejected by admin | **Yes** | **Yes** | Includes reason / next steps |

## **5.7 Blog**

* Publicly accessible blog managed from the FilamentPHP admin panel.

* Supports categories, tags, featured images, and rich text content.

* Each post has individual SEO fields: meta title, meta description, Open Graph image.

* Blog drives organic search traffic (e.g. 'how to import a car to Ghana', 'Toyota Corolla import cost Ghana').

* Estimated arrival date and related cost info articles can position Livingston Autos as the authority resource.

## **5.8 WhatsApp Integration**

* Floating WhatsApp button on all pages linking to the business WhatsApp number.

* Pre-filled message when clicking from a car listing: e.g. 'I am interested in the 2020 Toyota Corolla (Blue) listed on Livingston Autos.'

* Contact page with address, phone, email, and WhatsApp link.

* Payment instructions page with bank details and MoMo number.

## **5.9 Import Duty Calculator — Phase 2**

*⚠ This feature is deferred to Phase 2\. A simple cost summary (car price \+ shipping) is shown in Phase 1\.*

* Fully built in-app — no external links to third-party apps.

* Guest-accessible without registration. Prompt to register to save or share results.

* Inputs: Car Price (USD), Shipping Cost (USD), Vehicle Year, Engine Capacity, current exchange rate (auto-populated).

* Outputs: Estimated Import Duty, VAT, NHIL, GETFL, ECOWAS Levy, EXIM Levy, Estimated Total Clearing, Grand Total in USD and GHS.

* Prominent disclaimer: Clearing fees are estimates only. Actual amounts are set by Ghana Customs and may vary. Late clearing attracts demurrage and storage penalties.

* Formula to be validated against Auto Tax app sample calculations provided by Mr. Seth.

* Pre-fills when accessed from a car detail page.

# **6\. Non-Functional Requirements**

| ID | Requirement | Priority | Phase | Notes |
| :---- | :---- | :---- | :---- | :---- |
| **NFR-01** | Key pages load under 3 seconds on a standard 4G connection. | **Must Have** | **Phase 1** | Critical for Ghanaian mobile users |
| **NFR-02** | Fully mobile-responsive, optimised for smartphones. | **Must Have** | **Phase 1** | Majority of users on mobile |
| **NFR-03** | All KYC data encrypted at rest. | **Must Have** | **Phase 1** | Privacy & regulatory compliance |
| **NFR-04** | KYC documents stored in private, non-public cloud storage. | **Must Have** | **Phase 1** | Not directly accessible by URL |
| **NFR-05** | Admin panel protected with 2-factor authentication. | **Must Have** | **Phase 1** | Security for high-value orders |
| **NFR-06** | Support 200+ concurrent users without degradation. | **Should Have** | **Phase 1** | Growth headroom |
| **NFR-07** | SEO best practices: semantic HTML, meta tags, Open Graph, sitemap.xml, robots.txt. | **Must Have** | **Phase 1** | Organic discovery is critical |
| **NFR-08** | Sold cars auto-archive after 7 days via Laravel Scheduler. | **Must Have** | **Phase 1** | Core business rule |
| **NFR-09** | Cross-browser: Chrome, Firefox, Safari, Edge. | **Must Have** | **Phase 1** |  |
| **NFR-10** | Email \+ SMS notifications on all key order events. | **Must Have** | **Phase 1** | Via Arkesel or Hubtel for SMS |
| **NFR-11** | Calculator pre-fills from car listing page. | **Should Have** | **Phase 2** | UX improvement |
| **NFR-12** | Calculator accessible without login. | **Must Have** | **Phase 2** | Lead generation strategy |

# **7\. Technical Stack**

| Layer | Technology | Purpose |
| :---- | :---- | :---- |
| **Backend Framework** | **Laravel 13** | Application logic, routing, ORM, queues, scheduling |
| **Frontend Reactivity** | **Livewire 4** | Real-time UI (customer-facing) — no SPA overhead |
| **JavaScript** | **AlpineJS** | Lightweight interactivity — dropdowns, modals, tabs |
| **CSS Framework** | **Tailwind CSS** | Utility-first responsive styling |
| **Admin Panel** | **FilamentPHP** | Back-office CRUD, order management, blog, settings |
| **Roles & Permissions** | **FilamentShield \+ Spatie** | Granular permission management with admin UI |
| **Database** | **MySQL 8** | Relational data storage |
| **File Storage** | **Laravel Storage / S3** | Car images (public) and KYC documents (private) |
| **Queue & Jobs** | **Laravel Horizon \+ Redis** | Auto-archive, notification dispatch |
| **Authentication** | **Laravel Fortify** | Auth with 2FA for admin users |
| **SMS Gateway** | **Arkesel / Hubtel** | Ghana SMS delivery for order notifications |
| **Email** | **Laravel Mail \+ SMTP** | Transactional email notifications |
| **SEO** | **Artesaos/SEOTools** | Meta tags, Open Graph, Twitter Cards, sitemap |

# **8\. Application Modules**

| Phase | Module | Components |
| :---- | :---- | :---- |
| **Phase 1** | **Public Website** | Homepage, Car Catalogue, Car Detail Page, Basic Cost Summary, Blog, About Us, Contact, Payment Info |
| **Phase 1** | **Authentication** | Customer Registration (KYC), Login, Password Reset, Email Verification, 2FA (Admin) |
| **Phase 1** | **Customer Dashboard** | My Orders, Order Detail & Shipment Timeline, Upload Payment Proof, Profile & KYC Management, Notifications History |
| **Phase 1** | **Admin Panel** | FilamentPHP: Car CRUD, Order Management, Payment Confirmation, Shipment Stage Updates, User & KYC Management, Blog Management, Roles & Permissions, System Settings |
| **Phase 1** | **Notifications** | Email \+ SMS on all key order events via Laravel Mail and Arkesel/Hubtel |
| **Phase 1** | **Scheduler & Jobs** | Auto-archive sold cars after 7 days, exchange rate reminders |
| **Phase 1** | **SEO & Blog** | Sitemap, meta tags, Open Graph, blog with per-post SEO fields |
| **Phase 2** | **Duty Calculator** | Full in-app Ghana import duty calculator (GRA formula), guest-accessible, pre-fills from listing |

# **9\. Out of Scope — v1.0 (Phase 1\)**

* Online payment gateway (Paystack, Flutterwave, etc.) — payments handled offline.

* Import duty calculator — deferred to Phase 2\.

* Demurrage and port penalty calculations — variable, set by external authorities.

* Mobile application (iOS / Android).

* Multi-vendor support.

* Car financing or instalment plan management.

* Automated customs clearance workflows.

# **10\. Pending Action Items**

| \# | Action | Owner | Required For |
| :---- | :---- | :---- | :---- |
| **1** | Provide 3–5 sample Auto Tax calculations (different makes, years, engine sizes) so duty formula can be built and validated. | **Mr. Seth** | Phase 2 — Duty Calculator |
| **2** | Confirm who updates the USD/GHS exchange rate and the expected update frequency (daily, weekly). | **Mr. Seth** | Phase 1 — Admin Settings |
| **3** | Share logo files (SVG preferred) and brand colour codes for UI design. | **Mr. Seth** | Phase 1 — UI Design |
| **4** | Confirm Ghanaian SMS gateway preference: Arkesel or Hubtel. | **Ohene Adjei** | Phase 1 — Notifications |

# **11\. Sign-Off**

This document represents the full agreed requirements for Livingston Autos v2.0. Both parties should review, confirm, and sign before development begins on Phase 1\.

| Party | Name & Signature | Date |
| :---- | :---- | :---- |
| **Client — Mr. Seth (Livingston Autos)** |   |   |
| **Developer — Ohene Adjei** |   |   |

*End of Document — Livingston Autos SRS v2.0*