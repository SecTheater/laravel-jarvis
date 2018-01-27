<?php

namespace SecTheater\Jarvis\Post;

interface PostRepositoryInterface
{
    public function getPopularPosts($limit = 5);

    public function getApproved($relation = null, array $condition = null);

    public function getUnapproved($relation = null, array $condition = null);

    public function recentlyApproved();

    public function recentPosts(array $condition = null);

    public function archives();

    public function getPostsHave($relation, $operator = '=', $condition = null);

    public function getPostsDoesntHave($relation, array $condition = null);

    public function getPostsWhereHave($relation, array $condition);
}
