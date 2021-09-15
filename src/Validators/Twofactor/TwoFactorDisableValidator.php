<?php


namespace Lara\Jarvis\Validators\Twofactor;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TwoFactorDisableValidator
{
    public static function validate ($data)
    {
        $validator = Validator::make($data, [
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
