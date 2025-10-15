<?php

use Illuminate\Support\Facades\Route;
use Mbsoft\BanquetHallManager\Http\Controllers\ClientController;
use Mbsoft\BanquetHallManager\Http\Controllers\EventController;
use Mbsoft\BanquetHallManager\Http\Controllers\BookingController;
use Mbsoft\BanquetHallManager\Http\Controllers\InvoiceController;

Route::prefix('api/bhm')
    ->middleware('auth')
    ->group(function () {
    Route::get('health', function () {
        $version = 'dev';
        try {
            $composer = json_decode(file_get_contents(__DIR__.'/../../composer.json'), true, 512, JSON_THROW_ON_ERROR);
            $version = $composer['version'] ?? ($composer['extra']['branch-alias']['dev-main'] ?? 'dev');
        } catch (\Throwable) {
            // ignore
        }
        return response()->json(['status' => 'ok', 'package' => 'mbsoft/banquet-hall-manager', 'version' => $version]);
    });

    // CRUD endpoints
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('events', EventController::class);
    Route::apiResource('bookings', BookingController::class);
    Route::apiResource('invoices', InvoiceController::class)->only(['index','show','update']);
    Route::post('events/{event}/invoice', [InvoiceController::class, 'storeFromEvent']);
});
