<?php

namespace SecTheater\Jarvis\Role;

interface RoleInterface
{
    public function findRoleBySlug($slug);

    public function findRoleByName($name);

    public function findRoleById($id);
}
