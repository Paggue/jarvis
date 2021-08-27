<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Lara\Jarvis\Enums\UserRoles;
use Lara\Jarvis\Models\Setting;
use Laravel\Passport\Passport;
use Tests\TestCase;

class SettingsControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected $user = null;
    protected $guard_api = ['guard_name' => 'api'];

    public function setUp (): void
    {
        parent::setUp();

        $role = UserRoles::SUPER_ADMIN;

        $this->user = User::factory()->create();

        \Artisan::call('db:seed', ['-vvv' => true]);

        $this->user->assignRole($role);
    }

    private const STRUCTURE = [
        'data'  => [
            '*' => [
                'id', 'name', 'company_id', 'qtd_collaborators', 'observation', 'created_at', 'updated_at',
                'company' => [
                    'id', 'segment_id', 'hash', 'legal_name', 'name', 'document',
                    'legal_entity', 'cel_phone', 'email', 'birth_date', 'phone',
                    'created_at', 'updated_at',
                ]
            ]
        ],
        'links' => ['first', 'last', 'prev', 'next'],
        'meta'  => [
            'current_page', 'last_page', 'from',
            'to', 'path', 'per_page', 'total'
        ]
    ];

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
    public function non_permission_users_cannot_access_the_following_endpoints_for_the_settings_api ()
    {
        $user = User::factory()->create();

        Passport::actingAs($user);

        $index = $this->json('GET', '/api/settings');
        $index->assertStatus(403);

        $store = $this->json('PUT', '/api/settings');
        $store->assertStatus(403);
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
