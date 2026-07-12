<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('payment_status');
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->index(['active', 'expires_at']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('status');
            $table->index('store_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['payment_status']);
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->dropIndex(['active', 'expires_at']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['store_id']);
        });
    }
};
