<?php

namespace SecTheater\Jarvis\Reminder;

use SecTheater\Jarvis\Interfaces\RestrictionInterface;
use SecTheater\Jarvis\Repositories\Repository;

class ReminderRepository extends Repository implements ReminderInterface
{
    protected $model;

    public function __construct(EloquentReminder $model)
    {
        $this->model = $model;
    }

    public function tokenExists(RestrictionInterface $user, bool $create = false)
    {
        if ($create) {
            if (!$this->model->where('user_id', $user->id)->exists()) {
                return $this->generateToken($user);
            }
        }

        return ($this->model->where('user_id', $user->id)->exists()) ? $this->model->where('user_id', $user->id)->first() : false;
    }

    public function completed(RestrictionInterface $user)
    {
        if ($reminder = $this->tokenExists($user)) {
            return $reminder->completed;
        }

        return false;
    }

    public function complete(RestrictionInterface $user)
    {
        $reminder = $this->tokenExists($user);

        if ($reminder && $reminder->token !== null) {
            return (bool) $reminder->update([
                    'token'        => null,
                    'user_id'      => $user->id,
                    'completed'    => true,
                    'completed_at' => date('Y-m-d H:i:s'),
                ]);
        } elseif ($reminder && $reminder->completed === true) {
            return true;
        }

        return false;
    }

    public function clear(bool $completed = false):bool
    {
        return (bool) $this->model->where('completed', $completed)->delete();
    }

    public function clearFor(RestrictionInterface $user, bool $completed = false, bool $any = false):bool
    {
        if ($any) {
            return (bool) $this->model->where(['user_id' => $user->id])->delete();
        }

        return (bool) $this->model->where(['user_id' => $user->id, 'completed' => $completed])->delete();
    }

    public function generateToken(RestrictionInterface $user)
    {
        if ($reminder = $this->tokenExists($user)) {
            return $reminder;
        }

        return $this->create([
                'token'      => str_random(32),
                'user_id'    => $user->id,
                'completed'  => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => null,
            ]);
    }

    public function regenerateToken(RestrictionInterface $user, bool $create = false)
    {
        if ($reminder = $this->tokenExists($user)) {
            $reminder->update([
                    'token'        => str_random(32),
                    'completed'    => false,
                    'completed_at' => null,
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);

            return $reminder;
        }
        if ($create) {
            return $this->create([
                    'token'      => str_random(32),
                    'user_id'    => $user->id,
                    'completed'  => false,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => null,
                ]);
        }

        return false;
    }

    public function removeExpired()
    {
        return (bool) $this->model->where(['completed' => false, ['created_at', '>=', \Carbon\Carbon::now()->subDays(config('jarvis.reminders.expiration'))->format('Y-m-d H:i:s')]])->delete();
    }
}
