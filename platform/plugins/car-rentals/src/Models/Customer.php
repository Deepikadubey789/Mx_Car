<?php

namespace Botble\CarRentals\Models;

use Botble\ACL\Models\User;
use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Botble\CarRentals\Enums\CustomerStatusEnum;
use Botble\Base\Supports\Avatar;
use Botble\CarRentals\Notifications\ConfirmEmailNotification;
use Botble\CarRentals\Notifications\ResetPasswordNotification;
use Botble\Media\Facades\RvMedia;
use Exception;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Botble\CarRentals\Models\CustomerReview;

class Customer extends BaseModel implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Authenticatable;
    use Authorizable;
    use CanResetPassword;
    use MustVerifyEmail;
    use Notifiable;

    protected $table = 'cr_customers';

    protected $fillable = [
        'name',
        'email',
        'avatar',
        'phone',
        'whatsapp',
        'dob',
        'status',
        'block_reason',
        'remember_token',
        'password',
        'balance',
        'bank_info',
        'payout_payment_method',
        'is_vendor',
        'is_verified',
        'verified_at',
        'verified_by',
        'verification_note',
        'confirmed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'dob' => 'date',
        'password' => 'hashed',
        'status' => CustomerStatusEnum::class,
        'bank_info' => 'array',
        'is_vendor' => 'boolean',
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'whatsapp' => SafeContent::class,
    ];

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new ConfirmEmailNotification());
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(CarReview::class, 'customer_id');
    }

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    protected function avatarUrl(): Attribute
    {
        return Attribute::get(function () {
            if ($this->avatar) {
                return RvMedia::getImageUrl($this->avatar, 'thumb');
            }

            if ($defaultAvatar = setting('car_rentals_customer_default_avatar')) {
                return RvMedia::getImageUrl($defaultAvatar);
            }

            try {
                return (new Avatar())->create(Str::ucfirst($this->name))->toBase64();
            } catch (Exception) {
                return RvMedia::getDefaultImage();
            }
        });
    }

    protected function badge(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->is_verified) {
                    return '';
                }

                return view('plugins/car-rentals::partials.verified-badge', ['size' => 'sm'])->render();
            }
        );
    }

    protected function uploadFolder(): Attribute
    {
        return Attribute::make(
            get: function () {
                $folder = 'customers/' . $this->getKey();

                return apply_filters('car_rentals_customers_upload_folder', $folder, $this);
            }
        );
    }

    public function revenues(): HasMany
    {
        return $this->hasMany(Revenue::class, 'customer_id');
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class, 'vendor_id');
    }

    public function vendorBookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'vendor_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    protected static function booted(): void
    {
        static::deleting(function (Customer $customer): void {
            $folder = Storage::path($customer->upload_folder);
            if (File::isDirectory($folder) && Str::endsWith($customer->upload_folder, '/' . $customer->username)) {
                File::deleteDirectory($folder);
            }
        });
    }

    // 1. Relationship to fetch reviews about this customer
    public function receivedReviews(): HasMany
    {
        return $this->hasMany(CustomerReview::class, 'customer_id');
    }

    // 2. Helper to get the average rating quickly
    public function getAverageRatingAttribute(): float
    {
        return (float) $this->receivedReviews()->where('status', 'published')->avg('star') ?: 0.0;
    }

    // 3. Helper to get total number of reviews
    public function getTotalReviewsAttribute(): int
    {
        return $this->receivedReviews()->where('status', 'published')->count();
    }
}
