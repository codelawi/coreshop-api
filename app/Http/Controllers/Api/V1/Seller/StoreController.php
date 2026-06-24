<?php

namespace App\Http\Controllers\Api\V1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\SellerStoreResource;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function show(): JsonResponse
    {
        $store = Auth::user()
            ->store()
            ->withCount(['products', 'orders as pending_orders_count' => fn ($q) => $q->where('status', 'pending')])
            ->first();

        return response()->json([
            'success' => true,
            'data' => $store ? new SellerStoreResource($store) : null,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user->store()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a store.',
            ], 422);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:stores,name'],
            'description' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'delivery_radius_km' => ['nullable', 'integer', 'min:1', 'max:100'],
            'logo' => ['nullable', 'string', 'max:500'],
            'banner' => ['nullable', 'string', 'max:500'],
            'working_hours' => ['nullable', 'array'],
        ]);

        $data['seller_id'] = $user->id;
        $data['slug'] = Str::slug($data['name']);

        if (isset($data['slug']) && Store::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $data['slug'].'-'.Str::random(4);
        }

        $store = Store::create($data);
        $store->loadCount(['products', 'orders as pending_orders_count' => fn ($q) => $q->where('status', 'pending')]);

        return response()->json([
            'success' => true,
            'message' => 'Store created successfully.',
            'data' => new SellerStoreResource($store),
        ], 201);
    }

    public function update(Request $request): JsonResponse
    {
        $store = Auth::user()->store;

        if (! $store) {
            return response()->json(['success' => false, 'message' => 'No store found.'], 404);
        }

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:100', 'unique:stores,name,'.$store->id],
            'description' => ['nullable', 'string', 'max:1000'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'delivery_radius_km' => ['nullable', 'integer', 'min:1', 'max:100'],
            'logo' => ['nullable', 'string', 'max:500'],
            'banner' => ['nullable', 'string', 'max:500'],
            'working_hours' => ['nullable', 'array'],
        ]);

        $store->update($data);
        $store->loadCount(['products', 'orders as pending_orders_count' => fn ($q) => $q->where('status', 'pending')]);

        return response()->json([
            'success' => true,
            'message' => 'Store updated successfully.',
            'data' => new SellerStoreResource($store),
        ]);
    }

    public function toggleOpen(): JsonResponse
    {
        $store = Auth::user()->store;

        if (! $store) {
            return response()->json(['success' => false, 'message' => 'No store found.'], 404);
        }

        $store->update(['is_open' => ! $store->is_open]);

        return response()->json([
            'success' => true,
            'message' => $store->is_open ? 'Store is now open.' : 'Store is now closed.',
            'data' => ['is_open' => $store->is_open],
        ]);
    }
}
