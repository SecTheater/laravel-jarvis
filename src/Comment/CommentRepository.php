<?php

namespace SecTheater\Jarvis\Comment;

use SecTheater\Jarvis\Exceptions\ConfigException;
use SecTheater\Jarvis\Repositories\Repository;

class CommentRepository extends Repository implements CommentInterface
{
    protected $model;

    public function __construct(EloquentComment $model)
    {
        $this->model = $model;
    }

    public function getApproved($relation = null, array $condition = null)
    {
        return $this->fetchComments($relation,$condition,true);
    }

    public function getUnapproved($relation = null, array $condition = null)
    {
        return $this->fetchComments($relation,$condition,false);
    }

}
