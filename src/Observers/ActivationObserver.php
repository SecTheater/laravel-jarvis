<?php

namespace SecTheater\Jarvis\Observers;

use Illuminate\Database\Eloquent\Model;

class ActivationObserver
{
    public function retrieved(Model $activation)
    {
        $activation->completed = (bool) $activation->completed;
    }

    public function creating(Model $activation)
    {
        $activation->completed = (bool) $activation->completed;
        $activation->updated_at = null;
        $activation->token = str_random(32);
    }

    public function updating(Model $activation)
    {
        $activation->completed = (bool) $activation->completed;
        $activation->updated_at = date('Y-m-d H:i:s');
    }
}
