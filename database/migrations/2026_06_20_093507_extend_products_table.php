<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->after('seller_id')->constrained('stores')->nullOnDelete();
            $table->decimal('original_price', 10, 2)->nullable()->after('price');
            $table->unsignedInteger('weight_grams')->nullable()->after('stock');
            $table->decimal('rating', 3, 2)->default(0)->after('weight_grams');
            $table->unsignedInteger('reviews_count')->default(0)->after('rating');
            $table->unsignedInteger('sales_count')->default(0)->after('reviews_count');
            $table->unsignedInteger('views_count')->default(0)->after('sales_count');
            $table->boolean('is_featured')->default(false)->after('views_count');

            $table->index(['status', 'is_featured']);
            $table->index('rating');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropIndex(['status', 'is_featured']);
            $table->dropIndex(['rating']);
            $table->dropColumn([
                'store_id',
                'original_price',
                'weight_grams',
                'rating',
                'reviews_count',
                'sales_count',
                'views_count',
                'is_featured',
            ]);
        });
    }
};