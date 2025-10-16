<?php

use Illuminate\Support\Facades\Route;
use Mbsoft\BanquetHallManager\Http\Controllers\ClientController;
use Mbsoft\BanquetHallManager\Http\Controllers\EventController;
use Mbsoft\BanquetHallManager\Http\Controllers\BookingController;
use Mbsoft\BanquetHallManager\Http\Controllers\InvoiceController;
use Mbsoft\BanquetHallManager\Http\Controllers\ServiceTypeController;
use Mbsoft\BanquetHallManager\Http\Controllers\PaymentController;
use Mbsoft\BanquetHallManager\Http\Controllers\HallController;
use Mbsoft\BanquetHallManager\Http\Controllers\StaffController;
use Mbsoft\BanquetHallManager\Http\Controllers\AnalyticsController;

Route::prefix('api/bhm')
    ->middleware(['auth','bhm.tenant'])
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
    Route::patch('events/{event}/reschedule', [EventController::class, 'reschedule']);
    Route::post('events/{event}/cancel', [EventController::class, 'cancel']);
    Route::apiResource('bookings', BookingController::class);
    Route::apiResource('invoices', InvoiceController::class)->only(['index','show','update']);
    Route::post('events/{event}/invoice', [InvoiceController::class, 'storeFromEvent']);
    Route::get('invoices/{invoice}/balance', [InvoiceController::class, 'balance']);
    Route::apiResource('services', ServiceTypeController::class);
    Route::apiResource('payments', PaymentController::class)->only(['index','show','store']);
    Route::apiResource('halls', HallController::class);
    Route::apiResource('staff', StaffController::class);
    Route::post('events/{event}/staff/{staff}', [StaffController::class, 'attachToEvent']);
    Route::delete('events/{event}/staff/{staff}', [StaffController::class, 'detachFromEvent']);
    Route::get('analytics/revenue', [AnalyticsController::class, 'revenue']);
});
