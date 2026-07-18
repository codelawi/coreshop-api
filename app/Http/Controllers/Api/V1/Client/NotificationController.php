<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = 10;
        $beforeId = $request->query('before_id');

        $query = UserNotification::where('user_id', Auth::id())
            ->orderByDesc('id');

        if ($beforeId) {
            $query->where('id', '<', (int) $beforeId);
        }

        $items = $query->limit($limit + 1)->get();
        $hasMore = $items->count() > $limit;

        if ($hasMore) {
            $items = $items->take($limit);
        }

        $notifications = $items->map(fn (UserNotification $n) => [
            'id' => $n->id,
            'type' => $n->type,
            'title' => $n->title,
            'body' => $n->body,
            'data' => $n->data,
            'read_at' => $n->read_at?->toISOString(),
            'created_at' => $n->created_at->toISOString(),
        ]);

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'meta' => ['has_more' => $hasMore],
        ]);
    }

    public function unreadCount(): JsonResponse
    {
        $count = UserNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['success' => true, 'data' => ['count' => $count]]);
    }

    public function markRead(Request $request, int $id): JsonResponse
    {
        UserNotification::where('user_id', Auth::id())
            ->where('id', $id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function markAllRead(): JsonResponse
    {
        UserNotification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
