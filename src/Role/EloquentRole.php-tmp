<?php

namespace SecTheater\Jarvis\Role;

use SecTheater\Jarvis\Model\EloquentModel;

class EloquentRole extends EloquentModel {
	protected $table = 'roles';
	protected $casts = [
		'permissions' => 'array',
	];

	public function users() {
		return $this->belongsToMany($this->userModel, 'role_users', 'role_id', 'user_id');
	}
}
