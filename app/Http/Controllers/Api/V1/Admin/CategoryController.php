<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'is_active' => ['boolean'],
        ]);

        $slug = Str::slug($data['name']);
        if (Category::where('slug', $slug)->exists()) {
            $slug .= '-'.Str::random(4);
        }

        $category = Category::create([
            'name' => $data['name'],
            'slug' => $slug,
            'parent_id' => $data['parent_id'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'sort_order' => Category::where('parent_id', $data['parent_id'] ?? null)->max('sort_order') + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created.',
            'data' => $category,
        ], 201);
    }

    public function destroy(Category $category): JsonResponse
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted.',
        ]);
    }

    public function update(Request $request, Category $category): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'image' => ['sometimes', 'string', 'url'],
            'is_active' => ['boolean'],
        ]);

        $category->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Category updated.',
            'data' => $category->fresh(),
        ]);
    }
}
