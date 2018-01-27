<?php
namespace SecTheater\Jarvis\Reminder;

use SecTheater\Jarvis\Interfaces\RestrictionInterface;
interface ReminderInterface {

function tokenExists(RestrictionInterface $user, bool $create = false);

	function completed(RestrictionInterface $user);

	function complete(RestrictionInterface $user);

	function clear(bool $completed = false):bool;

	function clearFor(RestrictionInterface $user, bool $completed = false, bool $any = false):bool;

	function generateToken(RestrictionInterface $user);

	function regenerateToken(RestrictionInterface $user, bool $create = false);
	function getRemindersHave($relation, $operator = '=', $condition = null);

	function getRemindersDoesntHave($relation, array $condition = null);

	function getRemindersWhereHave($relation, array $condition);

}