<?php

namespace SecTheater\Jarvis\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use SecTheater\Jarvis\Http\Rules\EmailValidation;

class SecurityQuestionRequest extends FormRequest
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
        if (request()->path() === 'resetBySecurityQuestion/stage1') {
            $data = [
                'email'    => ['exists:users,email', new EmailValidation()],
                'location' => 'required|string|min:3|max:32',
                'dob'      => 'required|date',
            ];
        } elseif (request()->path() === 'resetBySecurityQuestion/stage2') {
            $data = [
                'sec_question' => 'required|string|in:who_is_your_favorite_doctor_or_teacher,where_are_you_from,what_is_your_hobby,what_is_your_favorite_car',
                'sec_answer'   => [
                    'required',
                    'min:2',
                    'max:32',
                    'regex:/^[a-zA-Z0-9 ]*$/',
                    'string',
                ],
            ];
        } else {
            $data = [
                'password' => 'required|min:8|max:32|confirmed',
            ];
        }

        return $data;
    }
}
