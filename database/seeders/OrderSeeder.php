<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // Create sellers
        $sellers = [];
        foreach (['TechStore', 'StyleHub', 'GlowShop'] as $name) {
            $sellers[] = User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '', $name)) . '@coreshop.com',
                'password' => bcrypt('password123'),
                'role' => 'seller',
                'status' => 'active',
            ]);
        }

        // Create clients
        $clients = [];
        foreach (['Ahmed Ali', 'Sara Mohammed', 'James Carter'] as $name) {
            $clients[] = User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@email.com',
                'password' => bcrypt('password123'),
                'role' => 'client',
                'status' => 'active',
            ]);
        }

        // Create categories
        $electronics = Category::create(['name' => 'Electronics', 'slug' => 'electronics']);
        $fashion = Category::create(['name' => 'Fashion', 'slug' => 'fashion']);

        // Create products
        $products = [];
        $products[] = Product::create([
            'seller_id' => $sellers[0]->id,
            'category_id' => $electronics->id,
            'name' => 'Wireless Headphones',
            'slug' => 'wireless-headphones',
            'price' => 299.99,
            'stock' => 50,
            'status' => 'approved',
        ]);
        $products[] = Product::create([
            'seller_id' => $sellers[1]->id,
            'category_id' => $fashion->id,
            'name' => 'Leather Bag',
            'slug' => 'leather-bag',
            'price' => 89.99,
            'stock' => 100,
            'status' => 'approved',
        ]);

        // Create coupon
        $coupon = Coupon::create([
            'code' => 'WELCOME20',
            'type' => 'percentage',
            'value' => 20,
            'min_order_amount' => 50,
            'usage_limit' => 500,
            'used_count' => 0,
            'active' => true,
            'expires_at' => now()->addMonths(3),
        ]);

        // Create orders
        $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        foreach ($clients as $client) {
            foreach (range(1, 3) as $i) {
                $product = $products[array_rand($products)];
                $quantity = rand(1, 3);
                $subtotal = $product->price * $quantity;
                $discount = 0;
                $useCoupon = rand(0, 1);

                if ($useCoupon) {
                    $discount = $subtotal * 0.20;
                }

                $order = Order::create([
                    'client_id' => $client->id,
                    'coupon_id' => $useCoupon ? $coupon->id : null,
                    'status' => $statuses[array_rand($statuses)],
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'total' => $subtotal - $discount,
                    'payment_method' => 'Credit Card',
                    'payment_status' => 'paid',
                ]);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $product->price,
                    'total' => $product->price * $quantity,
                ]);
            }
        }
    }
}