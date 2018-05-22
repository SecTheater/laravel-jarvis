<?php

namespace SecTheater\Jarvis\Reply;

use SecTheater\Jarvis\Interfaces\RestrictionInterface;

interface ReplyInterface
{
    public function repliesWith($relation);

    public function getApproved($relation = null);

    public function userReplies(RestrictionInterface $user, array $condition = null);

    public function getUnapproved($relation = null);

    public function recentlyApproved();

    public function recentReplies(array $condition = null);

    public function getRepliesHave($relation, $operator = '=', $condition = null);

    public function getRepliesDoesntHave($relation, array $condition = null);

    public function getRepliesWhereHave($relation, array $condition);
}
