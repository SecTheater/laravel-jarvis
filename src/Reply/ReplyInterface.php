<?php

namespace SecTheater\Jarvis\Reply;

use SecTheater\Jarvis\Interfaces\RestrictionInterface;

interface ReplyInterface
{
    public function repliesWith($relation);

    public function getApproved($relation = null);

    public function userReplies(RestrictionInterface $user, array $condition = null);

    public function getUnapproved($relation = null);
}
