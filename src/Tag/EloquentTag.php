<?php

namespace SecTheater\Jarvis\Tag;

use Illuminate\Database\Eloquent\Model;
use SecTheater\Jarvis\Post\EloquentPost;
use SecTheater\Jarvis\User\EloquentUser;

class EloquentTag extends Model
{
    protected $table = 'tags';
    protected $guarded = [];

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function posts()
    {
        return $this->belongsToMany(EloquentPost::class, 'post_tag', 'tag_id', 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(EloquentUser::class);
    }
}
