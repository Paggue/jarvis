<?php

namespace Lara\Jarvis\Database\Factories;

use Lara\Jarvis\Models\City;
use Lara\Jarvis\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Lara\Jarvis\Tests\User;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $commentable = City::factory()->create();
        $author = User::factory()->create();

        return [
            'text' => $this->faker->sentence,
            'user_id' => $author->id,
            'commentable_id' => $commentable->id,
            'commentable_type' => get_class($commentable)
        ];
    }
}
