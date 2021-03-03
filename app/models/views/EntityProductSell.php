<?php


class EntityProductSell extends BaseModel
{
    protected $table_name = 'vwEntityProductSell';
    private $entityProductSellBonusDetail;

    public function buildDataTableQuery($datatableQuery, $query)
    {
        if (!empty($datatableQuery['startDate'] && !empty($datatableQuery['endDate']))) {
            $query .= " AND stockUpdateDateTime BETWEEN '{$datatableQuery['startDate']}' AND '{$datatableQuery['endDate']}'";
        }

        $productName = $datatableQuery['productName'];
        if (isset($productName) && is_array($productName)) {
            $query .= " AND (";
            foreach ($productName as $key => $value) {
                if ($key !== 0) {
                    $query .= " OR ";
                }
                $query .= "productName_en LIKE '%{$value}%' OR productName_ar LIKE '%{$value}%' OR productName_fr LIKE '%{$value}%'";
            }
            $query .= ")";
        }

        $scientificName = $datatableQuery['scientificName'];
        if (isset($scientificName) && is_array($scientificName)) {
            $query .= " AND (";
            foreach ($scientificName as $key => $value) {
                if ($key !== 0) {
                    $query .= " OR ";
                }
                $query .= "scientificName LIKE '%{$value}%'";
            }
            $query .= ")";
        }

        $stockOption = $datatableQuery['stockOption'];
        if (isset($stockOption) && $stockOption == 1) {
            $query .= " AND stockStatusId = 1 ";
        }

        $categoryId = $datatableQuery['categoryId'];
        if (isset($categoryId) && is_array($categoryId)) {
            $query .= " AND ( categoryId in (" . implode(",", $categoryId) . ") OR subCategoryId in (" . implode(",", $categoryId) . ") )";
        }

        return $query;
    }

    /**
     * Retrieve related vwEntityProductSell based on entityProductId column
     * @param $entityProductSellArray
     */
    public function setEntityProductSellBonusDetail($entityProductSellArray)
    {
        $productIds = implode(',', array_column($entityProductSellArray, 'id'));
        $this->entityProductSellBonusDetail = (new EntityProductSellBonusDetail)->findWhere(
            "entityProductId IN ({$productIds}) AND isActive = 1"
        );

        return $this->entityProductSellBonusDetail;
    }

    public function mapBonusWithProducts($entityProductSellArray)
    {
        $this->entityProductSellBonusDetail = $this->entityProductSellBonusDetail ?? $this->setEntityProductSellBonusDetail($entityProductSellArray);
        $mapProductIdBonuses = [];

        foreach ($this->entityProductSellBonusDetail as $bonus) {
            $productId = $bonus['entityProductId'];
            $allBonuses = [];
            if (array_key_exists($productId, $mapProductIdBonuses)) {
                $allBonuses = $mapProductIdBonuses[$productId];
            }
            array_push($allBonuses, $bonus);
            $mapProductIdBonuses[$productId] = $allBonuses;
        }

        return $mapProductIdBonuses;
    }

    public function mapWithCartDetails($entityProductSell, $user)
    {
        $cartDetails = (new CartDetail())->getByField('accountId', $user->accountId);
        for ($i = 0; $i < count($entityProductSell); $i++) {
            $entityProductSell[$i]['cart'] = 0;
            if (is_array($cartDetails) || is_object($cartDetails)) {
                foreach ($cartDetails as $objCartItem) {
                    if ($objCartItem['entityProductId'] == $entityProductSell[$i]['id']) {
                        $entityProductSell[$i]['cartDetailId'] += $objCartItem['id'];
                        $entityProductSell[$i]['quantity'] = $objCartItem['quantity'];
                        $entityProductSell[$i]['cart'] += $objCartItem['quantity'];
                        $entityProductSell[$i]['cart'] += $objCartItem['quantityFree'];
                        break;
                    }
                }
            }
        }

        return $entityProductSell;
    }

