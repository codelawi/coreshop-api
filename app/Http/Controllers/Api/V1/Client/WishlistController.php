<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $products = $user->wishlist()
            ->with(['productImages' => fn ($q) => $q->where('is_primary', true)->orWhere('sort_order', 0)->limit(1)])
            ->latest('wishlists.created_at')
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'original_price' => $product->original_price,
                'discount_percent' => $product->original_price
                    ? (int) round((1 - $product->price / $product->original_price) * 100)
                    : null,
                'rating' => $product->rating,
                'reviews_count' => $product->reviews_count,
                'sales_count' => $product->sales_count,
                'image' => $product->productImages->first()?->url,
                'store_id' => $product->store_id,
            ]);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }

    public function ids(): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $ids = $user->wishlist()->pluck('products.id');

        return response()->json([
            'success' => true,
            'data' => $ids,
        ]);
    }

    public function toggle(Product $product): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $isWishlisted = $user->wishlist()->where('product_id', $product->id)->exists();

        if ($isWishlisted) {
            $user->wishlist()->detach($product->id);
        } else {
            $user->wishlist()->attach($product->id);
        }

        return response()->json([
            'success' => true,
            'data' => ['wishlisted' => ! $isWishlisted],
        ]);
    }
}
