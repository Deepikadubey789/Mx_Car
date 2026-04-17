<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;

class WhatsAppConfig extends BaseModel
{
    protected $table = 'whatsapp_configs';

    protected $fillable = [
        'api_version',
        'phone_number_id',
        'webhook_verify_token',
        'api_access_token',
        'business_account_id',
        'enabled',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'enabled' => 'boolean',
    ];

    protected $hidden = [
        'api_access_token',
        'webhook_verify_token',
    ];

    /**
     * Decrypt API access token for use
     */
    public function getDecryptedAccessToken(): string
    {
        // If using Laravel's encryption
        if ($this->api_access_token) {
            try {
                // Try to decrypt
                return decrypt($this->api_access_token);
            } catch (\Exception) {
                // If not encrypted or encryption fails, return as-is
                return $this->api_access_token;
            }
        }

        return '';
    }

    /**
     * Scope to get enabled configs
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    /**
     * Get the active configuration (first enabled one)
     */
    public static function getActive(): ?self
    {
        return self::enabled()->first();
    }
}
