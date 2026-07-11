<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('security_events', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->string('ip_address', 45);
            $table->string('country', 2)->nullable();
            $table->string('path', 500);
            $table->string('method', 10);
            $table->string('user_agent')->nullable();
            $table->json('details')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['type', 'created_at']);
            $table->index(['ip_address', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_events');
    }
};
