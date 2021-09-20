<?php

namespace Tests\Feature\Http\Controllers\Api;

use Lara\Jarvis\Models\Bank;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Lara\Jarvis\Tests\TestCase;
use Lara\Jarvis\Tests\User;
use Laravel\Passport\Passport;

class BankControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Collection|Model|mixed
     */
    private $user;

    public function setUp (): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /**
     * @test
     */
    public function non_authenticated_users_cannot_access_the_banks_api ()
    {
        $index = $this->json('GET', '/api/banks');
        $index->assertStatus(401);
    }

    /**
     * @test
     */
    public function can_return_a_collection_of_paginated_banks ()
    {
        $quantity = 20;

        Passport::actingAs($this->user, ['create-servers']);

        Bank::factory()->count($quantity)->create();

        $this->assertDatabaseCount('banks', $quantity);


        $response = $this->json('GET', "/api/banks");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data'  => [
                    '*' => [
                        'id', 'name', 'code', 'ispb',
                        'created_at', 'updated_at',
                    ]
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta'  => [
                    'current_page', 'last_page', 'from',
                    'to', 'path', 'per_page', 'total'
                ]
            ])
            ->assertJsonCount($quantity, 'data.*');


        Bank::factory()->count(10)->create();
        $response = $this->json('GET', "/api/banks", [
            'limit' => 30,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data'  => [
                    '*' => [
                        'id', 'name', 'code', 'ispb',
                        'created_at', 'updated_at',
                    ]
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta'  => [
                    'current_page', 'last_page', 'from',
                    'to', 'path', 'per_page', 'total'
                ]
            ])
            ->assertJsonCount(30, 'data.*');


        $data   = Bank::first();
        $wheres = "where[]=id,$data->id";

        $response = $this->json('GET', "/api/banks?$wheres");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    0 => [
                        'id' => $data->id,
                    ]
                ]
            ])
            ->assertJsonCount(1, 'data');

        // where
        $data   = Bank::latest('id')->first();
        $wheres = "where[]=name,$data->name";

        $response = $this->json('GET', "/api/banks?$wheres");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    0 => [
                        'id' => $data->id,
                    ]
                ]
            ])
            ->assertJsonCount(1, 'data');


        // like
        $data  = Bank::latest('id')->first();
        $likes = "like[]=name,$data->name";

        $response = $this->json('GET', "/api/banks?$likes");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    0 => [
                        'id' => $data->id,
                    ],
                ]
            ])
            ->assertJsonCount(1, 'data');


        // between
        $date    = $data->created_at->format('Y-m-d');
        $between = "between[]=created_at,$date,$date";

        $response = $this->json('GET', "/api/banks?$between");

        $response->assertStatus(200)
            ->assertJsonCount($quantity, 'data');


        // order by id desc
        $data  = Bank::latest('id')->first();
        $query = "order=id,desc";

        $response = $this->json('GET', "/api/banks?$query");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    0 => [
                        'id' => $data->id
                    ]]
            ])
            ->assertJsonCount($quantity, 'data');


        // search with order by
        $query = "order=id,asc";

        $data1 = Bank::first();
        $name1 = explode(',', $data1->name)[0];
        $query = $query . "&search[]=name,$name1";

        $data2 = Bank::latest('id')->first();
        $name2 = explode(',', $data2->name)[0];
        $query = $query . "&search[]=name,$name2";

        $response = $this->json('GET', "/api/banks?$query");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    0 => [
                        'id' => $data1->id
                    ],
                    1 => [
                        'id' => $data2->id
                    ]
                ]
            ])
            ->assertJsonCount(2, 'data');


        // various filters
        $query = "between[]=created_at,$date,$date";

        $data  = Bank::latest('id')->first();
        $query = $query . "&like[]=name,$data->name";
        $query = $query . "&where[]=id,$data->id";

        $response = $this->json('GET', "/api/banks?$query");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    0 => [
                        'id' => $data->id,
                    ],
                ]
            ])
            ->assertJsonCount(1, 'data');
    }

    /**
     * @test
     */
    public function can_order_by_name_default ()
    {
        $first = 'A';
        $last  = 'B';

        $bank1 = Bank::factory()->create([
            'name' => $last
        ]);

        $bank2 = Bank::factory()->create([
            'name' => $first
        ]);

        Passport::actingAs($this->user);

        $response = $this->json('GET', 'api/banks');

        $response->assertJson([
            'data' => [
                0 => [
                    'name' => $first,
                ],
                1 => [
                    'name' => $last,
                ],
            ],
        ]);
    }
}
