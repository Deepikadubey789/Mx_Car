<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Models\BaseModel;

class WhatsAppWebhookLog extends BaseModel
{
    protected $table = 'whatsapp_webhook_logs';

    protected $fillable = [
        'phone_number',
        'sender_phone',
        'message_id',
        'status',
        'raw_payload',
        'error_message',
        'linked_booking_id',
        'linked_claim_id',
        'linked_customer_id',
        'classification_result',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'classification_result' => 'array',
    ];
}
