# Claims Triage + Financial Smoke Checklist

## Preconditions
- Run migrations: `php artisan migrate`
- Ensure your admin role has:
  - `car-rentals.bookings.claims.index`
  - `car-rentals.bookings.claims.assign`
  - `car-rentals.bookings.claims.resolve`
  - `car-rentals.bookings.claims.financial`

## Queue Flow
1. Open `Car Rentals -> Claims queue`.
2. Verify metric cards render: total, open, SLA breached, escalated.
3. Apply filters (`status`, `assignee`, `priority`, `escalated`, `sla_breached`, `booking number`) and confirm URL query string reflects them.
4. Confirm unresolved claims display stale/SLA indicators when due date is past.

## Casefile Claim Workflow
1. Open booking edit page and go to `Trip timeline & case -> Claims`.
2. Create a claim with:
   - status: `open`
   - priority: `high`
   - outcome action: `manual_only`
   - resolution due date in future
3. Update the same claim through modal:
   - move to `under_review` then `ready_for_decision`
   - assign an agent
4. Confirm invalid transition is blocked (example: `open` directly to `ready_for_decision`).

## Financial Outcome Flow
1. Update claim to final state (`resolved` or `rejected`) with outcome action:
   - `capture_deposit` then verify booking `deposit_captured_amount` changed
   - `release_deposit` then verify booking `deposit_released_amount` changed
2. Re-submit same final update and verify idempotent behavior (no duplicate inconsistent state).
3. Confirm claim settlement fields are persisted:
   - `settlement_status`
   - `settlement_reference`
   - `settlement_completed_at`

## Audit + Timeline Verification
1. In booking casefile timeline verify:
   - claim created event
   - claim updated/resolved event
   - claim settlement event
2. Verify support action log entries include before/after payload details.
3. Verify evidence provenance summary appears on relevant timeline entries.

## Notifications
1. Assign claim to another admin and confirm assignment notification is stored.
2. Set claim `resolution_due_at` in the past for active status, update claim, and confirm SLA breach notification is stored.
