<?php

use Botble\Base\Http\Middleware\RequiresJsonRequestMiddleware;
use Botble\Theme\Facades\Theme;
use Illuminate\Support\Facades\Route;
use Theme\Carento\Http\Controllers\CarentoController;

// Custom routes
// You can delete this route group if you don't need to add your custom routes.
Route::group(['controller' => CarentoController::class, 'middleware' => ['web', 'core']], function (): void {
    Route::group(apply_filters(BASE_FILTER_GROUP_PUBLIC_ROUTE, []), function (): void {

        // Add your custom route here
        // Ex: Route::get('hello', 'getHello');

        // ========================================================
        // FIX: NEW FAQ CATEGORY ROUTE
        // ========================================================
        Route::get('faq-category/{id}', function ($id) {
            // 1. Find the category and its associated FAQs by ID
            $category = \Botble\Faq\Models\FaqCategory::with('faqs')->findOrFail($id);
            
            // 2. Render the custom view file we created!
            return Theme::scope('faq-category', [
                'category' => $category, 
                'categoryFaqs' => $category->faqs
            ])->render();
            
        })->name('public.faq-category');
        // ========================================================

        Route::post('calculate-loan-car', [CarentoController::class, 'calculateLoanCar'])->name('public.calculate-loan-car');

        Route::group(['prefix' => 'ajax', 'as' => 'public.ajax.', 'middleware' => [RequiresJsonRequestMiddleware::class]], function (): void {
            Route::get('search-popular-vehicles', 'ajaxSearchPopularVehicles')
                ->name('search-popular-vehicles');
            Route::get('cities', 'ajaxSearchCities')
                ->name('cities');
        });
    });
});

Theme::routes();