<?php

namespace SecTheater\Jarvis\Facades;

use Illuminate\Support\Facades\Facade;

class CommentRepository extends Facade {
	protected static function getFacadeAccessor() {
		return 'CommentRepository';
	}
}
