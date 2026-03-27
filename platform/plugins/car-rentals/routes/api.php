<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['api', 'api.enabled'],
    'prefix' => 'api/v1/car-rentals',
    'namespace' => 'Botble\CarRentals\Http\Controllers\API',
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

    // Booking routes (accessible to both guest and authenticated users)
    Route::prefix('bookings')->group(function (): void {
        Route::get('/', 'BookingController@index');
        Route::post('/', 'BookingController@store');
        Route::get('/{id}', 'BookingController@show');
        Route::put('/{id}', 'BookingController@update');
        Route::post('/{id}/cancel', 'BookingController@cancel');
        Route::get('/{id}/invoice', 'BookingController@getInvoice');
    });

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

            // Vendor dashboard (basic)
            Route::get('dashboard', 'Vendor\DashboardController@index');

            // Vendor reviews
            Route::get('reviews', 'Vendor\ReviewController@index');
            Route::post('reviews/{id}/reply', 'Vendor\ReviewController@reply')->wherePrimaryKey();

            // Vendor earnings
            Route::get('earnings', 'Vendor\EarningsController@index');
        });
    });
});
