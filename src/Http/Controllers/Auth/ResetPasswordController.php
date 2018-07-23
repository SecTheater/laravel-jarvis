<?php

namespace SecTheater\Jarvis\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use ReminderRepository as Reminder;
use SecTheater\Jarvis\Http\Requests\ResetPasswordRequest;
use UserRepository;

class ResetPasswordController extends Controller
{
    public function getPasswordResetThroughEmail($email, $token)
    {
        $user = UserRepository::findBy(['email' => $email])->first();
        if ($user) {
            if (Reminder::hasToken($user)->token === $token) {
                \Session::put('user', $user);
                \Session::put('token', $token);

                return view('auth.passwords.reset-password');
            } else {
                return redirect()->route('login')->with('error', 'Invalid Token');
            }
        } else {
            return redirect()->route('login')->with('error', 'Email Does not exist');
        }
    }

    public function postPasswordResetThroughEmail(ResetPasswordRequest $request)
    {
        if (Reminder::complete(\Session::get('user'))) {
            \UserRepository::update(\Session::get('user'), [
                    'password' => bcrypt(request('password')),
                ]);
            \Session::flush();

            return redirect()->route('login')->with('success', 'Password Has been changed successfully');
        } else {
            return redirect()->route('login')->with('error', 'Please Try again later');
        }
    }
}
