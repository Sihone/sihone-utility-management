<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApartmentController;
use App\Http\Controllers\Api\MeterReadingController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\PaymentsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// apartments
Route::apiResource('apartments', ApartmentController::class);

// meter readings
Route::apiResource('meter-readings', MeterReadingController::class);

// invoices
Route::apiResource('invoices', InvoiceController::class);

// settings
Route::get('/settings', [SettingsController::class, 'index']);
Route::post('/settings', [SettingsController::class, 'update']);

// payments
Route::apiResource('payments', PaymentsController::class);

