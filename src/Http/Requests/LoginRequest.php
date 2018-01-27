<?php

namespace SecTheater\Jarvis\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use SecTheater\Jarvis\Http\Rules\EmailOrUsernameValidation;

class LoginRequest extends FormRequest {
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize() {
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules() {
		return [
			'email'    => new EmailOrUsernameValidation(request('email')),
			'password' => 'required|string|min:8|max:32',
			'remember' => 'in:on,null'
		];
	}
}
