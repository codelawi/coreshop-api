<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Expand enum to include both old + new values
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'pending','processing','shipped','delivered','cancelled',
            'approved','preparing','ready_for_pickup','assigned',
            'out_for_delivery','completed','refunded'
        ) NOT NULL DEFAULT 'pending'");

        // 2. Migrate data
        DB::table('orders')->where('status', 'processing')->update(['status' => 'preparing']);
        DB::table('orders')->where('status', 'shipped')->update(['status' => 'out_for_delivery']);

        // 3. Narrow to new values only
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'pending','approved','preparing','ready_for_pickup','assigned',
            'out_for_delivery','delivered','completed','cancelled','refunded'
        ) NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'pending','processing','shipped','delivered','cancelled',
            'approved','preparing','ready_for_pickup','assigned',
            'out_for_delivery','completed','refunded'
        ) NOT NULL DEFAULT 'pending'");

        DB::table('orders')->where('status', 'preparing')->update(['status' => 'processing']);
        DB::table('orders')->whereIn('status', ['ready_for_pickup', 'assigned', 'out_for_delivery'])
            ->update(['status' => 'shipped']);
        DB::table('orders')->where('status', 'completed')->update(['status' => 'delivered']);
        DB::table('orders')->where('status', 'refunded')->update(['status' => 'cancelled']);
        DB::table('orders')->where('status', 'approved')->update(['status' => 'pending']);

        DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
            'pending','processing','shipped','delivered','cancelled'
        ) NOT NULL DEFAULT 'pending'");
    }
};