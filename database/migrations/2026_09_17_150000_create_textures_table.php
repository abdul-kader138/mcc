<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('textures', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('textures');
    }
};
