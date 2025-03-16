<?php

use App\Http\Controllers\Api\LocaleController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TranslationController;


//Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('locales', LocaleController::class);
    Route::get('translations/export', [TranslationController::class, 'export']);
    Route::get('translations/search', [TranslationController::class, 'search']);
    Route::apiResource('translations', TranslationController::class);

//});
