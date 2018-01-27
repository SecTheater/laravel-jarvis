<?php

namespace SecTheater\Jarvis\Reply;

use Illuminate\Database\Eloquent\Model;
use SecTheater\Jarvis\Comment\EloquentComment;
use SecTheater\Jarvis\Like\EloquentLike;
use SecTheater\Jarvis\Post\EloquentPost;
use SecTheater\Jarvis\User\EloquentUser;

class EloquentReply extends Model
{
    protected $table = 'replies';
    protected $guarded = [];

    public function comment()
    {
        return $this->belongsTo(EloquentComment::class);
    }

    public function user()
    {
        return $this->belongsTo(EloquentUser::class);
    }

    public function post()
    {
        return $this->belongsTo(EloquentPost::class);
    }

    public function likes()
    {
        return $this->morphMany(EloquentLike::class, 'likable');
    }
}
