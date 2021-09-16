<?php

namespace Lara\Jarvis\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lara\Jarvis\Models\UserDeviceToken;
use Lara\Jarvis\Tests\TestCase;
use Lara\Jarvis\Tests\User;

class UserDeviceTokenTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function an_user_device_token_has_token ()
    {
        $user = User::factory()->create();

        $token = (string)rand(0000, 9999);

        $userDeviceToken = UserDeviceToken::factory()->create([
            'user_id' => $user->id,
            'token'   => $token,
        ]);

        $this->assertEquals($user->id, $userDeviceToken->user_id);

        $this->assertEquals($token, $userDeviceToken->token);
    }
}
