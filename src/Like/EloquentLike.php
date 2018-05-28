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

    public function user()
    {
        return $this->belongsTo($this->userModel, 'user_id');
    }
}
