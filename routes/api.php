<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Chat routes - user_id is sent from frontend
Route::post('/chat/send', [\App\Http\Controllers\Api\ChatController::class, 'send'])
    ->middleware('throttle:50,1440')
    ->name('chat.send');

Route::get('/chat/history', [\App\Http\Controllers\Api\ChatController::class, 'history'])
    ->middleware('throttle:1000,1440')
    ->name('chat.history');

Route::get('/chat/suggested-questions', [\App\Http\Controllers\Api\ChatController::class, 'suggestedQuestions'])
    ->name('chat.suggested-questions');
