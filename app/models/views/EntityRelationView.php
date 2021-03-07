<?php

class EntityRelationView extends BaseModel
{
    protected $table_name = 'vwEntityRelation';

    public function loadCustomerIdentifiers($userId, $entityRelationIds)
    {
        return (new EntityRelationIdentifier)->find(['userId = ? AND entityRelationId IN (?)', $userId, implode(',', $entityRelationIds)]);
    }

    public function addCustomerIdentifiersToEntityRelations($entityRelations, $userId)
    {
        $entityRelationIds = array_column($entityRelations, 'id');
        $customers = $this->loadCustomerIdentifiers($userId, $entityRelationIds);
        foreach ($entityRelations as $entityRelation) {
            $entityRelation['id'];
        }
    }
}