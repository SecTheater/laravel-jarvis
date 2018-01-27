<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use SecTheater\Jarvis\Activation\EloquentActivation;
use SecTheater\Jarvis\User\EloquentUser;

class Activation extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $token;

    public function __construct(EloquentUser $user, EloquentActivation $activation)
    {
        $this->user = $user;
        $this->token = $activation->token;
    }

    public function build()
    {
        return $this->markdown('emails.activate');
    }
}
