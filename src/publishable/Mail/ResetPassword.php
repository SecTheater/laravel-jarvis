<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;
    public $user;
    public $token;

    public function __construct(\SecTheater\Jarvis\User\EloquentUser $user, \SecTheater\Jarvis\Reminder\EloquentReminder $reminder)
    {
        $this->user = $user;
        $this->token = $reminder->token;
    }

    public function build()
    {
        return $this->markdown('emails.resetpassword');
    }
}
