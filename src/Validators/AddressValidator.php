<?php


namespace Lara\Jarvis\Validators;

class AddressValidator
{
    use ValidatorTrait;

    protected function rules ($data = null)
    {
        return [
            'zip_code'     => 'sometimes',
            'street'       => 'sometimes|max:255',
            'house_number' => 'sometimes|numeric',
            'neighborhood' => 'sometimes|max:255',
            'state_id'     => 'sometimes|numeric|exists:states,id',
            'city_id'      => 'sometimes|numeric|exists:cities,id',
        ];
    }
}
