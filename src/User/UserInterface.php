<?php

namespace SecTheater\Jarvis\User;

interface UserInterface
{
    public function getUsersHave($relation, $operator = '=', $condition = null);

    public function getUsersDoesntHave($relation, array $condition = null);

    public function getUsersWhereHave($relation, array $condition);

    public function isOnline(int $id);

    public function PeopleCommentedOnAPost(\App\Post $post);
}
