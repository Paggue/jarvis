<?php

namespace Lara\Jarvis\Tests\Unit\Validators;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Lara\Jarvis\Tests\TestCase;
use Lara\Jarvis\Validators\Twofactor\TwoFactorEnableValidator;

class TwoFactorEnableValidatorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_validate ()
    {
        $code = [
            'secret' => (string)rand(111111, 999999),
        ];

        $validator = new TwoFactorEnableValidator();

        self::assertEquals(null, $validator->validate($code));

        $code = [
            'secret' => rand(111111, 999999),
        ];

        $this->expectException(ValidationException::class);

        $validator->validate($code);

        $code = [
            'secret' => (string)rand(111111, 99999),
        ];

        $validator->validate($code);
    }
}
