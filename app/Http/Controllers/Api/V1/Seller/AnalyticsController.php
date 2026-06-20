<?php

namespace App\Http\Controllers\Api\V1\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function overview(): JsonResponse
    {
        $store = Auth::user()->store;

        if (! $store) {
            return response()->json(['success' => false, 'message' => 'No store found.'], 404);
        }

        $baseQuery = Order::where('store_id', $store->id)
            ->whereIn('status', ['delivered', 'completed']);

        $totalRevenue = (float) $baseQuery->clone()->sum('total');
        $totalOrders = Order::where('store_id', $store->id)->count();
        $completedOrders = $baseQuery->clone()->count();
        $pendingOrders = Order::where('store_id', $store->id)
            ->whereIn('status', ['pending', 'approved', 'preparing', 'ready_for_pickup'])
            ->count();

        $thisMonthRevenue = (float) $baseQuery->clone()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        $lastMonthRevenue = (float) $baseQuery->clone()
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->sum('total');

        $avgOrderValue = $completedOrders > 0
            ? round($totalRevenue / $completedOrders, 2)
            : 0.0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'completed_orders' => $completedOrders,
                'pending_orders' => $pendingOrders,
                'avg_order_value' => $avgOrderValue,
                'this_month_revenue' => $thisMonthRevenue,
                'last_month_revenue' => $lastMonthRevenue,
                'products_count' => $store->products()->count(),
            ],
        ]);
    }

    public function revenue(): JsonResponse
    {
        $store = Auth::user()->store;

        if (! $store) {
            return response()->json(['success' => false, 'message' => 'No store found.'], 404);
        }

        $days = collect(range(29, 0))->map(fn ($d) => now()->subDays($d)->toDateString());

        $results = Order::where('store_id', $store->id)
            ->whereIn('status', ['delivered', 'completed'])
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->get()
            ->keyBy('date');

        $chart = $days->map(fn ($date) => [
            'date' => $date,
            'revenue' => isset($results[$date]) ? (float) $results[$date]->revenue : 0.0,
            'orders' => isset($results[$date]) ? (int) $results[$date]->orders : 0,
        ])->values()->toArray();

        return response()->json([
            'success' => true,
            'data' => $chart,
        ]);
    }

    public function topProducts(): JsonResponse
    {
        $store = Auth::user()->store;

        if (! $store) {
            return response()->json(['success' => false, 'message' => 'No store found.'], 404);
        }

        $products = OrderItem::whereHas('order', fn ($q) => $q->where('store_id', $store->id)
            ->whereIn('status', ['delivered', 'completed'])
        )
            ->selectRaw('product_id, product_name, product_image, SUM(quantity) as units_sold, SUM(total) as revenue')
            ->groupBy('product_id', 'product_name', 'product_image')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'product_id' => $item->product_id,
                'name' => $item->product_name,
                'image' => $item->product_image,
                'units_sold' => (int) $item->units_sold,
                'revenue' => (float) $item->revenue,
            ]);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
}
