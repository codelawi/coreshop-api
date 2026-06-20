<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Store::active();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('lat') && $request->filled('lng')) {
            $lat = $request->lat;
            $lng = $request->lng;
            $query->selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance_km", [$lat, $lng, $lat])
                ->orderBy('distance_km');
        } else {
            $query->orderByDesc('rating');
        }

        $stores = $query->paginate($request->get('per_page', 20));

        return response()->json([
            'success' => true,
            'data' => $stores->items(),
            'meta' => [
                'current_page' => $stores->currentPage(),
                'last_page' => $stores->lastPage(),
                'per_page' => $stores->perPage(),
                'total' => $stores->total(),
            ],
        ]);
    }

    public function show(Store $store): JsonResponse
    {
        $store->load(['products' => fn($q) => $q->approved()->inStock()
            ->with(['productImages' => fn($q) => $q->where('is_primary', true)])
            ->limit(40)]);

        $data = $store->toArray();
        $data['products'] = $store->products->map(fn($p) => [
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
        ])->all();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}