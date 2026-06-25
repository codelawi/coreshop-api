<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const IMAGES = [
        // Parent categories
        'fashion' => 'https://images.unsplash.com/photo-1445205170230-053b83016050?w=400&h=400&fit=crop&q=80',
        'electronics' => 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=400&h=400&fit=crop&q=80',
        'beauty' => 'https://images.unsplash.com/photo-1522335789203-aabd1fc54bc9?w=400&h=400&fit=crop&q=80',
        'home' => 'https://images.unsplash.com/photo-1484154218962-a197022b5858?w=400&h=400&fit=crop&q=80',
        'sports' => 'https://images.unsplash.com/photo-1517649763962-0c623066013b?w=400&h=400&fit=crop&q=80',
        'grocery' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=400&h=400&fit=crop&q=80',

        // Fashion children
        'fashion-men' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&h=300&fit=crop&q=80',
        'fashion-women' => 'https://images.unsplash.com/photo-1483985988355-763728e1935a?w=300&h=300&fit=crop&q=80',
        'fashion-kids' => 'https://images.unsplash.com/photo-1503454537195-1dcabb73ffb9?w=300&h=300&fit=crop&q=80',
        'fashion-accessories' => 'https://images.unsplash.com/photo-1608731267464-c0c889c2ff92?w=300&h=300&fit=crop&q=80',

        // Electronics children
        'electronics-phones' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=300&h=300&fit=crop&q=80',
        'electronics-laptops' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?w=300&h=300&fit=crop&q=80',
        'electronics-headphones' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=300&h=300&fit=crop&q=80',
        'electronics-wearables' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=300&h=300&fit=crop&q=80',

        // Beauty children
        'beauty-skincare' => 'https://images.unsplash.com/photo-1556228578-8c89e6adf883?w=300&h=300&fit=crop&q=80',
        'beauty-makeup' => 'https://images.unsplash.com/photo-1571781926291-c477ebfd024b?w=300&h=300&fit=crop&q=80',
        'beauty-fragrance' => 'https://images.unsplash.com/photo-1541643600606-c53ba9d13c11?w=300&h=300&fit=crop&q=80',
        'beauty-hair-care' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=300&h=300&fit=crop&q=80',

        // Home children
        'home-furniture' => 'https://images.unsplash.com/photo-1555041469-a586c61ea9bc?w=300&h=300&fit=crop&q=80',
        'home-kitchen' => 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?w=300&h=300&fit=crop&q=80',
        'home-decor' => 'https://images.unsplash.com/photo-1555696958-e1c5855dc06a?w=300&h=300&fit=crop&q=80',
        'home-lighting' => 'https://images.unsplash.com/photo-1513506003901-9b41d65d6fb5?w=300&h=300&fit=crop&q=80',

        // Sports children
        'sports-fitness' => 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=300&h=300&fit=crop&q=80',
        'sports-outdoor' => 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=300&h=300&fit=crop&q=80',
        'sports-footwear' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=300&h=300&fit=crop&q=80',

        // Grocery children
        'grocery-snacks' => 'https://images.unsplash.com/photo-1543362906-acfc16c67564?w=300&h=300&fit=crop&q=80',
        'grocery-beverages' => 'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=300&h=300&fit=crop&q=80',
        'grocery-pantry' => 'https://images.unsplash.com/photo-1610832958506-aa56368176cf?w=300&h=300&fit=crop&q=80',
    ];

    public function up(): void
    {
        foreach (self::IMAGES as $slug => $image) {
            DB::table('categories')
                ->where('slug', $slug)
                ->update(['image' => $image]);
        }
    }

    public function down(): void
    {
        // Restore picsum placeholders by slug pattern
        DB::table('categories')
            ->whereNull('parent_id')
            ->get()
            ->each(function ($cat, $i) {
                DB::table('categories')->where('id', $cat->id)->update([
                    'image' => "https://picsum.photos/seed/cat-{$i}/400/400",
                ]);
            });

        DB::table('categories')
            ->whereNotNull('parent_id')
            ->get()
            ->each(function ($cat, $i) {
                DB::table('categories')->where('id', $cat->id)->update([
                    'image' => "https://picsum.photos/seed/cat-sub-{$i}/300/300",
                ]);
            });
    }
};
