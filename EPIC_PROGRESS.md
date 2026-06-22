# Epic Progress Tracker

Source of truth for "is this epic actually done" is `docs.md` itself — every
Acceptance Criterion and Task already has a checkbox there. This file is just
the index: one line per epic, pointing at its line range in docs.md, with a
status. Update the docs.md checkboxes as each AC/task is verified working
(not just written), then update the status here.

**Rule: an epic is only DONE when every AC checkbox under it in docs.md is
checked.** No moving to the next epic with boxes left unchecked.

| # | Epic | docs.md line | Status |
|---|---|---|---|
| 1 | Project Setup & Base Configuration | 118 | DONE |
| 2 | Authentication & Roles/Permissions | 188 | DONE |
| 3 | Car Management | 261 | PARTIAL — T-05-8 (S3 storage) deliberately deferred, no AWS credentials in this environment |
| 4 | Order Management | 389 | PARTIAL — SMS half of "email + SMS" ACs deferred to Epic 21 (no Arkesel credentials yet); email side is built and tested |
| 5 | User & KYC Management | 526 | PARTIAL — SMS half of KYC resubmission notification deferred to Epic 21; email side built and tested |
| 6 | Blog Management | 575 | DONE |
| 7 | System Settings | 626 | DONE |
| 8 | Admin Dashboard Widgets | 692 | DONE |
| 9 | Homepage | 729 | DONE |
| 10 | Car Catalogue | 817 | DONE |
| 11 | Car Detail Page | 853 | DONE |
| 12 | Blog (Public) | 890 | DONE |
| 13 | Static Pages | 942 | DONE |
| 14 | SEO & Performance | 1007 | PARTIAL — 2 ACs deferred, see note below |
| 15 | Customer Auth & KYC Registration | 1072 | PARTIAL — S3 storage deferred (same as Epic 3); 1 AC blocked on Epic 17 |
| 16 | Customer Dashboard Home | 1130 | PARTIAL — 2 ACs differ from the literal spec, see note below |
| 17 | Order Placement | 1160 | PARTIAL — SMS confirmation deferred to Epic 21, same as Epics 4/5 |
| 18 | Payment Proof Upload | 1216 | PARTIAL — S3 + SMS deferred, same pattern as other epics |
| 19 | Shipment Tracking | 1248 | DONE |
| 20 | Profile & KYC Management | 1281 | DONE (S3 storage backend deferred, same as elsewhere) |
| 21 | Notifications | 1332 | NOT STARTED |

Last audited: 2026-06-22.

**Epic 20 notes:**
- KYC status display, the resubmission-reason banner, and the document upload itself were already built correctly. Added: a Full Name field (was missing entirely), a link to the existing `/settings/security` page for password changes rather than duplicating Fortify's already-correct, secured password logic into this component (DRY), old-file deletion on document replacement, and the `KycDocumentsSubmitted` admin notification (email only — SMS deferred to Epic 21 as usual).
- T-44-3's "private S3" wording — delete-old/store-new is implemented; the storage backend is the local `private` disk, same deferral as Epics 3/15/18.

**Epic 19 notes:**
- Everything was already built correctly except live updates — added `wire:poll.30s` plus a `refreshOrder()` method that actually reloads the order and its relations (polling alone doesn't refetch anything; it just re-runs `render()` on stale component state, which I confirmed and fixed). Added test coverage since none existed for this page at all.

**Epic 18 notes:**
- The component, table, upload validation, multi-proof support, and Pending Payment → Payment Uploaded transition were already correctly built before I got here. I added the admin notification (`PaymentProofUploaded` event, email-only, same SMS deferral as everywhere else) and fixed two bugs: the `transaction_note` → `note` column mismatch (see Epic 17 notes) and missing test coverage (none existed for this component at all).
- S3 deferred for the same reason as Epics 3/15 (no AWS credentials here) — already using the `private` disk by name, so swapping later is a config change, not a code change.

**Epic 17 notes:**
- This entire epic was missing before I got to it — there was no `PlaceOrder` flow at all, so the otherwise-solid Epic 18/19 work (payment proof upload, shipment tracking) was unreachable. Built `OrderService::createOrder()`, the `OrderPlaced` event/notification, and a modal in `CarDetail` (the existing car-detail Livewire component, rather than a brand-new component — it already owns the `$car` the modal needs).
- SMS confirmation is deferred to Epic 21, consistent with every other "email + SMS" AC in this project (no Arkesel credentials in this environment).
- Fixed in passing: the car detail route was excluding `Reserved` cars (404), which would have made every car a customer just ordered disappear from view for everyone else the moment it was reserved, contradicting the catalogue page's own visibility rule. Now uses the same `visibleOnCatalogue` scope as the catalogue.
- Fixed in passing: `OrderDetail::uploadPaymentProof()` was writing to a `transaction_note` key that doesn't exist on `PaymentProof` (real column is `note`) — the customer's note was being silently dropped on every upload.

**Epic 16 deferrals:**
- "Summary cards: Total Orders, Orders in Progress, Cars Delivered" — the dashboard's stat cards are Total Orders, Saved Cars, Open Tickets, New Alerts, with an "Order Pipeline" panel breaking orders down by every stage (which covers "in progress" and "delivered" as rows, just not as dedicated top-level cards). This was already built this way before I got to this epic — leaving as-is rather than rebuilding a working page to match the literal AC wording, but flagging the deviation.
- "Recent orders section: last 3 orders with status and stage progress indicator" — shows the last 5 with a status badge, not a stage progress indicator. Same reasoning: working as built, deviates from the literal spec.
- `T-38-2` (`KYCStatusBanner` as its own component) — it's an inline `<x-ui.alert>` block in the dashboard view rather than a separate component. Functionally equivalent; not worth extracting given it's used in exactly one place (YAGNI).

**Epic 15 deferrals:**
- "KYC documents stored in private S3 storage" / `T-36-4` — stored on a local `private` disk instead (same root as `local`, but with `serve: false` so it's never auto-routed). Same deferral as Epic 3's car-photo storage: no AWS credentials in this environment. Swapping to S3 later is a one-line `FILESYSTEM_DISK`/disk-config change, no code changes needed, since everything already calls `Storage::disk('private')` by name.
- "Email verification required before placing a first order" — can't be verified yet because order placement (Epic 17) doesn't exist. Revisit when that's built.
- Fixed in passing: KYC documents were actually being served from the *public* disk in `KycDocumentController` (a leftover from before the `private` disk existed) — this would have made every KYC document reachable through the `public/storage` symlink with no signature check at all. Now fixed and covered by tests.

**Epic 14 deferrals:**
- "Images resized to 1200px gallery / 600px thumbnails" — only the single 1200px-max gallery size is built (`ImageOptimizer`, used by `CarService::syncImages`). There's no separate thumbnail-sized derivative or `CarImage` column to hold one yet; the car card just displays the gallery image at a smaller CSS size. Adding a real second size would need a schema change (a `thumbnail_path` column) — flagging this for a decision rather than building unused output or silently checking the box.
- "Homepage and catalogue load under 3s on simulated 4G" — not something this environment can rigorously measure (no real network throttling / Lighthouse run available here). WebP conversion, lazy loading, and eager-loaded queries are all in place, which should satisfy this in practice, but it's unverified rather than confirmed.
