<?php

namespace Lara\Jarvis\Tests\Unit\Validators;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Lara\Jarvis\Tests\TestCase;
use Lara\Jarvis\Tests\User;
use Lara\Jarvis\Validators\Twofactor\TwoFactorDisableValidator;

class TwoFactorDisableValidatorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_validate ()
    {
        $user = User::factory()->create();

        $data = [
            'user_id' => $user->id,
        ];

        $validator = new TwoFactorDisableValidator();

        self::assertEquals(null, $validator->validate($data));

        $data = [
            'user_id' => 0,
        ];

        $this->expectException(ValidationException::class);

        $validator->validate($data);
    }
}
