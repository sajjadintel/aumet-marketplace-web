<?php

namespace App\Validators;

abstract class Validator
{
    const AVAILABLE_VALIDATORS = [
        Unique::class,
        Exists::class,
    ];

    public static abstract function validate($value, $ruleConfigs);
    public static function registerValidators()
    {
        foreach (self::AVAILABLE_VALIDATORS as $key => $validator) {
            \Validate::addValidator($validator::RULE, "{$validator}::validate", $validator::MESSAGE);
        }
    }
}