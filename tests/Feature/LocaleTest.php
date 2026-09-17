<?php

namespace Tests\Feature;

use App\Http\Middleware\SetLocale;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_default_language_is_applied_when_user_has_no_preference(): void
    {
        Setting::set('default_locale', 'fr');
        $this->actingAs(User::factory()->create());

        app(SetLocale::class)->handle(Request::create('/'), fn ($request) => response('ok'));

        $this->assertSame('fr', app()->getLocale());
        $this->assertSame('Utilisateurs', __('Users'));
    }

    public function test_user_language_preference_overrides_system_default(): void
    {
        Setting::set('default_locale', 'en');
        $user = User::factory()->create(['locale' => 'fr']);
        $this->actingAs($user);

        app(SetLocale::class)->handle(Request::create('/'), fn ($request) => response('ok'));

        $this->assertSame('fr', app()->getLocale());
        $this->assertSame('Utilisateurs', __('Users'));
    }

    public function test_authenticated_user_can_change_language_from_the_topbar_action(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('locale.update'), ['locale' => 'fr'])
            ->assertRedirect();

        $this->assertSame('fr', $user->fresh()->locale);
    }
}
