<?php

namespace SecTheater\Jarvis\Interfaces;

interface TokenInterface
{
    public function hasOrCreateToken(UserInterface $user);

    public function completed(UserInterface $user);

    public function complete(UserInterface $user, $token);

    public function clear(bool $completed = false):bool;

    public function clearFor(UserInterface $user, bool $completed = false, bool $any = false):bool;

    public function generateToken(UserInterface $user);

    public function regenerateToken(UserInterface $user, bool $create = false);

    public function removeExpired();
}
