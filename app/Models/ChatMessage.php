<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'meta',
        'source',
        'provider_message_id',
        'phone_number',
        'timestamp_ms',
        'whatsapp_metadata',
    ];

    protected $casts = [
        'meta' => 'array',
        'whatsapp_metadata' => 'array',
        'timestamp_ms' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    /**
     * Scope to filter WhatsApp messages only
     */
    public function scopeWhatsApp(Builder $query): Builder
    {
        return $query->where('source', 'whatsapp');
    }

    /**
     * Scope to filter platform messages only
     */
    public function scopePlatform(Builder $query): Builder
    {
        return $query->where('source', 'platform');
    }
}
