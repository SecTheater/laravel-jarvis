<?php

namespace SecTheater\Jarvis\Observers;

use Illuminate\Database\Eloquent\Model;

class PostObserver extends BaseObserver
{
    public function creating(Model $post)
    {
        $post->title = str_slug($post->title);
        $post->user_id = user()->id;
        if (config('jarvis.posts.approve') && user()->hasAnyRole(['approve-post'])) {
            $post->approved = true;
            $post->approved_at = date('Y-m-d H:i:s');
            $post->approved_by = user()->id;
        } elseif (config('jarvis.posts.approve') && !user()->hasAnyRole(['approve-post'])) {
            $post->approved = false;
            $post->approved_at = null;
            $post->approved_by = null;
        }
    }

    public function updating(Model $post)
    {
        $post->title = str_slug($post->title);
        $post->updated_by = user()->id;
        if (config('jarvis.posts.approve') && user()->hasAnyRole(['approve-post'])) {
            $post->approved = true;
            $post->approved_by = user()->id;
            $post->approved_at = date('Y-m-d H:i:s');
            $post->updated_at = date('Y-m-d H:i:s');
        } elseif (config('jarvis.posts.approve') && !user()->hasAnyRole(['approve-post'])) {
            $post->approved = false;
            $post->approved_by = null;
            $post->approved_at = null;
            $post->updated_at = date('Y-m-d H:i:s');
        }
    }

    public function retrieved(Model $post)
    {
        if (isset($post->title)) {
            $post->title = ucfirst(str_replace('-', ' ', $post->title));
        }
    }

}
