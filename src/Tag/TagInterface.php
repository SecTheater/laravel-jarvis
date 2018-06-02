<?php

namespace SecTheater\Jarvis\Tag;

interface TagInterface
{
    public function userTags($user_id);

    public function getTagPosts($name);
}
