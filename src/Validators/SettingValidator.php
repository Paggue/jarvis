<?php

namespace Lara\Jarvis\Validators;

class SettingValidator
{
    use ValidatorTrait;

    protected function rules ($data = null)
    {
        return [
            '*.*.value' => 'required',
        ];
    }
}
