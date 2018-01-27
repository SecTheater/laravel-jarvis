<?php

namespace SecTheater\Jarvis\Facades;

use Illuminate\Support\Facades\Facade;

class PostRepository extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'PostRepository';
    }
}
