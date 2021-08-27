<?php

namespace Lara\Jarvis\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Lara\Jarvis\Models\Setting;

class SettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition ()
    {

        return [
            "key"         => $this->faker->name,
            "value"       => $this->faker->name,
            "description" => $this->faker->sentence,
        ];
    }
}
