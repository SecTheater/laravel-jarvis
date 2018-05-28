<?php

namespace SecTheater\Jarvis\Like;

use SecTheater\Jarvis\Repositories\Repository;

class LikeRepository extends Repository implements LikeInterface {
	protected $model;

	public function __construct(EloquentLike $model) {
		$this->model = $model;
	}

	public function likeStatus($type, int $user_id = null) {
		return (bool) $type->likes()->where('user_id',$user_id ?? user()->id )->first()->status;
	}

	public function likeCounter($type, bool $status = true): int {
		return $type->likes()->whereStatus($status)->count();
	}

	public function like($type) {
		if ($type->likes()->exists()) {
			// already liked.
			if($type->likes->first()->status){
				return false;
			}
			$type->likes()->update(['status' => true]);
			return $type;
		}
		$like = model('Like');
	    $like->status = true;
	    $like->user_id = user()->id;
	    $type->likes()->save($like);
	    return $type;
	}

	public function removeLike($type) {
		$type->likes()->delete();
		return $type;
	}

	public function dislike($type) {
		if($type->likes()->exists()){
			// already disliked.
			if(!$type->likes->first()->status){
				return false;
			}
			$type->likes()->update(['status' => false]);
			return $type;
		}

		$like = model('Like');
	    $like->status = false;
	    $like->user_id = user()->id;
	    $type->likes()->save($like);
	    return $type;
	}
}
