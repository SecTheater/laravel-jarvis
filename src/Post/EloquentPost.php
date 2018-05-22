<?php

namespace SecTheater\Jarvis\Post;

use Carbon\Carbon;
use SecTheater\Jarvis\Model\EloquentModel;

class EloquentPost extends EloquentModel {
	protected $dates = [
		'approved_at', 'created_at', 'updated_at',
	];
	protected $table = 'posts';
	public function likes() {
		return $this->morphToMany($this->likeModel, 'likable', 'likables', 'likable_id', 'id');
	}

	public function getRouteKeyName() {
		return 'title';
	}

	public function tags() {
		return $this->belongsToMany($this->tagModel, 'post_tag', 'post_id', 'tag_id');
	}

	public function comments() {
		return $this->hasMany($this->commentModel, 'post_id');
	}

	public function replies() {
		return $this->hasMany($this->replyModel, 'post_id');
	}

	public function user() {
		return $this->belongsTo($this->userModel, 'user_id');
	}

	public function scopeFilter($query, $month = null, $year = null) {
		if (isset($month)) {
			$query->whereMonth('created_at', Carbon::parse($month)->month);
		}
		if (isset($year)) {
			$query->whereYear('created_at', $year);
		}
	}
}
