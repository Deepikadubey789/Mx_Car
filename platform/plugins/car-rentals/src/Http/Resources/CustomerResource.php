<?php

namespace Botble\CarRentals\Http\Resources;

use Botble\CarRentals\Models\Customer;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Customer
 */
class CustomerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar_url,
            'dob' => $this->dob,
            'is_verified' => (bool) $this->is_verified,
            'kyc_status' => $this->kyc_status,
            'kyc_level' => $this->kyc_level,
            'gender' => $this->gender,
            'description' => $this->description,
        ];
    }
}
