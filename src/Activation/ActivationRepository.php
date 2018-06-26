<?php

namespace SecTheater\Jarvis\Activation;

use SecTheater\Jarvis\Interfaces\TokenInterface;
use SecTheater\Jarvis\Repositories\Repository;
use SecTheater\Jarvis\Traits\IssueTokens;

class ActivationRepository extends Repository implements TokenInterface
{
    use IssueTokens;
    protected $model;
    protected $process = 'activation';

    public function __construct(EloquentActivation $model)
    {
        $this->model = $model;
    }
}
