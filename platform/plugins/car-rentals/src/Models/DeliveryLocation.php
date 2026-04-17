<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryLocation extends BaseModel
{
    protected $table = 'cr_delivery_locations';

    protected $fillable = [
        'vendor_id',
        'name',
        'type',
        'fee_amount',
        'latitude',
        'longitude',
        'status',
    ];

    /**
     * Get the cars that offer delivery to this location.
     */
    public function cars(): BelongsToMany
    {
        return $this->belongsToMany(Car::class, 'cr_car_delivery_locations', 'location_id', 'car_id');
    }

    /**
     * Get the vendor who owns this delivery rule.
     */
    public function vendor(): BelongsTo
    {
        // Assuming your vendor model is the Customer model in Botble
        return $this->belongsTo(Customer::class, 'vendor_id'); 
    }
}