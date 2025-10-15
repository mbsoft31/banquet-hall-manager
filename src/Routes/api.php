<?php

use Illuminate\Support\Facades\Route;

Route::prefix('api/bhm')->group(function () {
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
});

