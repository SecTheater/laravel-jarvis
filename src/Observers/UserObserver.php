<?php

namespace SecTheater\Jarvis\Observers;

use Illuminate\Database\Eloquent\Model;

class UserObserver
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

    public function deleting(Model $user)
    {
        if (config('jarvis.posts.register')) {
            $user->posts()->delete();
        }
        if (config('jarvis.activations.register')) {
            $user->activation()->delete();
        }
        if (config('jarvis.comments.register')) {
            $user->comments()->delete();
        }
        if (config('jarvis.replies.register')) {
            $user->replies()->delete();
        }
        if (config('jarvis.likes.register')) {
            $user->likes()->delete();
        }
        if (config('jarvis.tags.register')) {
            $user->tags()->delete();
        }
        $user->reminder()->delete();
    }
}
