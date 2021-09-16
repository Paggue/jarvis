<?php

namespace Lara\Jarvis\Tests\Unit\Validators;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Lara\Jarvis\Rules\MinValue;
use Lara\Jarvis\Tests\TestCase;

class MinValueRuleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_validate ()
    {
        $start = 0;
        $mid   = 50;
        $end   = 100;

        $data = [
            'value' => rand($mid + 1, $end),
        ];

        $validator = Validator::make($data, [
            'value' => ['required', 'numeric', new MinValue(rand($start, $mid))],
        ]);

        if ($validator->fails())
            throw new ValidationException($validator);

        self::assertEquals([], $validator->errors()->messages());


        $data = [
            'value' => rand($start, $mid),
        ];

        $validator = Validator::make($data, [
            'value' => ['required', 'numeric', new MinValue(rand($mid + 1, $end))],
        ]);

        $this->expectException(ValidationException::class);

        if ($validator->fails())
            throw new ValidationException($validator);
    }
}
