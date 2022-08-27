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

    public static function findByIdAndEntityId($id, $entityId)
    {
        $model = new self;
        $model = $model->findone(['id = ?', $id]);
        if ($model === false) {
            $model = new self;
            $model->hasErrors = true;
            $model->errors = ['id' => Base::instance()->get('customer_not_found')];
            return $model;
        }

        if ($model->entitySellerId !== $entityId) {
            $model->hasErrors = true;
            $model->errors = ['entitySellerId' => Base::instance()->get('unauthorized_to_edit_customer')];
            return $model;
        }

        return $model;
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
