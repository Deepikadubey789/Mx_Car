<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Models\BaseModel;

class WhatsAppMessageTemplate extends BaseModel
{
    protected $table = 'whatsapp_message_templates';

    protected $fillable = [
        'name',
        'event_type',
        'label',
        'template_content',
        'description',
        'placeholders',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'placeholders' => 'array',
        'metadata' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Scope to get active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get by event type
     */
    public function scopeByEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Get by name
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('name', $name);
    }
}
