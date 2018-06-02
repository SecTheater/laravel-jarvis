<?php

namespace SecTheater\Jarvis\Http\Rules;

use Illuminate\Contracts\Validation\Rule;

class EmailValidation implements Rule
{
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
        $data = request()->validate([
                $attribute => 'required|email',
            ]);
        if (is_array($data)) {
            $record = 'MX';
            list($user, $domain) = explode('@', $data[$attribute]);

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
        return 'This Email does not exist';
    }
}
