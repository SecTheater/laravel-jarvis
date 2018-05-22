@component('mail::message')
# Reset Your Password !
Hello {{ $user->first_name }},
Hopefully you are having a great day :) 
please click on the link below to finish up the reset password process
@component('mail::button', ['url' => env('APP_URL','http://localhost:8000'). '/reset/'. $user->email .'/' . $token])
Reset Password
@endcomponent

Cheers,<br>
{{ config('app.name') }} !
@endcomponent
