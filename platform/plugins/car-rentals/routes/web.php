<?php

use Botble\Base\Facades\AdminHelper;
use Botble\CarRentals\Http\Controllers\Cars\CarController;
use Botble\CarRentals\Http\Controllers\CouponController;
use Botble\CarRentals\Http\Controllers\MessageController;
use Botble\CarRentals\Http\Controllers\Settings\CarFilterSettingController;
use Botble\CarRentals\Http\Controllers\Settings\CurrencySettingController;
use Botble\CarRentals\Http\Controllers\Settings\CustomerSettingController;
use Botble\CarRentals\Http\Controllers\Settings\GeneralSettingController;
use Botble\CarRentals\Http\Controllers\Settings\InvoiceSettingController;
use Botble\CarRentals\Http\Controllers\Settings\InvoiceTemplateSettingController;
use Botble\CarRentals\Http\Controllers\Settings\PriceLockSettingController;
use Botble\CarRentals\Http\Controllers\Settings\ReviewSettingController;
use Botble\CarRentals\Http\Controllers\Settings\TaxSettingController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Botble\CarRentals\Http\Controllers'], function (): void {
    AdminHelper::registerRoutes(function (): void {
        Route::group(['prefix' => 'car-rentals', 'as' => 'car-rentals.'], function (): void {
            Route::resource('customers', 'CustomerController')->parameters(['' => 'customers']);

            Route::get('customers/{customer}/view', [
                'as' => 'customers.view',
                'uses' => 'CustomerController@view',
                'permission' => 'car-rentals.customers.edit',
            ]);

            Route::post('customers/{customer}/verify', [
                'as' => 'customers.verify',
                'uses' => 'CustomerController@verify',
                'permission' => 'car-rentals.customers.edit',
            ]);

            Route::post('customers/{customer}/unverify', [
                'as' => 'customers.unverify',
                'uses' => 'CustomerController@unverify',
                'permission' => 'car-rentals.customers.edit',
            ]);

            Route::post('customers/{customer}/verify-email', [
                'as' => 'customers.verify-email',
                'uses' => 'CustomerController@verifyEmail',
                'permission' => 'car-rentals.customers.edit',
            ]);

            Route::post('customers/{customer}/resend-confirmation', [
                'as' => 'customers.resend-confirmation',
                'uses' => 'CustomerController@resendConfirmation',
                'permission' => 'car-rentals.customers.edit',
            ]);

            Route::post('customers/{customer}/upgrade-to-vendor', [
                'as' => 'customers.upgrade-to-vendor',
                'uses' => 'CustomerController@upgradeToVendor',
                'permission' => 'car-rentals.customers.edit',
            ]);

            Route::get('customers/list', [
                'as' => 'customers.list',
                'uses' => 'CustomerController@getList',
                'permission' => 'car-rentals.customers.index',
            ]);

            Route::resource('vendors', 'VendorController')->parameters(['' => 'vendors'])->except(['create', 'store']);

            Route::get('vendors/{vendor}/view', [
                'as' => 'vendors.view',
                'uses' => 'VendorController@view',
                'permission' => 'car-rentals.vendors.view',
            ]);

            Route::post('vendors/{vendor}/verify', [
                'as' => 'vendors.verify',
                'uses' => 'VendorController@verify',
                'permission' => 'car-rentals.vendors.edit',
            ]);

            Route::post('vendors/{vendor}/unverify', [
                'as' => 'vendors.unverify',
                'uses' => 'VendorController@unverify',
                'permission' => 'car-rentals.vendors.edit',
            ]);

            Route::post('vendors/{vendor}/block', [
                'as' => 'vendors.block',
                'uses' => 'VendorBlockedController@store',
                'permission' => 'car-rentals.vendors.edit',
            ]);

            Route::post('vendors/{vendor}/unblock', [
                'as' => 'vendors.unblock',
                'uses' => 'VendorBlockedController@destroy',
                'permission' => 'car-rentals.vendors.edit',
            ]);

            Route::resource('taxes', 'TaxController')->parameters(['' => 'taxes']);
            Route::resource('bookings', 'BookingController')
                ->parameters(['' => 'bookings']);

            Route::group(['prefix' => 'bookings', 'as' => 'bookings.'], function (): void {
                Route::get('{booking}/print', [
                    'as' => 'print',
                    'uses' => 'BookingController@print',
                    'permission' => 'car-rentals.bookings.index',
                ]);
                Route::get('search-cars', [
                    'as' => 'search-cars',
                    'uses' => 'BookingController@searchCars',
                    'permission' => 'car-rentals.bookings.create',
                ]);

                Route::get('search-customers', [
                    'as' => 'search-customers',
                    'uses' => 'BookingController@searchCustomers',
                    'permission' => 'car-rentals.bookings.create',
                ]);

                Route::get('get-customer', [
                    'as' => 'get-customer',
                    'uses' => 'BookingController@getCustomer',
                    'permission' => 'car-rentals.bookings.create',
                ]);

                Route::post('create-customer', [
                    'as' => 'create-customer',
                    'uses' => 'BookingController@createCustomer',
                    'permission' => 'car-rentals.bookings.create',
                ]);

                Route::put('{booking}/completion', [
                    'as' => 'update-completion',
                    'uses' => 'BookingController@updateCompletion',
                    'permission' => 'car-rentals.bookings.edit',
                ]);
            });
            Route::resource('invoices', 'InvoiceController')
                ->parameters(['' => 'invoices'])
                ->except(['create', 'store']);
            Route::resource('cars', 'Cars\CarController')->parameters(['' => 'cars']);

            Route::group(['prefix' => 'cars', 'permission' => 'car-rentals.cars.edit'], function (): void {
                Route::post('{car}/approve', [CarController::class, 'approve'])->name('cars.approve')
                    ->wherePrimaryKey();
                Route::post('{car}/reject', [CarController::class, 'reject'])->name('cars.reject')
                    ->wherePrimaryKey();

                Route::get('{car}/pricing-calendar', [CarController::class, 'getCarPricing'])
                    ->name('cars.pricing-calendar')
                    ->wherePrimaryKey();
                Route::post('{car}/pricing-calendar', [CarController::class, 'storeCarPricing'])
                    ->name('cars.pricing-calendar.store')
                    ->wherePrimaryKey();
            });

            Route::resource('car-makes', 'Cars\CarMakeController')->parameters(['' => 'car-makes']);
            Route::resource('car-types', 'Cars\CarTypeController')->parameters(['' => 'car-types']);
            Route::resource('car-transmissions', 'Cars\CarTransmissionController')->parameters(
                ['' => 'car-transmissions']
            );
            Route::resource('car-fuels', 'Cars\CarFuelController')->parameters(['' => 'car-fuels']);
            Route::resource('car-tags', 'Cars\CarTagController')->parameters(['' => 'car-tags']);
            Route::resource('reviews', 'Cars\CarReviewController')->parameters(['' => 'reviews']);
            Route::resource('car-colors', 'Cars\CarColorController')->parameters(['' => 'car-colors']);
            Route::resource('car-amenity-categories', 'Cars\CarAmenityCategoryController')->parameters(['' => 'car-amenity-categories']);
            Route::resource('car-amenities', 'Cars\CarAmenityController')->parameters(['' => 'car-amenities']);
            Route::resource('car-maintenance-histories', 'Cars\CarMaintenanceHistoryController')->parameters(['' => 'car-maintenance-histories']);

            Route::group(['prefix' => 'car-categories', 'as' => 'car-categories.'], function (): void {
                Route::resource('', 'Cars\CarCategoryController')->parameters(['' => 'carCategory']);
                Route::put('update-tree', [
                    'as' => 'update-tree',
                    'uses' => 'Cars\CarCategoryController@updateTree',
                    'permission' => 'car-rentals.car-categories.index',
                ]);
            });
            Route::resource('services', 'ServiceController')->parameters(['' => 'services']);
            Route::resource('coupons', CouponController::class)->parameters(['' => 'coupons']);
            Route::group(['permission' => 'car-rentals.coupons.edit'], function (): void {
                Route::post('coupons/generate-coupon', [CouponController::class, 'postGenerateCoupon'])
                    ->name('coupons.generate-coupon');
            });

            Route::get('/booking-reports/bookings', [
                'uses' => 'BookingReportController@index',
                'as' => 'booking.reports.index',
            ]);

            Route::post('recent-bookings', [
                'as' => 'booking.reports.recent-bookings',
                'uses' => 'BookingReportController@getRecentBookings',
                'permission' => 'car-rentals.booking.reports.index',
            ]);

            Route::get('/booking-calendar', [
                'uses' => 'BookingCalendarController@index',
                'as' => 'booking.calendar.index',
                'permission' => 'car-rentals.booking.calendar.index',
            ]);

            Route::get('/booking-reports/records', [
                'uses' => 'BookingReportRecordController@index',
                'as' => 'booking.reports.records.index',
                'permission' => 'car-rentals.booking.reports.index',
            ]);

            // Car Availability Calendar Routes
            Route::get('/car-availability-calendar', [
                'uses' => 'CarAvailabilityCalendarController@index',
                'as' => 'car-availability.calendar.index',
                'permission' => 'car-rentals.cars.index',
            ]);

            Route::get('/car-availability/events', [
                'uses' => 'CarAvailabilityCalendarController@getCarAvailability',
                'as' => 'car-availability.events',
                'permission' => 'car-rentals.cars.index',
            ]);

            Route::get('/car-availability/status', [
                'uses' => 'CarAvailabilityCalendarController@getCarAvailabilityStatus',
                'as' => 'car-availability.status',
                'permission' => 'car-rentals.cars.index',
            ]);

            Route::get('/car-availability/booking-details', [
                'uses' => 'CarAvailabilityCalendarController@getBookingDetails',
                'as' => 'car-availability.booking-details',
                'permission' => 'car-rentals.bookings.index',
            ]);

            Route::group(['prefix' => 'messages', 'as' => 'message.'], function (): void {
                Route::resource('', MessageController::class)
                    ->parameters(['' => 'message'])
                    ->except(['create', 'store']);
            });
        });

        Route::group(
            [
                'prefix' => 'car-rentals/settings',
                'as' => 'car-rentals.settings.',
                'permission' => 'car-rentals.settings',
            ],
            function (): void {
                Route::get('general', [GeneralSettingController::class, 'edit'])->name('general');
                Route::put('general', [GeneralSettingController::class, 'update'])->name('general.update');

                Route::get('price-lock', [PriceLockSettingController::class, 'edit'])->name('price-lock');
                Route::put('price-lock', [PriceLockSettingController::class, 'update'])->name('price-lock.update');

                Route::get('customers', [CustomerSettingController::class, 'edit'])->name('customers');
                Route::put('customers', [CustomerSettingController::class, 'update'])->name('customers.update');

                Route::get('reviews', [ReviewSettingController::class, 'edit'])->name('reviews');
                Route::put('reviews', [ReviewSettingController::class, 'update'])->name('reviews.update');

                Route::get('car-filters', [CarFilterSettingController::class, 'edit'])->name('car-filters');
                Route::put('car-filters', [CarFilterSettingController::class, 'update'])->name('car-filters.update');

                Route::get('currencies', [CurrencySettingController::class, 'edit'])->name('currencies');
                Route::put('currencies', [CurrencySettingController::class, 'update'])->name('currencies.update');
                Route::post('currencies/update-from-exchange-api', [CurrencySettingController::class, 'updateCurrenciesFromExchangeApi'])->name('update-currencies-from-exchange-api');
                Route::post('currencies/clear-cache-rates', [CurrencySettingController::class, 'clearCacheCurrencyRates'])->name('clear-cache-currency-rates');

                Route::get('invoices', [InvoiceSettingController::class, 'edit'])->name('invoices');
                Route::put('invoices', [InvoiceSettingController::class, 'update'])->name('invoices.update');

                Route::get('invoice-template', [InvoiceTemplateSettingController::class, 'edit'])->name(
                    'invoice-template'
                );
                Route::put('invoice-template', [InvoiceTemplateSettingController::class, 'update'])->name(
                    'invoice-template.update'
                );
                Route::post('invoice-template/reset', [InvoiceTemplateSettingController::class, 'reset'])->name(
                    'invoice-template.reset'
                );
                Route::get('invoice-template/preview', [InvoiceTemplateSettingController::class, 'preview'])->name(
                    'invoice-template.preview'
                );

                Route::match(['GET', 'POST'], 'taxes', [TaxSettingController::class, 'edit'])->name('taxes');
                Route::put('taxes', [TaxSettingController::class, 'update'])->name('taxes.update');
            }
        );

        Route::group(['prefix' => 'invoices', 'as' => 'invoices.'], function (): void {
            Route::resource('', 'InvoiceController')->parameters(['' => 'invoice']);
            Route::get('{invoice}/generate-invoice', 'InvoiceController@getGenerateInvoice')
                ->name('generate')
                ->wherePrimaryKey();
        });
    });
});
