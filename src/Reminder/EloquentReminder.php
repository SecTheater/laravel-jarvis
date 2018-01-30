<?php

namespace SecTheater\Jarvis\Reminder;

use Illuminate\Database\Eloquent\Model;

class EloquentReminder extends Model
{
    protected $table = 'reminders';
    protected $guarded = [];
    protected $casts = [
      'completed' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(EloquentUser::class, 'user_id', 'id');
    }
}
