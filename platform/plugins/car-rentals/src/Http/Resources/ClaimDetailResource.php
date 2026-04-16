<?php

namespace Botble\CarRentals\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClaimDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'booking_number' => $this->booking?->booking_number,
            'category' => $this->category,
            'status' => $this->status,
            'priority' => $this->priority,
            'claimed_amount' => $this->claimed_amount,
            'approved_amount' => $this->approved_amount,
            'reason' => $this->reason,
            'resolution_note' => $this->resolution_note,
            'assignee' => $this->assignee ? [
                'id' => $this->assignee->id,
                'name' => $this->assignee->name,
                'email' => $this->assignee->email,
            ] : null,
            'liability_decision' => $this->liability_decision,
            'policy_basis' => $this->policy_basis,
            'evidence_completeness' => $this->evidence_completeness,
            'requires_additional_docs' => (bool) $this->requires_additional_docs,
            'checklist_notes' => $this->checklist_notes,
            'outcome_action' => $this->outcome_action,
            'settlement_status' => $this->settlement_status,
            'settlement_reference' => $this->settlement_reference,
            'settlement_error' => $this->settlement_error,
            'settlement_metadata' => $this->settlement_metadata,
            'first_response_due_at' => $this->first_response_due_at,
            'resolution_due_at' => $this->resolution_due_at,
            'escalated_at' => $this->escalated_at,
            'escalation_note' => $this->escalation_note,
            'evidence' => $this->evidence,
            'evidence_provenance' => $this->evidence_provenance,
            'resolved_at' => $this->resolved_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
