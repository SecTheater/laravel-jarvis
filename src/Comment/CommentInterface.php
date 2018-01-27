<?php

namespace SecTheater\Jarvis\Comment;
use SecTheater\Jarvis\Interfaces\RestrictionInterface;
interface CommentInterface {

function commentsWith($relation);
	function getApproved($relation = null);
	function userComments(RestrictionInterface $user, array $condition = null);
	function getUnapproved($relation = null);
	function recentlyApproved();
	function recentComments(array $condition = null);
	function getCommentsHave($relation, $operator = '=', $condition = null);
	function getCommentsDoesntHave($relation, array $condition = null);
	function getCommentsWhereHave($relation, array $condition);

}
