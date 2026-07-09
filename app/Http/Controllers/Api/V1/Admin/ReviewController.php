<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $reviews = Review::with(['user', 'product', 'store'])
            ->when($request->search, fn ($q) => $q->where('comment', 'like', "%{$request->search}%"))
            ->when($request->rating, fn ($q) => $q->where('rating', $request->rating))
            ->when($request->type, function ($q) use ($request) {
                if ($request->type === 'product') {
                    return $q->whereNotNull('product_id');
                }

                if ($request->type === 'store') {
                    return $q->whereNotNull('store_id')->whereNull('product_id');
                }
            })
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => $reviews->map(fn ($r) => [
                'id' => $r->id,
                'rating' => $r->rating,
                'comment' => $r->comment,
                'type' => $r->product_id ? 'product' : 'store',
                'reviewer' => $r->user ? [
                    'id' => $r->user->id,
                    'name' => $r->user->name,
                    'email' => $r->user->email,
                ] : null,
                'product' => $r->product ? [
                    'id' => $r->product->id,
                    'name' => $r->product->name,
                ] : null,
                'store' => $r->store ? [
                    'id' => $r->store->id,
                    'name' => $r->store->name,
                ] : null,
                'created_at' => $r->created_at->toDateString(),
            ]),
            'meta' => [
                'total' => $reviews->total(),
                'per_page' => $reviews->perPage(),
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
            ],
        ]);
    }

    public function destroy(Review $review): JsonResponse
    {
        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully.',
        ]);
    }
}
