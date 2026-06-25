<?php

namespace App\Http\Controllers\Api\V1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\SellerOrderResource;
use App\Models\Order;
use App\Services\ExpoPushService;
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

    public function __construct(private readonly ExpoPushService $push) {}

    public function index(Request $request): JsonResponse
    {
        $store = Auth::user()->store;

        if (! $store) {
            return response()->json(['success' => false, 'message' => 'No store found.'], 404);
        }

        $orders = Order::where('store_id', $store->id)
            ->with(['client', 'address'])
            ->withCount('items')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => SellerOrderResource::collection($orders),
        ]);
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorizeOrder($order);

        $order->loadCount('items')->load(['client', 'address', 'items', 'coupon']);

        return response()->json([
            'success' => true,
            'data' => new SellerOrderResource($order),
        ]);
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $this->authorizeOrder($order);

        $allowed = self::ALLOWED_TRANSITIONS[$order->status] ?? null;

        if (! $allowed) {
            return response()->json([
                'success' => false,
                'message' => 'This order cannot be advanced further by the seller.',
            ], 422);
        }

        $request->validate([
            'status' => ['required', 'in:'.$allowed],
        ]);

        $order->update(['status' => $request->status]);

        $order->load(['client', 'store.seller']);

        $this->notifyClient($order, $request->status);
        $this->notifySeller($order, $request->status);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated.',
            'data' => new SellerOrderResource($order->load(['client', 'address', 'items'])),
        ]);
    }

    private function notifyClient(Order $order, string $status): void
    {
        $messages = [
            'approved' => ['Order Approved!', 'Your order #'.$order->id.' has been approved and will be prepared soon.'],
            'preparing' => ['Being Prepared', 'The seller is preparing your order #'.$order->id.'.'],
            'ready_for_pickup' => ['Ready for Pickup', 'Your order #'.$order->id.' is ready and waiting for a driver.'],
        ];

        if (! isset($messages[$status]) || ! $order->client) {
            return;
        }

        [$title, $body] = $messages[$status];

        $this->push->sendToUser($order->client, $title, $body, [
            'type' => 'order_status',
            'order_id' => $order->id,
            'status' => $status,
        ]);
    }

    private function notifySeller(Order $order, string $status): void
    {
        $seller = $order->store?->seller;

        if (! $seller) {
            return;
        }

        $messages = [
            'approved' => ['Order Confirmed', 'You confirmed order #'.$order->id.'. Start preparing it.'],
            'preparing' => ['Preparing Order', 'You marked order #'.$order->id.' as being prepared.'],
            'ready_for_pickup' => ['Awaiting Driver', 'Order #'.$order->id.' is ready. Waiting for a driver to pick it up.'],
        ];

        if (! isset($messages[$status])) {
            return;
        }

        [$title, $body] = $messages[$status];

        $this->push->sendToUser($seller, $title, $body, [
            'type' => 'order_status',
            'order_id' => $order->id,
            'status' => $status,
        ]);
    }

    private function authorizeOrder(Order $order): void
    {
        $store = Auth::user()->store;

        abort_unless($store && $order->store_id === $store->id, 403, 'Unauthorized.');
    }
}
