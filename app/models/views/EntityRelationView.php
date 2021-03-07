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

    public function withIdentifiers($entityRelations)
    {
        $entityRelationIds = implode(',', array_column($entityRelations, 'id'));
        $customerIdentifiers = (new EntityRelationIdentifier)->findWhere("entityRelationId in ({$entityRelationIds})");

        $entityRelations = array_map(function($element) use ($customerIdentifiers) {
            $customerIdentifier = null;
            foreach ($customerIdentifiers as $key => $id) {
                if ($id['entityRelationId'] == $element['id']){
                    $customerIdentifier = $id['identifier'];
                    unset($customerIdentifiers[$key]);
                }
            }
            return array_merge($element, ['customerIdentifier' => $customerIdentifier]);
        }, $entityRelations);

        return $entityRelations;
    }
}