<?php

namespace Botble\CarRentals\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GuestProtectionPlanResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'daily_fee' => (float) $this->daily_fee,
            'deductible_amount' => (float) $this->deductible_amount,
            'liability_limit' => $this->liability_limit ? (float) $this->liability_limit : null,
        ];
    }
}