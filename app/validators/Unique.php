<?php


class Unique extends Validator
{
    const RULE = 'unique';
    const MESSAGE = 'Already Exists';

    public static function validate($value, $ruleConfigs)
    {
        $ruleConfigs = [
            'field_name' => $ruleConfigs[0],
            'table_name' => $ruleConfigs[1],
        ];
        $model = new BaseModel($GLOBALS['dbConnection'], $ruleConfigs['table_name']);
        $exists = $model->findone(["{$ruleConfigs['field_name']} = ?", $value]);
        return $exists === false;
    }
}