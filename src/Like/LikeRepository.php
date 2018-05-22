<?php

namespace SecTheater\Jarvis\Like;

use SecTheater\Jarvis\Repositories\Repository;

class LikeRepository extends Repository implements LikeInterface {
	protected $model;

	public function __construct(EloquentLike $model) {
		$this->model = $model;
	}

	public function likeStatus($type, int $user_id = null) {
		$existence = $this->findBy(['user_id' => $user_id ?? user()->id, 'likable_type' => class_basename($type), 'likable_id' => $type->id]);
		if (count($existence) > 0) {
			return $existence->first()->like_status;
		}
	}

	public function likeCounter($type, bool $status = true): int {
		return $this->model->where(['likable_id' => $type->id, 'likable_type' => class_basename($type), 'like_status' => $status])->count();
	}

	public function like($type) {
		if ($this->exists(['user_id' => user()->id, 'likable_id' => $type->id, 'likable_type' => class_basename($type)])) {
			$record = $this->model->where(['user_id' => user()->id, 'likable_id' => $type->id, 'likable_type' => class_basename($type)])->first();
			// type is already liked, and failed to like it again.
			if ($record->like_status) {
				return false;
			}

			return (bool) $record->update(['like_status' => true]);
		}

		return $this->create([
			'user_id' => user()->id,
			'likable_type' => class_basename($type),
			'likable_id' => $type->id,
			'like_status' => true,
		]);
	}

	public function removeLike($type) {
		if ($this->exists(['user_id' => user()->id, 'likable_id' => $type->id, 'likable_type' => class_basename($type)])) {
			return (bool) $this->model->where(['user_id' => user()->id, 'likable_id' => $type->id, 'likable_type' => class_basename($type)])->delete();
		}

		return false;
	}

	public function dislike($type) {
		if ($this->exists(['user_id' => user()->id, 'likable_id' => $type->id, 'likable_type' => class_basename($type)])) {
			$record = $this->model->where(['user_id' => user()->id, 'likable_id' => $type->id, 'likable_type' => class_basename($type)])->first();
			// type is already disliked, and failed to like it again.
			if (!$record->like_status) {
				return false;
			}

			return (bool) $record->update(['like_status' => false]);
		}

		return $this->create([
			'user_id' => user()->id,
			'likable_type' => class_basename($type),
			'likable_id' => $type->id,
			'like_status' => false,
		]);
	}
}
