<?php

namespace SecTheater\Jarvis\Facades;

use Illuminate\Support\Facades\Facade;

class ActivationRepository extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'ActivationRepository';
    }
}
