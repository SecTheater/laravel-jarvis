<?php

namespace SecTheater\Jarvis\Reply;

use SecTheater\Jarvis\Model\EloquentModel;

class EloquentReply extends EloquentModel {
	protected $table = 'replies';
	protected $guarded = [];

	public function comment() {
		return $this->belongsTo($this->commentModel);
	}

	public function user() {
		return $this->belongsTo($this->userModel);
	}

	public function post() {
		return $this->belongsTo($this->postModel);
	}

    public function likes()
    {
        return $this->morphMany($this->likeModel, 'likable');
    }
}
