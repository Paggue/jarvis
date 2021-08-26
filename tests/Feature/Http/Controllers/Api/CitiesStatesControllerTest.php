<?php

namespace Lara\Jarvis\Tests\Feature\Http\Controllers\Api;

use Lara\Jarvis\Models\City;
use Lara\Jarvis\Models\State;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Lara\Jarvis\Tests\TestCase;

class CitiesStatesControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_return_a_collection_of_paginated_states ()
    {
        $quantity = 10;

        $response = $this->json('GET', "/api/states");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data'  => [
                    '*' => [
                        'id', 'name', 'uf'
                    ]
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta'  => [
                    'current_page', 'last_page', 'from',
                    'to', 'path', 'per_page', 'total'
                ]
            ])
            ->assertJsonCount(0, 'data.*');

        State::factory()->count($quantity)->create();

        $response = $this->json('GET', "/api/states");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data'  => [
                    '*' => [
                        'id', 'name', 'uf'
                    ]
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta'  => [
                    'current_page', 'last_page', 'from',
                    'to', 'path', 'per_page', 'total'
                ]
            ])
            ->assertJsonCount($quantity, 'data.*');
    }

    /**
     * @test
     */
    public function can_return_a_collection_of_paginated_cities ()
    {
        $quantity = 10;

        $response = $this->json('GET', "/api/cities");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data'  => [
                    '*' => [
                        'id', 'name', 'latitude', 'longitude', 'state_id',
                        'capital', 'created_at', 'updated_at',
                    ]
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta'  => [
                    'current_page', 'last_page', 'from',
                    'to', 'path', 'per_page', 'total'
                ]
            ])
            ->assertJsonCount(0, 'data.*');

        City::factory()->count($quantity)->create();

        $response = $this->json('GET', "/api/cities");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data'  => [
                    '*' => [
                        'id', 'name', 'latitude', 'longitude', 'state_id',
                        'capital', 'created_at', 'updated_at',
                    ]
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta'  => [
                    'current_page', 'last_page', 'from',
                    'to', 'path', 'per_page', 'total'
                ]
            ])
            ->assertJsonCount($quantity, 'data.*');
    }

    /**
     * @test
     */
    public function can_return_a_collection_of_paginated_cities_from_specified_state ()
    {
        $quantity = 10;

        $states = State::factory()->count(2)->create();
        $states = $states->toArray();

        $cities_from_same_state = City::factory()->count($quantity / 2)->create([
            'state_id' => $states[0]["id"],
        ]);

        $other_cities = City::factory()->count($quantity / 2)->create();

        $state_id = $states[0]["id"];

        $response = $this->json('GET', "/api/cities", ['where' => ["state_id,$state_id"]]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data'  => [
                    '*' => [
                        'id', 'name', 'latitude', 'longitude', 'state_id',
                        'capital', 'created_at', 'updated_at',
                    ]
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta'  => [
                    'current_page', 'last_page', 'from',
                    'to', 'path', 'per_page', 'total'
                ]
            ])
            ->assertJsonCount($quantity / 2, 'data.*');


        $response = $this->json('GET', "/api/cities", ['where' => ["state_id,-1"]]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data'  => [
                    '*' => [
                        'id', 'name', 'latitude', 'longitude', 'state_id',
                        'capital', 'created_at', 'updated_at',
                    ]
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta'  => [
                    'current_page', 'last_page', 'from',
                    'to', 'path', 'per_page', 'total'
                ]
            ])
            ->assertJsonCount(0, 'data.*');
    }
}
