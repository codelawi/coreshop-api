<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\SellerProductResource;
use App\Models\Order;
use App\Models\Store;
use App\Services\ExpoPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $stores = Store::with('seller')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->withCount(['products', 'orders'])
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => $stores->map(fn ($store) => [
                'id' => $store->id,
                'name' => $store->name,
                'logo' => $store->logo,
                'city' => $store->city,
                'status' => $store->status,
                'is_open' => $store->is_open,
                'rating' => $store->rating,
                'sales_count' => $store->sales_count,
                'products_count' => $store->products_count,
                'orders_count' => $store->orders_count,
                'seller' => [
                    'id' => $store->seller->id,
                    'name' => $store->seller->name,
                    'email' => $store->seller->email,
                ],
                'created_at' => $store->created_at->toDateString(),
            ]),
            'meta' => [
                'current_page' => $stores->currentPage(),
                'last_page' => $stores->lastPage(),
                'total' => $stores->total(),
            ],
        ]);
    }

    public function show(Store $store): JsonResponse
    {
        $store->load('seller');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $store->id,
                'name' => $store->name,
                'slug' => $store->slug,
                'logo' => $store->logo,
                'banner' => $store->banner,
                'description' => $store->description,
                'phone' => $store->phone,
                'address' => $store->address,
                'city' => $store->city,
                'status' => $store->status,
                'is_open' => $store->is_open,
                'rating' => $store->rating,
                'reviews_count' => $store->reviews_count,
                'sales_count' => $store->sales_count,
                'delivery_radius_km' => $store->delivery_radius_km,
                'seller' => [
                    'id' => $store->seller->id,
                    'name' => $store->seller->name,
                    'email' => $store->seller->email,
                ],
                'products_count' => $store->products()->count(),
                'orders_count' => $store->orders()->count(),
                'total_revenue' => $store->orders()->whereIn('status', ['delivered', 'completed'])->sum('total'),
                'created_at' => $store->created_at->toDateString(),
            ],
        ]);
    }

    public function orders(Request $request, Store $store): JsonResponse
    {
        $orders = Order::where('store_id', $store->id)
            ->with(['client', 'items'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function products(Request $request, Store $store): JsonResponse
    {
        $products = $store->products()
            ->with(['category', 'productImages'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => SellerProductResource::collection($products),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function updateStatus(Request $request, Store $store): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:pending,active,suspended,closed'],
        ]);

        $store->update(['status' => $request->status]);

        $store->load('seller');

        $messages = [
            'active' => [
                'title' => 'Store Approved!',
                'body' => "Your store \"{$store->name}\" is now active and visible to customers.",
            ],
            'suspended' => [
                'title' => 'Store Suspended',
                'body' => "Your store \"{$store->name}\" has been suspended. Please contact support.",
            ],
            'closed' => [
                'title' => 'Store Closed',
                'body' => "Your store \"{$store->name}\" has been closed by the admin.",
            ],
            'pending' => [
                'title' => 'Store Under Review',
                'body' => "Your store \"{$store->name}\" is currently under review.",
            ],
        ];

        if ($store->seller && isset($messages[$request->status])) {
            app(ExpoPushService::class)->sendToUser(
                $store->seller,
                $messages[$request->status]['title'],
                $messages[$request->status]['body'],
                ['type' => 'store_status', 'store_id' => $store->id, 'status' => $request->status]
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Store status updated.',
        ]);
    }
}
