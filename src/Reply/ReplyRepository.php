<?php

namespace SecTheater\Jarvis\Reply;

use SecTheater\Jarvis\Exceptions\ConfigException;
use SecTheater\Jarvis\Repositories\Repository;

class ReplyRepository extends Repository implements ReplyInterface
{
    protected $model;

    public function __construct(EloquentReply $model)
    {
        $this->model = $model;
    }

    public function getApproved($relation = null, array $condition = null)
    {
        return $this->fetchReplies($relation,$condition,true);
    }

    public function getUnapproved($relation = null, array $condition = null)
    {
        return $this->fetchReplies($relation,$condition,false);
    }
}
