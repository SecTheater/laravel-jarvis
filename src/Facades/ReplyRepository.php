<?php

namespace SecTheater\Jarvis\Facades;

use Illuminate\Support\Facades\Facade;

class ReplyRepository extends Facade {
	protected static function getFacadeAccessor() {
		return 'ReplyRepository';
	}
}
