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
| 14 | SEO & Performance | 1007 | PARTIAL — only car detail page wired |
| 15 | Customer Auth & KYC Registration | 1072 | NOT STARTED |
| 16 | Customer Dashboard Home | 1130 | NOT STARTED |
| 17 | Order Placement | 1160 | NOT STARTED |
| 18 | Payment Proof Upload | 1216 | NOT STARTED |
| 19 | Shipment Tracking | 1248 | NOT STARTED |
| 20 | Profile & KYC Management | 1281 | NOT STARTED |
| 21 | Notifications | 1332 | NOT STARTED |

Last audited: 2026-06-21.
