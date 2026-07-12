<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min((int) $request->get('per_page', 20), 100);
        $version = Cache::get('products.version', 1);
        $key = 'products.index.v'.$version.'.'.md5(json_encode($request->all()));

        $products = Cache::remember($key, now()->addMinutes(5), function () use ($request, $perPage) {
            $query = Product::approved()
                ->inStock()
                ->with(['productImages' => fn ($q) => $q->where('is_primary', true)]);

            if ($request->filled('category_id')) {
                $query->where('category_id', $request->category_id);
            }

            if ($request->filled('store_id')) {
                $query->where('store_id', $request->store_id);
            }

            if ($request->filled('search')) {
                $query->where('name', 'like', '%'.$request->search.'%');
            }

            if ($request->filled('min_price')) {
                $query->where('price', '>=', $request->min_price);
            }

            if ($request->filled('max_price')) {
                $query->where('price', '<=', $request->max_price);
            }

            $sort = $request->get('sort', 'newest');
            match ($sort) {
                'price_low' => $query->orderBy('price'),
                'price_high' => $query->orderByDesc('price'),
                'rating' => $query->orderByDesc('rating'),
                'popular' => $query->orderByDesc('sales_count'),
                default => $query->latest(),
            };

            return $query->paginate($perPage);
        });

        return response()->json([
            'success' => true,
            'data' => $products->getCollection()->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => $p->price,
                'original_price' => $p->original_price,
                'discount_percent' => $p->discount_percent,
                'rating' => $p->rating,
                'reviews_count' => $p->reviews_count,
                'sales_count' => $p->sales_count,
                'image' => $p->productImages->first()?->url,
                'store_id' => $p->store_id,
            ])->all(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function show(Product $product): JsonResponse
    {
        if ($product->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Product not available',
            ], 404);
        }

        // Always increment views — do not cache this side effect
        $product->increment('views_count');

        $data = Cache::remember("products.show.{$product->id}", now()->addMinutes(10), function () use ($product) {
            $product->load([
                'productImages',
                'variants',
                'category',
                'store:id,name,slug,logo,rating,reviews_count,city',
                'reviews' => fn ($q) => $q->latest()->limit(5),
                'reviews.user:id,name,avatar',
            ]);

            $result = $product->toArray();
            $result['discount_percent'] = $product->discount_percent;

            return $result;
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
