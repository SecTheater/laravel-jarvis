<?php

namespace SecTheater\Jarvis\Interfaces;

interface RepositoryInterface
{
    public function all();

    public function find($attribute);

    public function findBy($attribute, $operator = '=', $value = null);

    public function create(array $attributes);

    public function update($identifier, array $attributes);

    public function delete($identifier);

    public function ordered($attribute, $order = 'desc');

    public function exists($condition, $operator = '=', $value = null);

    public function getEloquentHave(string $relation, $operator = '=', $condition = null);

    public function getEloquentWhereHave(string $relation, array $condition);

    public function getEloquentWhereDoesntHave(string $relation, array $condition = null);

    public function __call($method, $arguments);
}
