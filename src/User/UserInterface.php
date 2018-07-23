<?php

namespace SecTheater\Jarvis\User;

use SecTheater\Jarvis\Post\EloquentPost;

interface UserInterface
{
    public function peopleCommentedOnAPost(EloquentPost $post);

    public function peopleRepliedOnAPost(EloquentPost $post);
}
