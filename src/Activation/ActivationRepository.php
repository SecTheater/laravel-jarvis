<?php

namespace SecTheater\Jarvis\Activation;

use SecTheater\Jarvis\Interfaces\RestrictionInterface;
use SecTheater\Jarvis\Repositories\Repository;

class ActivationRepository extends Repository implements ActivationInterface
{
    protected $model;

    public function __construct(EloquentActivation $model)
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
        if ($activation = $this->tokenExists($user)) {
            return $activation->completed;
        }

        return false;
    }

    public function complete(RestrictionInterface $user)
    {
        $activation = $this->tokenExists($user);
        if ($activation && $activation->token !== null) {
            return (bool) $activation->update([
                    'token'        => null,
                    'user_id'      => $user->id,
                    'completed'    => true,
                    'completed_at' => date('Y-m-d H:i:s'),
                ]);
        } elseif ($activation && $activation->completed === true) {
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
        if ($activation = $this->tokenExists($user)) {
            return $activation;
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
        if ($activation = $this->tokenExists($user)) {
            $activation->update([
                    'token'        => str_random(32),
                    'completed'    => false,
                    'completed_at' => null,
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);

            return $activation;
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
        return (bool) $this->model->where(['completed' => false, ['created_at', '>=', \Carbon\Carbon::now()->subDays(config('jarvis.activations.expiration'))->format('Y-m-d H:i:s')]])->delete();
    }
}
