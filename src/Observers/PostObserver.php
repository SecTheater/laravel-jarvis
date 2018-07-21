<?php

namespace SecTheater\Jarvis\Observers;

use Illuminate\Database\Eloquent\Model;

class PostObserver extends BaseObserver
{
    public function creating(Model $post)
    {
        $post->user_id = user()->id;
        $this->fireApprovalListeners($post);
    }

    public function updating(Model $post)
    {
        $post->updated_by = user()->id;
        $this->fireApprovalListeners($post);
    }

    public function retrieved(Model $post)
    {
        if (isset($post->title)) {
            $post->title = str_slug($post->title, ' ');
        }
    }
}
