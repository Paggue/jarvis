<?php

namespace Lara\Jarvis\Models\Traits;

use Lara\Jarvis\Utils\Helpers;

trait HasSanitizer
{
    protected static function bootHasSanitizer()
    {
        static::saving(function ($model) {
            self::sanitizeParams($model);
        });
    }

    private static function sanitizeParams ($model)
    {
        isset($model->phone) ? $model->phone = Helpers::sanitizeString($model->phone) : null;

        isset($model->cel_phone) ? $model->cel_phone = Helpers::sanitizeString($model->cel_phone) : null;

        isset($model->zip_code) ? $model->zip_code = Helpers::sanitizeString($model->zip_code) : null;

        isset($model->document) ? $model->document = Helpers::sanitizeString($model->document) : null;

        isset($model->plate) ? $model->plate = Helpers::sanitizeStringWithLetters($model->plate) : null;
    }
}
