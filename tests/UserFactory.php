<?php

namespace Lara\Jarvis\Tests;

use Orchestra\Testbench\Factories\UserFactory as TestbenchUserFactory;
use Faker\Provider\pt_BR\Company;
use Faker\Provider\pt_BR\Person;

class UserFactory extends TestbenchUserFactory
{
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition ()
    {
        $this->faker->addProvider(new Person($this->faker));
        $this->faker->addProvider(new Company($this->faker));

        return [
            'name'              => $this->faker->name,
            'email'             => $this->faker->unique()->safeEmail,
            'document'          => rand() % 2 == 0 ? $this->faker->cnpj(false) : $this->faker->cpf(false),
            'legal_name'        => $this->faker->name,
            'email_verified_at' => now(),
            'password'          => bcrypt('password'),
            'remember_token'    => \Illuminate\Support\Str::random(10),
        ];
    }
}
