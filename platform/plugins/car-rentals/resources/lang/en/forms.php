<?php

return [
    'whatsapp_enabled' => 'Enable WhatsApp Integration',
    'whatsapp_enabled_help' => 'Enable WhatsApp webhook to receive and mirror booking/dispute messages',
    'whatsapp_phone_number_id' => 'WhatsApp Phone Number ID',
    'whatsapp_phone_number_id_help' => 'Get this from your Meta Business Account WhatsApp manager',
    'whatsapp_business_account_id' => 'Business Account ID',
    'whatsapp_business_account_id_help' => 'Optional: Your Meta Business Account ID for reference',
    'whatsapp_api_access_token' => 'API Access Token',
    'whatsapp_api_access_token_help' => 'Bearer token from Meta. Keep this secure and do not share',
    'whatsapp_webhook_verify_token' => 'Webhook Verify Token',
    'whatsapp_webhook_verify_token_help' => 'Token used by Meta to verify webhook requests. Generate a strong random token',
    'whatsapp_api_version' => 'API Version',
    'whatsapp_api_version_help' => 'Meta WhatsApp Business API version (default: v18.0)',
    'whatsapp_webhook_url' => 'Webhook URL',
    'whatsapp_webhook_url_help' => 'Configure this URL in your Meta Business Account: /api/car-rentals/webhooks/whatsapp',
];
