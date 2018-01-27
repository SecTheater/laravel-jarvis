<?php

namespace SecTheater\Jarvis\Post;

use SecTheater\Jarvis\Exceptions\ConfigException;
use SecTheater\Jarvis\Repositories\Repository;

class PostRepository extends Repository implements PostRepositoryInterface
{
    protected $model;

    public function __construct(EloquentPost $model)
    {
        $this->model = $model;
    }

    public function getPopularPosts($limit = 5)
    {
        if (config('jarvis.posts.approve')) {
            return \DB::table($this->model->getTable())->where($this->model->getTable().'.approved', '=', true)->join('comments', 'comments.'.str_singular($this->model->getTable()).'_id', '=', $this->model->getTable().'.id')->join('replies', 'replies.'.str_singular($this->model->getTable()).'_id', '=', $this->model->getTable().'.id')->limit($limit)->get();
        }

        return \DB::table($this->model->getTable())->join('comments', 'comments.'.str_singular($this->model->getTable()).'_id', '=', $this->model->getTable().'.id')->join('replies', 'replies.'.str_singular($this->model->getTable()).'_id', '=', $this->model->getTable().'.id')->limit($limit)->get();
    }

    public function getApproved($relation = null, array $condition = null)
    {
        if (!config('jarvis.posts.approve')) {
            throw new ConfigException('Approval Is not enabled for posts.');
        }
        if (isset($relation, $condition)) {
            return $this->model->where('approved', true)->withCount([
                    $relation,
                    "$relation AS related_{$relation}"=> function ($query) use ($condition) {
                        $query->where($condition);
                    },
                ])->get();
        }
        if (isset($relation)) {
            return $this->getPostsHave($relation, ['posts.approved' => true]);
        }

        return $this->model->whereApproved(true)->get();
    }

    public function getUnapproved($relation = null, array $condition = null)
    {
        if (!config('jarvis.posts.approve')) {
            throw new ConfigException('Approval Is not enabled for posts.');
        }

        if (isset($relation, $condition)) {
            return $this->model->where('approved', true)->withCount([
                    $relation,
                    "$relation AS related_{$relation}"=> function ($query) {
                        $query->where($condition);
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

    public function recentPosts(array $condition = null)
    {
        if ($condition) {
            return $this->model->where($condition)->get()->sortByDesc('created_at');
        }

        return $this->all()->sortByDesc('created_at');
    }

    public function archives()
    {
        return $this->model
                    ->selectRaw('year(created_at) year, monthname(created_at) month , count(*) count')
                    ->groupBy('year', 'month', 'title')
                    ->orderByRaw('min(created_at) desc')
                    ->get();
    }

    public function getPostsHave($relation, $operator = '=', $condition = null)
    {
        if (is_array($condition) || is_array($operator)) {
            list($condition) = [$condition ?? $operator];

            return $this->getPostsWhereHave($relation, $condition);
        }
        if (func_num_args() === 2) {
            list($relation, $condition) = func_get_args();

            return $this->model->has($relation, $operator, $condition)->get();
        } elseif (func_num_args() === 3) {
            return $this->model->has($relation, $operator, $condition)->get();
        }

        return $this->model->has($relation)->get();
    }

    public function getPostsDoesntHave($relation, array $condition = null)
    {
        if ($condition) {
            return $this->model->whereDoesntHave($relation, function ($query) use ($condition) {
                return $query->where($condition);
            })->get();
        }

        return $this->model->doesntHave($relation)->get();
    }

    public function getPostsWhereHave($relation, array $condition)
    {
        return $this->model->whereHas($relation, function ($query) use ($condition) {
            $query->where($condition);
        })->get();
    }
}
