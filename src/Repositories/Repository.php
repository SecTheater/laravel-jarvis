<?php

namespace SecTheater\Jarvis\Repositories;

use Illuminate\Database\Eloquent\Model;
use SecTheater\Jarvis\Interfaces\RepositoryInterface;

abstract class Repository implements RepositoryInterface
{
    public function all($columns = ['*'])
    {
        return $this->model->all($columns);
    }

    public function find($attribute, $columns = ['*'])
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
        if (isset($attributes['user_id']) && is_object($attributes['user_id'])) {
            $attributes['user_id'] = $attributes['user_id']->id;
        }
        if (isset($attributes['post_id']) && is_object($attributes['post_id'])) {
            $attributes['post_id'] = $attributes['post_id']->id;
        }
        if (isset($attributes['likable_id']) && is_object($attributes['likable_id'])) {
            $attributes['likable_id'] = $attributes['likable_id']->id;
        }
        if (isset($attributes['comment_id']) && is_object($attributes['comment_id'])) {
            $attributes['comment_id'] = $attributes['comment_id']->id;
        }
        if (isset($attributes['reply_id']) && is_object($attributes['reply_id'])) {
            $attributes['reply_id'] = $attributes['reply_id']->id;
        }
        if (isset($attributes['tag_id']) && is_object($attributes['tag_id'])) {
            $attributes['tag_id'] = $attributes['tag_id']->id;
        }

        return $this->model->create($attributes);
    }

    public function update(Model $repository, array $attributes)
    {
        if (isset($attributes['user_id']) && is_object($attributes['user_id'])) {
            $attributes['user_id'] = $attributes['user_id']->id;
        }
        if (isset($attributes['post_id']) && is_object($attributes['post_id'])) {
            $attributes['post_id'] = $attributes['post_id']->id;
        }
        if (isset($attributes['likable_id']) && is_object($attributes['likable_id'])) {
            $attributes['likable_id'] = $attributes['likable_id']->id;
        }
        if (isset($attributes['comment_id']) && is_object($attributes['comment_id'])) {
            $attributes['comment_id'] = $attributes['comment_id']->id;
        }
        if (isset($attributes['reply_id']) && is_object($attributes['reply_id'])) {
            $attributes['reply_id'] = $attributes['reply_id']->id;
        }
        if (isset($attributes['tag_id']) && is_object($attributes['tag_id'])) {
            $attributes['tag_id'] = $attributes['tag_id']->id;
        }

        return ($repository->update($attributes)) ? $repository : false;
    }

    public function delete(Model $repository)
    {
        return (bool) $repository->delete();
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
        } elseif ($method == $getCalledMethodName.'DoesntHave') {
            return $this->getEloquentDoesntHave(...$arguments);
        }

        return $this->model->$method(...$arguments);
    }

    public function recent(array $attributes = null)
    {
        if (!$attributes) {
            return $this->model->all()->sortByDesc('created_at');
        }
        if ($attributes['condition'] && !in_array('approvals', $attributes)) {
            return $this->findBy(...$attributes['condition'])->sortByDesc('created_at');
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

    public function getEloquentDoesntHave($relation, array $condition = null)
    {
        if (isset($condition)) {
            return $this->model->whereDoesntHave($relation, function ($query) use ($condition) {
                return $query->where($condition);
            })->get();
        }

        return $this->model->doesntHave($relation)->get();
    }
}
