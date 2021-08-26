<?php

namespace Lara\Jarvis\Models\Traits;


use Lara\Jarvis\Utils\Helpers;

trait HasLegalEntity
{
    protected static function bootHasLegalEntity()
    {
        static::saving(function ($model) {
            $model->legal_entity = Helpers::legalEntity($model->document);
        });
    }
}
