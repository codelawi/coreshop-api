<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AnalyticsController extends Controller
{
    public function overview(): JsonResponse
    {
        $data = Cache::remember('admin.analytics.overview', now()->addMinutes(15), function () {
            $completedOrders = Order::whereIn('status', ['delivered', 'completed']);

            return [
                'total_revenue' => (clone $completedOrders)->sum('total'),
                'total_orders' => Order::count(),
                'total_users' => User::where('role', '!=', 'admin')->count(),
                'total_products' => Product::where('status', 'approved')->count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'avg_order_value' => (clone $completedOrders)->avg('total'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function revenue(): JsonResponse
    {
        $data = Cache::remember('admin.analytics.revenue', now()->addMinutes(15), function () {
            return Order::whereIn('status', ['delivered', 'completed'])
                ->selectRaw('MONTH(created_at) as month, SUM(total) as total')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function orders(): JsonResponse
    {
        $data = Cache::remember('admin.analytics.orders', now()->addMinutes(15), function () {
            $monthly = Order::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            $byStatus = Order::selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->get();

            return [
                'monthly' => $monthly,
                'by_status' => $byStatus,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function users(): JsonResponse
    {
        $data = Cache::remember('admin.analytics.users', now()->addMinutes(15), function () {
            return User::selectRaw('MONTH(created_at) as month, role, COUNT(*) as total')
                ->whereYear('created_at', now()->year)
                ->where('role', '!=', 'admin')
                ->groupBy('month', 'role')
                ->orderBy('month')
                ->get();
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function topProducts(): JsonResponse
    {
        $data = Cache::remember('admin.analytics.top_products', now()->addMinutes(15), function () {
            $items = OrderItem::selectRaw('product_id, SUM(quantity) as sales')
                ->with('product:id,name')
                ->groupBy('product_id')
                ->orderByDesc('sales')
                ->limit(5)
                ->get();

            return $items->map(fn ($i) => [
                'id' => $i->product_id,
                'name' => $i->product?->name ?? 'Unknown',
                'sales' => (int) $i->sales,
            ]);
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function topSellers(): JsonResponse
    {
        $data = Cache::remember('admin.analytics.top_sellers', now()->addMinutes(15), function () {
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

            return $sellers->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
                'email' => $s->email,
                'revenue' => (float) $s->revenue,
                'orders' => (int) $s->orders_count,
            ]);
        });

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
