<?php

namespace Lara\Jarvis\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Lara\Jarvis\Models\Role;

class RoleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Role::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition ()
    {
        return [
            'name'        => $this->faker->name,
            'guard_name'  => $this->faker->name,
            'description' => $this->faker->sentence,
        ];
    }
}
