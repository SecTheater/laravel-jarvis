<?php

namespace SecTheater\Jarvis\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use SecTheater\Jarvis\Exceptions\ConfigException;
use SecTheater\Jarvis\Interfaces\RepositoryInterface;

abstract class Repository implements RepositoryInterface
{
    public function all($columns = ['*'])
    {
        return $this->model->all($columns);
    }

    public function find($attribute, array $columns = ['*'])
    {
        return $this->model->find($attribute, $columns);
    }

    public function findBy($attribute, $operator = '=', $value = null)
    {
        if (is_array($attribute)) {
            return $this->model->where($attribute)->get();
        }
        if (func_num_args() === 2) {
            list($attribute, $value) = func_get_args();
        }

        return $this->model->where($attribute, $operator, $value)->get();
    }

    public function create(array $attributes)
    {
        return $this->model->create($attributes);
    }

    public function update($identifier, array $attributes)
    {
        if (!is_object($identifier)) {
            $identifier = $this->model->find($identifier);
        }

        return ($identifier->update($attributes)) ? $identifier : false;
    }

    public function delete($identifier)
    {
        if ($identifier instanceof Model) {
            return (bool) $identifier->delete();
        }

        return (bool) $this->model->find($identifier)->delete();
    }

    public function exists($attribute, $operator = '=', $value = null)
    {
        if (is_array($attribute)) {
            return $this->model->where($attribute)->exists();
        }
        if (func_num_args() === 2) {
            list($attribute, $value) = func_get_args();
        }

        return $this->model->where($attribute, $operator, $value)->exists();
    }

    public function ordered($attribute, $order = 'desc')
    {
        return $this->model->orderBy($attribute, $order)->get();
    }

    public function __call($method, $arguments)
    {
        $getCalledMethodName = 'get'.str_plural(str_replace('Eloquent', '', class_basename($this->model)));
        if ($method == $getCalledMethodName.'Have') {
            return $this->getEloquentHave(...$arguments);
        } elseif ($method == $getCalledMethodName.'WhereHave') {
            return $this->getEloquentWhereHave(...$arguments);
        } elseif ($method == $getCalledMethodName.'WhereDoesntHave') {
            return $this->getEloquentDoesntHave(...$arguments);
        } elseif ($method == $getCalledMethodName.'With') {
            return $this->getEloquentWith(...$arguments);
        }

        return $this->model->$method(...$arguments);
    }

    public function recent(array $attributes = null, $column = 'created_at')
    {
        if (!Schema::hasColumn($this->model->getTable(), $column)) {
            throw new ConfigException("{$this->model->getTable()} Doesn't have $column");
        }
        if (!$attributes) {
            return $this->model->all()->sortByDesc($column);
        }
        if ($attributes['condition'] && !in_array('approvals', $attributes)) {
            return $this->findBy(...$attributes['condition'])->sortByDesc($column);
        }
        if (in_array('approvals', $attributes)) {
            return $this->recentApprovals($attributes['condition']);
        }
    }

    public function recentApprovals($attributes)
    {
        if (is_array($attributes[0]) && count($attributes) > 1) {
            // Array Condition contains an array with sub-condition.
            if (count($attributes) > 1) {
                $attributes['approved'] = true;
            } elseif (count($attributes) == 1) {
                $attributes[0]['approved'] = true;
            }
        } elseif (count($attributes) == 2) {
            list($key, $value) = $attributes;
            unset($attributes);
            $attributes = [$key => $value, 'approved' => true];
        } elseif (count($attributes) == 3) {
            list($key, $operator, $value) = $attributes;
            unset($attributes);
            $attributes = ['approved' => true, [$key, $operator, $value]];
        }

        return $this->findBy($attributes)->sortByDesc('created_at');
    }

    public function getEloquentHave($relation, $operator = '=', $condition = null)
    {
        if (is_array($condition) || is_array($operator)) {
            list($condition) = [$condition ?? $operator];

            return $this->getEloquentWhereHave($relation, $condition);
        }
        if (func_num_args() === 2) {
            list($relation, $condition) = func_get_args();

            return $this->model->has($relation, $operator, $condition)->get();
        } elseif (func_num_args() === 3) {
            return $this->model->has($relation, $operator, $condition)->get();
        }

        return $this->model->has($relation)->get();
    }

    public function getEloquentWhereHave($relation, array $condition)
    {
        return $this->model->whereHas($relation, function ($query) use ($condition) {
            $query->where($condition);
        })->get();
    }

    public function getEloquentWith($relation, $count = null, $condition = null)
    {
        if ($condition && $count) {
            return $this->model->where($condition)->with($relation)->withCount($count)->get();
        }
        if ($condition) {
            return $this->model->where($condition)->with($relation)->get();
        }
        if ($count) {
            return $this->model->with($relation)->withCount($count)->get();
        }

        return $this->model->with($relation)->get();
    }

    public function getEloquentDoesntHave($relation, array $condition = null)
    {
        if (isset($condition)) {
            return $this->model->whereDoesntHave($relation, function ($query) use ($condition) {
                return $query->where($condition);
            })->get();
        }

        return $this->model->whereDoesntHave($relation)->get();
    }
}
