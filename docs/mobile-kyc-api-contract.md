# Mobile KYC API Contract

Base prefix: `/api/v1/car-rentals/profile/kyc`  
Auth: `auth:sanctum` required for all endpoints.

## 1) Bootstrap

- `GET /bootstrap`
- Returns active verification and upload checklist for mobile UI.

Response shape:
- `verification`: normalized verification object
- `bootstrap.verification_id`
- `bootstrap.required_documents` (`license_front`, `license_back`, `selfie`)
- `bootstrap.uploaded_documents`
- `bootstrap.can_create_stripe_session`
- `bootstrap.awaiting_webhook`

## 2) Start verification

- `POST /start`
- Creates a draft verification and returns normalized verification + bootstrap payload.

## 3) Upload document

- `POST /{verificationId}/upload`
- Multipart fields:
  - `document_type`: `license_front|license_back|selfie`
  - `file`: image file
- Returns uploaded document metadata, verification summary, and bootstrap payload.

## 4) Create Stripe session

- `POST /{verificationId}/stripe-identity-session`
- Body:
  - `license_number` (optional)
- Returns:
  - `client_secret`
  - `verification_id`
  - `session_status`
  - `awaiting_webhook`
  - `requires_retry`
  - `verification`

## 5) Submit

- `POST /{verificationId}/submit`
- Body:
  - `license_number` (optional)
- Returns verification and webhook-awaiting status.

## 6) Status

- `GET /status`
- Returns:
  - `kyc_status`
  - `kyc_level`
  - `verification`
  - `bootstrap`
  - `retry_hints`

## Notes

- Webhook remains source of truth for final Stripe outcomes.
- API responses intentionally exclude raw provider payloads and sensitive fields.
