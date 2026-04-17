<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatConversation extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'context_type',
        'context_id',
        'source',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }

    /**
     * Get associated booking if context is booking
     */
    public function getBookingContext()
    {
        if ($this->context_type === 'booking' && $this->context_id) {
            return \Botble\CarRentals\Models\Booking::find($this->context_id);
        }
        return null;
    }

    /**
     * Get associated claim if context is claim
     */
    public function getClaimContext()
    {
        if ($this->context_type === 'claim' && $this->context_id) {
            return \Botble\CarRentals\Models\BookingClaim::find($this->context_id);
        }
        return null;
    }
}
