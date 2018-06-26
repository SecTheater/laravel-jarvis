<?php

namespace SecTheater\Jarvis\Reminder;

use SecTheater\Jarvis\Interfaces\TokenInterface;
use SecTheater\Jarvis\Repositories\Repository;
use SecTheater\Jarvis\Traits\IssueTokens;

class ReminderRepository extends Repository implements TokenInterface
{
    use IssueTokens;
    protected $model;
    protected $process = 'reminder';

    public function __construct(EloquentReminder $model)
    {
        $this->model = $model;
    }
}
