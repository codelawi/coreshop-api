<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClientOrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'address_id' => ['required', 'integer'],
            'coupon_code' => ['nullable', 'string'],
            'payment_method' => ['nullable', 'string', 'in:cash_on_delivery,cliq'],
            'cliq_reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.variant_id' => ['nullable', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $order = $this->orderService->place(Auth::id(), $data);

        return response()->json([
            'success' => true,
            'message' => 'Order placed successfully',
            'data' => new ClientOrderResource($order),
        ], 201);
    }

    public function index(Request $request): JsonResponse
    {
        $orders = Order::with(['store', 'items'])
            ->where('client_id', Auth::id())
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => ClientOrderResource::collection($orders),
        ]);
    }

    public function show(Order $order): JsonResponse
    {
        abort_unless($order->client_id === Auth::id(), 403);
        $order->load(['store', 'address', 'items', 'coupon']);

        return response()->json([
            'success' => true,
            'data' => new ClientOrderResource($order),
        ]);
    }

    public function cancel(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->client_id === Auth::id(), 403);

        $nonCancellableStatuses = ['delivered', 'completed', 'cancelled', 'refunded'];

        if (in_array($order->status, $nonCancellableStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'This order cannot be cancelled.',
            ], 422);
        }

        $cancellationFee = $order->status !== 'pending' ? 2.00 : 0.00;

        $order->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->reason ?? 'Cancelled by customer',
            'cancelled_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled.',
            'data' => [
                'cancellation_fee' => $cancellationFee,
            ],
        ]);
    }
}