    /**
     * @param array $data
     * @return array
     */
    public function getRelatedBonuses($data, $user)
    {
        $f3 = Base::instance();
        $arrProductId = array_column($data, 'id');
        $mapProductIdBonus = [];
        $mapBonusIdRelationGroup = [];
        $mapSellerIdRelationGroupId = [];
        if (count($arrProductId) > 0) {
            $dbBonus = new BaseModel($this->db, "vwEntityProductSellBonusDetail");
            $dbBonus->bonusTypeName = "bonusTypeName_" . $user->language;
            $arrBonus = $dbBonus->getWhere("entityProductId IN (" . implode(",", $arrProductId) . ") AND isActive = 1");
            $arrBonusId = [];
            foreach ($arrBonus as $bonus) {
                array_push($arrBonusId, $bonus['id']);
            }

            // Get special bonuses
            if (count($arrBonusId) > 0) {
                $arrBonusRelationGroup = (new EntityProductSellBonusDetailRelationGroup)->getWhere("bonusId IN (" . implode(",", $arrBonusId) . ")");

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
            $dbEntityRelation = new BaseModel($this->db, "entityRelation");
            $arrEntityRelation = $dbEntityRelation->getWhere("entityBuyerId IN ($arrEntityId)");
            foreach ($arrEntityRelation as $entityRelation) {
                $mapSellerIdRelationGroupId[$entityRelation['entitySellerId']] = $entityRelation['relationGroupId'];
            }

            foreach ($arrBonus as $bonus) {
                $productId = $bonus['entityProductId'];
                $arrProductBonus = [];
                if (array_key_exists($productId, $mapProductIdBonus)) {
                    $arrProductBonus = $mapProductIdBonus[$productId];
                }
                array_push($arrProductBonus, $bonus);
                $mapProductIdBonus[$productId] = $arrProductBonus;
            }
        }

        for ($i = 0; $i < sizeof($data); $i++) {
            $sellerId = $data[$i]['entityId'];
            $productId = $data[$i]['id'];

            $arrProductBonus = [];
            $activeBonus = new stdClass();
            $activeBonus->totalBonus = 0;
            if (array_key_exists($productId, $mapProductIdBonus)) {

                $arrBonus = $mapProductIdBonus[$productId];
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
                    $productDetail = $data[$i];
                    $availableQuantity = min($productDetail['stock'], $productDetail['maximumOrderQuantity']);

                    if (!$productDetail['maximumOrderQuantity'])
                        $availableQuantity = $productDetail['stock'];
                    if (!$productDetail['stock'])
                        $availableQuantity = 0;

                    $totalOrder = 0;
                    if ($bonusTypeId == Constants::BONUS_TYPE_FIXED || $bonusTypeId == Constants::BONUS_TYPE_DYNAMIC) {
                        $totalOrder = $bonusMinOrder + $bonusBonus;
                    } else if ($bonusTypeId == Constants::BONUS_TYPE_PERCENTAGE) {
                        $totalOrder = $bonusMinOrder + floor($bonusBonus * $bonusMinOrder / 100);
                    }
                    if ($totalOrder > $availableQuantity) {
                        continue;
                    }

                    $totalBonus = 0;
                    if ($productDetail['quantity'] >= $bonusMinOrder) {
                        if ($bonusTypeId == Constants::BONUS_TYPE_FIXED) {
                            $totalBonus = $bonusBonus;
                        } else if ($bonusTypeId == Constants::BONUS_TYPE_DYNAMIC) {
                            $totalBonus = floor($productDetail['quantity'] / $bonusMinOrder) * $bonusBonus;
                        } else if ($bonusTypeId == Constants::BONUS_TYPE_PERCENTAGE) {
                            $totalBonus = floor($productDetail['quantity'] * $bonusBonus / 100);
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
                    }

                    $found = false;
                    for ($j = 0; $j < count($arrProductBonus); $j++) {
                        $productBonus = $arrProductBonus[$j];
                        if ($productBonus->bonusType == $bonusType) {
                            $arrMinQty = $productBonus->arrMinQty;
                            array_push($arrMinQty, $bonusMinOrder);
                            $productBonus->arrMinQty = $arrMinQty;

                            $arrBonuses = $productBonus->arrBonuses;
                            array_push($arrBonuses, $bonusBonus);
                            $productBonus->arrBonuses = $arrBonuses;

                            $arrProductBonus[$j] = $productBonus;
                            $found = true;
                            break;
                        }
                    }

                    if (!$found) {
                        $productBonus = new stdClass();
                        $productBonus->bonusType = $bonusType;
                        $productBonus->arrMinQty = [$bonusMinOrder];
                        $productBonus->arrBonuses = [$bonusBonus];
                        array_push($arrProductBonus, $productBonus);
                    }
                }
            }

            $data[$i]['arrBonus'] = htmlspecialchars(json_encode($arrProductBonus), ENT_QUOTES, 'UTF-8');
            $data[$i]['activeBonus'] = htmlspecialchars(json_encode($activeBonus), ENT_QUOTES, 'UTF-8');
        }

        return $data;
    }

    public function getFilterQuery($queryArray)
    {
        $arrEntityId = Helper::idListFromArray(Base::instance()->get('SESSION.arrEntities'));
        $query = "entityId IN ($arrEntityId)";

        if (is_array($queryArray)) {
            $productName = $queryArray['productName'];
            if (isset($productName) && is_array($productName)) {
                $query .= " AND (";
                foreach ($productName as $key => $value) {
                    if ($key !== 0) {
                        $query .= " OR ";
                    }
                    $query .= "productName_en LIKE '%{$value}%' OR productName_ar LIKE '%{$value}%' OR productName_fr LIKE '%{$value}%'";
                }
                $query .= ")";
            }

            $scientificName = $queryArray['scientificName'];
            if (isset($scientificName) && is_array($scientificName)) {
                $query .= " AND (";
                foreach ($scientificName as $key => $value) {
                    if ($key !== 0) {
                        $query .= " OR ";
                    }
                    $query .= "scientificName LIKE '%{$value}%'";
                }
                $query .= ")";
            }

            $stockOption = $queryArray['stockOption'];
            if (isset($stockOption) && $stockOption == 1) {
                $query .= " AND stockStatusId = 1 ";
            }

            $categoryId = $queryArray['categoryId'];
            if (isset($categoryId) && is_array($categoryId)) {
                $query .= " AND ( categoryId in (" . implode(",", $categoryId) . ") OR subCategoryId in (" . implode(",", $categoryId) . ") )";
            }
        }

        $query .= " AND statusId = 1";
        return $query;
    }
}