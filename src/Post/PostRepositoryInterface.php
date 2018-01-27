<?php

namespace SecTheater\Jarvis\Post;
interface PostRepositoryInterface {

	function getPopularPosts($limit = 5);

	function getApproved($relation = null, array $condition = null);

	function getUnapproved($relation = null, array $condition = null);

	function recentlyApproved();

	function recentPosts(array $condition = null);

	function archives();

	function getPostsHave($relation, $operator = '=', $condition = null);

	function getPostsDoesntHave($relation, array $condition = null);

	function getPostsWhereHave($relation, array $condition);

}