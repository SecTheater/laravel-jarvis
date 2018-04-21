<?php

namespace SecTheater\Jarvis\User;

use Illuminate\Foundation\Auth\User as Authenticatable;
use SecTheater\Jarvis\Activation\EloquentActivation;
use SecTheater\Jarvis\Comment\EloquentComment;
use SecTheater\Jarvis\Interfaces\RestrictionInterface;
use SecTheater\Jarvis\Like\EloquentLike;
use SecTheater\Jarvis\Post\EloquentPost;
use SecTheater\Jarvis\Reminder\EloquentReminder;
use SecTheater\Jarvis\Reply\EloquentReply;
use SecTheater\Jarvis\Role\EloquentRole;
use SecTheater\Jarvis\Tag\EloquentTag;
use SecTheater\Jarvis\Traits\Roles\Roles;

class EloquentUser extends Authenticatable implements RestrictionInterface
{
    use Roles;
    protected $table = 'users';
    protected $guarded = [];
    protected $hidden = [
        'password', 'remember_token',
    ];
    public static $loginNames = ['string' => 'username', 'email' => 'email'];
    public $casts = [
        'permissions' => 'array',
    ];

    public function getRouteKeyName()
    {
        return 'username';
    }

    public function comments()
    {
        return $this->hasMany(EloquentComment::class, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany(EloquentReply::class, 'user_id');
    }

    public function tags()
    {
        return $this->hasMany(EloquentTag::class, 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(EloquentLike::class, 'user_id');
    }

    public function posts()
    {
        return $this->hasMany(EloquentPost::class, 'user_id');
    }

    public function activation()
    {
        return $this->hasMany(EloquentActivation::class, 'user_id', 'id');
    }

    public function reminder()
    {
        return $this->hasMany(EloquentReminder::class, 'user_id', 'id');
    }

    public function roles()
    {
        return $this->belongsToMany(EloquentRole::class, 'role_users', 'user_id', 'role_id');
    }
}
