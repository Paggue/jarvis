<?php

namespace Lara\Jarvis\Models;

use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    protected $fillable = [
        "name", "type", "date",
    ];
}
