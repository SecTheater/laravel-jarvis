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
