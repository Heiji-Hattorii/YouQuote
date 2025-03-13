<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuoteController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('quote/rand/{nombre}', [QuoteController::class, 'rand']);
Route::get('quote/filter/{min}/{max}', [QuoteController::class, 'filter']);
Route::get('quote/popularity', [QuoteController::class, 'popularity']);
Route::apiResource("quote", QuoteController::class);
