<?php

namespace SecTheater\Jarvis\Post;

interface PostRepositoryInterface
{
    public function getPopularPosts($limit = 5);

    public function fetchPosts($relation = null, array $condition = null, bool $approved = false);

    public function getApproved($relation = null, array $condition = null);

    public function getUnapproved($relation = null, array $condition = null);

    public function archives();
}
