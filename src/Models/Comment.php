<?php

namespace Lara\Jarvis\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'text'];

    protected static function newFactory ()
    {
        return \Lara\Jarvis\Database\Factories\CommentFactory::new();
    }

    /**
     * Get the parent commentable model (post or video).
     */
    public function commentable ()
    {
        return $this->morphTo();
    }

    public function user ()
    {
        return $this->belongsTo(config('jarvis.providers.users.model'));
    }
}
