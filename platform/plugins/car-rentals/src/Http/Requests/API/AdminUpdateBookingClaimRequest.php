<?php

namespace Botble\CarRentals\Http\Requests\API;

use Botble\CarRentals\Http\Controllers\BookingClaimController;
use Botble\CarRentals\Models\BookingClaim;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AdminUpdateBookingClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth('sanctum')->check();
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(BookingClaim::STATUSES)],
            'assignee_id' => ['nullable', 'integer', 'exists:users,id'],
            'approved_amount' => ['nullable', 'numeric', 'min:0'],
            'resolution_note' => ['nullable', 'string', 'max:5000'],
            'reason' => ['nullable', 'string', 'max:5000'],
            'category' => ['nullable', 'string', 'max:60'],
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
            'escalated' => ['nullable', 'boolean'],
            'escalation_note' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
