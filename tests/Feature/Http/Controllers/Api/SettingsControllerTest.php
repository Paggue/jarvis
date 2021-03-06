<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lara\Jarvis\Models\Setting;
use Lara\Jarvis\Tests\User;
use Laravel\Passport\Passport;
use Lara\Jarvis\Tests\TestCase;

class SettingsControllerTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp (): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * @test
     */
    public function non_authenticated_users_cannot_access_the_following_endpoints_for_the_settings_api ()
    {
        $index = $this->json('GET', '/api/settings');
        $index->assertStatus(401);

        $store = $this->json('PUT', '/api/settings');
        $store->assertStatus(401);
    }

    /**
     * @test
     */
    public function can_return_settings ()
    {

        Passport::actingAs($this->user);

        $response = $this->json('GET', "/api/settings");

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function will_return_422_if_setting_fields_are_missing ()
    {
        Passport::actingAs($this->user);

        $response = $this->json('PUT', "/api/settings", [
            ['ak47' => null],
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function can_update_setting ()
    {
        $data = [];

        $newItem = Setting::factory()->make();

        Passport::actingAs($this->user);

        foreach (Setting::all() as $item) {
            $data[$item->key] = $item->value;
        }


        //same params
        $response = $this->json('PUT', "/api/settings", array_merge($data, [
            $newItem->key => $newItem->value
        ]));

        $key        = preg_replace('/\s+/', '', $newItem->key);
        $data[$key] = $newItem->value;

        $response->assertStatus(200)
            ->assertJson($data);

        $this->assertDatabaseHas('settings', [
            'key' => $key,
        ]);

        //changing value
        $response = $this->json('PUT', "/api/settings", [
            $key => $newItem->value . '_updated',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                $key => $newItem->value . '_updated',
            ]);

        $this->assertDatabaseHas('settings', [
            'value' => $newItem->value . '_updated',
        ]);
    }
}
