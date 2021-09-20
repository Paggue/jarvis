<?php


namespace Lara\Jarvis\Validators;

class CommentValidator
{
    use ValidatorTrait;

    protected function rules ($data = null)
    {
        return [
            'text' => 'required|string|max:255',
        ];
    }
}
