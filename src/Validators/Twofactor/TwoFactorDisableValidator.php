<?php


namespace Lara\Jarvis\Validators\Twofactor;

use Lara\Jarvis\Validators\ValidatorTrait;

class TwoFactorDisableValidator
{
    use ValidatorTrait;

    protected function rules ($data = null)
    {
        return [
            'user_id' => 'required|exists:users,id',
        ];
    }
}
