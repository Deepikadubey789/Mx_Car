<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ChatSettingController;

Route::middleware(['web', 'auth'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::prefix('chat-settings')->name('chat-settings.')->group(function (): void {
        Route::get('/', [ChatSettingController::class, 'index'])->name('index');
        Route::put('{setting}', [ChatSettingController::class, 'update'])->name('update');
        Route::post('bulk-update', [ChatSettingController::class, 'bulkUpdate'])->name('bulk-update');
        Route::post('update-questions', [ChatSettingController::class, 'updateQuestions'])->name('update-questions');
        Route::get('preview-prompt', [ChatSettingController::class, 'previewPrompt'])->name('preview-prompt');
        Route::post('reset', [ChatSettingController::class, 'reset'])->name('reset');
    });
});
