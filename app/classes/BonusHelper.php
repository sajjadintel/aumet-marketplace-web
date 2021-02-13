<?php

class BonusHelper {

    /**
     * calculateBonusQuantity
     *
     * @param \Base $f3 f3 instance
     * @param BaseModel $dbConnection db connection instance
     * @param string $language user language
     * @param int $entityProductId product id
     * @param int $quantity quantity
     * @param boolean $isTotalQuantity is total quantity
     *
     * @return stdClass bonusDetail
     */
    public static function calculateBonusQuantity($f3, $dbConnection, $language, $entityProductId, $quantity, $isTotalQuantity = false): stdClass
    {
        $dbEntityProduct = new BaseModel($dbConnection, "vwEntityProductSell");
        $dbEntityProduct->getWhere("id=$entityProductId");
        $productId = $dbEntityProduct->productId;
        $sellerId = $dbEntityProduct->entityId;

        $maxOrder = min($dbEntityProduct->stock, $dbEntityProduct->maximumOrderQuantity);

        if (!$dbEntityProduct->maximumOrderQuantity)
            $maxOrder = $dbEntityProduct->stock;
        if (!$dbEntityProduct->stock)
            $maxOrder = 0;

        // Get all related bonuses
        $mapBonusIdRelationGroup = [];
        $mapSellerIdRelationGroupId = [];
        $dbBonus = new BaseModel($dbConnection, "vwEntityProductSellBonusDetail");
        $dbBonus->bonusTypeName = "bonusTypeName_" . $language;
        $arrBonus = $dbBonus->getWhere("entityProductId = $productId AND isActive = 1");
        $arrBonusId = [];
        foreach ($arrBonus as $bonus) {
            array_push($arrBonusId, $bonus['id']);
        }

        // Get special bonuses
        if (count($arrBonusId) > 0) {
            $dbBonusRelationGroup = new BaseModel($dbConnection, "entityProductSellBonusDetailRelationGroup");
            $arrBonusRelationGroup = $dbBonusRelationGroup->getWhere("bonusId IN (" . implode(",", $arrBonusId) . ")");

            foreach ($arrBonusRelationGroup as $bonusRelationGroup) {
                $bonusId = $bonusRelationGroup['bonusId'];
                $arrRelationGroup = [];
                if (array_key_exists($bonusId, $mapBonusIdRelationGroup)) {
                    $arrRelationGroup = $mapBonusIdRelationGroup[$bonusId];
                }

                array_push($arrRelationGroup, $bonusRelationGroup['relationGroupId']);
                $mapBonusIdRelationGroup[$bonusId] = $arrRelationGroup;
            }
        }

        $arrEntityId = Helper::idListFromArray($f3->get('SESSION.arrEntities'));
        $dbEntityRelation = new BaseModel($dbConnection, "entityRelation");
        $arrEntityRelation = $dbEntityRelation->getWhere("entityBuyerId IN ($arrEntityId)");
        foreach ($arrEntityRelation as $entityRelation) {
            $mapSellerIdRelationGroupId[$entityRelation['entitySellerId']] = $entityRelation['relationGroupId'];
        }

        $quantityFree = 0;
        $activeBonus = new stdClass();
        $activeBonus->totalBonus = 0;
        foreach ($arrBonus as $bonus) {
            $bonusId = $bonus['id'];

            // Check if bonus available for buyer
            $valid = false;
            if (array_key_exists($bonusId, $mapBonusIdRelationGroup)) {
                $arrRelationGroup = $mapBonusIdRelationGroup[$bonusId];
                if (array_key_exists($sellerId, $mapSellerIdRelationGroupId)) {
                    $relationGroupId = $mapSellerIdRelationGroupId[$sellerId];
                    if (in_array($relationGroupId, $arrRelationGroup)) {
                        $valid = true;
                    }
                }
            } else {
                $valid = true;
            }

            if (!$valid) {
                continue;
            }

            $bonusType = $bonus['bonusTypeName'];
            $bonusTypeId = $bonus['bonusTypeId'];
            $bonusMinOrder = $bonus['minOrder'];
            $bonusBonus = $bonus['bonus'];

            // Check if bonus is possible
            $totalOrder = 0;
            if ($bonusTypeId == Constants::BONUS_TYPE_FIXED || $bonusTypeId == Constants::BONUS_TYPE_DYNAMIC) {
                $totalOrder = $bonusMinOrder + $bonusBonus;
            } else if ($bonusTypeId == Constants::BONUS_TYPE_PERCENTAGE) {
                $totalOrder = $bonusMinOrder + floor($bonusBonus * $bonusMinOrder / 100);
            }
            if ($totalOrder > $maxOrder) {
                continue;
            }
            // if it's total quantity total (max bonus value) should be less than quantity
            if ($isTotalQuantity) {
                if ($totalOrder > $quantity) {
                    continue;
                }
            }


            $totalBonus = 0;
            if ($quantity >= $bonusMinOrder) {
                if ($bonusTypeId == Constants::BONUS_TYPE_FIXED) {
                    $totalBonus = $bonusBonus;
                } else if ($bonusTypeId == Constants::BONUS_TYPE_DYNAMIC) {
                    $totalBonus = floor($quantity / $bonusMinOrder) * $bonusBonus;
                } else if ($bonusTypeId == Constants::BONUS_TYPE_PERCENTAGE) {
                    $totalBonus = floor($quantity * $bonusBonus / 100);
                }
            }

            if ($bonusTypeId == Constants::BONUS_TYPE_PERCENTAGE) {
                $bonusBonus .= "%";
            }

            if ($totalBonus > $activeBonus->totalBonus) {
                $activeBonus->bonusType = $bonusType;
                $activeBonus->minQty = $bonusMinOrder;
                $activeBonus->bonuses = $bonusBonus;
                $activeBonus->totalBonus = $totalBonus;
                $quantityFree = $totalBonus;
            }
        }

        $bonusDetail = new stdClass();
        $bonusDetail->quantityFree = $quantityFree;
        $bonusDetail->quantity = $quantity;
        $bonusDetail->total = $quantity + $quantityFree;
        $bonusDetail->maxOrder = $maxOrder;
        $bonusDetail->activeBonus = $activeBonus;
        $bonusDetail->arrBonus = $arrBonus;
        $bonusDetail->dbEntityProduct = $dbEntityProduct;

        // if it's total quantity change max order with right value and consider bonus
        if ($isTotalQuantity) {
            $bonusDetail->maxOrder = $quantity - $quantityFree;
        }

        return $bonusDetail;
    }

}
