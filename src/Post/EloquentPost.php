<?php

namespace SecTheater\Jarvis\Post;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use SecTheater\Jarvis\Comment\EloquentComment;
use SecTheater\Jarvis\Reply\EloquentReply;
use SecTheater\Jarvis\Tag\EloquentTag;
use SecTheater\Jarvis\User\EloquentUser;

class EloquentPost extends Model
{
    protected $guarded = [];
    protected $table = 'posts';
    protected $dates = [
        'approved_at', 'created_at', 'updated_at',
    ];

    public function likes()
    {
        return $this->morphMany(EloquentLike::class, 'likable');
    }

    public function getRouteKeyName()
    {
        return 'title';
    }

    public function tags()
    {
        return $this->belongsToMany(EloquentTag::class, 'post_tag', 'post_id', 'tag_id');
    }

    public function comments()
    {
        return $this->hasMany(EloquentComment::class, 'post_id');
    }

    public function replies()
    {
        return $this->hasMany(EloquentReply::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(EloquentUser::class, 'user_id');
    }

    public function scopeFilter($query, $month = null, $year = null)
    {
        if (isset($month)) {
            $query->whereMonth('created_at', Carbon::parse($month)->month);
        }
        if (isset($year)) {
            $query->whereYear('created_at', $year);
        }
    }
}
