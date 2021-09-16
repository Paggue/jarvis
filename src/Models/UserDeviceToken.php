<?php

namespace Lara\Jarvis\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDeviceToken extends Model
{
    use HasFactory;

    protected $fillable = ['token'];

    protected static function newFactory ()
    {
        return \Lara\Jarvis\Database\Factories\UserDeviceTokenFactory::new();
    }
}
