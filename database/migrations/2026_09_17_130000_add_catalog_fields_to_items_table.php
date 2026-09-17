<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table): void {
            $table->string('category')->nullable()->after('description')->index();
            $table->json('tags')->nullable()->after('category');
            $table->boolean('is_featured')->default(false)->after('is_published')->index();
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table): void {
            $table->dropColumn(['category', 'tags', 'is_featured']);
        });
    }
};
