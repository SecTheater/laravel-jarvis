<?php

namespace SecTheater\Jarvis\User;

use Artify\Artify\Traits\Roles\Roles;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use SecTheater\Jarvis\Interfaces\UserInterface;
use SecTheater\Jarvis\Model\EloquentModel;

class EloquentUser extends EloquentModel implements UserInterface, AuthenticatableContract, AuthorizableContract, CanResetPasswordContract
{
    use Roles, Authenticatable, Authorizable, CanResetPassword;
    protected $table = 'users';
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
        return $this->hasMany($this->commentModel, 'user_id');
    }

    public function replies()
    {
        return $this->hasMany($this->replyModel, 'user_id');
    }

    public function tags()
    {
        return $this->hasMany($this->tagModel, 'user_id');
    }

    public function likes()
    {
        return $this->hasMany($this->likeModel, 'user_id');
    }

    public function posts()
    {
        return $this->hasMany($this->postModel, 'user_id');
    }

    public function activation()
    {
        return $this->hasMany($this->activationModel, 'user_id', 'id');
    }

    public function reminder()
    {
        return $this->hasMany($this->reminderModel, 'user_id', 'id');
    }

    public function roles()
    {
        return $this->belongsToMany($this->roleModel, 'role_users', 'user_id', 'role_id');
    }
}
