<?php
namespace SecTheater\Jarvis\Http\Controllers\Auth;
use ActivationRepository as Activation;
use Jarvis;
use ReminderRepository as Reminder;
use SecTheater\Jarvis\Http\Requests\ResetPasswordRequest;
use SecTheater\Jarvis\Http\Requests\SecurityQuestionRequest;
use UserRepository;
use \App\Http\Controllers\Controller;

class ResetPasswordController extends Controller {
	function getPasswordResetThroughEmail($email, $token) {

		$user = UserRepository::findBy(['email' => $email])->first();
		if ($user) {
			if (Reminder::tokenExists($user)->token === $token) {
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
	function postPasswordResetThroughEmail(ResetPasswordRequest $request) {
		if (Reminder::complete(\Session::get('user'))) {
			\UserRepository::update(\Session::get('user'), [
					'password' => bcrypt(request('password'))
				]);
			\Session::flush();
			return redirect()->route('login')->with('success', 'Password Has been changed successfully');
		} else {
			return redirect()->route('login')->with('error', 'Please Try again later');
		}
	}
	function getPasswordResetThroughQuestion() {
		\Session::flash('info', 'Do not refresh while the process else you will start all over again');
		return view('auth.passwords.resetByQuestion');

	}
	function postPasswordResetThroughQuestion1(SecurityQuestionRequest $request) {
		$user = UserRepository::findBy(['email' => request('email'), 'dob' => requesT('dob'), 'location' => request('location')])->first();
		if ($user) {
			if ((config('jarvis.activation.register') && Activation::completed($user)) || config('jarvis.activation.register') === false) {
				\Session::put('user', $user);
				\Session::flash('stage 2', 'stage 2');
				\Session::flash('success', 'Stage 2 : answering the security question');
				return redirect()->back()->with('question', $user->sec_question);
			} else {
				return redirect()->back()->with('error', 'Account is not activated yet');
			}
		}
		return redirect()->back()->with('error', 'Account does not exist');

	}
	function postPasswordResetThroughQuestion2(SecurityQuestionRequest $request) {
		if (\Session::exists('user')) {
			$user = UserRepository::findBy([
					'email'        => \Session::get('user')->email,
					'sec_question' => request('sec_question'),

				])->first();
			if (\Hash::check(request('sec_answer'), $user->sec_answer)) {
				return redirect()                           ->back()->with(['success' => 'Stage 3 : submit new password', 'stage 3' => 'This is a stage 3']);
			} else {
				\Session::flush();
				return redirect()->back()->with('error', 'Invalid Data');
			}
		}
	}
	function postPasswordResetThroughQuestion3(SecurityQuestionRequest $request) {
		if (\Session::exists('user')) {
			$user = UserRepository::update(\Session::get('user'), [
					'password' => bcrypt(request('password'))
				]);
			\Session::flush();
			Jarvis::loginById($user);
			return redirect()->home()->with('success', 'Password has been changed successfully');
		}
		\Session::flush();
		return redirect()->back()->with('error', 'Invalid Data');

	}
}