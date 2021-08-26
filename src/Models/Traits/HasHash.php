<?php

namespace Lara\Jarvis\Models\Traits;

use Illuminate\Support\Str;

trait HasHash
{
    protected static function bootHasHash ()
    {
        static::creating(function ($model) {
            if (!$model->hash) {
                $model->hash = (string)Str::uuid();
            }
        });
    }
}
