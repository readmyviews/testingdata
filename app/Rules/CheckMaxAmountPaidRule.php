<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckMaxAmountPaidRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (request()->input('max_amount_paid') < $value) {
            return false;
        } elseif ($value == 0) {
            return false;
        } else if (request()->input('max_amount_paid') == $value) {
            return true;
        } else {
            return true;    
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Please enter a value  greater than 0 and less than or equal to '.request()->input('max_amount_paid'). '.';
    }
}
