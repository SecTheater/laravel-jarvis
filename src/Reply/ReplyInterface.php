<?php

namespace SecTheater\Jarvis\Reply;

interface ReplyInterface
{
    public function getApproved($relation = null, array $condition = null);

    public function getUnapproved($relation = null, array $condition = null);
}
