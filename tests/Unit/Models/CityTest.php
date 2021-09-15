<?php

namespace Lara\Jarvis\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lara\Jarvis\Models\State;
use Lara\Jarvis\Tests\TestCase;
use Lara\Jarvis\Models\City;

class CityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_city_has_a_name ()
    {
        $city = City::factory()->create(['name' => 'Fake Title']);
        $this->assertEquals('Fake Title', $city->name);
    }

    /** @test */
    function a_city_has_a_uf ()
    {
        $state = State::factory()->create();
        $city  = City::factory()->create(['state_id' => $state->id]);

        $this->assertEquals($state->id, $city->state_id);
    }
}
