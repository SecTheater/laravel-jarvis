<?php

namespace SecTheater\Jarvis\Traits;

use SecTheater\Jarvis\Interfaces\RestrictionInterface;

/**
 * Issuing Tokens within Activation & Reminder Process.
 */
trait IssueTokens
{
    public function hasToken(RestrictionInterface $user)
    {
        if ($user->{$this->process}->count() && $user->{$this->process}()->orderBy('created_at','desc')->first()->token) {
            return $user->{$this->process}()->orderBy('created_at','desc')->first();
        }
    }

    public function hasOrCreateToken(RestrictionInterface $user)
    {
        if (!$this->hasToken($user)) {
            return $this->generateToken($user);
        }
        return $this->hasToken($user) ?? false;
    }

    public function completed(RestrictionInterface $user)
    {
        if (${$this->process} = $this->hasOrCreateToken($user)) {
            return ${$this->process}->completed;
        }

        return false;
    }

    public function complete(RestrictionInterface $user, $token)
    {
        ${$this->process} = $this->hasOrCreateToken($user);
        if (!${$this->process}) {
            $exception = "SecTheater\Jarvis\\".ucfirst($this->process).'\\'.ucfirst($this->process).'Exception';

            throw new $exception('User Does not have token');
        }
        if (${$this->process} && ${$this->process}->token !== null && ${$this->process}->token === $token) {
            ${$this->process}->token = null;
            ${$this->process}->completed_at = date('Y-m-d H:i:s');
            ${$this->process}->completed = true;
            ${$this->process}->save();
            return true;
        } elseif (${$this->process} && ${$this->process}->completed === true) {
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
        return $this->create([
                'user_id'    => $user->id,
                'completed'  => false,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
    }

    public function regenerateToken(RestrictionInterface $user, bool $create = false)
    {
        if (${$this->process} = $this->hasOrCreateToken($user)) {
            ${$this->process}->update([
                    'token'        => str_random(32),
                    'completed'    => false,
                    'completed_at' => null,
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);

            return ${$this->process};
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
        return (bool) $this->model->where(['completed' => false, ['created_at', '>=', \Carbon\Carbon::now()->subDays(config("jarvis.{$this->process}s.expiration"))->format('Y-m-d H:i:s')]])->delete();
    }
}
