<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->string('type')->default('text')->after('body');
            $table->unsignedBigInteger('reference_id')->nullable()->after('type');
            $table->json('reference_data')->nullable()->after('reference_id');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['type', 'reference_id', 'reference_data']);
        });
    }
};
