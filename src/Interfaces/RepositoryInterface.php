<?php

namespace SecTheater\Jarvis\Interfaces;

use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public function all();

    public function find($attribute);

    public function findBy($attribute, $operator = '=', $value = null);

    public function create(array $attributes);

    public function update(Model $model, array $attributes);

    public function delete(Model $model);

    public function ordered($attribute, $order = 'desc');

    public function exists($condition, $operator = '=', $value = null);

    public function getEloquentHave($relation, $operator = '=', $condition = null);

    public function getEloquentWhereHave($relation, array $condition);

    public function getEloquentDoesntHave($relation, array $condition = null);

    public function __call($method, $arguments);
}
