<?php

namespace Lara\Jarvis\Models\Traits;

use Lara\Jarvis\Models\Address;

trait HasAddress
{
    public function address ()
    {
        return $this->morphOne(Address::class, 'personable');
    }
}
