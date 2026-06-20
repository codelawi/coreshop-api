<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use App\Models\Store;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->client_id === Auth::id(), 403);

        if (! in_array($order->status, ['delivered', 'completed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Order must be delivered before you can leave a review.',
            ], 422);
        }

        if (Review::where('order_id', $order->id)->where('user_id', Auth::id())->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this order.',
            ], 422);
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Review::create([
            'user_id' => Auth::id(),
            'store_id' => $order->store_id,
            'order_id' => $order->id,
            'rating' => $data['rating'],
            'comment' => $data['comment'] ?? null,
        ]);

        $this->recalculateStoreRating($order->store_id);

        return response()->json(['success' => true, 'message' => 'Review submitted.']);
    }

    public function show(Order $order): JsonResponse
    {
        abort_unless($order->client_id === Auth::id(), 403);

        $review = Review::where('order_id', $order->id)
            ->where('user_id', Auth::id())
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'reviewed' => (bool) $review,
                'rating' => $review?->rating,
                'comment' => $review?->comment,
            ],
        ]);
    }

    private function recalculateStoreRating(int $storeId): void
    {
        $avg = Review::where('store_id', $storeId)->avg('rating');
        $count = Review::where('store_id', $storeId)->count();

        Store::where('id', $storeId)->update([
            'rating' => round((float) $avg, 2),
            'reviews_count' => $count,
        ]);
    }
}
