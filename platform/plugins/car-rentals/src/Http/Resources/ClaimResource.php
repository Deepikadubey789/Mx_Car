<?php

namespace Botble\CarRentals\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClaimResource extends JsonResource
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
            'outcome_action' => $this->outcome_action,
            'resolution_due_at' => $this->resolution_due_at,
            'resolved_at' => $this->resolved_at,
            'updated_at' => $this->updated_at,
            'created_at' => $this->created_at,
        ];
    }
}
