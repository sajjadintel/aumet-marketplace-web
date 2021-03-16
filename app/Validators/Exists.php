<?php

namespace App\Validators;

class Exists extends Validator
{
    const RULE = 'exists';
    const MESSAGE = 'Does not exist';

    public static function validate($value, $ruleConfigs)
    {
        $ruleConfigs = [
            'field_name' => $ruleConfigs[0],
            'table_name' => $ruleConfigs[1],
        ];
        $model = new \BaseModel($GLOBALS['dbConnection'], $ruleConfigs['table_name']);
        $exists = $model->findone(["{$ruleConfigs['field_name']} = ?", $value]);
        return $exists !== false;
    }
}