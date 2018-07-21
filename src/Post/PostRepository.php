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
            $collection =  $this->model->whereHas('comments.replies',
                    function($query){
                        if (config('jarvis.comments.approve')) {
                            $query->where('comments.approved',true);
                        }
                        if (config('jarvis.replies.approve')) {
                            $query->where('replies.approved',true);
                        }
                    })
                ->orWhereHas('comments',function($query){
                        if (config('jarvis.comments.approve')) {
                            $query->where('comments.approved',true);
                        }
                })
                ->withCount([
                    'comments' => function($query){
                        if (config('jarvis.comments.approve')) {
                            $query->where('comments.approved',true);
                        }
                    },
                    'replies' => function($query){
                        if (config('jarvis.replies.approve')) {
                            $query->where('replies.approved',true);
                        }
                    }
                ])
                ->limit($limit)
                ->get()
                ->sortBy(function($post,$key){
                    return $post['comments_count'] + $post['replies_count'];
                });
       if (config('jarvis.posts.approve')) {
            return collect($collection->filter(function ($post, $key) {
                return $post->approved;
            })->all());
       }
       return $collection;
    }

    public function getApproved($relation = null, array $condition = null)
    {
        if (!config('jarvis.posts.approve')) {
            throw new ConfigException('Approval Is not enabled for posts.');
        }
        if (isset($relation, $condition)) {
            return $this->model->where('approved', true)->withCount([
                $relation,
                "$relation AS related_{$relation}" => function ($query) use ($condition) {
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
                "$relation AS related_{$relation}" => function ($query) {
                    $query->where($condition);
                },
            ])->get();
        }

        return $this->model->whereApproved(false)->get();
    }

    public function archives()
    {
        return $this->model
            ->selectRaw('year(created_at) year, monthname(created_at) month , count(*) count')
            ->groupBy('year', 'month')
            ->orderByRaw('min(created_at) desc')
            ->get();
    }
}
