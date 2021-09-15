<?php


namespace Lara\Jarvis\Validators\Twofactor;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TwoFactorEnableValidator
{
    public static function validate ($data)
    {
        $validator = Validator::make($data, [
            'secret' => 'required|string|digits:6',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
