<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index(): JsonResponse
    {
        $data = Cache::remember('home.index', now()->addMinutes(10), function () {
            $banners = Banner::active()
                ->orderBy('sort_order')
                ->limit(5)
                ->get(['id', 'title', 'subtitle', 'image', 'link_type', 'link_value']);

            $categories = Category::active()
                ->roots()
                ->orderBy('sort_order')
                ->limit(10)
                ->get(['id', 'name', 'name_ar', 'slug', 'image', 'icon']);

            $flashDeals = Product::approved()
                ->inStock()
                ->whereNotNull('original_price')
                ->whereColumn('price', '<', 'original_price')
                ->with(['productImages' => fn ($q) => $q->where('is_primary', true)])
                ->orderByDesc('sales_count')
                ->limit(10)
                ->get();

            $trending = Product::approved()
                ->inStock()
                ->with(['productImages' => fn ($q) => $q->where('is_primary', true)])
                ->orderByDesc('sales_count')
                ->limit(10)
                ->get();

            $featured = Product::approved()
                ->inStock()
                ->where('is_featured', true)
                ->with(['productImages' => fn ($q) => $q->where('is_primary', true)])
                ->limit(10)
                ->get();

            $topStores = Store::active()
                ->orderByDesc('rating')
                ->limit(8)
                ->get(['id', 'name', 'slug', 'logo', 'banner', 'rating', 'reviews_count', 'city']);

            return [
                'banners' => $banners->map(fn ($b) => [
                    'id' => $b->id,
                    'title' => $b->title,
                    'subtitle' => $b->subtitle,
                    'image' => $b->image,
                    'link_type' => $b->link_type,
                    'link_value' => $b->link_value,
                ])->all(),
                'categories' => $categories->map(fn ($c) => [
                    'id' => $c->id,
                    'name' => $c->name,
                    'name_ar' => $c->name_ar,
                    'slug' => $c->slug,
                    'image' => $c->image,
                    'icon' => $c->icon,
                ])->all(),
                'flash_deals' => $this->mapProducts($flashDeals),
                'trending' => $this->mapProducts($trending),
                'featured' => $this->mapProducts($featured),
                'top_stores' => $topStores->map(fn ($s) => [
                    'id' => $s->id,
                    'name' => $s->name,
                    'slug' => $s->slug,
                    'logo' => $s->logo,
                    'banner' => $s->banner,
                    'rating' => $s->rating,
                    'reviews_count' => $s->reviews_count,
                    'city' => $s->city,
                ])->all(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    private function mapProducts($products): array
    {
        return $products->map(function ($p) {
            $primary = $p->productImages->first();

            return [
                'id' => $p->id,
                'name' => $p->name,
                'slug' => $p->slug,
                'price' => $p->price,
                'original_price' => $p->original_price,
                'discount_percent' => $p->discount_percent,
                'rating' => $p->rating,
                'reviews_count' => $p->reviews_count,
                'sales_count' => $p->sales_count,
                'image' => $primary?->url,
                'store_id' => $p->store_id,
            ];
        })->all();
    }
}
