<?php

namespace SecTheater\Jarvis\Facades;

use Illuminate\Support\Facades\Facade;

class ReminderRepository extends Facade {
	protected static function getFacadeAccessor() {
		return 'ReminderRepository';
	}
}
