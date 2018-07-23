<?php

namespace SecTheater\Jarvis\User;
use SecTheater\Jarvis\Post\EloquentPost;
use SecTheater\Jarvis\Repositories\Repository;

class UserRepository extends Repository implements UserInterface
{
    protected $model;

    public function __construct(EloquentUser $model)
    {
        $this->model = $model;
    }

    public function peopleCommentedOnAPost(EloquentPost $post)
    {
      return $this->peopleRelatedToPost($post,'comments');
    }
    protected function peopleRelatedToPost(EloquentPost $post,$relation){
        $related = $post->$relation()->where('user_id','!=' , $post->user->id);
        if (config('jarvis.'.$relation. '.approve')) {
            $related->whereApproved(true);
        }
        return $related->distinct()->get()->unique('user_id');
    }
    public function peopleRepliedOnAPost(EloquentPost $post)
    {
      return $this->peopleRelatedToPost($post,'replies');
    }
}
