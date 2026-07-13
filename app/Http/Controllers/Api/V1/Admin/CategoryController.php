<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'is_active' => ['boolean'],
        ]);

        $base = Str::slug($data['name']);
        $slug = $base;
        $i = 1;
        while (Category::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        $category = Category::create([
            'name' => $data['name'],
            'name_ar' => $data['name_ar'] ?? null,
            'slug' => $slug,
            'parent_id' => $data['parent_id'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'sort_order' => Category::where('parent_id', $data['parent_id'] ?? null)->max('sort_order') + 1,
        ]);

        Cache::forget('categories.index');

        return response()->json([
            'success' => true,
            'message' => 'Category created.',
            'data' => $category,
        ], 201);
    }

    public function destroy(Category $category): JsonResponse
    {
        Cache::forget('categories.index');
        Cache::forget("categories.show.{$category->id}");

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted.',
        ]);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255', 'unique:categories,name,'.$category->id],
            'name_ar' => ['nullable', 'string', 'max:255'],
            'image' => ['sometimes', 'string', 'url'],
            'is_active' => ['boolean'],
        ]);

        $category->update($data);

        Cache::forget('categories.index');
        Cache::forget("categories.show.{$category->id}");

        return response()->json([
            'success' => true,
            'message' => 'Category updated.',
            'data' => $category->fresh(),
        ]);
    }
}
