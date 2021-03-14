<?php

class Account extends BaseModel
{
    use Validate;

    public $hasErrors = false;
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
        if ($this->check($data) !== true) {
            $this->hasErrors = true;
            return $this;
        }

        $this->entityId = $data['entityId'];
        $this->number = $data['number'];
        $this->statusId = $data['statusId'];
        $this->save();

        return $this;
    }
}