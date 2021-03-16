<?php

class EntityRelation extends BaseModel
{
    use Validate;

    protected $table_name = 'entityRelation';
    public $hasErrors = false;

    public function getRules()
    {
        return [
            'buyerIdentifier' => 'unique,buyerIdentifier,entityRelation',
        ];
    }

    public function saveIdentifier($identifier)
    {
        if (empty($identifier)) {
            $this->hasErrors = true;
            $this->errors = ['buyerIdentifier' => str_replace('{0}', 'identifier', $this->getDefaultErrorMessages()['required'])];
            return $this;
        }

        if ($this->check(['buyerIdentifier' => $identifier]) !== true) {
            $this->hasErrors = true;
            return $this;
        }

        $this->buyerIdentifier = $identifier;
        $this->save();

        return $this;
    }
}
