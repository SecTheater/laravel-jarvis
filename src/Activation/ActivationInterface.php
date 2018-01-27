<?php
namespace SecTheater\Jarvis\Activation;
use SecTheater\Jarvis\Interfaces\RestrictionInterface;
interface ActivationInterface {

function tokenExists(RestrictionInterface $user, bool $create = false);
	function completed(RestrictionInterface $user);

	function complete(RestrictionInterface $user);

	function clear(bool $completed = false):bool;

	function clearFor(RestrictionInterface $user, bool $completed = false, bool $any = false):bool;

	function generateToken(RestrictionInterface $user);

	function regenerateToken(RestrictionInterface $user, bool $create = false);

	function removeExpired();
	function getActivationsHave($relation, $operator = '=', $condition = null);

	function getActivationsDoesntHave($relation, array $condition = null);

	function getActivationsWhereHave($relation, array $condition);

}