<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Models\BaseModel;
use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\Payment\Enums\PaymentStatusEnum;
use Botble\Payment\Models\Payment;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Booking extends BaseModel
{
    protected $table = 'cr_bookings';

    protected $fillable = [
        'booking_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_id',
        'kyc_verification_id',
        'vendor_id',
        'amount',
        'sub_total',
        'coupon_amount',
        'coupon_code',
        'tax_amount',
        'price_lock_expires_at',
        'price_snapshot',
        'fee_name',
        'fee_value',
        'fee_amount',
        'deposit_base_amount',
        'deposit_amount',
        'deposit_type',
        'deposit_rate',
        'deposit_risk_multiplier',
        'deposit_risk_level',
        'deposit_risk_reasons',
        'eligibility_state',
        'eligibility_reasons',
        'deposit_hold_status',
        'deposit_hold_amount',
        'deposit_authorized_at',
        'deposit_settled_at',
        'deposit_captured_amount',
        'deposit_released_amount',
        'currency_id',
        'payment_id',
        'note',
        'status',
        'completion_miles',
        'distance_unit',
        'start_mileage',
        'start_mileage_snapshot',
        'included_distance_limit',
        'distance_overage_billing_mode',
        'extra_distance_unit_price',
        'distance_travelled',
        'distance_overage_units',
        'distance_overage_amount',
        'completion_gas_level',
        'completion_damage_images',
        'completion_notes',
        'checkin_fuel_level',
        'fuel_difference_charge',
        'actual_return_datetime',
        'late_fee_charge',
        'damage_amount',
        'damage_status',
        'damage_dispute_reason',
        'damage_settled_at',
        'completed_at',
        'is_escalated',
        'key_instructions',
        'key_instructions_sent_at',
        'pickup_photos',
        'pickup_photos_uploaded_at',
        'after_photos',
        'after_photos_uploaded_at',
        'modification_type',
        'modification_status',
        'modification_reason',
        'modified_at',
        'cancellation_policy',
        'refund_amount',
        'cancellation_reason',
        'cancelled_at',
        'cancelled_by',
    ];

    protected $casts = [
        'status' => BookingStatusEnum::class,
        'fuel_difference_charge' => 'float',
        'completion_damage_images' => 'array',
        'completed_at' => 'datetime',
        'key_instructions_sent_at' => 'datetime',
        'actual_return_datetime' => 'datetime',
        'late_fee_charge' => 'float',
        'damage_amount' => 'float',
        'damage_settled_at' => 'datetime',
        'pickup_photos' => 'array',
        'pickup_photos_uploaded_at' => 'datetime',
        'after_photos' => 'array',
        'after_photos_uploaded_at' => 'datetime',
        'price_lock_expires_at' => 'datetime',
        'price_snapshot' => 'array',
        'is_escalated' => 'boolean',
        'deposit_risk_reasons' => 'array',
        'eligibility_reasons' => 'array',
        'deposit_authorized_at' => 'datetime',
        'deposit_settled_at' => 'datetime',
        'start_mileage_snapshot' => 'integer',
        'extra_distance_unit_price' => 'float',
        'distance_overage_amount' => 'float',
        'original_end_date' => 'datetime',
        'modified_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refund_amount' => 'float',
    ];

    public function car(): HasOne
    {
        return $this->hasOne(BookingCar::class, 'booking_id')->withDefault();
    }

    public function tripMessages(): HasMany
    {
        return $this->hasMany(TripMessage::class, 'booking_id')->oldest('id');
    }

    public function supportActions(): HasMany
    {
        return $this->hasMany(BookingSupportAction::class, 'booking_id')->oldest('id');
    }

    public function claims(): HasMany
    {
        return $this->hasMany(BookingClaim::class, 'booking_id')->latest('id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id')->withDefault();
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id')->withDefault();
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'reference_id')->withDefault();
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id')->withDefault();
    }

    public function kycVerification(): BelongsTo
    {
        return $this->belongsTo(CustomerKycVerification::class, 'kyc_verification_id')->withDefault();
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'vendor_id')->withDefault();
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'cr_booking_service', 'booking_id', 'service_id');
    }

    public static function generateUniqueBookingNumber(): string
    {
        $nextInsertId = BaseModel::determineIfUsingUuidsForId() ?
            static::query()->count() + 1 :
            static::query()->max('id') + 1;

        do {
            $code = CarRentalsHelper::getBookingNumber($nextInsertId);
            $nextInsertId++;
        } while (static::query()->where('booking_number', $code)->exists());

        return $code;
    }

    public static function getRevenueData(
        CarbonInterface $startDate,
        CarbonInterface $endDate,
        $select = []
    ): Collection {
        if (empty($select)) {
            $select = [
                DB::raw('DATE(payments.created_at) AS date'),
                DB::raw('SUM(COALESCE(payments.amount, 0) - COALESCE(payments.refunded_amount, 0)) as revenue'),
            ];
        }

        return self::query()
            ->join('payments', 'payments.id', '=', 'cr_bookings.payment_id')
            ->whereDate('payments.created_at', '>=', $startDate)
            ->whereDate('payments.created_at', '<=', $endDate)
            ->where('payments.status', PaymentStatusEnum::COMPLETED)
            ->groupBy('date')
            ->select($select)
            ->get();
    }

    public function canBeApproved(): bool
    {
        return $this->status == BookingStatusEnum::PENDING;
    }

    public function canBeCancelled(): bool
    {
        return ! in_array($this->status, [BookingStatusEnum::COMPLETED, BookingStatusEnum::CANCELLED]);
    }

    public function insurances(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Insurance::class, 'cr_booking_insurances', 'booking_id', 'insurance_id');
    }
}
