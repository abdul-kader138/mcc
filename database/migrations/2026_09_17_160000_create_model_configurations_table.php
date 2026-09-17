<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('model_configurations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('token', 40)->unique();
            $table->string('name', 120)->default('My configuration');
            $table->json('configuration');
            $table->timestamps();
            $table->index(['item_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('model_configurations');
    }
};
