<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Resources\SellerProductResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Store;
use App\Services\ExpoPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $stores = Store::with('seller')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->withCount(['products', 'orders'])
            ->withSum(['orders as total_revenue' => fn ($q) => $q->whereIn('status', ['delivered', 'completed'])], 'total')
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
                'total_revenue' => (float) ($store->total_revenue ?? 0),
                'seller' => $store->seller ? [
                    'id' => $store->seller->id,
                    'name' => $store->seller->name,
                    'email' => $store->seller->email,
                ] : null,
                'created_at' => $store->created_at->toDateString(),
            ]),
            'meta' => [
                'current_page' => $stores->currentPage(),
                'last_page' => $stores->lastPage(),
                'total' => $stores->total(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'seller_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $slug = Str::slug($data['name']);
        if (Store::where('slug', $slug)->exists()) {
            $slug .= '-'.Str::random(4);
        }

        $store = Store::create([
            'seller_id' => $data['seller_id'],
            'name' => $data['name'],
            'slug' => $slug,
            'description' => $data['description'] ?? null,
            'phone' => $data['phone'] ?? null,
            'city' => $data['city'] ?? null,
            'address' => $data['address'] ?? null,
            'status' => 'pending',
        ]);

        $store->load('seller');

        return response()->json([
            'success' => true,
            'message' => 'Store created.',
            'data' => [
                'id' => $store->id,
                'name' => $store->name,
                'slug' => $store->slug,
                'city' => $store->city,
                'status' => $store->status,
                'is_open' => $store->is_open,
                'rating' => null,
                'sales_count' => 0,
                'products_count' => 0,
                'orders_count' => 0,
                'seller' => $store->seller ? [
                    'id' => $store->seller->id,
                    'name' => $store->seller->name,
                    'email' => $store->seller->email,
                ] : null,
                'created_at' => $store->created_at->toDateString(),
            ],
        ], 201);
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
                'seller' => $store->seller ? [
                    'id' => $store->seller->id,
                    'name' => $store->seller->name,
                    'email' => $store->seller->email,
                ] : null,
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

    public function createProduct(Request $request, Store $store): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'weight_grams' => ['nullable', 'integer', 'min:0'],
            'images' => ['nullable', 'array', 'max:7'],
            'images.*' => ['string', 'max:500'],
            'variants' => ['nullable', 'array'],
            'variants.*.size' => ['nullable', 'string', 'max:50'],
            'variants.*.color' => ['nullable', 'string', 'max:50'],
            'variants.*.color_hex' => ['nullable', 'string', 'max:7'],
            'variants.*.description' => ['nullable', 'string', 'max:255'],
            'variants.*.sku' => ['nullable', 'string', 'max:100'],
            'variants.*.price_adjustment' => ['nullable', 'numeric'],
            'variants.*.stock' => ['required_with:variants', 'integer', 'min:0'],
        ]);

        $product = DB::transaction(function () use ($data, $store) {
            $slug = Str::slug($data['name']);
            if (Product::where('slug', $slug)->exists()) {
                $slug .= '-'.Str::random(4);
            }

            $product = Product::create([
                'seller_id' => $store->seller_id,
                'store_id' => $store->id,
                'category_id' => $data['category_id'],
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'original_price' => $data['original_price'] ?? null,
                'stock' => $data['stock'],
                'weight_grams' => $data['weight_grams'] ?? null,
                'status' => 'approved',
            ]);

            foreach ($data['images'] ?? [] as $i => $url) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'url' => $url,
                    'sort_order' => $i,
                    'is_primary' => $i === 0,
                ]);
            }

            foreach ($data['variants'] ?? [] as $variant) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'size' => $variant['size'] ?? null,
                    'color' => $variant['color'] ?? null,
                    'color_hex' => $variant['color_hex'] ?? null,
                    'description' => $variant['description'] ?? null,
                    'sku' => $variant['sku'] ?? null,
                    'price_adjustment' => $variant['price_adjustment'] ?? 0,
                    'stock' => $variant['stock'],
                    'is_active' => true,
                ]);
            }

            return $product;
        });

        $product->load(['category', 'productImages', 'variants']);

        return response()->json([
            'success' => true,
            'message' => 'Product created and approved.',
            'data' => new SellerProductResource($product),
        ], 201);
    }
}
