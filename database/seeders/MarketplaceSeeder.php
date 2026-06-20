<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        // CATEGORIES (parents + children)
        $tree = [
            ['Fashion', 'shirt-01', ['Men', 'Women', 'Kids', 'Accessories']],
            ['Electronics', 'smart-phone-01', ['Phones', 'Laptops', 'Headphones', 'Wearables']],
            ['Beauty', 'make-up', ['Skincare', 'Makeup', 'Fragrance', 'Hair Care']],
            ['Home', 'home-01', ['Furniture', 'Kitchen', 'Decor', 'Lighting']],
            ['Sports', 'football', ['Fitness', 'Outdoor', 'Footwear']],
            ['Grocery', 'shopping-cart-01', ['Snacks', 'Beverages', 'Pantry']],
        ];

        foreach ($tree as $i => [$name, $icon, $children]) {
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'icon' => $icon,
                    'sort_order' => $i,
                    'is_active' => true,
                    'image' => "https://picsum.photos/seed/cat-{$i}/400/400",
                ]
            );
            foreach ($children as $j => $child) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($name . '-' . $child)],
                    [
                        'parent_id' => $parent->id,
                        'name' => $child,
                        'sort_order' => $j,
                        'is_active' => true,
                        'image' => "https://picsum.photos/seed/cat-{$i}-{$j}/300/300",
                    ]
                );
            }
        }

        // SELLERS + STORES
        $storeData = [
            ['Trendy Threads', 'Fashion', 31.9539, 35.9106],
            ['Tech Hub', 'Electronics', 31.9700, 35.9000],
            ['Glow Beauty', 'Beauty', 31.9450, 35.9200],
            ['Cozy Home', 'Home', 31.9600, 35.9300],
            ['Active Gear', 'Sports', 31.9400, 35.8900],
        ];

        foreach ($storeData as $i => [$storeName, $catName, $lat, $lng]) {
            $seller = User::firstOrCreate(
                ['email' => 'seller' . ($i + 1) . '@coreshop.com'],
                [
                    'name' => $storeName . ' Owner',
                    'password' => Hash::make('password123'),
                    'role' => 'seller',
                    'status' => 'active',
                    'onboarding_completed' => true,
                    'email_verified_at' => now(),
                    'city' => 'Amman',
                    'latitude' => $lat,
                    'longitude' => $lng,
                ]
            );

            $store = Store::updateOrCreate(
                ['seller_id' => $seller->id],
                [
                    'name' => $storeName,
                    'slug' => Str::slug($storeName),
                    'logo' => "https://picsum.photos/seed/store-{$i}-logo/200/200",
                    'banner' => "https://picsum.photos/seed/store-{$i}-banner/800/300",
                    'description' => "Welcome to {$storeName}. We sell quality products at great prices.",
                    'phone' => '+96279' . rand(1000000, 9999999),
                    'address' => 'Amman, Jordan',
                    'city' => 'Amman',
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'delivery_radius_km' => 15,
                    'status' => 'active',
                    'is_open' => true,
                    'rating' => round(rand(40, 50) / 10, 2),
                    'reviews_count' => rand(20, 200),
                    'sales_count' => rand(50, 500),
                ]
            );

            $category = Category::where('name', $catName)->first();
            $subcategories = $category->children;

            // PRODUCTS for this store
            for ($p = 1; $p <= 8; $p++) {
                $price = rand(10, 200);
                $hasDiscount = rand(0, 1);
                $originalPrice = $hasDiscount ? round($price * (1 + rand(15, 60) / 100), 2) : null;
                $subcategory = $subcategories->random();

                $product = Product::updateOrCreate(
                    ['slug' => Str::slug($store->slug . '-product-' . $p)],
                    [
                        'seller_id' => $seller->id,
                        'store_id' => $store->id,
                        'category_id' => $subcategory->id,
                        'name' => $storeName . ' Item ' . $p,
                        'description' => 'A high-quality product from ' . $storeName . '. Crafted with care and attention to detail.',
                        'price' => $price,
                        'original_price' => $originalPrice,
                        'stock' => rand(5, 100),
                        'weight_grams' => rand(100, 2000),
                        'images' => [],
                        'status' => 'approved',
                        'rating' => round(rand(35, 50) / 10, 2),
                        'reviews_count' => rand(5, 100),
                        'sales_count' => rand(10, 300),
                        'views_count' => rand(50, 1000),
                        'is_featured' => $p <= 2,
                    ]
                );

                // Product images (3-5 per product)
                $imgCount = rand(3, 5);
                $product->productImages()->delete();
                for ($img = 0; $img < $imgCount; $img++) {
                    ProductImage::create([
                        'product_id' => $product->id,
                        'url' => "https://picsum.photos/seed/{$store->id}-{$p}-{$img}/600/600",
                        'sort_order' => $img,
                        'is_primary' => $img === 0,
                    ]);
                }

                // Variants (sizes for fashion, colors for some)
                if ($catName === 'Fashion') {
                    $product->variants()->delete();
                    foreach (['S', 'M', 'L', 'XL'] as $size) {
                        ProductVariant::create([
                            'product_id' => $product->id,
                            'size' => $size,
                            'stock' => rand(0, 20),
                            'is_active' => true,
                        ]);
                    }
                }
            }
        }

        // BANNERS
        $banners = [
            ['Mega Sale', 'Up to 70% off', 'flash-deals'],
            ['New Arrivals', 'Fresh picks for you', 'new'],
            ['Free Delivery', 'On orders over JOD 30', 'promo'],
        ];
        foreach ($banners as $i => [$title, $subtitle, $linkValue]) {
            Banner::updateOrCreate(
                ['title' => $title],
                [
                    'subtitle' => $subtitle,
                    'image' => "https://picsum.photos/seed/banner-{$i}/1200/600",
                    'link_type' => 'category',
                    'link_value' => $linkValue,
                    'sort_order' => $i,
                    'is_active' => true,
                ]
            );
        }
    }
}