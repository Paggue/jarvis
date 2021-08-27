<?php

namespace Lara\Jarvis\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SettingValidator
{
    public static function validate ($data)
    {
        $validator = Validator::make($data, [
            '*.*.value' => 'required',
        ]);

        if ($validator->fails())
            throw new ValidationException($validator);
    }
}
