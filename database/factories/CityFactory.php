<?php

namespace Lara\Jarvis\Database\Factories;

use Faker\Provider\pt_BR\Company;
use Faker\Provider\pt_BR\Person;
use Illuminate\Database\Eloquent\Factories\Factory;
use Lara\Jarvis\Models\City;
use Lara\Jarvis\Models\State;

class CityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = City::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $state = State::factory()->create();
        return [
            'name' => $this->faker->city,
            'state_id' => $state->id,
        ];
    }
}
