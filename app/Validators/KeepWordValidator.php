<?php

namespace App\Validators;

/**
 * Class IdNumberValidator.
 */
class KeepWordValidator
{
    public function validate($attribute, $value, $parameters, $validator)
    {
        return !in_array($value, config('filter.words'));
    }
}