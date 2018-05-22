<?php

namespace SecTheater\Jarvis\Reminder;

use SecTheater\Jarvis\Interfaces\RestrictionInterface;

interface ReminderInterface
{
    public function tokenExists(RestrictionInterface $user, bool $create = false);

    public function completed(RestrictionInterface $user);

    public function complete(RestrictionInterface $user);

    public function clear(bool $completed = false):bool;

    public function clearFor(RestrictionInterface $user, bool $completed = false, bool $any = false):bool;

    public function generateToken(RestrictionInterface $user);

    public function regenerateToken(RestrictionInterface $user, bool $create = false);

}
