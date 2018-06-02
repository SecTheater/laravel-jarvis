<?php

namespace SecTheater\Jarvis\Tag;

use SecTheater\Jarvis\Model\EloquentModel;

class EloquentTag extends EloquentModel
{
    protected $table = 'tags';

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function posts()
    {
        return $this->belongsToMany($this->postModel, 'post_tag', 'tag_id', 'post_id');
    }

    public function user()
    {
        return $this->belongsTo($this->userModel);
    }
}
