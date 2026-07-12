<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CouponController extends Controller
{
    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
            'subtotal' => ['required', 'numeric', 'min:0'],
        ]);

        $coupon = Cache::remember(
            'coupons.check.'.strtoupper($request->code),
            now()->addMinutes(2),
            fn () => Coupon::where('code', $request->code)->first()
        );

        if (! $coupon || ! $coupon->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired coupon code.',
            ], 422);
        }

        $subtotal = (float) $request->subtotal;

        if ($subtotal < (float) $coupon->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => "Minimum order of JOD {$coupon->min_order_amount} required.",
            ], 422);
        }

        $discount = $coupon->type === 'percentage'
            ? round($subtotal * ((float) $coupon->value / 100), 2)
            : min((float) $coupon->value, $subtotal);

        $label = $coupon->type === 'percentage'
            ? "{$coupon->value}% off"
            : "JOD {$coupon->value} off";

        return response()->json([
            'success' => true,
            'data' => [
                'code' => $coupon->code,
                'discount' => $discount,
                'label' => $label,
            ],
        ]);
    }
}
