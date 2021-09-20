<?php

namespace Lara\Jarvis\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'ispb',
    ];

    protected static function newFactory ()
    {
        return \Lara\Jarvis\Database\Factories\BankFactory::new();
    }
}
