<?php

namespace SecTheater\Jarvis\Reminder;

use SecTheater\Jarvis\Model\EloquentModel;

class EloquentReminder extends EloquentModel {
	protected $table = 'reminders';
	protected $casts = [
		'completed' => 'boolean',
	];

	public function user() {
		return $this->belongsTo($this->userModel, 'user_id', 'id');
	}
}
