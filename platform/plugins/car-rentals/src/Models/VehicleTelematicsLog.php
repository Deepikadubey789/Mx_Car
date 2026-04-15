<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleTelematicsLog extends BaseModel
{
    protected $table = 'cr_vehicle_telematics_logs';

    protected $fillable = [
        'car_id',
        'event_type',
        'latitude',
        'longitude',
        'speed_mph',
        'odometer_miles',
        'fuel_percentage',
        'raw_payload',
    ];

    protected $casts = [
        'raw_payload' => 'json',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
}