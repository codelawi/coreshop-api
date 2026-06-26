<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function payment(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'platform_fee_percentage' => (float) Setting::get('platform_fee_percentage', 10),
                'delivery_fee_per_km' => (float) Setting::get('delivery_fee_per_km', 0.3),
                'delivery_fee_minimum' => (float) Setting::get('delivery_fee_minimum', 1.0),
            ],
        ]);
    }

    public function updatePayment(Request $request): JsonResponse
    {
        $data = $request->validate([
            'platform_fee_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'delivery_fee_per_km' => ['required', 'numeric', 'min:0'],
            'delivery_fee_minimum' => ['required', 'numeric', 'min:0'],
        ]);

        Setting::set('platform_fee_percentage', $data['platform_fee_percentage']);
        Setting::set('delivery_fee_per_km', $data['delivery_fee_per_km']);
        Setting::set('delivery_fee_minimum', $data['delivery_fee_minimum']);

        return response()->json([
            'success' => true,
            'message' => 'Payment settings updated.',
            'data' => [
                'platform_fee_percentage' => (float) $data['platform_fee_percentage'],
                'delivery_fee_per_km' => (float) $data['delivery_fee_per_km'],
                'delivery_fee_minimum' => (float) $data['delivery_fee_minimum'],
            ],
        ]);
    }
}
