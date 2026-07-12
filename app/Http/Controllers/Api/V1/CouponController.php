<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CouponResource;
use App\Models\Coupon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CouponController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $coupons = Coupon::when($request->active, fn ($q) => $q->where('active', $request->active === 'true'))
            ->latest()
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => CouponResource::collection($coupons),
            'meta' => [
                'total' => $coupons->total(),
                'page' => $coupons->currentPage(),
                'per_page' => $coupons->perPage(),
                'last_page' => $coupons->lastPage(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'unique:coupons,code'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['required', 'integer', 'min:1'],
            'active' => ['boolean'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $coupon = Coupon::create($data);

        Cache::forget('coupons.check.'.strtoupper($coupon->code));

        return response()->json([
            'success' => true,
            'message' => 'Coupon created successfully',
            'data' => new CouponResource($coupon),
        ], 201);
    }

    public function update(Request $request, Coupon $coupon): JsonResponse
    {
        $data = $request->validate([
            'code' => ['sometimes', 'string', 'unique:coupons,code,'.$coupon->id],
            'type' => ['sometimes', 'in:percentage,fixed'],
            'value' => ['sometimes', 'numeric', 'min:0'],
            'min_order_amount' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['sometimes', 'integer', 'min:1'],
            'active' => ['boolean'],
            'expires_at' => ['nullable', 'date'],
        ]);

        $oldCode = $coupon->code;
        $coupon->update($data);

        Cache::forget('coupons.check.'.strtoupper($oldCode));
        if (isset($data['code'])) {
            Cache::forget('coupons.check.'.strtoupper($coupon->code));
        }

        return response()->json([
            'success' => true,
            'message' => 'Coupon updated successfully',
            'data' => new CouponResource($coupon),
        ]);
    }

    public function destroy(Coupon $coupon): JsonResponse
    {
        $code = $coupon->code;
        $coupon->delete();

        Cache::forget('coupons.check.'.strtoupper($code));

        return response()->json([
            'success' => true,
            'message' => 'Coupon deleted successfully',
        ]);
    }
}
