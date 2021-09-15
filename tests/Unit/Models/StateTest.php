<?php

namespace Lara\Jarvis\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lara\Jarvis\Tests\TestCase;
use Lara\Jarvis\Models\State;

class StateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_state_has_a_name ()
    {
        $state = State::factory()->create(['name' => 'Fake Title']);
        $this->assertEquals('Fake Title', $state->name);
    }

    /** @test */
    function a_state_has_a_uf ()
    {
        $state = State::factory()->create(['uf' => 'BA']);
        $this->assertEquals('BA', $state->uf);
    }
}
