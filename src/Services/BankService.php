<?php

namespace Lara\Jarvis\Services;

use Lara\Jarvis\Models\Bank;

class BankService
{
    use ServiceTrait;

    function model ()
    {
        return new Bank;
    }
}
