<?php

namespace Lara\Jarvis\Tests\Unit\Validators;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Lara\Jarvis\Models\Address;
use Lara\Jarvis\Tests\TestCase;
use Lara\Jarvis\Validators\AddressValidator;

class AddressValidatorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_validate ()
    {
        $address = Address::factory()->make();

        self::assertEquals(null, AddressValidator::validate($address->toArray()));

        $address = Address::factory()->make([
            'state_id' => null
        ]);

        $this->expectException(ValidationException::class);

        AddressValidator::validate($address->toArray());
    }
}
