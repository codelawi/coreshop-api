<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $products = Product::with(['seller', 'category', 'store'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%"))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return ProductResource::collection($products);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load(['seller', 'category', 'productImages', 'variants', 'store']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'price' => $product->price,
                'original_price' => $product->original_price,
                'stock' => $product->stock,
                'weight_grams' => $product->weight_grams,
                'status' => $product->status,
                'is_featured' => $product->is_featured,
                'rating' => $product->rating,
                'reviews_count' => $product->reviews_count,
                'sales_count' => $product->sales_count,
                'views_count' => $product->views_count,
                'created_at' => $product->created_at->toDateString(),
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name,
                ] : null,
                'seller' => $product->seller ? [
                    'id' => $product->seller->id,
                    'name' => $product->seller->name,
                    'email' => $product->seller->email,
                ] : null,
                'store' => $product->store ? [
                    'id' => $product->store->id,
                    'name' => $product->store->name,
                ] : null,
                'images' => $product->productImages->map(fn ($img) => [
                    'id' => $img->id,
                    'url' => $img->url,
                    'is_primary' => $img->is_primary,
                ]),
                'variants' => $product->variants->map(fn ($v) => [
                    'id' => $v->id,
                    'size' => $v->size,
                    'color' => $v->color,
                    'color_hex' => $v->color_hex,
                    'description' => $v->description,
                    'sku' => $v->sku,
                    'price_adjustment' => $v->price_adjustment,
                    'stock' => $v->stock,
                    'is_active' => $v->is_active,
                ]),
            ],
        ]);
    }

    public function updateStatus(Request $request, Product $product): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:pending_review,approved,flagged,removed'],
        ]);

        $product->update(['status' => $request->status]);

        Cache::increment('products.version');
        Cache::forget("products.show.{$product->id}");
        Cache::forget('home.index');

        return response()->json([
            'success' => true,
            'message' => 'Product status updated successfully',
            'data' => new ProductResource($product->load(['seller', 'category'])),
        ]);
    }
}
