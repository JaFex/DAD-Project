<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use App\Meal;

class MealIsActive implements Rule
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
        $meal = Meal::findOrFail($value);
        return $meal->state == "active";
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The meal need to be active to add new orders.';
    }
}
