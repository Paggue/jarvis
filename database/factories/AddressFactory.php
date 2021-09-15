<?php

namespace Lara\Jarvis\Database\Factories;

use Faker\Provider\pt_BR\Company;
use Faker\Provider\pt_BR\Address as AddressFaker;
use Faker\Provider\pt_BR\Person;
use Illuminate\Database\Eloquent\Factories\Factory;
use Lara\Jarvis\Models\Address;
use Lara\Jarvis\Models\City;
use Lara\Jarvis\Models\State;
use Lara\Jarvis\Utils\Helpers;

class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition ()
    {
        $this->faker->addProvider(new Person($this->faker));
        $this->faker->addProvider(new Company($this->faker));
        $this->faker->addProvider(new AddressFaker($this->faker));

        return [
            'zip_code' => Helpers::sanitizeString($this->faker->postcode),
            'street' => $this->faker->streetName,
            'house_number' => rand(1,2000),
            'neighborhood' => $this->faker->name,
            'state_id' => State::factory()->create(),
            'city_id' => City::factory()->create(),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude
        ];
    }
}
