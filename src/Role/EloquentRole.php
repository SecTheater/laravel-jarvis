<?php

namespace SecTheater\Jarvis\Role;

use Illuminate\Database\Eloquent\Model;
use SecTheater\Jarvis\User\EloquentUser;

class EloquentRole extends Model
{
    protected $table = 'roles';
    protected $guarded = [];
    protected $casts = [
        'permissions' => 'array'
    ];


    public function users()
    {
        return $this->belongsToMany(EloquentUser::class, 'role_users', 'role_id', 'user_id');
    }
}
