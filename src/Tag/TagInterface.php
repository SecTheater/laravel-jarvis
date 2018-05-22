<?php

namespace SecTheater\Jarvis\Tag;

interface TagInterface
{
    public function getTagsHave($relation, $operator = '=', $condition = null);

    public function getTagsDoesntHave($relation, array $condition = null);

    public function getTagsWhereHave($relation, array $condition);
}
