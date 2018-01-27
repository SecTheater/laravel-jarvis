<?php
namespace SecTheater\Jarvis\Tag;
interface TagInterface {
	function getTagsHave($relation, $operator = '=', $condition = null);

	function getTagsDoesntHave($relation, array $condition = null);

	function getTagsWhereHave($relation, array $condition);
}