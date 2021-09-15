<?php


namespace Lara\Jarvis\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AddressValidator
{
    public static function validate ($data)
    {
        $validator = Validator::make($data, array_merge([
            'zip_code'     => 'sometimes',
            'street'       => 'sometimes|max:255',
            'house_number' => 'sometimes|numeric',
            'neighborhood' => 'sometimes|max:255',
            'state_id'     => 'sometimes|numeric|exists:states,id',
            'city_id'      => 'sometimes|numeric|exists:cities,id',
        ]));

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
