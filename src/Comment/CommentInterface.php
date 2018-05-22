<?php

namespace SecTheater\Jarvis\Comment;

use SecTheater\Jarvis\Interfaces\RestrictionInterface;

interface CommentInterface {
	public function commentsWith($relation);

	public function getApproved($relation = null);

	public function userComments(RestrictionInterface $user, array $condition = null);

	public function getUnapproved($relation = null);
}
