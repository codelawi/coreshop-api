<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SecurityEventController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $events = SecurityEvent::query()
            ->when($request->type, fn ($q) => $q->where('type', $request->type))
            ->when($request->ip, fn ($q) => $q->where('ip_address', $request->ip))
            ->when($request->from, fn ($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to, fn ($q) => $q->whereDate('created_at', '<=', $request->to))
            ->latest('created_at')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $events->items(),
            'meta' => [
                'current_page' => $events->currentPage(),
                'last_page' => $events->lastPage(),
                'per_page' => $events->perPage(),
                'total' => $events->total(),
            ],
        ]);
    }

    public function stats(): JsonResponse
    {
        $since = now()->subHours(24);

        $counts = SecurityEvent::where('created_at', '>=', $since)
            ->selectRaw('type, COUNT(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $topIps = SecurityEvent::where('created_at', '>=', $since)
            ->selectRaw('ip_address, country, COUNT(*) as total')
            ->groupBy('ip_address', 'country')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $counts->sum(),
                'failed_login' => $counts->get('failed_login', 0),
                'rate_limited' => $counts->get('rate_limited', 0),
                'unauthorized' => $counts->get('unauthorized', 0),
                'unique_ips' => SecurityEvent::where('created_at', '>=', $since)
                    ->distinct('ip_address')
                    ->count('ip_address'),
                'top_ips' => $topIps,
            ],
        ]);
    }
}
