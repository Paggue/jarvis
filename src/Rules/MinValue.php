<?php

namespace Lara\Jarvis\Rules;

use Illuminate\Contracts\Validation\Rule;

class MinValue implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(int $minValue = 100)
    {
        $this->minValue = $minValue;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return $value >=  $this->minValue;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid value';
    }
}
