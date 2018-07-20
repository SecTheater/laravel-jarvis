<?php

namespace SecTheater\Jarvis\Observers;

use Illuminate\Database\Eloquent\Model;

class ReminderObserver extends BaseObserver
{
    public function retrieved(Model $reminder)
    {
        $reminder->completed = (bool) $reminder->completed;
    }

    public function creating(Model $reminder)
    {
        $reminder->completed = (bool) $reminder->completed;
        $reminder->updated_at = null;
        $reminder->token = str_random(32);
    }

    public function updating(Model $reminder)
    {
        $reminder->completed = (bool) $reminder->completed;
        $reminder->updated_at = date('Y-m-d H:i:s');
        $reminder->token = str_random(32);
    }
}
