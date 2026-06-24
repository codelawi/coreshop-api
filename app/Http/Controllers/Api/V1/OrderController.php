<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderCollection;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\ExpoPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(private readonly ExpoPushService $push) {}

    public function index(Request $request): OrderCollection
    {
        $orders = Order::with(['client', 'coupon', 'items'])
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->search, fn ($q) => $q->whereHas('client', fn ($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%")
            ))
            ->latest()
            ->paginate($request->per_page ?? 10);

        return new OrderCollection($orders);
    }

    public function show(Order $order): JsonResponse
    {
        $order->load(['client', 'coupon', 'items.product']);

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order),
        ]);
    }

    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:pending,approved,preparing,ready_for_pickup,assigned,out_for_delivery,delivered,completed,cancelled,refunded'],
        ]);

        $order->update(['status' => $request->status]);

        $order->load('client');
        $this->notifyClient($order, $request->status);

        return response()->json([
            'success' => true,
            'message' => 'Order status updated successfully',
            'data' => new OrderResource($order->load(['client', 'coupon', 'items'])),
        ]);
    }

    private function notifyClient(Order $order, string $status): void
    {
        $messages = [
            'approved' => ['Order Approved!', 'Your order #'.$order->id.' has been approved and will be prepared soon.'],
            'assigned' => ['Driver Assigned', 'A driver has been assigned to your order #'.$order->id.'.'],
            'out_for_delivery' => ['On the Way!', 'Your order #'.$order->id.' is out for delivery.'],
            'delivered' => ['Order Delivered!', 'Your order #'.$order->id.' has been delivered. Enjoy!'],
            'cancelled' => ['Order Cancelled', 'Your order #'.$order->id.' has been cancelled.'],
            'refunded' => ['Order Refunded', 'Your order #'.$order->id.' has been refunded.'],
        ];

        if (! isset($messages[$status])) {
            return;
        }

        [$title, $body] = $messages[$status];

        $this->push->sendToUser($order->client, $title, $body, [
            'type' => 'order_status',
            'order_id' => $order->id,
            'status' => $status,
        ]);
    }
}
