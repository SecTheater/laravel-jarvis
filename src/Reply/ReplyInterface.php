<?php

namespace SecTheater\Jarvis\Reply;
use SecTheater\Jarvis\Interfaces\RestrictionInterface;
interface ReplyInterface {

function repliesWith($relation);
	function getApproved($relation = null);
	function userReplies(RestrictionInterface $user, array $condition = null);
	function getUnapproved($relation = null);
	function recentlyApproved();
	function recentReplies(array $condition = null);
	function getRepliesHave($relation, $operator = '=', $condition = null);
	function getRepliesDoesntHave($relation, array $condition = null);
	function getRepliesWhereHave($relation, array $condition);

}
