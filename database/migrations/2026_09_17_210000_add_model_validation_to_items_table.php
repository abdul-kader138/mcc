<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table): void {
            $table->string('model_validation_status', 20)->default('pending')->after('view_count');
            $table->json('model_validation_messages')->nullable()->after('model_validation_status');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table): void {
            $table->dropColumn(['model_validation_status', 'model_validation_messages']);
        });
    }
};
