<?php

use Botble\Api\Http\Middleware\ApiEnabledMiddleware;
use Botble\Api\Http\Middleware\ApiKeyMiddleware;
use Botble\CarRentals\Http\Controllers\API\StripeIdentityWebhookController;
use Illuminate\Support\Facades\Route;

/*
 * KYC webhooks — loaded even when Botble API is disabled, so provider callbacks always resolve.
 * The global `api` middleware group includes ApiEnabledMiddleware + ApiKeyMiddleware; exclude them here
 * so external providers can POST without enabling Botble API or sending X-API-KEY.
 */
Route::group([
    'middleware' => ['api'],
    'prefix' => 'api/v1/car-rentals',
], function (): void {
    Route::post('webhooks/kyc/stripe', StripeIdentityWebhookController::class)
        ->withoutMiddleware([
            ApiEnabledMiddleware::class,
            ApiKeyMiddleware::class,
        ]);
});
