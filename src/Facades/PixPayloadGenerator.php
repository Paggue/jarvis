<?php

namespace Lara\Jarvis\Facades;

use Illuminate\Support\Facades\Facade;

class PixPayloadGenerator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'pixPayloadGenerator';
    }
}
