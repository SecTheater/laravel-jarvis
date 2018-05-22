<?php

namespace SecTheater\Jarvis\Like;

use Illuminate\Database\Eloquent\Relations\Relation;
use SecTheater\Jarvis\Comment\EloquentComment;
use SecTheater\Jarvis\Model\EloquentModel;
use SecTheater\Jarvis\Post\EloquentPost;
use SecTheater\Jarvis\Reply\EloquentReply;

Relation::morphMap([
	'Post' => model_exists('Post') ? \App\Post::class : EloquentPost::class,
	'Comment' => model_exists('Comment') ? \App\Comment::class : EloquentComment::class,
	'Reply' => model_exists('Reply') ? \App\Reply::class : EloquentReply::class,
]);

class EloquentLike extends EloquentModel {
	protected $casts = [
		'like_status' => 'boolean',
	];
	protected $table = 'likes';
	// public function __call($name, $arguments) {
	// 	return $this->morphedByMany();
	// }
	public function posts() {
		return $this->morphedByMany($this->postModel, 'likable', 'likables', 'like_id', 'likable_id');
	}

	public function comments() {
		return $this->morphedByMany($this->commentModel, 'likable', 'likables', 'like_id', 'likable_id');
	}

	public function replies() {
		return $this->morphedByMany($this->replyModel, 'likable', 'likables', 'like_id', 'likable_id');
	}

	public function user() {
		return $this->belongsTo($this->userModel, 'user_id');
	}
}
