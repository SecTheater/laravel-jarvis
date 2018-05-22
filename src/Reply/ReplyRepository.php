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

    public function recentlyApproved()
    {
        if (!config('jarvis.posts.approve')) {
            throw new ConfigException('Approval Is not enabled for posts.');
        }

        return $this->model->whereApproved(true)->get()->sortByDesc('approved_at');
    }

    public function recentReplies(array $condition = null)
    {
        if (isset($condition)) {
            return $this->model->where($condition)->get()->sortByDesc('created_at');
        }

        return $this->all()->sortByDesc('created_at');
    }

    public function getRepliesHave($relation, $operator = '=', $condition = null)
    {
        if (is_array($condition) || is_array($operator)) {
            list($condition) = [$condition ?? $operator];

            return $this->getRepliesWhereHave($relation, $condition);
        }
        if (func_num_args() === 2) {
            list($relation, $condition) = func_get_args();

            return $this->model->has($relation, $operator, $condition)->get();
        } elseif (func_num_args() === 3) {
            return $this->model->has($relation, $operator, $condition)->get();
        }

        return $this->model->has($relation)->get();
    }

    public function getRepliesDoesntHave($relation, array $condition = null)
    {
        if (isset($condition)) {
            return $this->model->whereDoesntHave($relation, function ($query) use ($condition) {
                return $query->where($condition);
            })->get();
        }

        return $this->model->doesntHave($relation)->get();
    }

    public function getRepliesWhereHave($relation, array $condition)
    {
        return $this->model->whereHas($relation, function ($query) use ($condition) {
            $query->where($condition);
        })->get();
    }
}
