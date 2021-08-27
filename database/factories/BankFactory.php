<?php

namespace Lara\Jarvis\Database\Factories;

use Lara\Jarvis\Models\Bank;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Bank::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'code' => $this->faker->randomNumber(3),
            'ispb' => $this->faker->randomNumber(6),
        ];
    }
}
