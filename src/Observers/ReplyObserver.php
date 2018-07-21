<?php

namespace SecTheater\Jarvis\Observers;

use Illuminate\Database\Eloquent\Model;

class ReplyObserver extends BaseObserver
{
    public function creating(Model $reply)
    {
        $reply->user_id = user()->id;
        $this->fireApprovalListeners($reply);
    }

    public function updating(Model $reply)
    {
        $this->fireApprovalListeners($reply);
    }
}
