<?php
namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CustomRule implements Rule
{
    public function passes($attribute, $value)
    {
        // Check if the string contains any repeated characters more than three times consecutively
        return !preg_match('/(.)\1{3,}/', $value);
    }


    public function message()
    {
        return 'The :attribute cannot contain repeated characters more than three times consecutively.';
    }
}