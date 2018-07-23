<?php

namespace SecTheater\Jarvis\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use SecTheater\Jarvis\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    public function getLogin()
    {
        return view('auth.login');
    }

    public function postLogin(LoginRequest $request)
    {
        if (jarvis()->login($request->validated(), $remember)) {
            return redirect()->home();
        }

        return redirect()->route('login');
    }

    public function logout()
    {
        jarvis()->logout();

        return redirect()->route('login')->with('success', 'Come back again whenever you can ');
    }
}
