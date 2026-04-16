<?php

namespace Botble\CarRentals\Http\Requests\API;

use Botble\CarRentals\Http\Controllers\BookingClaimController;
use Botble\CarRentals\Models\BookingClaim;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminStoreBookingClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('sanctum')->check();
    }

    public function rules(): array
    {
        return [
            'category' => ['required', 'string', 'max:60'],
            'claimed_amount' => ['nullable', 'numeric', 'min:0'],
            'reason' => ['required', 'string', 'max:5000'],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['nullable', Rule::in(BookingClaim::STATUSES)],
            'priority' => ['nullable', Rule::in(BookingClaimController::PRIORITIES)],
            'liability_decision' => ['nullable', Rule::in(BookingClaimController::LIABILITY_DECISIONS)],
            'policy_basis' => ['nullable', 'string', 'max:5000'],
            'evidence_completeness' => ['nullable', Rule::in(BookingClaimController::EVIDENCE_COMPLETENESS)],
            'requires_additional_docs' => ['nullable', 'boolean'],
            'checklist_notes' => ['nullable', 'string', 'max:5000'],
            'resolution_due_at' => ['nullable', 'date'],
            'first_response_due_at' => ['nullable', 'date'],
            'outcome_action' => ['nullable', Rule::in(BookingClaim::OUTCOME_ACTIONS)],
            'evidence_provenance' => ['nullable', 'array'],
        ];
    }
}
