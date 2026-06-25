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
            ['Fashion', 'shirt-01', 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=400&h=400&fit=crop&q=80', [
                ['Men',         'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=300&fit=crop&q=80'],
                ['Women',       'https://images.unsplash.com/photo-1483985988355-763728e1935a?w=300&h=300&fit=crop&q=80'],
                ['Kids',        'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?w=300&h=300&fit=crop&q=80'],
                ['Accessories', 'https://images.unsplash.com/photo-1608731267464-c0c889c2ff92?w=300&h=300&fit=crop&q=80'],
            ]],
            ['Electronics', 'smart-phone-01', 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=400&h=400&fit=crop&q=80', [
                ['Phones',      'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=300&h=300&fit=crop&q=80'],
                ['Laptops',     'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=300&h=300&fit=crop&q=80'],
                ['Headphones',  'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=300&h=300&fit=crop&q=80'],
                ['Wearables',   'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=300&h=300&fit=crop&q=80'],
            ]],
            ['Beauty', 'make-up', 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=400&h=400&fit=crop&q=80', [
                ['Skincare',  'https://images.unsplash.com/photo-1556228578-8c89e6adf883?w=300&h=300&fit=crop&q=80'],
                ['Makeup',    'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?w=300&h=300&fit=crop&q=80'],
                ['Fragrance', 'https://images.unsplash.com/photo-1541643600606-c53ba9d13c11?w=300&h=300&fit=crop&q=80'],
                ['Hair Care', 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=300&h=300&fit=crop&q=80'],
            ]],
            ['Home', 'home-01', 'https://images.unsplash.com/photo-1484154218962-a197022b5858?w=400&h=400&fit=crop&q=80', [
                ['Furniture', 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=300&h=300&fit=crop&q=80'],
                ['Kitchen',   'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=300&h=300&fit=crop&q=80'],
                ['Decor',     'https://images.unsplash.com/photo-1555696958-e1c5855dc06a?w=300&h=300&fit=crop&q=80'],
                ['Lighting',  'https://images.unsplash.com/photo-1513506003901-9b41d65d6fb5?w=300&h=300&fit=crop&q=80'],
            ]],
            ['Sports', 'football', 'https://images.unsplash.com/photo-1517649763962-0c623066013b?w=400&h=400&fit=crop&q=80', [
                ['Fitness',  'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=300&h=300&fit=crop&q=80'],
                ['Outdoor',  'https://images.unsplash.com/photo-1551632811-561732d1e306?w=300&h=300&fit=crop&q=80'],
                ['Footwear', 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=300&h=300&fit=crop&q=80'],
            ]],
            ['Grocery', 'shopping-cart-01', 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&h=400&fit=crop&q=80', [
                ['Snacks',    'https://images.unsplash.com/photo-1543362906-acfc16c67564?w=300&h=300&fit=crop&q=80'],
                ['Beverages', 'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=300&h=300&fit=crop&q=80'],
                ['Pantry',    'https://images.unsplash.com/photo-1610832958506-aa56368176cf?w=300&h=300&fit=crop&q=80'],
            ]],
        ];

        foreach ($tree as $i => [$name, $icon, $image, $children]) {
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'icon' => $icon,
                    'sort_order' => $i,
                    'is_active' => true,
                    'image' => $image,
                ]
            );
            foreach ($children as $j => [$child, $childImage]) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($name.'-'.$child)],
                    [
                        'parent_id' => $parent->id,
                        'name' => $child,
                        'sort_order' => $j,
                        'is_active' => true,
                        'image' => $childImage,
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
                ['email' => 'seller'.($i + 1).'@coreshop.com'],
                [
                    'name' => $storeName.' Owner',
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
                    'phone' => '+96279'.rand(1000000, 9999999),
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
                    ['slug' => Str::slug($store->slug.'-product-'.$p)],
                    [
                        'seller_id' => $seller->id,
                        'store_id' => $store->id,
                        'category_id' => $subcategory->id,
                        'name' => $storeName.' Item '.$p,
                        'description' => 'A high-quality product from '.$storeName.'. Crafted with care and attention to detail.',
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
