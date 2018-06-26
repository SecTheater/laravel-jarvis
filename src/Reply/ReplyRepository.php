<?php

namespace SecTheater\Jarvis\Reply;

use SecTheater\Jarvis\Exceptions\ConfigException;
use SecTheater\Jarvis\Interfaces\RestrictionInterface;
use SecTheater\Jarvis\Repositories\Repository;

class ReplyRepository extends Repository implements ReplyInterface
{
    protected $model;

    public function __construct(EloquentReply $model)
    {
        $this->model = $model;
    }

    public function userReplies(RestrictionInterface $user, array $condition = null)
    {
        if (isset($condition)) {
            return $user->replies()->where($condition)->get();
        }

        return $user->replies;
    }

    public function repliesWith($relation)
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
                    "$relation AS pending_{$relation}"=> function ($query) {
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
                    "$relation AS pending_{$relation}"=> function ($query) {
                        $query->where('approved', false);
                    },
                ])->get();
        }

        return $this->model->whereApproved(false)->get();
    }
}
