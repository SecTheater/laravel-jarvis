<?php

namespace SecTheater\Jarvis\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
        return
        [
            'old_password' => 'min:8|max:32|string|required',
            'password'     => 'required|min:8|max:32|string|confirmed',
        ];
    }
}
