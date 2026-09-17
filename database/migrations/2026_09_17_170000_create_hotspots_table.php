<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hotspots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->string('title', 120);
            $table->text('description')->nullable();
            $table->decimal('position_x', 8, 4)->default(0);
            $table->decimal('position_y', 8, 4)->default(0);
            $table->decimal('position_z', 8, 4)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->index(['item_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hotspots');
    }
};
