<?php

namespace SecTheater\Jarvis\Role;

use SecTheater\Jarvis\Repositories\Repository;

class RoleRepository extends Repository implements RoleInterface
{
    protected $model;

    public function __construct(EloquentRole $model)
    {
        $this->model = $model;
    }

    public function findRoleBySlug($slug)
    {
        return $this->model->whereSlug($slug)->first();
    }

    public function findRoleByName($name)
    {
        return $this->model->whereName($name)->first();
    }

    public function findRoleById($id)
    {
        return $this->model->whereId($id)->first();
    }
}
