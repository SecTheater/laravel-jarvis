<?php

namespace SecTheater\Jarvis\Like;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use SecTheater\Jarvis\Comment\EloquentComment;
use SecTheater\Jarvis\Post\EloquentPost;
use SecTheater\Jarvis\Reply\EloquentReply;
use SecTheater\Jarvis\User\EloquentUser;
Relation::morphMap([
		'Post' => EloquentPost::

class ,
		'Comment' => EloquentComment::class ,
		'Reply'   => EloquentReply::class
	]);

class EloquentLike extends Model {
	protected $guarded = [];
	protected $casts   = [
		'like_status' => 'boolean'
	];
	protected $table = 'likes';
	public function likable() {
		return $this->morphTo();
	}
	public function user() {
		return $this->belongsTo(EloquentUser::class , 'user_id');
	}

}