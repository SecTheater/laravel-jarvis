<?php

namespace SecTheater\Jarvis\Interfaces;

interface TokenInterface
{
    public function hasOrCreateToken(RestrictionInterface $user);

    public function completed(RestrictionInterface $user);

    public function complete(RestrictionInterface $user, $token);

    public function clear(bool $completed = false):bool;

    public function clearFor(RestrictionInterface $user, bool $completed = false, bool $any = false):bool;

    public function generateToken(RestrictionInterface $user);

    public function regenerateToken(RestrictionInterface $user, bool $create = false);

    public function removeExpired();
}
