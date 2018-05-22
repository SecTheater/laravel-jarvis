<?php

namespace SecTheater\Jarvis\Tag;

use SecTheater\Jarvis\Repositories\Repository;

class TagRepository extends Repository implements TagInterface
{
    protected $model;

    public function __construct(EloquentTag $model)
    {
        $this->model = $model;
    }

    public function getTagsHave($relation, $operator = '=', $condition = null)
    {
        if (is_array($condition) || is_array($operator)) {
            list($condition) = [$condition ?? $operator];

            return $this->getTagsWhereHave($relation, $condition);
        }
        if (func_num_args() === 2) {
            list($relation, $condition) = func_get_args();

            return $this->model->has($relation, $operator, $condition)->get();
        } elseif (func_num_args() === 3) {
            return $this->model->has($relation, $operator, $condition)->get();
        }

        return $this->model->has($relation)->get();
    }

    public function getTagsDoesntHave($relation, array $condition = null)
    {
        if (isset($condition)) {
            return $this->model->whereDoesntHave($relation, function ($query) use ($condition) {
                return $query->where($condition);
            })->get();
        }

        return $this->model->doesntHave($relation)->get();
    }

    public function getTagsWhereHave($relation, array $condition)
    {
        return $this->model->whereHas($relation, function ($query) use ($condition) {
            $query->where($condition);
        })->get();
    }

    public function userTags($user_id)
    {
        if (is_object($user_id)) {
            $user_id = $user_id->id;
        }

        return $this->findBy(['user_id' => $user_id]);
    }

    public function getTagPosts($name)
    {
        if (is_object($name)) {
            $name = $name->name;
        }

        return $this->model->where(['name' => $name])->first()->posts;
    }
}
