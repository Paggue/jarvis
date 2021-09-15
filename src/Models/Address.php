<?php

namespace Lara\Jarvis\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Lara\Jarvis\Models\Traits\HasCoordinates;
use Lara\Jarvis\Models\Traits\HasSanitizer;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContracts;

class Address extends Model implements AuditableContracts
{
    use HasFactory, SoftDeletes, Auditable, HasCoordinates, HasSanitizer;

    protected $fillable = [
        "zip_code",
        "street",
        "house_number",
        "neighborhood",
        "state_id",
        "city_id",
        "complement",
        "observation",
        "phone",
        "latitude",
        "longitude",
    ];

    protected static function newFactory()
    {
        return \Lara\Jarvis\Database\Factories\AddressFactory::new();
    }

    /**
     * Get the parent address model.
     */
    public function personable()
    {
        return $this->morphTo();
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
