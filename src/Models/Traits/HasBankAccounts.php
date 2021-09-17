<?php

namespace Lara\Jarvis\Models\Traits;

use Lara\Jarvis\Models\BankAccount;

trait HasBankAccounts
{
    public function bankAccounts ()
    {
        return $this->morphMany(BankAccount::class, 'bank_accountable');
    }
}
