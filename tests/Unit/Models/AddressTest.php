<?php

namespace Lara\Jarvis\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lara\Jarvis\Models\Address;
use Lara\Jarvis\Models\City;
use Lara\Jarvis\Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function an_address_has_a_street ()
    {
        $city = City::factory()->create();

        $model = Address::factory()->create([
            'street'          => 'Fake Title',
            'personable_type' => City::class,
            'personable_id'   => $city->id,
        ]);

        $this->assertEquals('Fake Title', $model->street);
        $this->assertEquals(City::class, $model->personable_type);
        $this->assertEquals($city->id, $model->personable_id);
    }
}
