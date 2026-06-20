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
            ->paginate($request->integer('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => ClientOrderResource::collection($orders),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ],
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
}
