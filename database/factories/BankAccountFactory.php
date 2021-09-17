<?php

namespace Lara\Jarvis\Database\Factories;

use Faker\Provider\pt_BR\Person;
use Illuminate\Database\Eloquent\Factories\Factory;
use Lara\Jarvis\Models\Bank;
use Lara\Jarvis\Models\BankAccount;

class BankAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BankAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition ()
    {
        $this->faker->addProvider(new Person($this->faker));
        $this->faker->addProvider(new \Faker\Provider\pt_BR\Company($this->faker));
        $document = rand() % 2 == 0 ? $this->faker->cnpj(false) : $this->faker->cpf(false);

        return [
            'holder'        => $this->faker->name,
            "document"      => $document,
            'account_type'  => $this->faker->randomElement(['cc', 'cp']),
            'agency'        => $this->faker->randomNumber(4),
            'agency_digit'  => $this->faker->randomDigit(),
            'account'       => $this->faker->randomNumber(4),
            'account_digit' => $this->faker->randomDigit(),
            "pix_key"       => $this->faker->e164PhoneNumber,
            'operation'     => $this->faker->randomDigit(),
            'bank_id'       => Bank::factory()->create([
                "name" => "Caixa Econômica",
                "code" => "104",
                "ispb" => "00360305"
            ])->id,
        ];
    }
}
