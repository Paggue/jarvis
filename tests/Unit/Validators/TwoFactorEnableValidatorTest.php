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
    function can_validate_two_factor_enable ()
    {
        $code = [
            'secret' => (string)rand(000000, 999999),
        ];

        self::assertEquals(null, TwoFactorEnableValidator::validate($code));

        $code = [
            'secret' => rand(000000, 999999),
        ];

        $this->expectException(ValidationException::class);

        TwoFactorEnableValidator::validate($code);

        $code = [
            'secret' => (string)rand(00000, 99999),
        ];

        TwoFactorEnableValidator::validate($code);
    }
}
