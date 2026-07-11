<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/debug-ip', function () {
    return response()->json([
        'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? null,
        'laravel_ip' => request()->ip(),
        'cf_connecting_ip' => request()->header('CF-Connecting-IP'),
        'x_forwarded_for' => request()->header('X-Forwarded-For'),
        'x_forwarded_proto' => request()->header('X-Forwarded-Proto'),
        'all_headers' => collect(request()->headers->all())
            ->map(fn ($v) => $v[0])
            ->toArray(),
    ]);
});
