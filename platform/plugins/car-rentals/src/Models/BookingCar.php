<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Models\BaseModel;
use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\Location\Models\City;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingCar extends BaseModel
{
    protected $table = 'cr_booking_cars';

    protected $fillable = [
        'car_image',
        'car_name',
        'price',
        'currency_id',
        'rental_start_date',
        'rental_end_date',
        'rental_end_date',
        'booking_id',
        'car_id',
        'pickup_city_id',
        'return_city_id',
    ];

    protected $casts = [
        'rental_start_date' => 'datetime',
        'rental_end_date' => 'datetime',
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id')->withDefault();
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id')->withDefault();
    }

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id')->withDefault();
    }

    public function pickupCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'pickup_city_id')->withDefault();
    }

    public function returnCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'return_city_id')->withDefault();
    }

    public function pickupAddressText(): Attribute
    {
        return Attribute::get(function () {
            if ($this->pickupCity && $this->pickupCity->id) {
                $location = $this->pickupCity->name;
                if ($this->pickupCity->state) {
                    $location .= ', ' . $this->pickupCity->state->name;
                }
                if ($this->pickupCity->country) {
                    $location .= ', ' . $this->pickupCity->country->name;
                }

                return $location;
            }

            return '';
        });
    }

    public function returnAddressText(): Attribute
    {
        return Attribute::get(function () {
            if ($this->returnCity && $this->returnCity->id) {
                $location = $this->returnCity->name;
                if ($this->returnCity->state) {
                    $location .= ', ' . $this->returnCity->state->name;
                }
                if ($this->returnCity->country) {
                    $location .= ', ' . $this->returnCity->country->name;
                }

                return $location;
            }

            return '';
        });
    }

    public function scopeActive($query)
    {
        return $query
            ->whereHas('booking', fn ($query) => $query->whereNotIn('status', [BookingStatusEnum::CANCELLED]));
    }

    protected function bookingPeriod(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['rental_start_date'] . ' → ' . $attributes['rental_end_date'],
        );
    }

    public function rentalStartDateFormatted(): Attribute
    {
        return Attribute::get(function () {
            return $this->rental_start_date ? $this->rental_start_date->format('M d, Y H:i') : '';
        });
    }

    public function rentalEndDateFormatted(): Attribute
    {
        return Attribute::get(function () {
            return $this->rental_end_date ? $this->rental_end_date->format('M d, Y H:i') : '';
        });
    }
}
