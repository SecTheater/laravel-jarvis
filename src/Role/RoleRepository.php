<?php
namespace SecTheater\Jarvis\Role;
use Illuminate\Database\Eloquent\Model;

use SecTheater\Jarvis\Repositories\Repository;

class RoleRepository extends Repository implements RoleInterface {
	protected $model;
	function __construct(EloquentRole $model) {
		$this->model = $model;
	}

	function findRoleBySlug($slug) {
		return $this->model->whereSlug($slug)->first();
	}
	function findRoleByName($name) {
		return $this->model->whereName($name)->first();
	}
	function findRoleById($id) {
		return $this->model->whereId($id)->first();
	}

}
