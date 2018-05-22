<?php

namespace SecTheater\Jarvis\User;

use Cache;
use SecTheater\Jarvis\Repositories\Repository;

class UserRepository extends Repository implements UserInterface {
	protected $user;

	public function __construct(EloquentUser $user) {
		$this->user = $user;
	}

	public function PeopleCommentedOnAPost(\App\Post $post) {
		if (config('jarvis.comments.approve')) {
			return $post->comments()->where(['approved' => true, ['user_id', '!=', $post->user->id]])->distinct()->get()->unique('user_id');
		}

		return $post->comments()->where('user_id', '!=', $post->user->id)->distinct()->get()->unique('user_id');
	}

	public function PeopleRepliedOnAPost(\App\Post $post) {
		if (config('jarvis.replies.approve')) {
			return $post->replies()->where(['approved' => true, ['user_id', '!=', $post->user->id]])->distinct()->get()->unique('user_id');
		}

		return $post->replies()->where('user_id', '!=', $post->user->id)->distinct()->get()->unique('user_id');
	}
}
