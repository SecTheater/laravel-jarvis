<?php
namespace SecTheater\Jarvis\Comment;
use Illuminate\Database\Eloquent\Model;
use SecTheater\Jarvis\Exceptions\ConfigException;
use SecTheater\Jarvis\Interfaces\RestrictionInterface;
use SecTheater\Jarvis\Repositories\Repository;

class CommentRepository extends Repository implements CommentInterface {
	protected $model;
	function __construct(EloquentComment $model) {
		$this->model = $model;
	}

	function userComments(RestrictionInterface $user, array $condition = null) {
		if (isset($condition)) {
			return $user->comments()->where($condition)->get();
		}
		return $user->comments;
	}
	function commentsWith($relation) {
		return $this->model->with($relation)->get();
	}
	function getApproved($relation = null) {
		if (!config('jarvis.posts.approve')) {
			throw new ConfigException('Approval Is not enabled for posts.');
		}

		if (isset($relation)) {
			return $this->model->where('approved', true)->withCount([
					$relation,
					"$relation AS pending_{$relation}"=> function ($query) {
						$query->where('approved', true);
					}
				])->get();
		}
		return $this->model->whereApproved(true)->get();
	}
	function getUnapproved($relation = null) {
		if (!config('jarvis.posts.approve')) {
			throw new ConfigException('Approval Is not enabled for posts.');
		}

		if (isset($relation)) {
			return $this->model->where('approved', false)->withCount([
					$relation,
					"$relation AS pending_{$relation}"=> function ($query) {
						$query->where('approved', false);
					}
				])->get();
		}

		return $this->model->whereApproved(false)->get();
	}
	function recentlyApproved() {
		if (!config('jarvis.posts.approve')) {
			throw new ConfigException('Approval Is not enabled for posts.');
		}

		return $this->model->whereApproved(true)->get()->sortByDesc('approved_at');
	}
	function recentComments(array $condition = null) {
		if (isset($condition)) {
			return $this->model->where($condition)->get()->sortByDesc('created_at');
		}
		return $this->all()->sortByDesc('created_at');
	}
	function getCommentsHave($relation, $operator = '=', $condition = null) {
		if (is_array($condition) || is_array($operator)) {
			list($condition) = [$condition??$operator];
			return $this->getCommentsWhereHave($relation, $condition);
		}
		if (func_num_args() === 2) {
			list($relation, $condition) = func_get_args();
			return $this->model->has($relation, $operator, $condition)->get();
		} else if (func_num_args() === 3) {
			return $this->model->has($relation, $operator, $condition)->get();
		}
		return $this->model->has($relation)->get();
	}
	function getCommentsDoesntHave($relation, array $condition = null) {
		if (isset($condition)) {
			return $this->model->whereDoesntHave($relation, function ($query) use ($condition) {
					return $query->where($condition);
				})->get();

		}
		return $this->model->doesntHave($relation)->get();

	}
	function getCommentsWhereHave($relation, array $condition) {
		return $this->model->whereHas($relation, function ($query) use ($condition) {
				$query->where($condition);
			})->get();

	}

}