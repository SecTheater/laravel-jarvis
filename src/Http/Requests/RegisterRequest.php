<?php

namespace SecTheater\Jarvis\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use SecTheater\Jarvis\Http\Rules\EmailValidation;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => [
                'required', 'min:6', 'max:32', 'string', 'regex:/^[a-zA-Z0-9-_.]*$/', 'unique:users,username',
            ],
            'email'        => ['unique:users,email', new EmailValidation()],
            'first_name'   => 'required|string|min:3|max:16|alpha',
            'last_name'    => 'required|string|min:3|max:16|alpha',
            'password'     => 'required|string|confirmed|min:8|max:32',
            'location' => 'required|string|min:3|max:32',
        ];
    }
}
