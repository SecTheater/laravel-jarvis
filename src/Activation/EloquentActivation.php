<?php

namespace SecTheater\Jarvis\Activation;

use SecTheater\Jarvis\Model\EloquentModel;

class EloquentActivation extends EloquentModel
{
    protected $table = 'activations';
    protected $casts = [
        'completed' => 'boolean',
    ];
    public function user()
    {
        return $this->belongsTo($this->userModel, 'user_id', 'id');
    }
}
