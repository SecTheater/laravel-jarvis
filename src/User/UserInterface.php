<?php

namespace SecTheater\Jarvis\User;

interface UserInterface {
	function getUsersHave($relation, $operator = '=', $condition = null);
	function getUsersDoesntHave($relation, array $condition = null);
	function getUsersWhereHave($relation, array $condition);
	function isOnline(int $id);
	function PeopleCommentedOnAPost(\App\Post $post);
}