<?php

namespace SecTheater\Jarvis\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SecTheater\Jarvis\Http\Requests\LoginRequest;

class LoginController extends Controller
{
    public function getLogin()
    {
        return view('auth.login');
    }

    public function postLogin(LoginRequest $request)
    {
        $remember = false;
        if (request('remember') === 'on') {
            $remember = true;
        }
        if (\Jarvis::login($request->except('_token'), $remember)) {
            return redirect()->home();
        }

        return redirect()->route('login');
    }

    public function logout()
    {
        \Jarvis::logout();

        return redirect()->route('login')->with('success', 'Come back again whenever you can ');
    }
}
