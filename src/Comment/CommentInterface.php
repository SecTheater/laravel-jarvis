<?php

namespace SecTheater\Jarvis\Comment;

interface CommentInterface
{
    public function getApproved($relation = null, array $condition = null);

    public function getUnapproved($relation = null, array $condition = null);
}
