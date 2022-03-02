<?php

namespace Lara\Jarvis\Tests\Unit\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Lara\Jarvis\Models\City;
use Lara\Jarvis\Models\Comment;
use Lara\Jarvis\Tests\TestCase;
use Lara\Jarvis\Tests\User;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function a_comment_has_a_text()
    {
        $model = Comment::factory()->create([
            'text'             => 'Fake Title',
            'commentable_type' => City::class,
            'commentable_id'   => City::factory()->create()->id,
            'user_id'          => User::factory()->create()->id,
        ]);

        $this->assertEquals('Fake Title', $model->text);
        $this->assertEquals(City::class, $model->commentable_type);
    }
}
