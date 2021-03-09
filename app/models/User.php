<?php


class User extends BaseModel
{
    use Validate;


    public function getRules()
    {
        return [
            'email' => 'required|unique,email,user',
            'mobile' => 'required|unique,mobile,user',

        ];
    }
}