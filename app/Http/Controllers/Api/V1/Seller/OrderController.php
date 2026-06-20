<?php

namespace App\Http\Controllers\Api\V1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\SellerOrderResource;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    private const ALLOWED_TRANSITIONS = [
        'pending' => 'approved',
        'approved' => 'preparing',
        'preparing' => 'ready_for_pickup',
    ];

    public function index(Request $request): JsonResponse
    {
        $store = Auth::user()->store;

        if (!$store) {
            return response()->json(['success' => false, 'message' => 'No store found.'], 404);
        }

        $orders = Order::where('store_id', $store->id)
            ->with(['client', 'address'])
            ->withCount('items')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => SellerOrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorizeOrder($order);

        $order->load(['client', 'address', 'items', 'coupon']);

        return response()->json([
            'success' => true,
            'data' => new SellerOrderResource($order),
        ]);
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $this->authorizeOrder($order);

        $allowed = self::ALLOWED_TRANSITIONS[$order->status] ?? null;

        if (!$allowed) {
            return response()->json([
                'success' => false,
                'message' => 'This order cannot be advanced further by the seller.',
            ], 422);
        }

        $request->validate([
            'status' => ['required', 'in:' . $allowed],
        ]);

        $order->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated.',
            'data' => new SellerOrderResource($order->load(['client', 'address', 'items'])),
        ]);
    }

    private function authorizeOrder(Order $order): void
    {
        $store = Auth::user()->store;

        abort_unless($store && $order->store_id === $store->id, 403, 'Unauthorized.');
    }
}
