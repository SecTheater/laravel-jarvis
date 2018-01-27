<?php

namespace SecTheater\Jarvis\Observers;

use Illuminate\Database\Eloquent\Model;

class ReminderObserver
{
    public function retrieved(Model $reminder)
    {
        $reminder->completed = (bool) $reminder->completed;
    }

    public function creating(Model $reminder)
    {
        $reminder->completed = (bool) $reminder->completed;
        $reminder->updated_at = null;
    }

    public function updating(Model $reminder)
    {
        $reminder->completed = (bool) $reminder->completed;
        $reminder->updated_at = date('Y-m-d H:i:s');
    }
}
