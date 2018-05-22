<?php

namespace SecTheater\Jarvis\Like;

interface LikeInterface {
	public function likeStatus($type, int $user_id = null);

	public function likeCounter($type, bool $status = true): int;

	public function like($type);

	public function removeLike($type);

	public function dislike($type);
}
