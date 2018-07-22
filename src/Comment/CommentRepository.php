<?php

namespace SecTheater\Jarvis\Comment;

use SecTheater\Jarvis\Interfaces\RestrictionInterface;
use SecTheater\Jarvis\Repositories\Repository;

class CommentRepository extends Repository implements CommentInterface
{
    protected $model;

    public function __construct(EloquentComment $model)
    {
        $this->model = $model;
    }

    public function userComments(RestrictionInterface $user, array $condition = null)
    {
        if (isset($condition)) {
            return $user->comments()->where($condition)->get();
        }

        return $user->comments;
    }

    public function commentsWith($relation)
    {
        return $this->getCommentsWith($relation);
    }

    public function getApproved($relation = null, array $condition = null)
    {
        return $this->fetchComments($relation, $condition, true);
    }

    public function getUnapproved($relation = null, array $condition = null)
    {
        return $this->fetchComments($relation, $condition, false);
    }
}
