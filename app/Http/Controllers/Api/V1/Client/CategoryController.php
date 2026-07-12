<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Cache::remember('categories.index', now()->addMinutes(30), function () {
            return Category::active()
                ->roots()
                ->with(['children' => fn ($q) => $q->active()->orderBy('sort_order')])
                ->orderBy('sort_order')
                ->get();
        });

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function show(Category $category): JsonResponse
    {
        $data = Cache::remember("categories.show.{$category->id}", now()->addMinutes(30), function () use ($category) {
            $category->load(['children' => fn ($q) => $q->active()->orderBy('sort_order')]);

            return $category;
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
