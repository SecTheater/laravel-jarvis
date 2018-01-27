<?php

namespace SecTheater\Jarvis\Comment;
use Illuminate\Database\Eloquent\Model;
use SecTheater\Jarvis\Like\EloquentLike;
use SecTheater\Jarvis\Post\EloquentPost;
use SecTheater\Jarvis\Reply\EloquentReply;
use SecTheater\Jarvis\User\EloquentUser;

class EloquentComment extends Model {
	protected $table   = 'comments';
	protected $guarded = [];
	public function post() {
		return $this->belongsTo(EloquentPost::class );
	}
	public function user() {
		return $this->belongsTo(EloquentUser::class );
	}
	public function replies() {
		return $this->hasMany(EloquentReply::class , 'comment_id');
	}

	public function likes() {
		return $this->morphMany(EloquentLike::class , 'likable');
	}

}
