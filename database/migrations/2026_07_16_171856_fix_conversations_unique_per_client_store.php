<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: For each (client_id, store_id) group, reassign all messages
        // from duplicate conversations to the oldest one (lowest id).
        DB::statement('
            UPDATE messages m
            INNER JOIN conversations c ON m.conversation_id = c.id
            INNER JOIN (
                SELECT client_id, store_id, MIN(id) AS primary_id
                FROM conversations
                GROUP BY client_id, store_id
            ) primary_conv
                ON c.client_id = primary_conv.client_id
                AND c.store_id = primary_conv.store_id
            SET m.conversation_id = primary_conv.primary_id
            WHERE c.id != primary_conv.primary_id
        ');

        // Step 2: Delete duplicate conversations (keep only the lowest-id per pair).
        DB::statement('
            DELETE c FROM conversations c
            INNER JOIN (
                SELECT client_id, store_id, MIN(id) AS primary_id
                FROM conversations
                GROUP BY client_id, store_id
            ) primary_conv
                ON c.client_id = primary_conv.client_id
                AND c.store_id = primary_conv.store_id
            WHERE c.id != primary_conv.primary_id
        ');

        // Step 3: Add the new index first so it can serve as the FK backing index,
        // then drop the old one (MySQL requires a covering index to exist before
        // the only existing one is removed when a FK references that column).
        Schema::table('conversations', function (Blueprint $table) {
            $table->unique(['client_id', 'store_id']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropUnique(['client_id', 'store_id', 'order_id']);
        });
    }

    public function down(): void
    {
        Schema::table('conversations', function (Blueprint $table) {
            $table->unique(['client_id', 'store_id', 'order_id']);
        });

        Schema::table('conversations', function (Blueprint $table) {
            $table->dropUnique(['client_id', 'store_id']);
        });
    }
};
