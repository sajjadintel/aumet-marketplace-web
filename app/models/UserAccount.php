<?php

class UserAccount extends BaseModel
{
    use Validate;

    protected $table_name = 'userAccount';

    public function getRules()
    {
        return [
            'userId' => 'required|exists,id,user',
            'accountId' => 'required|exists,id,account',
            'statusId' => 'required',
        ];
    }

    public function create($data)
    {
        $validation = $this->check($data);
        if ($validation !== true) {
            return $validation;
        }

        $this->userId = $data['userId'];
        $this->accountId = $data['accountId'];
        $this->statusId = $data['statusId'];
        $this->save();

        return $this;
    }
}