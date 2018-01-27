<?php
namespace SecTheater\Jarvis\Repositories;
use Illuminate\Database\Eloquent\Model;
use SecTheater\Jarvis\Interfaces\RepositoryInterface;

abstract

class Repository implements RepositoryInterface {
	function all($columns = ['*']) {
		return $this->model->all($columns);
	}
	function find($attribute, $columns = ['*']) {
		return $this->model->find($attribute, $columns);
	}
	function findBy($attribute, $operator = '=', $value = null) {
		if (is_array($attribute)) {
			return $this->model->where($attribute)->get();
		}
		if (func_num_args() === 2) {
			list($attribute, $value) = func_get_args();

		}
		return $this->model->where($attribute, $operator, $value)->get();
	}
	function create(array $attributes) {
		if (isset($attributes['user_id']) && is_object($attributes['user_id'])) {
			$attributes['user_id'] = $attributes['user_id']->id;
		}
		if (isset($attributes['post_id']) && is_object($attributes['post_id'])) {
			$attributes['post_id'] = $attributes['post_id']->id;
		}
		if (isset($attributes['likable_id']) && is_object($attributes['likable_id'])) {
			$attributes['likable_id'] = $attributes['likable_id']->id;
		}
		if (isset($attributes['comment_id']) && is_object($attributes['comment_id'])) {
			$attributes['comment_id'] = $attributes['comment_id']->id;

		}
		if (isset($attributes['reply_id']) && is_object($attributes['reply_id'])) {
			$attributes['reply_id'] = $attributes['reply_id']->id;
		}
		if (isset($attributes['tag_id']) && is_object($attributes['tag_id'])) {
			$attributes['tag_id'] = $attributes['tag_id']->id;
		}

		return $this->model->create($attributes);
	}
	function update(Model $repository, array $attributes) {
		if (isset($attributes['user_id']) && is_object($attributes['user_id'])) {
			$attributes['user_id'] = $attributes['user_id']->id;
		}
		if (isset($attributes['post_id']) && is_object($attributes['post_id'])) {
			$attributes['post_id'] = $attributes['post_id']->id;
		}
		if (isset($attributes['likable_id']) && is_object($attributes['likable_id'])) {
			$attributes['likable_id'] = $attributes['likable_id']->id;
		}
		if (isset($attributes['comment_id']) && is_object($attributes['comment_id'])) {
			$attributes['comment_id'] = $attributes['comment_id']->id;

		}
		if (isset($attributes['reply_id']) && is_object($attributes['reply_id'])) {
			$attributes['reply_id'] = $attributes['reply_id']->id;
		}
		if (isset($attributes['tag_id']) && is_object($attributes['tag_id'])) {
			$attributes['tag_id'] = $attributes['tag_id']->id;
		}

		return ($repository->update($attributes))?$repository:false;
	}
	function delete(Model $repository) {
		return (bool) $repository->delete();
	}
	function exists($attribute, $operator = '=', $value = null) {
		if (is_array($attribute)) {
			return $this->model->where($attribute)->exists();
		}
		if (func_num_args() === 2) {
			list($attribute, $value) = func_get_args();

		}
		return $this->model->where($attribute, $operator, $value)->exists();
	}
	function ordered($attribute, $order = 'desc') {
		return $this->model->orderBy($attribute, $order)->get();
	}
	function __call($method, $arguments) {
		return $this->model->$method(...$arguments);
	}
}
