<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index(): JsonResponse
    {
        $banners = Banner::orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'data' => $banners,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'string', 'url'],
            'link_type' => ['required', 'in:category,product,store,url'],
            'link_value' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ]);

        $data['sort_order'] = Banner::max('sort_order') + 1;

        $banner = Banner::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Banner created.',
            'data' => $banner,
        ], 201);
    }

    public function update(Request $request, Banner $banner): JsonResponse
    {
        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['sometimes', 'string', 'url'],
            'link_type' => ['sometimes', 'in:category,product,store,url'],
            'link_value' => ['sometimes', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date'],
        ]);

        $banner->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Banner updated.',
            'data' => $banner->fresh(),
        ]);
    }

    public function destroy(Banner $banner): JsonResponse
    {
        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Banner deleted.',
        ]);
    }

    public function toggle(Banner $banner): JsonResponse
    {
        $banner->update(['is_active' => ! $banner->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Banner '.($banner->is_active ? 'activated' : 'deactivated').'.',
            'data' => $banner->fresh(),
        ]);
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:banners,id'],
        ]);

        foreach ($request->ids as $position => $id) {
            Banner::where('id', $id)->update(['sort_order' => $position]);
        }

        return response()->json(['success' => true, 'message' => 'Banners reordered.']);
    }
}
