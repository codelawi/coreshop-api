<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function overview(): JsonResponse
    {
        $completedOrders = Order::whereIn('status', ['delivered', 'completed']);

        return response()->json([
            'success' => true,
            'data' => [
                'total_revenue' => (clone $completedOrders)->sum('total'),
                'total_platform_fee' => (clone $completedOrders)->sum('platform_fee'),
                'total_orders' => Order::count(),
                'total_users' => User::where('role', '!=', 'admin')->count(),
                'total_products' => Product::where('status', 'approved')->count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'avg_order_value' => (clone $completedOrders)->avg('total'),
            ],
        ]);
    }

    public function revenue(Request $request): JsonResponse
    {
        $period = $request->query('period', 'monthly');

        if ($period === 'hourly') {
            $revenue = Order::whereIn('status', ['delivered', 'completed'])
                ->selectRaw('HOUR(created_at) as hour, SUM(total) as total')
                ->whereDate('created_at', today())
                ->groupBy('hour')
                ->orderBy('hour')
                ->get();
        } else {
            $revenue = Order::whereIn('status', ['delivered', 'completed'])
                ->selectRaw('MONTH(created_at) as month, SUM(total) as total')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        return response()->json([
            'success' => true,
            'period' => $period,
            'data' => $revenue,
        ]);
    }

    public function orders(Request $request): JsonResponse
    {
        $period = $request->query('period', 'monthly');

        if ($period === 'hourly') {
            $timeSeries = Order::selectRaw('HOUR(created_at) as hour, COUNT(*) as total')
                ->whereDate('created_at', today())
                ->groupBy('hour')
                ->orderBy('hour')
                ->get();
        } else {
            $timeSeries = Order::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }

        $byStatus = Order::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        return response()->json([
            'success' => true,
            'period' => $period,
            'data' => [
                'time_series' => $timeSeries,
                'by_status' => $byStatus,
            ],
        ]);
    }

    public function users(Request $request): JsonResponse
    {
        $period = $request->query('period', 'monthly');

        if ($period === 'hourly') {
            $users = User::selectRaw('HOUR(created_at) as hour, role, COUNT(*) as total')
                ->whereDate('created_at', today())
                ->where('role', '!=', 'admin')
                ->groupBy('hour', 'role')
                ->orderBy('hour')
                ->get();
        } else {
            $users = User::selectRaw('MONTH(created_at) as month, role, COUNT(*) as total')
                ->whereYear('created_at', now()->year)
                ->where('role', '!=', 'admin')
                ->groupBy('month', 'role')
                ->orderBy('month')
                ->get();
        }

        return response()->json([
            'success' => true,
            'period' => $period,
            'data' => $users,
        ]);
    }

    public function topProducts(): JsonResponse
    {
        $items = OrderItem::selectRaw('product_id, SUM(quantity) as sales')
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderByDesc('sales')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $items->map(fn ($i) => [
                'id' => $i->product_id,
                'name' => $i->product?->name ?? 'Unknown',
                'sales' => (int) $i->sales,
            ]),
        ]);
    }

    public function topSellers(): JsonResponse
    {
        $sellers = User::query()
            ->where('role', 'seller')
            ->select('users.id', 'users.name', 'users.email')
            ->selectSub(
                OrderItem::selectRaw('COALESCE(SUM(order_items.total), 0)')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->whereIn('orders.status', ['delivered', 'completed'])
                    ->whereColumn('products.seller_id', 'users.id'),
                'revenue'
            )
            ->selectSub(
                OrderItem::selectRaw('COUNT(DISTINCT order_items.order_id)')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->whereIn('orders.status', ['delivered', 'completed'])
                    ->whereColumn('products.seller_id', 'users.id'),
                'orders_count'
            )
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $sellers->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'email' => $s->email,
                'revenue' => (float) $s->revenue,
                'orders' => (int) $s->orders_count,
            ]),
        ]);
    }

    public function categories(): JsonResponse
    {
        $data = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['delivered', 'completed'])
            ->selectRaw('categories.name as category, SUM(order_items.total) as revenue, COUNT(DISTINCT orders.id) as `orders`')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('revenue')
            ->limit(8)
            ->get()
            ->map(fn ($r) => [
                'category' => $r->category,
                'revenue' => (float) $r->revenue,
                'orders' => (int) $r->orders,
            ]);

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function earnings(Request $request): JsonResponse
    {
        $period = $request->query('period', 'monthly');

        if ($period === 'hourly') {
            $data = Order::whereIn('status', ['delivered', 'completed'])
                ->whereDate('created_at', today())
                ->selectRaw('HOUR(created_at) as hour, SUM(total) as revenue, SUM(platform_fee) as earnings')
                ->groupBy('hour')
                ->orderBy('hour')
                ->get()
                ->map(fn ($r) => [
                    'hour' => (int) $r->hour,
                    'revenue' => (float) $r->revenue,
                    'earnings' => (float) $r->earnings,
                ]);
        } else {
            $data = Order::whereIn('status', ['delivered', 'completed'])
                ->whereYear('created_at', now()->year)
                ->selectRaw('MONTH(created_at) as month, SUM(total) as revenue, SUM(platform_fee) as earnings')
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->map(fn ($r) => [
                    'month' => (int) $r->month,
                    'revenue' => (float) $r->revenue,
                    'earnings' => (float) $r->earnings,
                ]);
        }

        return response()->json(['success' => true, 'period' => $period, 'data' => $data]);
    }

    public function storeStats(): JsonResponse
    {
        $data = Store::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->map(fn ($r) => [
                'status' => $r->status,
                'count' => (int) $r->count,
            ]);

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function cities(): JsonResponse
    {
        $data = Order::join('addresses', 'orders.address_id', '=', 'addresses.id')
            ->whereNotNull('addresses.city')
            ->where('addresses.city', '!=', '')
            ->selectRaw('addresses.city as city, COUNT(*) as `orders`')
            ->groupBy('addresses.city')
            ->orderByDesc('orders')
            ->limit(8)
            ->get()
            ->map(fn ($r) => [
                'city' => $r->city,
                'orders' => (int) $r->orders,
            ]);

        return response()->json(['success' => true, 'data' => $data]);
    }
}
