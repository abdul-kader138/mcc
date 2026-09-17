<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_analytics_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('item_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event', 40)->index();
            $table->string('visitor_hash', 64)->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent()->index();
            $table->index(['item_id', 'event', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_analytics_events');
    }
};
