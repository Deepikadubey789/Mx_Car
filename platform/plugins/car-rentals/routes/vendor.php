<?php

use Botble\CarRentals\Http\Controllers\Vendor\MessageController;
use Botble\CarRentals\Http\Middleware\LocaleMiddleware;
use Illuminate\Support\Facades\Route;

Route::group([
    'namespace' => 'Botble\CarRentals\Http\Controllers\Vendor',
    'middleware' => ['web', 'core', 'vendor', LocaleMiddleware::class],
], function (): void {
    Route::group([
        'prefix' => config('plugins.car-rentals.general.vendor_panel_dir', 'vendor'),
        'as' => 'car-rentals.vendor.',
        'middleware' => ['vendor'],
    ], function (): void {

        require core_path('table/routes/web-actions.php');

        Route::group(['prefix' => 'ajax'], function (): void {
            Route::post('upload', [
                'as' => 'upload',
                'uses' => 'DashboardController@postUpload',
            ]);

            Route::post('upload-from-editor', [
                'as' => 'upload-from-editor',
                'uses' => 'DashboardController@postUploadFromEditor',
            ]);

            Route::group(['prefix' => 'chart', 'as' => 'chart.'], function (): void {
                Route::get('month', [
                    'as' => 'month',
                    'uses' => 'RevenueController@getMonthChart',
                ]);
            });
        });

        Route::get('dashboard', [
            'as' => 'dashboard',
            'uses' => 'DashboardController@index',
        ]);

        Route::resource('cars', 'CarController')->parameters(['' => 'car']);

        Route::get('car-availability-calendar', [
            'as' => 'car-availability.calendar',
            'uses' => 'DashboardController@carAvailabilityCalendar',
        ]);

        Route::get('car-availability/events', [
            'as' => 'car-availability.events',
            'uses' => 'DashboardController@getCarAvailabilityEvents',
        ]);

        Route::get('car-availability/booking-details', [
            'as' => 'car-availability.booking-details',
            'uses' => 'DashboardController@getBookingDetails',
        ]);

        Route::post('car-maintenance-histories', 'CarMaintenanceHistoryController@store')
            ->name('car-maintenance-histories.store');

        Route::get(
            'car-maintenance-histories/edit/{serviceHistory}',
            'CarMaintenanceHistoryController@edit'
        )->name('car-maintenance-histories.edit');

        Route::post(
            'car-maintenance-histories/edit/{serviceHistory}',
            'CarMaintenanceHistoryController@update'
        )->name('car-maintenance-histories.update');

        Route::delete(
            'car-maintenance-histories/delete/{serviceHistory}',
            'CarMaintenanceHistoryController@destroy'
        )->name('car-maintenance-histories.destroy');

        Route::resource('bookings', 'BookingController')
            ->parameters(['' => 'booking'])
            ->only(['index']);

        Route::get('bookings/{booking}', [
            'as' => 'bookings.show',
            'uses' => 'BookingController@show',
        ])->wherePrimaryKey('booking');

        Route::get('bookings/{booking}/print', [
            'as' => 'bookings.print',
            'uses' => 'BookingController@print',
        ])->wherePrimaryKey('booking');

        Route::post('bookings/{booking}/approve', [
            'as' => 'bookings.approve',
            'uses' => 'BookingController@approve',
        ])->wherePrimaryKey('booking');

        Route::post('bookings/{booking}/cancel', [
            'as' => 'bookings.cancel',
            'uses' => 'BookingController@cancel',
        ])->wherePrimaryKey('booking');

        Route::group(['prefix' => 'invoices', 'as' => 'invoices.'], function (): void {
            Route::get('{invoice}/generate-invoice', 'InvoiceController@getGenerateInvoice')
                ->name('generate')
                ->wherePrimaryKey();
        });

        Route::group(['prefix' => 'messages', 'as' => 'message.'], function (): void {
            Route::resource('', MessageController::class)
                ->parameters(['' => 'message'])
                ->except(['create', 'store']);
        });

        Route::get('settings', 'SettingController@index')->name('settings.index');
        Route::put('settings', 'SettingController@update')->name('settings.update');

        Route::resource('revenues', 'RevenueController')
            ->parameters(['' => 'revenue'])
            ->only(['index']);

        Route::get('statements', fn () => to_route('car-rentals.vendor.revenues.index'))
            ->name('statements.index');

        Route::resource('withdrawals', 'WithdrawalController')
            ->parameters(['' => 'withdrawal'])
            ->only([
                'index',
                'create',
                'store',
                'edit',
                'update',
            ]);

        Route::group(['prefix' => 'withdrawals'], function (): void {
            Route::get('show/{id}', [
                'as' => 'withdrawals.show',
                'uses' => 'WithdrawalController@show',
            ])->wherePrimaryKey();
        });

        Route::resource('reviews', 'ReviewController')
            ->parameters(['' => 'review'])
            ->only(['index', 'destroy']);
    });
});
