<?php

namespace Lara\Jarvis\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lara\Jarvis\Models\City;
use Lara\Jarvis\Models\Comment;
use Lara\Jarvis\Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_comment_has_a_text ()
    {
        $model = Comment::factory()->create([
            'text'             => 'Fake Title',
            'commentable_type' => City::class
        ]);

        $this->assertEquals('Fake Title', $model->text);
        $this->assertEquals(City::class, $model->commentable_type);
    }
}
