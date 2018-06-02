<?php

namespace SecTheater\Jarvis\Http\Controllers\Auth;

use ActivationRepository;
use App\Http\Controllers\Controller;
use ReminderRepository;
use SecTheater\Jarvis\Http\Requests\ForgotPasswordRequest;
use UserRepository;

class ForgotPasswordController extends Controller
{
    public function getForgotPassword()
    {
        return view('auth.passwords.forgot-password');
    }

    public function postForgotPassword(ForgotPasswordRequest $request)
    {
        $user = UserRepository::whereEmailOrUsername(request('email'), request('email'))->first();
        if (config('jarvis.activations.register') && ActivationRepository::completed($user)) {
            if (count($user) === 0) {
                return redirect()->route('login')->with('success', 'Reset Code Has been sent to your email');
            }
            $reminder = ReminderRepository::tokenExists($user) ?: ReminderRepository::generateToken($user);
            //			Mail::to($user)->send(new ResetPassword($user, $reminder));
            return redirect()->route('login')->with('success', 'Reset Code Has Been sent to your email');
        } elseif (config('jarvis.activations.register') === false) {
            if (count($user) === 0) {
                return redirect()->route('login')->with('success', 'Reset Code Has been sent to your email');
            }
            $reminder = ReminderRepository::tokenExists($user) ?: ReminderRepository::generateToken($user);
            //			Mail::to($user)->send(new ResetPassword($user, $reminder));
            return redirect()->route('login')->with('success', 'Reset Code Has Been sent to your email');
        }
    }
}
