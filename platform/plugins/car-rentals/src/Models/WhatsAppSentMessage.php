<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppSentMessage extends BaseModel
{
    protected $table = 'whatsapp_sent_messages';

    protected $fillable = [
        'customer_id',
        'booking_id',
        'claim_id',
        'phone_number',
        'event_type',
        'template_name',
        'message_content',
        'status',
        'provider_message_id',
        'meta_response',
        'error_message',
        'sent_at',
        'status_updated_at',
    ];

    protected $casts = [
        'meta_response' => 'array',
        'sent_at' => 'datetime',
        'status_updated_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function claim(): BelongsTo
    {
        return $this->belongsTo(BookingClaim::class, 'claim_id');
    }

    /**
     * Scope to get successful sends
     */
    public function scopeSuccessful($query)
    {
        return $query->whereIn('status', ['sent', 'delivered', 'read']);
    }

    /**
     * Scope to get failed sends
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to get by event type
     */
    public function scopeByEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }
}
