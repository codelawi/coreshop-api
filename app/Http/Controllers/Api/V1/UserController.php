<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Order;
use App\Models\User;
use App\Services\ExpoPushService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::with('store')
            ->when($request->role, fn ($q) => $q->where('role', $request->role))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->search, fn ($q) => $q->where('name', 'like', "%{$request->search}%")
                ->orWhere('email', 'like', "%{$request->search}%"))
            ->where('role', '!=', 'admin')
            ->latest()
            ->paginate($request->per_page ?? 10);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
            'meta' => [
                'total' => $users->total(),
                'page' => $users->currentPage(),
                'per_page' => $users->perPage(),
                'last_page' => $users->lastPage(),
            ],
        ]);
    }

    public function updateStatus(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:active,inactive,suspended'],
        ]);

        $newStatus = $request->status;
        $wasBanned = $user->status === 'suspended';

        DB::transaction(function () use ($user, $newStatus) {
            $user->update(['status' => $newStatus]);

            if ($newStatus === 'suspended') {
                $this->handleBan($user);
            }
        });

        // Push notifications run outside transaction (non-critical)
        if ($newStatus === 'suspended' && ! $wasBanned) {
            app(ExpoPushService::class)->sendToUser(
                $user,
                'Account Suspended',
                'Your account has been suspended. Please contact support for assistance.',
                ['type' => 'account_banned']
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully',
            'data' => new UserResource($user),
        ]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'unique:users,email,'.$user->id],
            'role' => ['sometimes', 'in:seller,client,driver'],
        ]);

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => new UserResource($user),
        ]);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully',
        ]);
    }

    private function handleBan(User $user): void
    {
        $store = $user->store;

        if (! $store) {
            return;
        }

        // Close the store and set it to suspended
        $store->update(['is_open' => false, 'status' => 'suspended']);

        // Set all store products to pending_review so they disappear from listings
        $store->products()->update(['status' => 'pending_review']);

        // Cancel all active orders from this store and collect affected clients
        $activeStatuses = ['pending', 'approved', 'preparing', 'ready_for_pickup', 'assigned', 'out_for_delivery'];

        $orders = Order::where('store_id', $store->id)
            ->whereIn('status', $activeStatuses)
            ->with('client')
            ->get();

        if ($orders->isEmpty()) {
            return;
        }

        Order::where('store_id', $store->id)
            ->whereIn('status', $activeStatuses)
            ->update(['status' => 'cancelled', 'cancelled_at' => now()]);

        // Notify each affected client once
        $push = app(ExpoPushService::class);
        $notifiedClients = [];

        foreach ($orders as $order) {
            $client = $order->client;

            if (! $client || in_array($client->id, $notifiedClients) || empty($client->expo_push_token)) {
                continue;
            }

            $push->sendToUser(
                $client,
                'Order Cancelled',
                "Your order #{$order->id} has been cancelled because the store is no longer available.",
                ['type' => 'order_cancelled', 'order_id' => $order->id]
            );

            $notifiedClients[] = $client->id;
        }
    }
}
