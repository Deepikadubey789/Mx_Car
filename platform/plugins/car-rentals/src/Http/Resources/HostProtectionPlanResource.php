<?php

namespace Botble\CarRentals\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HostProtectionPlanResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'revenue_share_percentage' => (float) $this->revenue_share_percentage,
            'deductible_amount' => (float) $this->deductible_amount,
        ];
    }
}