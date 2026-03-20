# Event Management Project Audit Report

Date: 2026-03-19
Scope: Full repository review (controllers, routes, middleware, models, migrations, views, tests)
Constraint respected: No source code was modified during this audit. Only this report file was added.

## Audit Method
- Ran static editor diagnostics: no IDE/runtime diagnostics reported.
- Ran automated tests: 25 passed.
- Performed manual logic/security/data-integrity review of critical flows:
  - authentication and role access
  - booking and checkout/payment
  - admin/staff status workflows
  - slot lifecycle and event updates
  - PII handling and external dependencies

## Executive Summary
The project is in a good baseline state (tests green, routes and auth flow working), but there are several high-impact business and security issues that should be fixed before production.

Most serious risks:
1. Payment can be marked paid without real gateway verification.
2. Slots are consumed before payment and never automatically released on abandoned checkout.
3. Admin event updates delete/recreate all slots, which can break historical booking integrity.
4. Booking status transitions are unrestricted (admin and staff), allowing invalid lifecycle jumps.

---

## Findings (Ordered by Severity)

## 1) CRITICAL - Razorpay payments are trusted without signature verification
- Evidence:
  - app/Http/Controllers/CheckoutController.php:38-44
  - app/Http/Controllers/CheckoutController.php:50-56
- Problem:
  - Any client can submit an arbitrary razorpay_payment_id and the system marks payment as paid.
  - No order creation, no signature/hash verification, no webhook reconciliation.
- Impact:
  - Fraudulent "paid" bookings and revenue/report distortion.
- Recommendation:
  - Implement real Razorpay flow: server-side order creation, signature validation, and webhook-based finalization.

## 2) CRITICAL - Slot inventory is consumed before successful payment and not released
- Evidence:
  - app/Http/Controllers/BookingController.php:35
  - app/Http/Controllers/BookingController.php:42-44
- Problem:
  - booked_count increments when booking is created (pending payment).
  - If checkout is abandoned, capacity remains reduced indefinitely.
- Impact:
  - Artificial slot exhaustion and lost sales.
- Recommendation:
  - Use reservation TTL (temporary hold) with background release job, or increment booked_count only after successful payment.

## 3) HIGH - Event update deletes all slots, breaking operational integrity
- Evidence:
  - app/Http/Controllers/Admin/EventController.php:94-97
- Problem:
  - On event update, all existing slots are deleted and recreated.
  - Existing bookings keep date/slot text only; no stable slot linkage to preserve operational history and capacity logic.
- Impact:
  - Inconsistent historical data, potential mismatch between active slots and booked attendees.
- Recommendation:
  - Model slot edits as upsert/diff operations; do not delete booked slots; introduce explicit immutable slot IDs for booked records.

## 4) HIGH - Booking status transitions are unrestricted
- Evidence:
  - app/Http/Controllers/Admin/BookingManagementController.php:42-47
  - app/Http/Controllers/Staff/BookingController.php:17-19
  - app/Http/Controllers/Staff/BookingController.php:27
- Problem:
  - Status can jump directly between states (e.g., pending -> completed, rejected -> completed).
  - No transition rules, no role-specific state guardrails.
- Impact:
  - Workflow corruption, auditability issues, and incorrect customer communication.
- Recommendation:
  - Enforce finite-state-machine style transitions with policy checks.

## 5) HIGH - Already-paid bookings can be re-processed and overwritten
- Evidence:
  - app/Http/Controllers/CheckoutController.php:50-59
  - app/Http/Controllers/CheckoutController.php:61-65
- Problem:
  - updateOrCreate allows payment record overwrite for existing booking without locking or final-state guard.
  - A paid booking can be switched to cod or vice versa by repeated requests.
- Impact:
  - Accounting inconsistency and potential abuse.
- Recommendation:
  - Add guard: reject checkout processing once payment status is final (paid/cod confirmed) unless explicit admin override.

## 6) MEDIUM - Sensitive booking metadata sent to third-party QR API
- Evidence:
  - app/Http/Controllers/CheckoutController.php:63-64
- Problem:
  - QR data includes booking ID, event ID, user ID and is generated via external API URL.
- Impact:
  - PII and internal identifiers leak outside platform.
- Recommendation:
  - Generate QR locally/server-side and encode opaque signed token instead of raw IDs.

## 7) MEDIUM - Mail send is synchronous in checkout critical path
- Evidence:
  - app/Http/Controllers/CheckoutController.php:68
- Problem:
  - SMTP/network failure can interrupt response after payment updates.
- Impact:
  - User sees error despite completed payment update; poor reliability.
- Recommendation:
  - Queue mail (ShouldQueue) and decouple from transaction completion path.

## 8) MEDIUM - Booking does not reference event_slots table directly
- Evidence:
  - app/Http/Controllers/BookingController.php:40-41
  - database/migrations/2026_03_19_063104_create_bookings_table.php:14-24
- Problem:
  - Booking stores date and slot text, but no foreign key to event_slots.
- Impact:
  - Harder reconciliation for capacity, rescheduling, and reporting.
- Recommendation:
  - Add event_slot_id to bookings with FK (nullable for legacy migration if needed).

## 9) LOW - Role-based redirect code contains redundant branches
- Evidence:
  - app/Http/Controllers/Auth/AuthenticatedSessionController.php:40-48
- Problem:
  - Admin, staff, and user branches all redirect to the same route.
- Impact:
  - Minor maintainability/readability issue.
- Recommendation:
  - Collapse to one redirect or use explicit per-role routes if intended.

## 10) LOW - Test coverage gap for core business workflows
- Evidence:
  - Existing test suite passes but focuses on auth/profile and generic page health.
- Problem:
  - No automated tests for booking lifecycle, payment processing rules, slot capacity integrity, and admin/staff transitions.
- Impact:
  - High chance of regressions in critical domain logic.
- Recommendation:
  - Add feature tests for booking/payment/assignment/status transitions and abuse scenarios.

---

## Positive Observations
- Role middleware exists and blocks unauthorized role access.
- Blocked users are denied login and protected routes.
- Core CRUD and dashboard flows are implemented and navigable.
- Pagination and validation are broadly present.
- Baseline auth tests are healthy.

## Production Readiness Verdict
Status: NOT production-ready yet due to unresolved critical payment and booking-inventory integrity issues.

## Suggested Prioritized Fix Plan
1. Fix payment authenticity and finalization model (webhook + signature verification).
2. Redesign reservation lifecycle to prevent slot leakage.
3. Implement strict status transition rules.
4. Preserve slot identity and avoid destructive slot rewrites.
5. Add domain-level test coverage for booking and payment flows.
