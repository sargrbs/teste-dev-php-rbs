<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'welcome' => 'API Supplier',
        'timestamp' => now()->toDateTimeString(),
    ]);
});
