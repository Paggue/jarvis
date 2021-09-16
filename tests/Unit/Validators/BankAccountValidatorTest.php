<?php

namespace Lara\Jarvis\Tests\Unit\Validators;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Lara\Jarvis\Models\BankAccount;
use Lara\Jarvis\Tests\TestCase;
use Lara\Jarvis\Tests\User;
use Lara\Jarvis\Validators\BankAccountValidator;

class BankAccountValidatorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_validate ()
    {
        $user = User::factory()->create();

        $bankAccount = BankAccount::factory()->make([
            'bank_accountable_id'   => $user->id,
            'bank_accountable_type' => User::class,
        ]);

        self::assertEquals(null, BankAccountValidator::validate($bankAccount->toArray()));

        $bankAccount = BankAccount::factory()->make([
            'agency'                => null,
            'bank_accountable_id'   => $user->id,
            'bank_accountable_type' => User::class,
        ]);

        $this->expectException(ValidationException::class);

        BankAccountValidator::validate($bankAccount->toArray());
    }
}
