<?php

namespace App\Http\Controllers\Api\V1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\SellerProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $store = Auth::user()->store;

        if (!$store) {
            return response()->json(['success' => false, 'message' => 'No store found.'], 404);
        }

        $products = Product::where('store_id', $store->id)
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

    public function show(Product $product): JsonResponse
    {
        $this->authorizeProduct($product);

        $product->load(['category', 'productImages', 'variants']);

        return response()->json([
            'success' => true,
            'data' => new SellerProductResource($product),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $store = Auth::user()->store;

        if (!$store) {
            return response()->json(['success' => false, 'message' => 'No store found.'], 404);
        }

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
            'variants.*.sku' => ['nullable', 'string', 'max:100'],
            'variants.*.price_adjustment' => ['nullable', 'numeric'],
            'variants.*.stock' => ['required_with:variants', 'integer', 'min:0'],
        ]);

        $product = DB::transaction(function () use ($data, $store) {
            $slug = Str::slug($data['name']);
            if (Product::where('slug', $slug)->exists()) {
                $slug .= '-' . Str::random(4);
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
                'status' => 'pending_review',
            ]);

            $images = $data['images'] ?? [];
            foreach ($images as $i => $url) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'url' => $url,
                    'sort_order' => $i,
                    'is_primary' => $i === 0,
                ]);
            }

            $variants = $data['variants'] ?? [];
            foreach ($variants as $variant) {
                ProductVariant::create([
                    'product_id' => $product->id,
                    'size' => $variant['size'] ?? null,
                    'color' => $variant['color'] ?? null,
                    'color_hex' => $variant['color_hex'] ?? null,
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
            'message' => 'Product submitted for review.',
            'data' => new SellerProductResource($product),
        ], 201);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $this->authorizeProduct($product);

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category_id' => ['sometimes', 'exists:categories,id'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'weight_grams' => ['nullable', 'integer', 'min:0'],
        ]);

        $product->update($data);
        $product->load(['category', 'productImages', 'variants']);

        return response()->json([
            'success' => true,
            'message' => 'Product updated.',
            'data' => new SellerProductResource($product),
        ]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->authorizeProduct($product);

        $product->delete();

        return response()->json(['success' => true, 'message' => 'Product deleted.']);
    }

    private function authorizeProduct(Product $product): void
    {
        $store = Auth::user()->store;

        abort_unless($store && $product->store_id === $store->id, 403, 'Unauthorized.');
    }
}
