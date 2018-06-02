@component('mail::message')
# Email Activation
Hello {{ $user->first_name }},
We are glad that you are joining us, Let's hope you enjoy our website
Please activate your account now in order to be able to be a member of our website

@component('mail::button', ['url' => env('APP_URL','http://localhost:8000'). '/activate/'. $user->email .'/' . $token])
Activate Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
