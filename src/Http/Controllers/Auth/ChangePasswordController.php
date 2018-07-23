<?php

namespace SecTheater\Jarvis\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use SecTheater\Jarvis\Http\Requests\ChangePasswordRequest;

class ChangePasswordController extends Controller
{
    public function getChangePassword()
    {
        return view('auth.passwords.change-password');
    }

    public function postChangePassword(ChangePasswordRequest $request)
    {
        if (jarvis()->changePassword(request('old_password'), request('password'))) {
            //redirect to somewhere with a success flash message
        }
        //redirect to somewhere with error message
    }
}
