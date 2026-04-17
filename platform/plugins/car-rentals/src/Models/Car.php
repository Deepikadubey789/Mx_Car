<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Models\BaseModel;
use Botble\Base\Models\BaseQueryBuilder;
use Botble\CarRentals\Enums\CarConditionEnum;
use Botble\CarRentals\Enums\CarDateValueTypeEnum;
use Botble\CarRentals\Enums\CarForSaleStatusEnum;
use Botble\CarRentals\Enums\CarRentalTypeEnum;
use Botble\CarRentals\Enums\CarStatusEnum;
use Botble\CarRentals\Enums\DistanceUnitEnum;
use Botble\CarRentals\Enums\ModerationStatusEnum;
use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Models\Builders\FilterCarsBuilder;
use Botble\CarRentals\Models\Scopes\ApprovedCarScope;
use Botble\Faq\Models\Faq;
use Botble\Location\Models\City;
use Botble\Location\Models\Country;
use Botble\Location\Models\State;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;

class Car extends BaseModel
{
    protected $table = 'cr_cars';

    protected $fillable = [
        'license_plate',
        'name',
        'description',
        'content',
        'images',
        'make_id',
        'vehicle_type_id',
        'transmission_id',
        'fuel_type_id',
        'number_of_seats',
        'number_of_doors',
        'year',
        'mileage',
        'fuel_rate_per_liter',
        'late_fee_per_hour',
        'horsepower',
        'rental_rate',
        'rental_type',
        'rental_available_types',
        'status',
        'insurance_info',
        'vin',
        'location',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'is_featured',
        'order',
        'is_used',
        'author_id',
        'author_type',
        'is_for_sale',
        'sale_price',
        'condition',
        'ownership_history',
        'warranty_information',
        'insurance_info',
        'sale_status',
        'tax_id',
        'vendor_id',
        'external_booking_url',
        'currency_id',
        'default_pickup_instructions',
        'is_delivery_enabled',
        'free_delivery_days_threshold',
        'max_delivery_distance_miles',
    ];

    protected $casts = [
        'images' => 'array',
        'status' => CarStatusEnum::class,
        'rental_type' => CarRentalTypeEnum::class,
        'moderation_status' => ModerationStatusEnum::class,
        'sale_status' => CarForSaleStatusEnum::class,
        'condition' => CarConditionEnum::class,
        'rental_rate' => 'double',
        'sale_price' => 'double',
        'horsepower' => 'double',
        'fuel_rate_per_liter' => 'double',
        'late_fee_per_hour' => 'double',
    ];

    protected $appends = [
        'avg_review',
        'price_text',
        'price_html',
        'mileage_display',
        'mileage_icon',
        'transmission_icon',
        'fuel_icon',
        'seats_icon',
    ];

