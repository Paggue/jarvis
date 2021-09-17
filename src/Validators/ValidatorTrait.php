<?php


namespace Lara\Jarvis\Validators;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait ValidatorTrait
{
    protected function rules ()
    {
        if (isset($this->rules)) {
            return $this->rules;
        }

        return [];
    }

    public function validate ($data)
    {
        $validator = Validator::make($data, $this->rules());

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
