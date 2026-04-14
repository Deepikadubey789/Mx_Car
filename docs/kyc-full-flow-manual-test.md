# Full KYC Manual Test Runbook

## Preconditions

- Stripe Identity keys and webhook secret are configured in KYC settings.
- A customer account exists and is able to log in.
- At least one restricted-category car and one unrestricted-category car are available.
- Webhook endpoint is reachable from Stripe.

## Customer verification flow

1. Log in as customer and open `/customer/kyc`.
2. Upload `license_front`, `license_back`, and `selfie`.
3. Confirm all three rows show `Uploaded`.
4. Click `Verify identity` and complete Stripe Identity modal.
5. Return to KYC page and confirm status progresses from `pending` to final state after webhook.

## Expected status and reasons

- Successful verification:
  - customer `kyc_status = verified`
  - customer `kyc_level = driver_verified`
  - verification `ocr_payload.source = stripe` with normalized extracted fields when Stripe provides them
- Requires input / mismatch:
  - customer receives actionable retry hints on KYC page.
  - verification stores canonical `decision_reasons`.
  - if Stripe OCR fields are incomplete, expect `stripe_ocr_required_fields_missing` in reasons.

## Booking gate checks

1. Attempt booking as unverified customer:
   - Web booking should redirect to KYC.
   - API booking should return error with `next_url` pointing to KYC.
2. Attempt booking as verified customer:
   - Booking should proceed.
3. Attempt restricted category with basic KYC:
   - Eligibility should block with category reason.

## Risk and deposit checks

1. Request quote for different vehicle categories/types.
2. Confirm `deposit_risk` and `eligibility` are persisted into booking `price_snapshot`.
3. Validate multiplier and reason consistency between quote and booking record.

## Compliance checks

1. After verification submission/session creation, confirm uploaded KYC document rows are redacted (`file_path` cleared).
2. Run `php artisan car-rentals:prune-kyc-payloads`.
3. Confirm stale verification payloads are minimized to event/session metadata.
