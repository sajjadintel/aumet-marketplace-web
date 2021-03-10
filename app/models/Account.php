<?php

class Account extends BaseModel
{
    use Validate;

    protected $table_name = 'account';

    public function getRules()
    {
        return [
            'entityId' => 'required|exists,id,entity',
            'number' => 'required|numeric',
            'statusId' => 'required|numeric',
        ];
    }

    public function create($data)
    {
        $validation = $this->check($data);
        if ($validation !== true) {
            return $validation;
        }

        $this->entityId = $data['entityId'];
        $this->number = $data['number'];
        $this->statusId = $data['statusId'];
        $this->save();

        return $this;
    }
}