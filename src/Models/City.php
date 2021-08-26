<?php

namespace Lara\Jarvis\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'state_id',
        'latitude',
        'longitude',
        'capital',
    ];

    protected static function newFactory()
    {
        return \Lara\Jarvis\Database\Factories\CityFactory::new();
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
