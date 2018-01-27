<?php

namespace SecTheater\Jarvis\Role;

interface RoleInterface {

	function findRoleBySlug($slug);
	function findRoleByName($name);
	function findRoleById($id);
}
