<?php

namespace Tests\Feature\Http\Controllers\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lara\Jarvis\Tests\TestCase;
use Lara\Jarvis\Tests\User;
use Laravel\Passport\Passport;

class TwoFactorControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    public function setUp (): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * @test
     */
    public function can_get_url_code ()
    {
        Passport::actingAs($this->user);

        $response = $this->json('get', '/api/auth/2fa');

        $response->assertOk();
    }

    /**
     * @test
     */
    public function will_fail_with_422_if_2fa_is_already_enabled ()
    {
        Passport::actingAs($this->user);

        //enabling user 2fa
        $this->user->update(['two_factor_enable' => true,]);

        $response = $this->json('get', '/api/auth/2fa');

        $response->assertStatus(422)
            ->assertJsonStructure(['message']);

        $response = $this->json('post', '/api/auth/2fa');

        $response->assertStatus(422)
            ->assertJsonStructure(['message']);
    }

    /**
     * @test
     */
    public function will_fail_with_422_if_disable_user_id_missing ()
    {
        //create a super admin
        $user = User::factory()->create();

        Passport::actingAs($user);

        $response = $this->json('POST', '/api/auth/2fa/disable', ['user_id', -1]);

        $response->assertStatus(422);
    }

    /**
     * @test
     */
    public function can_disable_2fa ()
    {
        //create a super admin
        $user = User::factory()->create();

        Passport::actingAs($user);

        //enabling user 2fa
        $this->user->update(['two_factor_enable' => true,]);

        //the test start here
        $response = $this->json('POST', '/api/auth/2fa/disable', ['user_id' => 1]);

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function can_check_2fa ()
    {
        //create a super admin
        $user = User::factory()->create();

        Passport::actingAs($user);

        $response = $this->json('GET', '/api/auth/2fa/check');

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Autenticação não está habilitada.'
            ]);


        //enabling user 2fa
        $user->update(['two_factor_enable'=> true,]);

        $response = $this->json('GET', '/api/auth/2fa/check');

        $response->assertStatus(200)
            ->assertSee(null);
    }
}
