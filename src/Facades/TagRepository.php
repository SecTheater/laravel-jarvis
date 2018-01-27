<?php

namespace SecTheater\Jarvis\Facades;

use Illuminate\Support\Facades\Facade;

class TagRepository extends Facade {
	protected static function getFacadeAccessor() {
		return 'TagRepository';
	}
}
