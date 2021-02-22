<?php

class EntityRelationGroup extends BaseModel
{
    const ENTITY_TABLENAME = "entity";
    const CURRENCY_TABLENAME = "currency";
    const ENTITY_RELATION_TABLENAME = "entityRelation";
    const ENTITY_RELATION_GROUP_TABLENAME = "entityRelationGroup";
    const ENTITY_RELATION_GROUP_VIEWNAME = "vwEntityRelationGroup";

    private $language;

    public function __construct(DB\SQL $db, $language)
    {
        $this->language = $language;
        parent::__construct($db, EntityRelationGroup::ENTITY_RELATION_GROUP_TABLENAME);
    }

    public function getDetailedData($arrEntityIdStr, $orderStr, $limit, $offset)
    {
        // Get currency symbol
        $dbEntity = new BaseModel($this->db, EntityRelationGroup::ENTITY_TABLENAME);
        $dbEntity->getWhere("id IN ($arrEntityIdStr)");
        $currencyId = $dbEntity->currencyId;
        $dbCurrency = new BaseModel($this->db, EntityRelationGroup::CURRENCY_TABLENAME);
        $dbCurrency->getWhere("id=$currencyId");
        $currencySymbol = $dbCurrency->symbol;

        // Get all relation groups
        $this->name = "name_" . $this->language;
        $arrEntityRelationGroup = $this->getWhere("entityId IN ($arrEntityIdStr)");
        $mapRelationGroupIdDetailedData = [];
        foreach($arrEntityRelationGroup as $entityRelationGroup) {
            $relationGroupId = $entityRelationGroup['id'];

            $detailedData = new stdClass();
            $detailedData->id = $relationGroupId;
            $detailedData->relationGroupName = $entityRelationGroup['name'];
            $detailedData->currencySymbol = $currencySymbol;

            $detailedData->groupMembers = 0;
            $detailedData->revenue = 0;
            $detailedData->totalOrders = 0;
            $detailedData->recentOrdersWeekly = 0;
            $detailedData->recentOrdersMonthly = 0;
            $detailedData->mapEntityIdEntity = [];

            $mapRelationGroupIdDetailedData[$relationGroupId] = $detailedData;
        }
        
        // Get members by relation group
        $dbEntityRelation = new BaseModel($this->db, EntityRelationGroup::ENTITY_RELATION_TABLENAME);
        $arrEntityRelation = $dbEntityRelation->getWhere("entitySellerId IN ($arrEntityIdStr)");
        $mapRelationGroupIdArrEntityId = [];
        foreach($arrEntityRelation as $entityRelation) {
            $relationGroupId = $entityRelation['relationGroupId'];
            $entityBuyerId = $entityRelation['entityBuyerId'];
            if($relationGroupId) {
                if(array_key_exists($relationGroupId, $mapRelationGroupIdArrEntityId)) {
                    $arrEntityId = $mapRelationGroupIdArrEntityId[$relationGroupId];
                    array_push($arrEntityId, $entityBuyerId);
                    $mapRelationGroupIdArrEntityId[$relationGroupId] = $arrEntityId;
                } else {
                    $mapRelationGroupIdArrEntityId[$relationGroupId] = [$entityBuyerId];
                }
            }
        }

        // Get all details relation to each relation group
        $dbEntityRelationGroup = new BaseModel($this->db, EntityRelationGroup::ENTITY_RELATION_GROUP_VIEWNAME);
        $dbEntityRelationGroup->buyerName = "buyerName_" . $this->language;
        $arrEntityRelationGroupDetailed = $dbEntityRelationGroup->getWhere("entitySellerId IN ($arrEntityIdStr)");

        foreach($arrEntityRelationGroupDetailed as $entityRelationGroupDetailed) {
            $relationGroupId = $entityRelationGroupDetailed['relationGroupId'];
            $entityId = $entityRelationGroupDetailed['entityBuyerId'];
            $orderRelationGroupId = $entityRelationGroupDetailed['orderRelationGroupId'];
            if($orderRelationGroupId) {
                $relationGroupId = $orderRelationGroupId;
            }
            $detailedData = $mapRelationGroupIdDetailedData[$relationGroupId];

            if(!$detailedData) {
                continue;
            }
            
            if(!array_key_exists($entityId, $detailedData->mapEntityIdEntity)) {
                $entity = new stdClass();
                $entity->id = $entityId;
                $entity->name = $entityRelationGroupDetailed['buyerName'];
                $entity->revenue = 0;
                $entity->totalOrders = 0;
                $entity->recentOrdersWeekly = 0;
                $entity->recentOrdersMonthly = 0;
                $detailedData->mapEntityIdEntity[$entityId] = $entity;
            } else {
                $entity = $detailedData->mapEntityIdEntity[$entityId];
            }

            if($entityRelationGroupDetailed['orderStatusId'] == Constants::ORDER_STATUS_PAID) {
                $entity->revenue += $entityRelationGroupDetailed['orderTotal'];
                $entity->totalOrders += 1;
                
                $detailedData->revenue += $entityRelationGroupDetailed['orderTotal'];
                $detailedData->totalOrders += 1;

                $currentDatetime = $this->getCurrentDateTime();
                $orderInsertDateTime = $entityRelationGroupDetailed['orderInsertDateTime'];
                
                $dateTimeBeforeAWeek = date("Y-m-d H:i:s", strtotime($currentDatetime . " -7 DAY"));
                if(strtotime($orderInsertDateTime) >= $dateTimeBeforeAWeek) {
                    $detailedData->recentOrdersWeekly += 1;
                    $entity->recentOrdersWeekly += 1;
                }

                $dateTimeBeforeAMonth = date("Y-m-d H:i:s", strtotime($currentDatetime . " -1 MONTH"));
                if(strtotime($orderInsertDateTime) >= $dateTimeBeforeAMonth) {
                    $detailedData->recentOrdersMonthly += 1;
                    $entity->recentOrdersMonthly += 1;
                }
            }
            
            $detailedData->mapEntityIdEntity[$entityId] = $entity;
            $mapRelationGroupIdDetailedData[$relationGroupId] = $detailedData;
        }

        // Format data
        $arrDetailedData = [];
        foreach($mapRelationGroupIdDetailedData as $relationGroupId => $detailedData) {
            $arrEntity = [];
            $groupMembers = 0;
            foreach($detailedData->mapEntityIdEntity as $entityId => $entity) {
                if(array_key_exists($relationGroupId, $mapRelationGroupIdArrEntityId)) {
                    $arrEntityId = $mapRelationGroupIdArrEntityId[$relationGroupId];
                    if(in_array($entity->id, $arrEntityId)) {
                        $entity->isInGroup = 1;
                        $groupMembers += 1;
                    }
                }
                array_push($arrEntity, $entity);
            }
            unset($detailedData->mapEntityIdEntity);
            $detailedData->groupMembers = $groupMembers;
            $detailedData->arrEntity = $arrEntity;

            array_push($arrDetailedData, $detailedData);
        }

        // Order data
        $allParts = explode(" ", $orderStr);
        $field = $allParts[0];
        $order = $allParts[1];
        for ($i = 0; $i < count($arrDetailedData); $i++) {
            for ($j = $i + 1; $j < count($arrDetailedData); $j++) {
                $iVal = $arrDetailedData[$i]->$field;
                $jVal = $arrDetailedData[$j]->$field;
                
                if($order == "asc") {
                    $switch = $iVal > $jVal;
                } else {
                    $switch = $iVal < $jVal;
                }

                if ($switch) {
                    $temp = $arrDetailedData[$i];
                    $arrDetailedData[$i] = $arrDetailedData[$j];
                    $arrDetailedData[$j] = $temp;
                }
            }
        }

        // Limit data
        $result = [];
        if($limit) {
            for($i = 0; $i < count($arrDetailedData); $i++) {
                if($offset && $offset > $i) {
                    continue;
                }

                $detailedData = $arrDetailedData[$i];
                if(count($result) >= $limit) {
                    break;
                }
                array_push($result, $detailedData);
            }
        } else {
            $result = $arrDetailedData;
        }

        return $result;
    }
}
