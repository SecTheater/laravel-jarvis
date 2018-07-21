<?php

namespace SecTheater\Jarvis\Observers;

use Illuminate\Database\Eloquent\Model;

class CommentObserver extends BaseObserver
{
    public function creating(Model $comment)
    {
        $comment->user_id = user()->id;
        $this->fireApprovalListeners($comment);
    }

    public function updating(Model $comment)
    {
        $this->fireApprovalListeners($comment);
    }
}
