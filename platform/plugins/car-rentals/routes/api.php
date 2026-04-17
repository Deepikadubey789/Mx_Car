<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['api', 'api.enabled'],
    'prefix' => 'api/v1/car-rentals',
    'namespace' => 'Botble\CarRentals\Http\Controllers\API', // Note: Make sure your controller is in the 'API' folder (uppercase)
], function (): void {

    // Public endpoints (no authentication required)

    // Cars
    Route::get('cars', 'CarController@index');
    Route::get('cars/search', 'CarController@search');
    Route::get('cars/filters', 'CarController@getFilters');
    Route::get('cars/{slug}', 'CarController@findBySlug');
    Route::get('cars/id/{id}', 'CarController@show')->wherePrimaryKey();
    Route::get('cars/id/{id}/availability', 'CarController@checkAvailability')->wherePrimaryKey();
    Route::get('cars/id/{id}/similar', 'CarController@getSimilarCars')->wherePrimaryKey();
    // --- NEW: Fetch Delivery Options for Mobile App ---
    Route::get('cars/{id}/delivery-locations', 'DeliveryController@getCarDeliveryOptions')->wherePrimaryKey();

    // Car Makes (simplified)
    Route::get('car-makes', 'CarMakeController@index');

    // Car Types (simplified)
    Route::get('car-types', 'CarTypeController@index');

    // Car Categories (simplified)
    Route::get('car-categories', 'CarCategoryController@index');

    // Car Transmissions (simplified)
    Route::get('car-transmissions', 'CarTransmissionController@index');

    // Car Fuels (simplified)
    Route::get('car-fuels', 'CarFuelController@index');

    // Car Amenities (simplified)
    Route::get('car-amenities', 'CarAmenityController@index');

    // --- NEW: Protection Plans ---
    Route::get('guest-protection-plans', 'ProtectionPlanController@getGuestPlans');
    Route::get('host-protection-plans', 'ProtectionPlanController@getHostPlans');

    // Locations
    Route::get('locations', 'LocationController@index');
    Route::get('locations/search', 'LocationController@search');

    // Reviews (car-specific only)
    Route::get('cars/{car_id}/reviews', 'ReviewController@getCarReviews')->wherePrimaryKey('car_id');

    // Coupons (public validation)
    Route::post('coupons/validate', 'CouponController@validateCoupon');

    // Pricing calculator
    Route::post('calculate-price', 'PricingController@calculate');

    // Contact/Inquiry
    Route::post('inquiries', 'InquiryController@store');

    // --- GPS Telematics ---
    Route::post('webhooks/telematics', [
        'uses' => 'Webhook\TelematicsWebhookController@handle',
        'as' => 'api.car-rentals.webhooks.telematics',
    ]);
    // -------------------------------------------

    // Booking routes (accessible to both guest and authenticated users)
    Route::prefix('bookings')->group(function (): void {
        // --- NEW: Estimate Booking Price for Mobile App ---
        Route::post('/estimate', 'BookingController@estimateBooking');
        Route::get('/', 'BookingController@index');
        Route::post('/', 'BookingController@store');
        Route::get('/{id}', 'BookingController@show');
        Route::put('/{id}', 'BookingController@update');
        Route::delete('/{id}', 'BookingController@destroy');
        Route::get('/{id}/invoice', 'BookingController@getInvoice');

        // Trip modification routes
        Route::post('/{id}/cancel', 'BookingController@cancel');
        Route::post('/{id}/extend', 'BookingController@extend');
        Route::post('/{id}/shorten', 'BookingController@shorten');
        Route::post('/{id}/early-return', 'BookingController@earlyReturn');
        Route::post('/{id}/late-return', 'BookingController@lateReturn');
    });

    // Admin modification approve/reject (no extra auth needed - called from admin panel)
    Route::post('admin/bookings/{id}/modification/approve', 'BookingController@approveModification');
    Route::post('admin/bookings/{id}/modification/reject', 'BookingController@rejectModification');

    // Customer authentication routes (car-rental specific)
    Route::prefix('auth')->group(function (): void {
        Route::post('register', 'AuthController@register');
        Route::post('login', 'AuthController@login');
        Route::post('forgot-password', 'AuthController@forgotPassword');
        Route::post('reset-password', 'AuthController@resetPassword');
    });

    // Authenticated endpoints (require auth:sanctum middleware)
    Route::group(['middleware' => ['auth:sanctum']], function (): void {

        // Account profile
        Route::prefix('profile')->group(function (): void {
            Route::get('/', 'ProfileController@show');
            Route::put('/', 'ProfileController@update');
            Route::post('avatar', 'ProfileController@updateAvatar');
            Route::post('change-password', 'ProfileController@changePassword');
            Route::post('kyc/start', 'KycController@start');
            Route::get('kyc/bootstrap', 'KycController@bootstrap');
            Route::post('kyc/{verificationId}/upload', 'KycController@upload');
            Route::post('kyc/{verificationId}/submit', 'KycController@submit');
            Route::post('kyc/{verificationId}/stripe-identity-session', 'KycController@stripeIdentitySession');
            Route::get('kyc/status', 'KycController@status');
        });

        // Authentication actions
        Route::post('auth/logout', 'AuthController@logout');

        // Reviews (authenticated actions)
        Route::prefix('reviews')->group(function (): void {
            Route::post('/', 'ReviewController@store');
        });

        // Favorites/Wishlist
        Route::prefix('favorites')->group(function (): void {
            Route::get('/', 'FavoriteController@index');
            Route::post('/{car_id}', 'FavoriteController@store')->wherePrimaryKey('car_id');
            Route::delete('/{car_id}', 'FavoriteController@destroy')->wherePrimaryKey('car_id');
        });

        // Coupons (authenticated actions)
        Route::post('coupons/apply', 'CouponController@apply');
        Route::post('coupons/remove', 'CouponController@remove');

        // Customer claims (mobile public-safe)
        Route::prefix('bookings/{booking}/claims')->group(function (): void {
            Route::get('/', 'Claims\CustomerClaimController@index')->wherePrimaryKey('booking');
            Route::get('/{claim}', 'Claims\CustomerClaimController@show')->wherePrimaryKey('booking')->wherePrimaryKey('claim');
            Route::get('/timeline', 'Claims\CustomerClaimController@timeline')->wherePrimaryKey('booking');
        });

        // Vendor routes (require vendor verification)
        Route::middleware(['vendor'])->prefix('vendor')->group(function (): void {
            // Vendor profile
            Route::get('profile', 'Vendor\ProfileController@show');
            Route::put('profile', 'Vendor\ProfileController@update');

            // Vendor cars
            Route::prefix('cars')->group(function (): void {
                Route::get('/', 'Vendor\CarController@index');
                Route::post('/', 'Vendor\CarController@store');
                Route::get('/{id}', 'Vendor\CarController@show')->wherePrimaryKey();
                Route::put('/{id}', 'Vendor\CarController@update')->wherePrimaryKey();
                Route::delete('/{id}', 'Vendor\CarController@destroy')->wherePrimaryKey();
                Route::post('/{id}/images', 'Vendor\CarController@uploadImages')->wherePrimaryKey();
            });

            // Vendor bookings
            Route::prefix('bookings')->group(function (): void {
                Route::get('/', 'Vendor\BookingController@index');
                Route::get('/{id}', 'Vendor\BookingController@show')->wherePrimaryKey();
                Route::put('/{id}/status', 'Vendor\BookingController@updateStatus')->wherePrimaryKey();
                Route::post('/{id}/complete', 'Vendor\BookingController@complete')->wherePrimaryKey();
            });

            Route::prefix('bookings/{booking}/claims')->group(function (): void {
                Route::get('/', 'Claims\VendorClaimController@index')->wherePrimaryKey('booking');
                Route::get('/{claim}', 'Claims\VendorClaimController@show')->wherePrimaryKey('booking')->wherePrimaryKey('claim');
                Route::get('/timeline', 'Claims\VendorClaimController@timeline')->wherePrimaryKey('booking');
            });

            // Vendor dashboard (basic & advanced)
            Route::get('dashboard', 'Vendor\DashboardController@index');
            Route::get('dashboard/fleet-calendar', 'Vendor\DashboardController@getFleetCalendarEvents');
            
            // --- Mobile GPS Tracking APIs ---
            Route::get('dashboard/fleet-locations', 'Vendor\DashboardController@getFleetLocations');
            Route::get('dashboard/telematics-logs', 'Vendor\DashboardController@getTelematicsLogs');
            // ---------------------------------

            // Vendor reviews
            Route::get('reviews', 'Vendor\ReviewController@index');
            Route::post('reviews/{id}/reply', 'Vendor\ReviewController@reply')->wherePrimaryKey();

            // Vendor earnings
            Route::get('earnings', 'Vendor\EarningsController@index');

            // Vendor demand pricing recommendations (mobile API)
            Route::prefix('recommendations')->group(function (): void {
                Route::get('/', 'Vendor\RecommendationApiController@index');
                Route::get('/{recommendation}', 'Vendor\RecommendationApiController@show')->wherePrimaryKey();
                Route::post('/{recommendation}/apply', 'Vendor\RecommendationApiController@apply')->wherePrimaryKey();
                Route::post('/{recommendation}/dismiss', 'Vendor\RecommendationApiController@dismiss')->wherePrimaryKey();
                Route::post('/{recommendation}/adjust', 'Vendor\RecommendationApiController@adjust')->wherePrimaryKey();
                Route::get('/metrics/summary', 'Vendor\RecommendationApiController@summary');
            });

            // Vendor auto-pricing settings (mobile API)
            Route::prefix('cars/{car}/auto-pricing')->group(function (): void {
                Route::get('/', 'Vendor\AutoPricingApiController@show')->wherePrimaryKey('car');
                Route::put('/', 'Vendor\AutoPricingApiController@update')->wherePrimaryKey('car');
                Route::post('/pause', 'Vendor\AutoPricingApiController@pause')->wherePrimaryKey('car');
                Route::post('/resume', 'Vendor\AutoPricingApiController@resume')->wherePrimaryKey('car');
                Route::get('/history', 'Vendor\AutoPricingApiController@history')->wherePrimaryKey('car');
            });

            // Vendor reviews
            Route::prefix('reviews')->group(function (): void {
                Route::get('/', 'Vendor\ReviewController@index');
                Route::post('{id}/reply', 'Vendor\ReviewController@reply')->wherePrimaryKey();
                
                // --- NEW: Vendor rates a customer after a trip ---
                Route::post('rate-customer', 'Vendor\ReviewController@rateCustomer');
            });
        });

        // Admin/support claims (mobile internal)
        Route::prefix('admin/bookings')->group(function (): void {
            Route::get('claims', 'Claims\AdminClaimController@queue');
            Route::get('claims/metrics', 'Claims\AdminClaimController@metrics');
            Route::get('{booking}/claims', 'Claims\AdminClaimController@index')->wherePrimaryKey('booking');
            Route::post('{booking}/claims', 'Claims\AdminClaimController@store')->wherePrimaryKey('booking');
            Route::get('{booking}/claims/timeline', 'Claims\AdminClaimController@timeline')->wherePrimaryKey('booking');
            Route::get('{booking}/claims/{claim}', 'Claims\AdminClaimController@show')->wherePrimaryKey('booking')->wherePrimaryKey('claim');
            Route::put('{booking}/claims/{claim}', 'Claims\AdminClaimController@update')->wherePrimaryKey('booking')->wherePrimaryKey('claim');
        });
    });
});