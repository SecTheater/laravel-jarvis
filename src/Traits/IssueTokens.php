<?php

namespace SecTheater\Jarvis\Traits;

use SecTheater\Jarvis\Interfaces\UserInterface;

/**
 * Issuing Tokens within Activation & Reminder Process.
 */
trait IssueTokens
{
    /**
     * Retrieves the first applied token.
     *
     * @param UserInterface $user
     * @param [string]      $token
     *
     * @return Illuminate\Database\Eloquent\Model|null
     */
    public function hasToken(UserInterface $user, string $token = null)
    {
        ${$this->process} = $user->{$this->process}()->where(['completed' => false, ['created_at', '>=', \Carbon\Carbon::now()->subDays(config("jarvis.{$this->process}s.expiration"))->format('Y-m-d H:i:s')]]);
        if ($token) {
            ${$this->process}->whereToken($token);
        }

        return ${$this->process}->first();
    }

    /**
     * Retrieves the first applied or token or create new one.
     *
     * @param UserInterface $user [description]
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function hasOrCreateToken(UserInterface $user)
    {
        return $this->hasToken($user) ?? $this->generateToken($user);
    }

    /**
     * Check if the user has completed.
     *
     * @param UserInterface $user
     *
     * @return bool
     */
    public function completed(UserInterface $user)
    {
        return $user->activation()->whereCompleted(true)->first()->completed ?? false;
    }

    /**
     * Complete the process to a specific user.
     *
     * @param UserInterface $user
     * @param [string]      $token
     *
     * @return bool|ActivationException|ReminderException
     */
    public function complete(UserInterface $user, string $token)
    {
        ${$this->process} = $this->hasToken($user, $token);
        if (!${$this->process}) {
            $exception = "SecTheater\Jarvis\\".ucfirst($this->process).'\\'.ucfirst($this->process).'Exception';

            throw new $exception('User Does not have this token');
        }
        if (${$this->process} && ${$this->process}->token === $token) {
            ${$this->process}->token = null;
            ${$this->process}->completed_at = date('Y-m-d H:i:s');
            ${$this->process}->completed = true;
            ${$this->process}->save();

            return true;
        }
    }

    /**
     * Deletes Activation/Reminder Records based on completed column.
     *
     * @param bool|bool $completed [description]
     *
     * @return bool
     */
    public function clear(bool $completed = false):bool
    {
        return (bool) $this->model->where('completed', $completed)->delete();
    }

    /**
     * Delete Activation/Reminder Record for sepcific user , based on completed column or any.
     *
     * @param UserInterface $user
     * @param bool|bool     $completed
     * @param bool|bool     $any
     *
     * @return bool
     */
    public function clearFor(UserInterface $user, bool $completed = false, bool $any = false):bool
    {
        if ($any) {
            return (bool) $user->{$this->process}()->delete();
        }

        return (bool) $user->{$this->process}()->whereCompleted($completed)->delete();
    }

    /**
     * generates token for specific user.
     *
     * @param UserInterface $user  [description]
     * @param bool|bool     $force [description]
     *
     * @return Illuminate\Database\Eloquent\Model|bool
     */
    public function generateToken(UserInterface $user, bool $force = false)
    {
        $user->{$this->process}()->whereCompleted(false)->delete();
        if ($user->{$this->process}->count() && !$force) {
            return false;
        }

        return $this->create([
                'user_id'    => $user->id,
                'completed'  => false,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
    }

    /**
     * generates a token for the passed user.
     *
     * @param UserInterface $user [description]
     *
     * @return Illuminate\Database\Eloquent\Model|bool
     */
    public function forceGenerateToken(UserInterface $user)
    {
        return $this->generateToken($user, true);
    }

    /**
     * Regnerates a token for the passed user.
     *
     * @param UserInterface $user   [description]
     * @param bool|bool     $create [description]
     *
     * @return Illuminate\Database\Eloquent\Model|bool
     */
    public function regenerateToken(UserInterface $user, bool $create = false)
    {
        if (${$this->process} = $this->hasToken($user)) {
            ${$this->process}->update([
                    'token'        => str_random(32),
                    'completed'    => false,
                    'completed_at' => null,
                    'updated_at'   => date('Y-m-d H:i:s'),
                ]);

            return ${$this->process};
        }
        if ($create) {
            return $this->generateToken($user);
        }

        return false;
    }

    /**
     * removes expired records which aren't completed yet.
     *
     * @param int|null $days
     *
     * @return bool
     */
    public function removeExpired(int $days = null)
    {
        return (bool) $this->model->where(['completed' => false, ['created_at', '>=', \Carbon\Carbon::now()->subDays($days ?? config("jarvis.{$this->process}s.expiration"))->format('Y-m-d H:i:s')]])->delete();
    }
}
