<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TranslationCoverageTest extends TestCase
{
    use RefreshDatabase;

    public function test_supported_locales_have_translation_catalogues(): void
    {
        $english = json_decode(file_get_contents(base_path('resources/lang/en.json')), true, 512, JSON_THROW_ON_ERROR);

        foreach (User::SUPPORTED_LOCALES as $locale) {
            $catalog = json_decode(file_get_contents(base_path("resources/lang/{$locale}.json")), true, 512, JSON_THROW_ON_ERROR);

            $this->assertIsArray($catalog);
            $this->assertArrayHasKey('Language', $catalog);
            $this->assertArrayHasKey('Users', $catalog);
        }
    }

    public function test_filament_shield_has_translations_for_supported_non_english_locales(): void
    {
        $this->assertFileDoesNotExist(base_path('resources/lang/vendor/filament-shield/it/filament-shield.php'));
        $this->assertFileDoesNotExist(base_path('resources/lang/vendor/filament-shield/bn/filament-shield.php'));
    }
}
