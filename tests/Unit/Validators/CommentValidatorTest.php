<?php

namespace Lara\Jarvis\Tests\Unit\Validators;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Lara\Jarvis\Models\Comment;
use Lara\Jarvis\Tests\TestCase;
use Lara\Jarvis\Validators\CommentValidator;

class CommentValidatorTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_validate ()
    {
        $address = Comment::factory()->make();

        $validator = new CommentValidator();

        self::assertEquals(null, $validator->validate($address->toArray()));

        $address = Comment::factory()->make([
            'text' => null
        ]);

        $this->expectException(ValidationException::class);

        $validator->validate($address->toArray());
    }
}
