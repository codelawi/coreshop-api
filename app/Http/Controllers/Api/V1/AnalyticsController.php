<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{
    public function overview(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_revenue' => Order::where('payment_status', 'paid')->sum('total'),
                'total_orders' => Order::count(),
                'total_users' => User::where('role', '!=', 'admin')->count(),
                'total_products' => Product::where('status', 'approved')->count(),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'avg_order_value' => Order::where('payment_status', 'paid')->avg('total'),
            ],
        ]);
    }

    public function revenue(): JsonResponse
    {
        $revenue = Order::where('payment_status', 'paid')
            ->selectRaw('MONTH(created_at) as month, SUM(total) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $revenue,
        ]);
    }

    public function orders(): JsonResponse
    {
        $orders = Order::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        $byStatus = Order::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'monthly' => $orders,
                'by_status' => $byStatus,
            ],
        ]);
    }

    public function users(): JsonResponse
    {
        $users = User::selectRaw('MONTH(created_at) as month, role, COUNT(*) as total')
            ->whereYear('created_at', now()->year)
            ->where('role', '!=', 'admin')
            ->groupBy('month', 'role')
            ->orderBy('month')
            ->get();

        return response()->json([
            'success' => true,
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
                    ->whereColumn('products.seller_id', 'users.id'),
                'revenue'
            )
            ->selectSub(
                OrderItem::selectRaw('COUNT(DISTINCT order_items.order_id)')
                    ->join('products', 'order_items.product_id', '=', 'products.id')
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
}