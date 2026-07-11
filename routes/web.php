<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/debug-ip', function () {
    return response()->json([
        'laravel_ip' => request()->ip(),
        'cf_connecting_ip' => request()->header('CF-Connecting-IP'),
        'x_forwarded_for' => request()->header('X-Forwarded-For'),
        'x_forwarded_proto' => request()->header('X-Forwarded-Proto'),
    ]);
});
