<?php

namespace SecTheater\Jarvis\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Jarvis;
use SecTheater\Jarvis\Http\Requests\RegisterRequest;

class RegisterController extends Controller
{
    public function getRegister()
    {
        return view('auth.register');
    }

    public function postRegister(RegisterRequest $request)
    {
        if ($user = Jarvis::registerWithRole($request->except(['password_confirmation', '_token']), false, 'user')) {
            //redirec the user with a nice message $user->first_name
        }
        //something goes here
    }
}
