<?php

namespace SecTheater\Jarvis\Reminder;
use Illuminate\Database\Eloquent\Model;
use SecTheater\Jarvis\Interfaces\RestrictionInterface;
use SecTheater\Jarvis\Reminder\ReminderInterface;
use SecTheater\Jarvis\Repositories\Repository;

class ReminderRepository extends Repository implements ReminderInterface {
	protected $model;
	function __construct(EloquentReminder $model) {
		$this->model = $model;
	}
	function getRemindersHave($relation, $operator = '=', $condition = null) {
		if (is_array($condition) || is_array($operator)) {
			list($condition) = [$condition??$operator];
			return $this->getRemindersWhereHave($relation, $condition);
		}
		if (func_num_args() === 2) {
			list($relation, $condition) = func_get_args();
			return $this->model->has($relation, $operator, $condition)->get();
		} elseif (func_num_args() === 3) {
			return $this->model->has($relation, $operator, $condition)->get();
		}
		return $this->model->has($relation)->get();
	}
	function getRemindersDoesntHave($relation, array $condition = null) {
		if (isset($condition)) {
			return $this->model->whereDoesntHave($relation, function ($query) use ($condition) {
					return $query->where($condition);
				})->get();

		}
		return $this->model->doesntHave($relation)->get();

	}
	function getRemindersWhereHave($relation, array $condition) {
		return $this->model->whereHas($relation, function ($query) use ($condition) {
				$query->where($condition);
			})->get();

	}

	function tokenExists(RestrictionInterface $user, bool $create = false) {
		if ($create) {
			if (!$this->model->where('user_id', $user->id)->exists()) {
				return $this->generateToken($user);
			}
		}
		return ($this->model->where('user_id', $user->id)->exists())?$this->model->where('user_id', $user->id)->first():false;
	}
	function completed(RestrictionInterface $user) {
		if ($reminder = $this->tokenExists($user)) {
			return $reminder->completed;
		}
		return false;

	}
	function complete(RestrictionInterface $user) {
		$reminder = $this->tokenExists($user);

		if ($reminder && $reminder->token !== null) {
			return (bool) $reminder->update([
					'token'        => null,
					'user_id'      => $user->id,
					'completed'    => true,
					'completed_at' => date('Y-m-d H:i:s')
				]);
		} elseif ($reminder && $reminder->completed === true) {
			return true;
		}
		return false;
	}
	function clear(bool $completed = false):bool {
		return (bool) $this->model->where('completed', $completed)->delete();
	}
	function clearFor(RestrictionInterface $user, bool $completed = false, bool $any = false):bool {
		if ($any) {
			return (bool) $this->model->where(['user_id' => $user->id])->delete();
		}
		return (bool) $this->model->where(['user_id' => $user->id, 'completed' => $completed])->delete();
	}

	function generateToken(RestrictionInterface $user) {
		if ($reminder = $this->tokenExists($user)) {
			return $reminder;
		}
		return $this->create([
				'token'      => str_random(32),
				'user_id'    => $user->id,
				'completed'  => false,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => NULL
			]);
	}
	function regenerateToken(RestrictionInterface $user, bool $create = false) {
		if ($reminder = $this->tokenExists($user)) {
			$reminder->update([
					'token'        => str_random(32),
					'completed'    => false,
					'completed_at' => NULL,
					'updated_at'   => date('Y-m-d H:i:s')
				]);
			return $reminder;
		}
		if ($create) {
			return $this->create([
					'token'      => str_random(32),
					'user_id'    => $user->id,
					'completed'  => false,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => NULL
				]);
		}
		return false;

	}
	function removeExpired() {
		return (bool) $this->model->where(['completed' => false, ['created_at', '>=', \Carbon\Carbon::now()->subDays(config('jarvis.activation.expiration'))->format('Y-m-d H:i:s')]])->delete();
	}

}