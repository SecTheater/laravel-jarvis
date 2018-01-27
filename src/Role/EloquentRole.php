<?php

namespace SecTheater\Jarvis\Role;
use SecTheater\Jarvis\User\EloquentUser;
use \Illuminate\Database\Eloquent\Model;

class EloquentRole extends Model {
	protected $table   = "roles";
	protected $guarded = [];

	public function users() {
		return $this->belongsToMany(EloquentUser::class , 'role_users', 'role_id', 'user_id');
	}

}
