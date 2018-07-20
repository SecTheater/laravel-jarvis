<?php

namespace SecTheater\Jarvis\Observers;

use Illuminate\Database\Eloquent\Model;

class UserObserver extends BaseObserver
{
    public function creating(Model $user)
    {
        if (isset($user->first_name)) {
            $user->first_name = ucfirst(strtolower($user->first_name));
        }
        if (isset($user->last_name)) {
            $user->last_name = ucfirst(strtolower($user->last_name));
        }
        if (isset($user->location)) {
            $user->location = ucfirst(strtolower($user->location));
        }
        if (isset($user->sec_answer)) {
            $user->sec_answer = bcrypt($user->sec_answer);
        }
    }

    public function updating(Model $user)
    {
        if (isset($user->first_name)) {
            $user->first_name = ucfirst(strtolower($user->first_name));
        }
        if (isset($user->last_name)) {
            $user->last_name = ucfirst(strtolower($user->last_name));
        }
        if (isset($user->location)) {
            $user->location = ucfirst(strtolower($user->location));
        }
    }

    public function retrieved(Model $user)
    {
        $user->username = ucfirst($user->username);
    }
}
