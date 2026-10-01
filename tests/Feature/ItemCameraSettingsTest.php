<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Database\Seeders\ShieldSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemCameraSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ShieldSeeder::class);
    }

    private function makeStaff(): User
    {
        $user = User::factory()->create();
        $user->assignRole('panel_user');

        return $user;
    }

    private function makeItem(): Item
    {
        return Item::create([
            'name' => 'Test Item',
            'slug' => 'test-item',
            'model_path' => 'items/models/test.glb',
            'is_published' => true,
        ]);
    }

    public function test_guest_cannot_update_camera_settings(): void
    {
        $item = $this->makeItem();

        // This app redirects guests to the admin login for every web route,
        // JSON or not (see bootstrap/app.php's redirectGuestsTo/shouldRenderJsonWhen),
        // so the guard check surfaces as a redirect rather than a 401 here.
        $response = $this->post(route('items.camera-settings.update', $item->slug), [
            'position' => ['x' => 1, 'y' => 2, 'z' => 3],
            'target' => ['x' => 0, 'y' => 1, 'z' => 0],
            'vertical_fov_degrees' => 50,
            'near' => 0.1,
            'far' => 100,
        ]);

        $response->assertRedirect(route('filament.admin.auth.login'));
        $this->assertNull($item->fresh()->camera_settings);
    }

    public function test_authenticated_staff_can_update_camera_settings(): void
    {
        $user = $this->makeStaff();
        $item = $this->makeItem();

        $response = $this->actingAs($user)->postJson(route('items.camera-settings.update', $item->slug), [
            'position' => ['x' => 1.25, 'y' => 2.5, 'z' => -3],
            'target' => ['x' => 0, 'y' => 1, 'z' => 0],
            'vertical_fov_degrees' => 55,
            'near' => 0.05,
            'far' => 200,
        ]);

        $response->assertOk()->assertJson(['saved' => true]);

        $item->refresh();
        $this->assertEquals(1.25, $item->camera_settings['position']['x']);
        $this->assertEquals(55, (float) $item->camera_settings['vertical_fov_degrees']);
    }

    public function test_far_must_be_greater_than_near(): void
    {
        $user = $this->makeStaff();
        $item = $this->makeItem();

        $response = $this->actingAs($user)->post(route('items.camera-settings.update', $item->slug), [
            'position' => ['x' => 1, 'y' => 2, 'z' => 3],
            'target' => ['x' => 0, 'y' => 1, 'z' => 0],
            'vertical_fov_degrees' => 50,
            'near' => 100,
            'far' => 10,
        ]);

        $response->assertSessionHasErrors('far');
        $this->assertNull($item->fresh()->camera_settings);
    }
}
