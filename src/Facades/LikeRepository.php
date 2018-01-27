<?php

namespace SecTheater\Jarvis\Facades;

use Illuminate\Support\Facades\Facade;

class LikeRepository extends Facade {
	protected static function getFacadeAccessor() {
		return 'LikeRepository';
	}
}
