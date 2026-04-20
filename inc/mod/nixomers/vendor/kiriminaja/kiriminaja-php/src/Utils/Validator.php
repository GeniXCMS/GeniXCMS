<?php

namespace KiriminAja\Utils;

class Validator
{
    /**
     * @param array $inputs
     * @param array $rules
     * @param array $messages
     * @return \Rakit\Validation\Validation
     */
    public static function make(array $inputs, array $rules, array $messages = []): \Rakit\Validation\Validation
    {
        return (new \Rakit\Validation\Validator())->make($inputs, $rules, $messages);
    }

    /**
     * @param array $inputs
     * @param array $rules
     * @param array $messages
     * @return \Rakit\Validation\Validation
     */
    public static function validate(array $inputs, array $rules, array $messages = []): \Rakit\Validation\Validation
    {
        return (new \Rakit\Validation\Validator())->validate($inputs, $rules, $messages);
    }
}
