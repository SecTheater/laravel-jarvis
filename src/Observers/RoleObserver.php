<?php
namespace SecTheater\Jarvis\Observers;
use Illuminate\Database\Eloquent\Model;

class RoleObserver {
	public function retrieved(Model $role) {
		$role->permissions = json_decode($role->permissions, true);
	}
	public function creating(Model $role) {
		$role->permissions = json_encode($role->permissions);
		$role->created_at  = date('Y-m-d H:i:s');
		$role->updated_at  = null;
	}
	public function updating(Model $role) {
		$role->permissions = json_encode($role->permissions);
		$role->updated_at  = date('Y-m-d H:i:s');
	}
	public function deleting(Model $role) {
		$role->users()->detach();
	}
}