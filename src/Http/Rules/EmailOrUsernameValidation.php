<?php

namespace SecTheater\Jarvis\Http\Rules;

use Illuminate\Contracts\Validation\Rule;

class EmailOrUsernameValidation implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        request()->validate([
                $attribute => 'required',
            ]);
        if (preg_match('/^[a-zA-Z0-9-_.]*$/', $value)) {
            request()->validate([
                    $attribute => 'min:6', 'max:32', 'string', 'regex:/^[a-zA-Z0-9-_.]*$/',
                ]);

            return true;
        } elseif (filter_var($value, FILTER_VALIDATE_EMAIL, FILTER_SANITIZE_EMAIL)) {
            $record = 'MX';
            list($user, $domain) = explode('@', $value);

            return checkdnsrr($domain, $record);
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Invalid Username Or Email Syntax';
    }
}
