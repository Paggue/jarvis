<?php

namespace Lara\Jarvis\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    public function cities ()
    {
        return $this->hasMany(City::class);
    }

    protected static function newFactory()
    {
        return \Lara\Jarvis\Database\Factories\StateFactory::new();
    }
}
