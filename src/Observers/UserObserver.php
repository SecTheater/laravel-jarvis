<?php

namespace SecTheater\Jarvis\Observers;

use Illuminate\Database\Eloquent\Model;
class UserObserver extends BaseObserver
{
    public function creating(Model $user)
    {
        $this->fireObserversListeners($user);
    }

    public function updating(Model $user)
    {
        $this->fireObserversListeners($user);
    }

}
