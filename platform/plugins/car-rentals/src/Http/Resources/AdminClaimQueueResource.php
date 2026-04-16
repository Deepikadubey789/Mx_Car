<?php

namespace Botble\CarRentals\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminClaimQueueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isOpen = in_array($this->status, ['open', 'under_review', 'awaiting_docs', 'ready_for_decision'], true);

        return [
            'id' => $this->id,
            'booking_id' => $this->booking_id,
            'booking_number' => $this->booking?->booking_number,
            'booking_customer_name' => $this->booking?->customer_name,
            'status' => $this->status,
            'category' => $this->category,
            'priority' => $this->priority,
            'claimed_amount' => $this->claimed_amount,
            'approved_amount' => $this->approved_amount,
            'resolution_due_at' => $this->resolution_due_at,
            'resolved_at' => $this->resolved_at,
            'updated_at' => $this->updated_at,
            'escalated_at' => $this->escalated_at,
            'sla_breached' => $isOpen && $this->resolution_due_at && $this->resolution_due_at->isPast(),
            'assignee' => $this->assignee ? [
                'id' => $this->assignee->id,
                'name' => $this->assignee->name,
                'email' => $this->assignee->email,
            ] : null,
        ];
    }
}
