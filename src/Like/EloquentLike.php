<?php

namespace SecTheater\Jarvis\Like;

use Illuminate\Database\Eloquent\Relations\Relation;
use SecTheater\Jarvis\Model\EloquentModel;

class EloquentLike extends EloquentModel
{
    protected $casts = [
        'like_status' => 'boolean',
    ];
    protected $table = 'likes';

    public function __construct()
    {
        Relation::morphMap([
            'Post'    => $this->postModel,
            'Comment' => $this->commentModel,
            'Reply'   => $this->replyModel,
        ]);
    }

    public function likable()
    {
        return $this->morphTo();
    }

    public function comments()
    {
        return $this->morphedByMany($this->commentModel, 'likable', 'likables', 'like_id', 'likable_id');
    }

    public function replies()
    {
        return $this->morphedByMany($this->replyModel, 'likable', 'likables', 'like_id', 'likable_id');
    }

    public function user()
    {
        return $this->belongsTo($this->userModel, 'user_id');
    }
}
