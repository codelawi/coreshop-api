<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE admin_notifications MODIFY COLUMN type ENUM('new_order', 'new_product', 'new_user', 'new_support_message') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE admin_notifications MODIFY COLUMN type ENUM('new_order', 'new_product', 'new_user') NOT NULL");
    }
};
