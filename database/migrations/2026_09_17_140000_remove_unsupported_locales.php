<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->whereIn('locale', ['it', 'bn'])->update(['locale' => null]);
        DB::table('settings')->where('key', 'default_locale')->whereIn('value', ['it', 'bn'])->update(['value' => 'en']);
    }

    public function down(): void
    {
        // Removed locales are intentionally not restored.
    }
};
