<?php

namespace Lara\Jarvis\Models\Traits;

use Illuminate\Support\Str;

trait UsesUUIDTrait
{
    protected static function bootUsesUUIDTrait()
    {
        static::creating(function ($model) {
            if (! $model->getKey()) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }
}
