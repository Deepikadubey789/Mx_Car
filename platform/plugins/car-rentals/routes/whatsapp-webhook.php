<?php

use Botble\CarRentals\Http\Controllers\Webhook\WhatsAppWebhookController;
use Illuminate\Support\Facades\Route;

/**
 * WhatsApp Webhook Routes
 *
 * These routes are loaded independently from the main API routes to ensure
 * they're accessible even when normal API authentication is disabled.
 *
 * Signature verification (HMAC-SHA256) is used instead of bearer tokens.
 */


    // WhatsApp webhook endpoint
    // GET: For Meta verification with hub_verify_token
    // POST: For incoming messages and status updates
    Route::match(['get', 'post'], '/car-rentals/webhooks/whatsapp', WhatsAppWebhookController::class . '@handle')
        ->name('api.car-rentals.webhooks.whatsapp');

