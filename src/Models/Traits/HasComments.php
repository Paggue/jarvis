<?php

namespace Lara\Jarvis\Models\Traits;

use Lara\Jarvis\Models\Comment;

trait HasComments
{
    public function comments ()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
