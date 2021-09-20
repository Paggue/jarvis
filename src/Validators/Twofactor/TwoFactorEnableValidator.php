<?php


namespace Lara\Jarvis\Validators\Twofactor;

use Lara\Jarvis\Validators\ValidatorTrait;

class TwoFactorEnableValidator
{
    use ValidatorTrait;

    protected function rules ($data = null)
    {
        return [
            'secret' => 'required|string|digits:6',
        ];
    }
}
