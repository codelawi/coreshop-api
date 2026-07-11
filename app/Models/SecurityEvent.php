<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class SecurityEvent extends Model
{
    const UPDATED_AT = null;

    protected $fillable = [
        'type',
        'ip_address',
        'country',
        'path',
        'method',
        'user_agent',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public static function log(string $type, Request $request, array $details = []): void
    {
        try {
            static::create([
                'type' => $type,
                'ip_address' => $request->ip(),
                'country' => $request->header('CF-IPCountry'),
                'path' => $request->path(),
                'method' => $request->method(),
                'user_agent' => $request->userAgent(),
                'details' => $details ?: null,
            ]);
        } catch (\Throwable) {
            // Never let logging break the request
        }
    }
}
