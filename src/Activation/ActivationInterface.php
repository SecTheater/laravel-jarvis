<?php

namespace SecTheater\Jarvis\Activation;

use SecTheater\Jarvis\Interfaces\RestrictionInterface;

interface ActivationInterface
{
    public function tokenExists(RestrictionInterface $user, bool $create = false);

    public function completed(RestrictionInterface $user);

    public function complete(RestrictionInterface $user);

    public function clear(bool $completed = false):bool;

    public function clearFor(RestrictionInterface $user, bool $completed = false, bool $any = false):bool;

    public function generateToken(RestrictionInterface $user);

    public function regenerateToken(RestrictionInterface $user, bool $create = false);

    public function removeExpired();

    public function getActivationsHave($relation, $operator = '=', $condition = null);

    public function getActivationsDoesntHave($relation, array $condition = null);

    public function getActivationsWhereHave($relation, array $condition);
}
