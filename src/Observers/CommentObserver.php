<?php

namespace SecTheater\Jarvis\Observers;

use Illuminate\Database\Eloquent\Model;

class CommentObserver
{
    // Any Comment Model could be passed here.
    public function creating(Model $comment)
    {
        $comment->user_id = user()->id;
        if (config('jarvis.comments.approve') && user()->hasAnyRole(['approve-comment'])) {
            $comment->approved = true;
            $comment->approved_by = user()->id;
            $comment->approved_at = date('Y-m-d H:i:s');
            $comment->updated_at = null;
        } elseif (config('jarvis.comments.approve') && !user()->hasAnyRole(['approve-comment'])) {
            $comment->approved = false;
            $comment->approved_by = null;
            $comment->approved_at = null;
            $comment->updated_at = date('Y-m-d H:i:s');
        }
    }

    public function updating(Model $comment)
    {
        if (config('jarvis.comments.approve') && user()->hasAnyRole(['approve-comment'])) {
            $comment->approved = true;
            $comment->approved_by = user()->id;
            $comment->approved_at = date('Y-m-d H:i:s');
            $comment->updated_at = date('Y-m-d H:i:s');
        } elseif (config('jarvis.comments.approve') && !user()->hasAnyRole(['approve-comment'])) {
            $comment->approved = false;
            $comment->approved_by = null;
            $comment->approved_at = null;
            $comment->updated_at = date('Y-m-d H:i:s');
        }
    }

    public function deleting(Model $comment)
    {
        if (config('jarvis.replies.register')) {
            $comment->replies()->delete();
        }
    }
}
