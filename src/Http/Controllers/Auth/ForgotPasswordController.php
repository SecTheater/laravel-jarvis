<?php
namespace SecTheater\Jarvis\Http\Controllers\Auth;

use ActivationRepository;

use ReminderRepository;
use SecTheater\Jarvis\Http\Requests\ForgotPasswordRequest;
use UserRepository;
use \App\Http\Controllers\Controller;

class ForgotPasswordController extends Controller {
	function getForgotPassword() {
		return view('auth.passwords.forgot-password');
	}
	function postForgotPassword(ForgotPasswordRequest $request) {
		$user = UserRepository::whereEmailOrUsername(request('email'), request('email'))->first();
		if (config('jarvis.activation.register') && ActivationRepository::completed($user)) {
			if (count($user) === 0) {
				return redirect()->route('login')->with('success', 'Reset Code Has been sent to your email');
			}
			$reminder = ReminderRepository::tokenExists($user)?:ReminderRepository::generateToken($user);
			//			Mail::to($user)->send(new ResetPassword($user, $reminder));
			return redirect()->route('login')->with('success', 'Reset Code Has Been sent to your email');
		} elseif (config('jarvis.activation.register') === false) {
			if (count($user) === 0) {
				return redirect()->route('login')->with('success', 'Reset Code Has been sent to your email');
			}
			$reminder = ReminderRepository::tokenExists($user)?:ReminderRepository::generateToken($user);
			//			Mail::to($user)->send(new ResetPassword($user, $reminder));
			return redirect()->route('login')->with('success', 'Reset Code Has Been sent to your email');}
	}

}