<?php

namespace SecTheater\Jarvis\Comment;

use SecTheater\Jarvis\Model\EloquentModel;

class EloquentComment extends EloquentModel {
	protected $table = 'comments';
	public function post() {
		return $this->belongsTo($this->postModel);
	}

	public function user() {
		return $this->belongsTo($this->userModel);
	}

	public function replies() {
		return $this->hasMany($this->replyModel, 'comment_id');
	}

	public function likes() {
		return $this->morphToMany($this->likeModel, 'likable', 'likables', 'likable_id', 'id');
	}
}
