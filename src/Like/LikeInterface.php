<?php

namespace SecTheater\Jarvis\Like;
interface LikeInterface {

	function getLikesHave($relation, $operator = '=', $condition = null);

	function getLikesDoesntHave($relation, array $condition = null);

	function getLikesWhereHave($relation, array $condition);

	function likeStatus($type, int $user_id = null);

	function likeCounter($type, bool $status = true):int;

	function like($type);

	function removeLike($type);

	function dislike($type);

}