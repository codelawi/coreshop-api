<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardNotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $perPage = min($request->integer('per_page', 20), 50);

        $paginator = AdminNotification::orderByDesc('created_at')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $paginator->getCollection()->map(fn (AdminNotification $n) => [
                'id' => $n->id,
                'type' => $n->type,
                'title' => $n->title,
                'body' => $n->body,
                'data' => $n->data,
                'read_at' => $n->read_at?->toISOString(),
                'created_at' => $n->created_at->toISOString(),
            ]),
            'has_more' => $paginator->hasMorePages(),
            'unread_count' => AdminNotification::whereNull('read_at')->count(),
        ]);
    }

    public function markRead(AdminNotification $notification): JsonResponse
    {
        $notification->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        $query = AdminNotification::whereNull('read_at');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $query->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function getSettings(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'notif_new_orders' => (bool) Setting::get('notif_new_orders', true),
                'notif_new_products' => (bool) Setting::get('notif_new_products', true),
                'notif_new_users' => (bool) Setting::get('notif_new_users', true),
            ],
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $request->validate([
            'notif_new_orders' => ['sometimes', 'boolean'],
            'notif_new_products' => ['sometimes', 'boolean'],
            'notif_new_users' => ['sometimes', 'boolean'],
        ]);

        foreach (['notif_new_orders', 'notif_new_products', 'notif_new_users'] as $key) {
            if ($request->has($key)) {
                Setting::set($key, $request->boolean($key));
            }
        }

        return response()->json(['success' => true]);
    }
}