    public function make(): BelongsTo
    {
        return $this->belongsTo(CarMake::class, 'make_id');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class)->withDefault();
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class)->withDefault();
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class)->withDefault();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(CarTag::class, 'cr_car_tag', 'car_id', 'tag_id');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CarReview::class, 'car_id');
    }

    public function pricingPolicy(): HasOne
    {
        return $this->hasOne(CarPricingPolicy::class, 'car_id')->withDefault();
    }

    public function tripDiscountRules(): HasMany
    {
        return $this->hasMany(CarTripDiscount::class, 'car_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(CarCategory::class, 'cr_cars_categories', 'cr_car_id', 'cr_car_category_id');
    }

    public function colors(): BelongsToMany
    {
        return $this->belongsToMany(CarColor::class, 'cr_cars_colors', 'cr_car_id', 'cr_car_color_id');
    }

    public function serviceHistories(): HasMany
    {
        return $this->hasMany(CarMaintenanceHistory::class, 'car_id');
    }

    public function carDates(): HasMany
    {
        return $this->hasMany(CarDate::class, 'car_id');
    }

    public function demandPricingRecommendations(): HasMany
    {
        return $this->hasMany(DemandPricingRecommendation::class, 'car_id');
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class, 'tax_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id')->withDefault();
    }

    public function fuel(): BelongsTo
    {
        return $this->belongsTo(CarFuel::class, 'fuel_type_id', 'id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(CarType::class, 'vehicle_type_id');
    }

    public function transmission(): BelongsTo
    {
        return $this->belongsTo(CarTransmission::class, 'transmission_id');
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(CarAmenity::class, 'cr_cars_amenities', 'cr_car_id', 'cr_car_amenity_id');
    }

    public function author(): MorphTo
    {
        return $this->morphTo()->withDefault();
    }

    protected function currentLocation(): Attribute
    {
        return Attribute::get(function () {
            if ($this->city && $this->city->id) {
                return implode(', ', array_filter([
                    $this->address,
                    $this->city->name . ($this->city->zip_code ? ' ' . $this->city->zip_code : null),
                    $this->state->name,
                    $this->country->name,
                ]));
            }

            return $this->location ?: '';
        });
    }

    public function isAvailableAt(array $filters = []): bool
    {
        if (empty($filters['start_date']) || empty($filters['end_date'])) {
            return true;
        }

        $dateFormat = CarRentalsHelper::getDateFormat();

        $allDates = [];

        for (
            $index = strtotime($filters['start_date']);
            $index <= strtotime($filters['end_date']);
            $index += 60 * 60 * 24
        ) {
            $allDates[] = date($dateFormat, $index);
        }

        $carBookings = $this->activeBookingCars;

        if ($carBookings->isNotEmpty()) {
            foreach ($carBookings as $carBooking) {
                for (
                    $index = strtotime($carBooking->rental_start_date);
                    $index < strtotime($carBooking->rental_end_date);
                    $index += 60 * 60 * 24
                ) {
                    if (in_array(date($dateFormat, $index), $allDates)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function activeBookingCars(): HasMany
    {
        return $this
            ->hasMany(BookingCar::class, 'car_id')
            ->active();
    }

    public function scopeWhereAvailableAt($query, array $filters = [])
    {
        if (empty($filters['start_date']) || empty($filters['end_date'])) {
            return $query;
        }

        $allDates = [];

        for (
            $index = strtotime($filters['start_date']);
            $index <= strtotime($filters['end_date']);
            $index += 60 * 60 * 24
        ) {
            $allDates[] = date('Y-m-d', $index);
        }

        return $query->whereDoesntHave('activeBookingCars', function ($query) use ($allDates): void {
            $query->where(function ($query) use ($allDates): void {
                foreach ($allDates as $date) {
                    $query->orWhere(function ($query) use ($date): void {
                        $query->where('rental_start_date', '<=', $date)
                            ->where('rental_end_date', '>=', $date);
                    });
                }
            });
        });
    }

    public function getBookedDatesInRange(string $startDate, string $endDate): array
    {
        $bookedDates = [];
        $bookings = $this->activeBookingCars()
            ->where(function ($query) use ($startDate, $endDate): void {
                $query->whereBetween('rental_start_date', [$startDate, $endDate])
                    ->orWhereBetween('rental_end_date', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate): void {
                        $query->where('rental_start_date', '<=', $startDate)
                            ->where('rental_end_date', '>=', $endDate);
                    });
            })
            ->get();

        foreach ($bookings as $booking) {
            $start = strtotime($booking->rental_start_date);
            $end = strtotime($booking->rental_end_date);

            for ($date = $start; $date < $end; $date += 60 * 60 * 24) {
                $bookedDates[] = date('Y-m-d', $date);
            }
        }

        return array_unique($bookedDates);
    }

    public function getAvailabilityPercentage(string $startDate, string $endDate): float
    {
        $totalDays = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24) + 1;
        $bookedDates = $this->getBookedDatesInRange($startDate, $endDate);
        $bookedDays = count($bookedDates);

        return $totalDays > 0 ? round((($totalDays - $bookedDays) / $totalDays) * 100, 2) : 0;
    }

    public function getCarRentalPrice(string $startDate, string $endDate): float|int
    {
        try {
            $startDate = Carbon::parse($startDate);
            $endDate = Carbon::parse($endDate);
        } catch (\Throwable) {
            return $this->rental_rate;
        }

        $carDates = $this->carDates()
            ->where('active', 1)
            ->whereBetween('start_date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get()
            ->keyBy(fn ($item) => $item->start_date->format('Y-m-d'));

        $totalPrice = 0;
        $currentDate = $startDate->copy();

        while ($currentDate->lt($endDate)) {
            $dateKey = $currentDate->format('Y-m-d');
            $dayPrice = $this->getDayPrice($carDates->get($dateKey));
            $totalPrice += $dayPrice;
            $currentDate->addDay();
        }

        if ($totalPrice === 0) {
            $dateKey = $startDate->format('Y-m-d');
            $totalPrice = $this->getDayPrice($carDates->get($dateKey));
        }

        return $totalPrice;
    }

    protected function getDayPrice(?CarDate $carDate): float
    {
        if (! $carDate || ! $carDate->active) {
            return $this->getBaseDayRate();
        }

        return match ($carDate->value_type->getValue()) {
            CarDateValueTypeEnum::FIXED => $carDate->value,
            CarDateValueTypeEnum::AMOUNT_ADJUST => $this->getBaseDayRate() + $carDate->value,
            CarDateValueTypeEnum::PERCENTAGE_ADJUST => $this->getBaseDayRate() + ($this->getBaseDayRate() * $carDate->value / 100),
            default => $this->getBaseDayRate(),
        };
    }

    protected function getBaseDayRate(): float
    {
        return match ($this->rental_type) {
            CarRentalTypeEnum::PER_HOUR => $this->rental_rate * 24,
            CarRentalTypeEnum::PER_WEEK => $this->rental_rate / 7,
            CarRentalTypeEnum::PER_MONTH => $this->rental_rate / 30,
            default => $this->rental_rate,
        };
    }

    public function getImages(): array
    {
        $images = $this->images;
        if (empty($images)) {
            return [];
        }

        if (! is_array($images)) {
            $images = json_decode($images, true);
        }

        return array_filter($images);
    }

    public function getImageAttribute()
    {
        return Arr::first($this->getImages()) ?? null;
    }

    public function scopeWhereFeatured($query): Builder
    {
        return $query->where('is_featured', true);
    }

    protected function faqItems(): Attribute
    {
        return Attribute::get(function () {
            $this->loadMissing('metadata');

            $faqs = (array) $this->getMetaData('faq_schema_config', true);

            if (is_plugin_active('faq')) {
                $selectedExistingFaqs = $this->getMetaData('faq_ids', true);

                if ($selectedExistingFaqs && is_array($selectedExistingFaqs)) {
                    $selectedExistingFaqs = array_filter($selectedExistingFaqs);

                    if ($selectedExistingFaqs) {
                        $selectedFaqs = Faq::query()
                            ->wherePublished()
                            ->whereIn('id', $selectedExistingFaqs)
                            ->select(['id', 'question', 'answer'])
                            ->get();

                        foreach ($selectedFaqs as $selectedFaq) {
                            $faqs[] = [
                                [
                                    'key' => 'question',
                                    'value' => $selectedFaq->question,
                                ],
                                [
                                    'key' => 'answer',
                                    'value' => $selectedFaq->answer,
                                ],
                            ];
                        }
                    }
                }
            }

            $faqs = array_filter($faqs);

            if (empty($faqs)) {
                return [];
            }

            foreach ($faqs as $key => $item) {
                if (! $item[0]['value'] && ! $item[1]['value']) {
                    Arr::forget($faqs, $key);
                }
            }

            return $faqs;
        })->shouldCache();
    }

    public function scopeActive(Builder $query): Builder
    {
        $query = $query->where('status', CarStatusEnum::AVAILABLE);

        $enabledPostApproval = CarRentalsHelper::isEnabledPostApproval();

        if ($enabledPostApproval) {
            $query->where('moderation_status', ModerationStatusEnum::APPROVED);
        }

        return $query;
    }

    public function newEloquentBuilder($query): BaseQueryBuilder
    {
        return new FilterCarsBuilder($query);
    }

    public function getAvgReviewAttribute(): float
    {
        if (array_key_exists('reviews_avg_star', $this->attributes)) {
            $average = $this->attributes['reviews_avg_star'];

            return $average ? round((float) $average, 2) : 0.0;
        }

        $average = $this->reviews()->average('star');

        return $average ? round((float) $average, 2) : 0.0;
    }

    protected function priceText(): Attribute
    {
        return Attribute::get(function () {
            if ($this->currency_id) {
                return format_price($this->rental_rate, $this->currency_id) . ' / ' . $this->rental_type->label();
            }

            return format_price($this->rental_rate) . ' / ' . $this->rental_type->label();
        });
    }

    protected function priceHtml(): Attribute
    {
        return Attribute::get(function () {
            $price = $this->is_for_sale ? $this->sale_price : $this->rental_rate;

            if (empty($price) || $price <= 0) {
                return __('Contact for price');
            }

            if ($this->currency_id) {
                $formattedPrice = format_price($price, $this->currency_id);

                if (! $this->is_for_sale) {
                    $formattedPrice .= ' / ' . $this->rental_type->label();
                }

                return $formattedPrice;
            }

            if ($this->is_for_sale) {
                return format_price($this->sale_price);
            }

            return format_price($this->rental_rate) . ' / ' . $this->rental_type->label();
        });
    }

    public function calculateTaxAmount(float $amount): float
    {
        if (! CarRentalsHelper::isTaxEnabled()) {
            return 0;
        }

        $taxAmount = CarRentalsHelper::getTaxAmount($amount);

        if ($taxAmount <= 0 && $this->tax && $this->tax->percentage > 0) {
            $taxAmount = ($amount * $this->tax->percentage / 100);
        }

        return $taxAmount;
    }

    public function getTaxInfo(float $taxAmount = 0): string
    {
        if ($taxAmount <= 0) {
            return '';
        }

        if (CarRentalsHelper::isTaxEnabled()) {
            $taxes = CarRentalsHelper::getAppliedTaxes();
            if ($taxes->isEmpty()) {
                return '';
            }

            $taxInfo = '';
            foreach ($taxes as $tax) {
                $taxInfo .= $tax->name . ' (' . $tax->percentage . '%), ';
            }

            return rtrim($taxInfo, ', ');
        } elseif ($this->tax) {
            return $this->tax->name . ' (' . $this->tax->percentage . '%)';
        }

        return '';
    }

    protected function isPendingModeration(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->exists) {
                return false;
            }

            return ! in_array($this->moderation_status, [ModerationStatusEnum::APPROVED, ModerationStatusEnum::REJECTED]);
        });
    }

    public function getCarPurposeAttribute(): string
    {
        return $this->is_for_sale ? 'sale' : 'rent';
    }

    public function setCarPurposeAttribute(string $value): void
    {
        $this->attributes['is_for_sale'] = $value === 'sale';
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'vendor_id')->withDefault();
    }

    public function hasExternalBookingUrl(): bool
    {
        return ! empty($this->external_booking_url);
    }

    public function getExternalBookingRoute(): string
    {
        return route('public.car.external-booking', $this->slug);
    }

    protected function mileageDisplay(): Attribute
    {
        return Attribute::get(function () {
            $distanceUnit = get_car_rentals_setting('distance_unit', DistanceUnitEnum::MILES);
            $unitLabel = $distanceUnit === DistanceUnitEnum::KILOMETERS ? __('km') : __('miles');

            return __(':number :unit', [
                'number' => number_format($this->mileage),
                'unit' => $unitLabel,
            ]);
        });
    }

    protected function mileageIcon(): Attribute
    {
        return Attribute::get(function () {
            return 'ti ti-gauge';
        });
    }

    protected function transmissionIcon(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->transmission) {
                return 'ti ti-automatic-gearbox';
            }

            return match (strtolower($this->transmission->name)) {
                'automatic' => 'ti ti-automatic-gearbox',
                'manual' => 'ti ti-manual-gearbox',
                default => 'ti ti-automatic-gearbox'
            };
        });
    }

    protected function fuelIcon(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->fuel) {
                return 'ti ti-car-fan';
            }

            return match (strtolower($this->fuel->name)) {
                'electric' => 'ti ti-bolt',
                'diesel' => 'ti ti-car-turbine',
                default => 'ti ti-car-fan'
            };
        });
    }

    protected function seatsIcon(): Attribute
    {
        return Attribute::get(function () {
            return 'ti ti-armchair';
        });
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new ApprovedCarScope());

        static::deleted(function (Car $car): void {
            $car->carDates()->delete();
        });
    }

    public function deliveryLocations()
    {
        return $this->belongsToMany(DeliveryLocation::class, 'cr_car_delivery_locations', 'car_id', 'location_id');
    }
}
