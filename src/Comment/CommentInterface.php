<?php

namespace SecTheater\Jarvis\Comment;

use SecTheater\Jarvis\Interfaces\RestrictionInterface;

interface CommentInterface
{
    public function commentsWith($relation);

    public function getApproved($relation = null);

    public function userComments(RestrictionInterface $user, array $condition = null);

    public function getUnapproved($relation = null);

    public function recentlyApproved();

    public function recentComments(array $condition = null);

    public function getCommentsHave($relation, $operator = '=', $condition = null);

    public function getCommentsDoesntHave($relation, array $condition = null);

    public function getCommentsWhereHave($relation, array $condition);
}
