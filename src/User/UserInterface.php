<?php

namespace SecTheater\Jarvis\User;

interface UserInterface
{
    public function PeopleCommentedOnAPost(\App\Post $post);
}
