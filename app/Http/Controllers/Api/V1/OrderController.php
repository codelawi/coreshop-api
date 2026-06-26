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
        $order->load(['client', 'coupon', 'items.product', 'store', 'address', 'driver']);

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

        $order->load(['client', 'store.seller']);

        $this->notifyClient($order, $request->status);
        $this->notifySeller($order, $request->status);

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
            'preparing' => ['Being Prepared', 'The seller is preparing your order #'.$order->id.'.'],
            'ready_for_pickup' => ['Ready for Pickup', 'Your order #'.$order->id.' is ready and waiting for a driver.'],
            'assigned' => ['Driver Assigned', 'A driver has been assigned to your order #'.$order->id.'.'],
            'out_for_delivery' => ['On the Way!', 'Your order #'.$order->id.' is out for delivery.'],
            'delivered' => ['Order Delivered!', 'Your order #'.$order->id.' has been delivered. Enjoy!'],
            'completed' => ['Order Completed', 'Your order #'.$order->id.' has been completed. Thank you!'],
            'cancelled' => ['Order Cancelled', 'Your order #'.$order->id.' has been cancelled.'],
            'refunded' => ['Order Refunded', 'Your order #'.$order->id.' has been refunded.'],
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
            'assigned' => ['Driver Assigned', 'A driver has been assigned to order #'.$order->id.'.'],
            'out_for_delivery' => ['Out for Delivery', 'Order #'.$order->id.' is now out for delivery.'],
            'delivered' => ['Order Delivered', 'Order #'.$order->id.' has been delivered to the customer.'],
            'completed' => ['Order Completed', 'Order #'.$order->id.' has been completed.'],
            'cancelled' => ['Order Cancelled', 'Order #'.$order->id.' has been cancelled by the admin.'],
            'refunded' => ['Order Refunded', 'Order #'.$order->id.' has been refunded by the admin.'],
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
}
