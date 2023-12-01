<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::apiResource('invoices', InvoiceController::class)
        ->only(['index', 'store']);

    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])
        ->middleware('can:view,invoice')
        ->name('invoices.show');

    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy'])
        ->middleware('can:delete,invoice')
        ->name('invoices.destroy');
});
