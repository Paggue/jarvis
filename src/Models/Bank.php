<?php

namespace Lara\Jarvis\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return \Lara\Jarvis\Database\Factories\BankFactory::new();
    }
}
