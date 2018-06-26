<?php

namespace SecTheater\Jarvis\Comment;

use SecTheater\Jarvis\Exceptions\ConfigException;
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
        return $this->model->with($relation)->get();
    }

    public function getApproved($relation = null)
    {
        if (!config('jarvis.posts.approve')) {
            throw new ConfigException('Approval Is not enabled for posts.');
        }

        if (isset($relation)) {
            return $this->model->where('approved', true)->withCount([
                $relation,
                "$relation AS pending_{$relation}" => function ($query) {
                    $query->where('approved', true);
                },
            ])->get();
        }

        return $this->model->whereApproved(true)->get();
    }

    public function getUnapproved($relation = null)
    {
        if (!config('jarvis.posts.approve')) {
            throw new ConfigException('Approval Is not enabled for posts.');
        }

        if (isset($relation)) {
            return $this->model->where('approved', false)->withCount([
                $relation,
                "$relation AS pending_{$relation}" => function ($query) {
                    $query->where('approved', false);
                },
            ])->get();
        }

        return $this->model->whereApproved(false)->get();
    }
}
