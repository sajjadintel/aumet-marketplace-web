<?php

class ProductsController extends Controller
{

    function getEntityProduct()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", $this->f3->get('SERVER.REQUEST_URI'));
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $entityId = $this->f3->get('PARAMS.entityId');
            $id = $this->f3->get('PARAMS.productId');

            $roleId = $this->f3->get('SESSION.objUser')->roleId;
            $this->f3->set('objUser', $this->objUser);

            if ($entityId == 0)
                $query = "id=$id";
            else
                $query = "entityId=$entityId and id=$id";

            $dbEntityProduct = new BaseModel($this->db, "vwEntityProductSell");
            $dbEntityProduct->productName = "productName_" . $this->objUser->language;
            $dbEntityProduct->entityName = "entityName_" . $this->objUser->language;
            $dbEntityProduct->madeInCountryName = "madeInCountryName_" . $this->objUser->language;
            $dbEntityProduct->subtitle = "subtitle_" . $this->objUser->language;
            $dbEntityProduct->description = "description_" . $this->objUser->language;
            $dbEntityProduct->getWhere($query);
            $productId = $dbEntityProduct['id'];

            $dbCartDetail = new BaseModel($this->db, "cartDetail");
            $userId = $this->objUser->id;
            $dbCartDetail->getWhere("entityProductId=$id and userId=$userId");

            $availableQuantity = min($dbEntityProduct['stock'], $dbEntityProduct['maximumOrderQuantity']);

            if (!$dbEntityProduct['maximumOrderQuantity'])
                $availableQuantity = $dbEntityProduct['stock'];
            if (!$dbEntityProduct['stock'])
                $availableQuantity = 0;

            $found = false;
            if (array_key_exists((string)$dbEntityProduct->entityId, $this->f3->get('SESSION.arrEntities'))) {
                $found = true;
            }

            if ($dbEntityProduct['statusId'] === 0 || (!$found && $this->objUser->menuId == Constants::MENU_DISTRIBUTOR)) {
                $this->webResponse->errorCode = Constants::STATUS_CODE_REDIRECT_TO_WEB;
                echo $this->webResponse->jsonResponse();
                return;
            }

            // Get all related bonuses
            $mapBonusIdRelationGroup = [];
            $mapSellerIdRelationGroupId = [];
            $dbBonus = new BaseModel($this->db, "vwEntityProductSellBonusDetail");
            $dbBonus->bonusTypeName = "bonusTypeName_" . $this->objUser->language;
            $arrBonus = $dbBonus->getWhere("entityProductId = $productId AND isActive = 1");
            $arrBonusId = [];
            foreach ($arrBonus as $bonus) {
                array_push($arrBonusId, $bonus['id']);
            }

            // Get special bonuses
            if (count($arrBonusId) > 0) {
                $dbBonusRelationGroup = new BaseModel($this->db, "entityProductSellBonusDetailRelationGroup");
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

            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $dbEntityRelation = new BaseModel($this->db, "entityRelation");
            $arrEntityRelation = $dbEntityRelation->getWhere("entityBuyerId IN ($arrEntityId)");
            foreach ($arrEntityRelation as $entityRelation) {
                $mapSellerIdRelationGroupId[$entityRelation['entitySellerId']] = $entityRelation['relationGroupId'];
            }

            $arrProductBonus = [];
            $activeBonus = new stdClass();
            $activeBonus->totalBonus = 0;
            foreach ($arrBonus as $bonus) {
                $bonusId = $bonus['id'];

                // Check if bonus available for buyer
                $valid = false;
                if (array_key_exists($bonusId, $mapBonusIdRelationGroup)) {
                    $arrRelationGroup = $mapBonusIdRelationGroup[$bonusId];
                    if (array_key_exists($entityId, $mapSellerIdRelationGroupId)) {
                        $relationGroupId = $mapSellerIdRelationGroupId[$entityId];
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
                if ($totalOrder > $availableQuantity) {
                    continue;
                }

                $totalBonus = 0;
                if ($dbCartDetail['quantity'] >= $bonusMinOrder) {
                    if ($bonusTypeId == Constants::BONUS_TYPE_FIXED) {
                        $totalBonus = $bonusBonus;
                    } else if ($bonusTypeId == Constants::BONUS_TYPE_DYNAMIC) {
                        $totalBonus = floor($dbCartDetail['quantity'] / $bonusMinOrder) * $bonusBonus;
                    } else if ($bonusTypeId == Constants::BONUS_TYPE_PERCENTAGE) {
                        $totalBonus = floor($dbCartDetail['quantity'] * $bonusBonus / 100);
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

            $dbEntityProduct['arrBonus'] = $arrProductBonus;
            $dbEntityProduct['activeBonus'] = $activeBonus;

            $this->f3->set('objEntityProduct', $dbEntityProduct);
            $arrRelatedEntityProduct = [];
            if (strlen($dbEntityProduct->scientificNameId) > 0) {
                $dbEntityProductRelated = new BaseModel($this->db, "vwEntityProductSell");
                $dbEntityProductRelated->productName = "productName_" . $this->objUser->language;
                $dbEntityProductRelated->entityName = "entityName_" . $this->objUser->language;
                $where = "stockStatusId=1 AND scientificNameId =$dbEntityProduct->scientificNameId AND id != $dbEntityProduct->id";
                if (Helper::isDistributor($roleId))
                    $where .= " AND entityId=$dbEntityProduct->entityId";

                $arrRelatedEntityProduct = $dbEntityProductRelated->getWhere($where, 'id', 12);
            }
            $this->f3->set('arrRelatedEntityProduct', $arrRelatedEntityProduct);


            $dbEntityProductFromThisDistributor = new BaseModel($this->db, "vwEntityProductSell");
            $dbEntityProductFromThisDistributor->productName = "productName_" . $this->objUser->language;
            $dbEntityProductFromThisDistributor->entityName = "entityName_" . $this->objUser->language;
            $dbEntityProductFromThisDistributor = $dbEntityProductFromThisDistributor->getWhere(["stockStatusId= ? and entityId= ? and id != ?", 1, $dbEntityProduct->entityId, $dbEntityProduct->id], 'id', 12);
            $this->f3->set('arrProductFromThisDistributor', $dbEntityProductFromThisDistributor);

            if (Helper::isPharmacy($roleId)) {
                $dbEntityProductOtherOffers = new BaseModel($this->db, "vwEntityProductSell");
                $dbEntityProductOtherOffers->productName = "productName_" . $this->objUser->language;
                $dbEntityProductOtherOffers->entityName = "entityName_" . $this->objUser->language;

                $where = [
                    "( (TRIM(productName_ar) LIKE ? OR TRIM(productName_en) LIKE ? OR TRIM(productName_fr) LIKE ? ) AND id != ? )",
                    trim($dbEntityProduct->productName_ar),
                    mb_strtolower(trim($dbEntityProduct->productName_en)),
                    mb_strtolower(trim($dbEntityProduct->productName_fr)),
                    $dbEntityProduct->id
                ];

                $dbEntityProductOtherOffers = $dbEntityProductOtherOffers->findWhere($where);


                $allProductId = [];
                foreach ($dbEntityProductOtherOffers as $product) {
                    array_push($allProductId, $product['id']);
                }
                $allProductId = implode(",", $allProductId);

                $dbCartDetail = new BaseModel($this->db, "cartDetail");
                $arrCartDetail = $dbCartDetail->getByField("accountId", $this->objUser->accountId);

                if ($allProductId != null) {
                    $dbBonus = new BaseModel($this->db, "entityProductSellBonusDetail");
                    $arrBonus = $dbBonus->findWhere("entityProductId IN ($allProductId) AND isActive = 1");

                    $mapProductIdBonuses = [];

                    foreach ($arrBonus as $bonus) {
                        $productId = $bonus['entityProductId'];
                        $allBonuses = [];
                        if (array_key_exists($productId, $mapProductIdBonuses)) {
                            $allBonuses = $mapProductIdBonuses[$productId];
                        }
                        array_push($allBonuses, $bonus);
                        $mapProductIdBonuses[$productId] = $allBonuses;
                    }
                }


                for ($i = 0; $i < count($dbEntityProductOtherOffers); $i++) {

                    if ($dbEntityProductOtherOffers[$i]['bonusTypeId'] == 2) {
                        $dbEntityProductOtherOffers[$i]['bonusOptions'] = json_decode($dbEntityProductOtherOffers[$i]['bonusConfig']);
                        $dbEntityProductOtherOffers[$i]['bonusConfig'] = $dbEntityProductOtherOffers[$i]['bonusOptions'];
                        $dbEntityProductOtherOffers[$i]['bonuses'] = $mapProductIdBonuses[$dbEntityProductOtherOffers[$i]['id']];
                    }

                    $cartDetail = new BaseModel($this->db, "cartDetail");
                    $cartDetail->getWhere("userID =" . $this->objUser->id . " and entityProductId = " . $dbEntityProductOtherOffers[$i]['id'] . "");

                    $dbEntityProductOtherOffers[$i]['cart'] = (!$cartDetail->dry()) ? $cartDetail->quantity : 0;
                }

                $this->f3->set('arrProductOtherOffers', $dbEntityProductOtherOffers);
            }

            $dbProductSubimage = new BaseModel($this->db, "productSubimage");
            $arrSubimage = $dbProductSubimage->getWhere("productId=" . $dbEntityProduct->productId);
            $this->f3->set('arrSubimage', $arrSubimage);

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vModule_product_detail') . " | " . $dbEntityProduct->productName;
            $this->webResponse->data = View::instance()->render('app/products/single/entityProduct.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function postEntityProduct()
    {
        ## Read values from Datatables
        $datatable = new Datatable($_POST);

        $entityId = $this->f3->get('PARAMS.entityId');
        $id = $this->f3->get('PARAMS.productId');

        if ($entityId == 0)
            $productQuery = "id=$id";
        else
            $productQuery = "entityId=$entityId and id=$id";

        $dbEntityProduct = new BaseModel($this->db, "vwEntityProductSell");
        $dbEntityProduct->productName = "productName_" . $this->objUser->language;
        $dbEntityProduct->entityName = "entityName_" . $this->objUser->language;
        $dbEntityProduct->madeInCountryName = "madeInCountryName_" . $this->objUser->language;
        $dbEntityProduct->subtitle = "subtitle_" . $this->objUser->language;
        $dbEntityProduct->description = "description_" . $this->objUser->language;
        $dbEntityProduct->getWhere($productQuery);

        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));

        $productName_ar = trim($dbEntityProduct->productName_ar);
        $productName_en = mb_strtolower(trim($dbEntityProduct->productName_en));
        $productName_fr = mb_strtolower(trim($dbEntityProduct->productName_fr));
        $productId = $dbEntityProduct->id;
        $query = [
            "( (TRIM(productName_ar) LIKE ? OR TRIM(productName_en) LIKE ? OR TRIM(productName_fr) LIKE ? ) AND id != ? )",
            trim($dbEntityProduct->productName_ar),
            mb_strtolower(trim($dbEntityProduct->productName_en)),
            mb_strtolower(trim($dbEntityProduct->productName_fr)),
            $dbEntityProduct->id
        ];

        $dbData = new BaseModel($this->db, "vwEntityProductSell");
        $dbData->productName = "productName_" . $this->objUser->language;
        $dbData->entityName = "entityName_" . $this->objUser->language;
        $data = [];

        $totalRecords = $dbData->count($query);
        $totalFiltered = $dbData->count($query);

        $order = "";
        if (strlen($datatable->sortBy) > 0 && strlen($datatable->sortByOrder) > 0) {
            $order = $datatable->sortBy . " " . $datatable->sortByOrder;
        }

        $limit = 0;
        if ($datatable->limit) {
            $limit = $datatable->limit;
        }

        $offset = 0;
        if ($datatable->offset) {
            $offset = $datatable->offset;
        }

        $data = $dbData->findWhere($query, $order, $limit, $offset);

        $allProductId = [];
        foreach ($data as $product) {
            array_push($allProductId, $product['productId']);
        }
        $allProductId = implode(",", $allProductId);

        $dbCartDetail = new BaseModel($this->db, "cartDetail");
        $arrCartDetail = $dbCartDetail->getByField("accountId", $this->objUser->accountId);

        if ($allProductId != null) {
            $dbBonus = new BaseModel($this->db, "entityProductSellBonusDetail");
            $arrBonus = $dbBonus->findWhere("entityProductId IN ($allProductId) AND isActive = 1");

            $mapProductIdBonuses = [];

            foreach ($arrBonus as $bonus) {
                $productId = $bonus['entityProductId'];
                $allBonuses = [];
                if (array_key_exists($productId, $mapProductIdBonuses)) {
                    $allBonuses = $mapProductIdBonuses[$productId];
                }
                array_push($allBonuses, $bonus);
                $mapProductIdBonuses[$productId] = $allBonuses;
            }
        }


        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['cart'] = 0;
            if (is_array($arrCartDetail) || is_object($arrCartDetail)) {
                foreach ($arrCartDetail as $objCartItem) {
                    if ($objCartItem['entityProductId'] == $data[$i]['id']) {
                        $data[$i]['cartDetailId'] += $objCartItem['id'];
                        $data[$i]['quantity'] = $objCartItem['quantity'];
                        $data[$i]['cart'] += $objCartItem['quantity'];
                        break;
                    }
                }
            }
        }


        // Get all product ids
        $arrProductId = [];
        foreach ($data as $productItem) {
            array_push($arrProductId, $productItem['id']);
        }

        // Get all related bonuses
        $mapProductIdBonus = [];
        $mapBonusIdRelationGroup = [];
        $mapSellerIdRelationGroupId = [];
        if (count($arrProductId) > 0) {
            $dbBonus = new BaseModel($this->db, "vwEntityProductSellBonusDetail");
            $dbBonus->bonusTypeName = "bonusTypeName_" . $this->objUser->language;
            $arrBonus = $dbBonus->getWhere("entityProductId IN (" . implode(",", $arrProductId) . ") AND isActive = 1");
            $arrBonusId = [];
            foreach ($arrBonus as $bonus) {
                array_push($arrBonusId, $bonus['id']);
            }

            // Get special bonuses
            if (count($arrBonusId) > 0) {
                $dbBonusRelationGroup = new BaseModel($this->db, "entityProductSellBonusDetailRelationGroup");
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

            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
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


        ## Response
        $response = array(
            "draw" => intval($datatable->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        );

        $this->jsonResponseAPI($response);
    }

    function getDistributorCanAddProduct()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $entityId = array_keys($this->f3->get('SESSION.arrEntities'))[0];
            $results = $this->db->exec("CALL spCanAddProduct($entityId)");

            if (count($results) > 0) {
                $this->webResponse->title = 'Can\'t add product</br>Please complete your profile first!';
                $this->webResponse->data = $results;
            } else {
                $this->webResponse->title = 'Can add product';
                $this->webResponse->data = [];
            }
            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            echo $this->webResponse->jsonResponse();
        }
    }

    function getDistributorProducts()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $dbStockStatus = new BaseModel($this->db, "stockStatus");
            $dbStockStatus->name = "name_" . $this->objUser->language;
            $arrStockStatus = $dbStockStatus->findAll("id asc");
            $this->f3->set('arrStockStatus', $arrStockStatus);

            $dbScientificName = new BaseModel($this->db, "scientificName");
            $arrScientificName = $dbScientificName->findAll();

            // Find buyer currency
            $dbCurrencies = new BaseModel($this->db, "currency");
            $allCurrencies = $dbCurrencies->all();

            $mapCurrencyIdCurrency = [];
            foreach ($allCurrencies as $currency) {
                $mapCurrencyIdCurrency[$currency['id']] = $currency;
            }

            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));

            $dbEntities = new BaseModel($this->db, "entity");
            $buyerEntity = $dbEntities->getWhere("id in ($arrEntityId)")[0];

            $buyerCurrency = $mapCurrencyIdCurrency[$buyerEntity['currencyId']];
            $this->f3->set('buyerCurrency', $buyerCurrency['symbol']);

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vModule_product_title');
            $this->webResponse->data = View::instance()->render('app/products/distributor/products.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function getProductDetails()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", $this->f3->get('SERVER.REQUEST_URI'));
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $id = $this->f3->get('PARAMS.productId');

            $dbProduct = new BaseModel($this->db, "vwEntityProductSell");
            $product = $dbProduct->findWhere("id = $id")[0];
            $productId = $product['productId'];

            $dbProductIngredient = new BaseModel($this->db, "vwProductIngredient");
            $arrActiveIngredients = $dbProductIngredient->findWhere("productId = $productId");

            $dbProductSubimage = new BaseModel($this->db, "productSubimage");
            $arrSubimages = $dbProductSubimage->findWhere("productId = $productId");

            $data['product'] = $product;
            $data['activeIngredients'] = $arrActiveIngredients;
            $data['subimages'] = $arrSubimages;

            echo $this->webResponse->jsonResponseV2(1, "", "", $data);
        }
    }

    function getProductQuantityDetails()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", $this->f3->get('SERVER.REQUEST_URI'));
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $productId = $this->f3->get('PARAMS.productId');

            $dbProduct = new BaseModel($this->db, "vwEntityProductSell");
            $arrProduct = $dbProduct->findWhere("id = '$productId'");

            $dbBonus = new BaseModel($this->db, "entityProductSellBonusDetail");
            $dbBonus->bonusId = 'id';
            $arrBonus = $dbBonus->findWhere("entityProductId = '$productId' AND isActive = 1");

            $data['product'] = $arrProduct[0];
            $data['bonus'] = $arrBonus;

            echo $this->webResponse->jsonResponseV2(1, "", "", $data);
            return;
        }
    }

    function getProductStockDetails()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", $this->f3->get('SERVER.REQUEST_URI'));
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $id = $this->f3->get('PARAMS.productId');

            $dbProduct = new BaseModel($this->db, "vwEntityProductSell");
            $product = $dbProduct->findWhere("id=$id")[0];
            $entityId = $product['entityId'];
            $productId = $product['id'];

            $dbBonusType = new BaseModel($this->db, "bonusType");
            $dbBonusType->name = "name_" . $this->objUser->language;
            $arrBonusType = $dbBonusType->findAll();

            $dbEntityRelationGroup = new BaseModel($this->db, "entityRelationGroup");
            $dbEntityRelationGroup->name = "name_" . $this->objUser->language;
            $arrRelationGroup = $dbEntityRelationGroup->findWhere("entityId=$entityId");

            // Get all bonuses
            $dbEntityProductBonus = new BaseModel($this->db, "entityProductSellBonusDetail");
            $arrBonus = $dbEntityProductBonus->findWhere("isActive = 1 AND entityProductId=$productId");
            $arrBonusId = [];
            foreach ($arrBonus as $bonus) {
                array_push($arrBonusId, $bonus['id']);
            }

            // Group all bonuses' relation groups
            $arrBonusGrouped = [];
            if (count($arrBonus) > 0) {
                $dbBonusRelationGroup = new BaseModel($this->db, "entityProductSellBonusDetailRelationGroup");
                $strBonusId = implode(",", $arrBonusId);
                $arrBonusRelationGroup = $dbBonusRelationGroup->getWhere("bonusId IN ($strBonusId)");

                $mapBonusIdRelationGroupId = [];
                foreach ($arrBonusRelationGroup as $bonusRelationGroup) {
                    $bonusId = $bonusRelationGroup['bonusId'];
                    $relationGroupId = $bonusRelationGroup['relationGroupId'];
                    if (array_key_exists($bonusId, $mapBonusIdRelationGroupId)) {
                        $allRelationGroupId = $mapBonusIdRelationGroupId[$bonusId];
                        array_push($allRelationGroupId, $relationGroupId);
                        $mapBonusIdRelationGroupId[$bonusId] = $allRelationGroupId;
                    } else {
                        $mapBonusIdRelationGroupId[$bonusId] = [$relationGroupId];
                    }
                }

                foreach ($arrBonus as $bonus) {
                    $bonusId = $bonus['id'];

                    $bonusGrouped = new stdClass();
                    $bonusGrouped->bonusId = $bonusId;
                    $bonusGrouped->bonusTypeId = $bonus['bonusTypeId'];
                    $bonusGrouped->minOrder = $bonus['minOrder'];
                    $bonusGrouped->bonus = $bonus['bonus'];
                    $bonusGrouped->arrRelationGroup = $mapBonusIdRelationGroupId[$bonusId];
                    array_push($arrBonusGrouped, $bonusGrouped);
                }
            }

            $data['product'] = $product;
            $data['arrBonusType'] = $arrBonusType;
            $data['arrBonus'] = $arrBonusGrouped;
            $data['arrRelationGroup'] = $arrRelationGroup;

            echo $this->webResponse->jsonResponseV2(1, "", "", $data);
            return;
        }
    }

    function postDistributorProducts()
    {
        ## Read values from Datatables
        $datatable = new Datatable($_POST);
        $query = "1=1 ";

        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
        $query = "entityId IN ($arrEntityId)";

        if (is_array($datatable->query)) {
            $productName = $datatable->query['productName'];
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

            $scientificName = $datatable->query['scientificName'];
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

            $stockOption = $datatable->query['stockOption'];
            if (isset($stockOption) && $stockOption == 1) {
                $query .= " AND stockStatusId = 1 ";
            }

            $categoryId = $datatable->query['categoryId'];
            if (isset($categoryId) && is_array($categoryId)) {
                $query .= " AND ( categoryId in (" . implode(",", $categoryId) . ") OR subCategoryId in (" . implode(",", $categoryId) . ") )";
            }
        }

        $query .= " AND statusId = 1";

        $fullQuery = $query;

        $dbData = new BaseModel($this->db, "vwEntityProductSell");
        $data = [];

        $totalRecords = $dbData->count($fullQuery);
        $totalFiltered = $dbData->count($query);
        $data = $dbData->findWhere($query, "$datatable->sortBy $datatable->sortByOrder", $datatable->limit, $datatable->offset);

        ## Response
        $response = array(
            "draw" => intval($datatable->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        );

        $this->jsonResponseAPI($response);
    }

    function postProductImage()
    {
        $allValidExtensions = [
            "jpeg",
            "jpg",
            "png",
        ];
        $success = false;

        $ext = pathinfo(basename($_FILES["product_image"]["name"]), PATHINFO_EXTENSION);
        if (in_array($ext, $allValidExtensions)) {
            $success = true;
        }
        $path = "";

        if ($success) {
            $objResult = AumetFileUploader::upload("s3", $_FILES["product_image"], $this->generateRandomString(64));
            $path = $objResult->fileLink;
        }

        $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
        $this->webResponse->title = "Product Image Upload";
        $this->webResponse->data = $path;
        echo $this->webResponse->jsonResponse();
    }

    function postProductSubimage()
    {
        $allValidExtensions = [
            "jpeg",
            "jpg",
            "png",
        ];
        $success = false;

        $ext = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_EXTENSION);
        if (in_array($ext, $allValidExtensions)) {
            $success = true;
        }

        if ($success) {
            $objResult = AumetFileUploader::upload("s3", $_FILES["file"], $this->generateRandomString(64));
            echo $objResult->fileLink;
        }
    }

    function postDistributorProductsBestSelling()
    {
        ## Read values from Datatables
        $datatable = new Datatable($_POST);

        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
        $query = "entityId IN ($arrEntityId) AND statusId = 1 ";
        $fullQuery = $query . " AND quantityOrdered > 0";
        $dbProducts = new BaseModel($this->db, "vwEntityProductSell");

        $data = [];

        $totalRecords = $dbProducts->count($query);
        $totalFiltered = MIN($dbProducts->count($fullQuery), 5);
        $data = $dbProducts->findWhere($fullQuery, "quantityOrdered DESC", 5, 0);

        ## Response
        $response = array(
            "draw" => intval($datatable->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        );

        $this->jsonResponseAPI($response);
    }

    function postEditDistributorProduct()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/distributor/product");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $productId = $this->f3->get('POST.id');

            $dbEntityProduct = new BaseModel($this->db, "entityProductSell");
            $dbEntityProduct->getWhere("productId=$productId");

            $dbProduct = new BaseModel($this->db, "product");
            $dbProduct->getWhere("id=$productId");

            if ($dbEntityProduct->dry() || $dbProduct->dry()) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->message = $this->f3->get('vModule_product_notFound');
                echo $this->webResponse->jsonResponse();
            } else {
                $scientificNameId = $this->f3->get('POST.scientificNameId');
                $madeInCountryId = $this->f3->get('POST.madeInCountryId');
                $name_en = $this->f3->get('POST.name_en');
                $name_ar = $this->f3->get('POST.name_ar');
                // $name_fr = $this->f3->get('POST.name_fr');
                $image = $this->f3->get('POST.image');
                $subimages = $this->f3->get('POST.subimages');
                $unitPrice = $this->f3->get('POST.unitPrice');
                $maximumOrderQuantity = $this->f3->get('POST.maximumOrderQuantity');
                $subtitle_ar = $this->f3->get('POST.subtitle_ar');
                $subtitle_en = $this->f3->get('POST.subtitle_en');
                // $subtitle_fr = $this->f3->get('POST.subtitle_fr');
                $description_ar = $this->f3->get('POST.description_ar');
                $description_en = $this->f3->get('POST.description_en');
                // $description_fr = $this->f3->get('POST.description_fr');
                $unitPrice = $this->f3->get('POST.unitPrice');
                $vat = $this->f3->get('POST.vat');
                $manufacturerName = $this->f3->get('POST.manufacturerName');
                $batchNumber = $this->f3->get('POST.batchNumber');
                $itemCode = $this->f3->get('POST.itemCode');
                $categoryId = $this->f3->get('POST.categoryId');
                $subcategoryId = $this->f3->get('POST.subcategoryId');
                $activeIngredientsId = $this->f3->get('POST.activeIngredientsId');
                $expiryDate = $this->f3->get('POST.expiryDate');;
                $strength = $this->f3->get('POST.strength');

                if (
                    strlen($madeInCountryId) == 0
                    // || strlen($scientificNameId) == 0
                    || strlen($name_en) == 0 || strlen($name_ar) == 0
                    // || strlen($name_fr) == 0
                    || strlen($unitPrice) == 0 || strlen($vat) == 0
                    || strlen($categoryId) == 0 || strlen($subcategoryId) == 0
                ) {
                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->message = $this->f3->get('vModule_product_missingFields');
                    echo $this->webResponse->jsonResponse();
                    return;
                }

                if ((strlen($maximumOrderQuantity) > 0 && (!(is_numeric($maximumOrderQuantity) && (int) $maximumOrderQuantity == $maximumOrderQuantity) || $maximumOrderQuantity < 0))
                    || (!is_numeric($unitPrice) || $unitPrice <= 0)
                    || (!is_numeric($vat) || $vat < 0 || $vat > 100)
                ) {
                    $arrError = [];
                    if (strlen($maximumOrderQuantity) > 0 && (!(is_numeric($maximumOrderQuantity) && (int) $maximumOrderQuantity == $maximumOrderQuantity) || $maximumOrderQuantity < 0)) {
                        array_push($arrError, $this->f3->get('vModule_product_maximumOrderQuantityInvalid'));
                    }
                    if (!is_numeric($unitPrice) || $unitPrice <= 0) {
                        array_push($arrError, $this->f3->get('vModule_product_unitPriceInvalid'));
                    }
                    if (!is_numeric($vat) || $vat < 0 || $vat > 100) {
                        array_push($arrError, $this->f3->get('vModule_product_vatInvalid'));
                    }

                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->message = implode("<br>", $arrError);
                    echo $this->webResponse->jsonResponse();
                    return;
                }

                $dbSubcategory = new BaseModel($this->db, "subcategory");
                $dbSubcategory->getWhere("id = $subcategoryId AND categoryId = $categoryId");
                if ($dbSubcategory->dry()) {
                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->message = $this->f3->get('vModule_product_subcategoryInvalid');
                    echo $this->webResponse->jsonResponse();
                    return;
                }

                $this->checkLength($name_en, 'nameEn', 200, 4);
                $this->checkLength($name_ar, 'nameAr', 200, 4);
                // $this->checkLength($name_fr, 'nameFr', 200, 4);

                if ($description_ar) {
                    $this->checkLength($description_ar, 'descriptionAr', 5000, 4);
                }

                if ($description_en) {
                    $this->checkLength($description_en, 'descriptionEn', 5000, 4);
                }

                // if ($description_fr) {
                //     $this->checkLength($description_fr, 'descriptionFr', 5000, 4);
                // }

                if ($subtitle_ar) {
                    $this->checkLength($subtitle_ar, 'subtitleAr', 200, 4);
                }

                if ($subtitle_en) {
                    $this->checkLength($subtitle_en, 'subtitleEn', 200, 4);
                }

                // if ($subtitle_fr) {
                //     $this->checkLength($subtitle_fr, 'subtitleFr', 200, 4);
                // }

                if ($manufacturerName) {
                    $this->checkLength($manufacturerName, 'manufacturerName', 200, 4);
                }

                if ($strength) {
                    $this->checkLength($strength, 'strength', 200, 4);
                }

                if ($scientificNameId) {
                    $dbProduct->scientificNameId = $scientificNameId;
                } else {
                    $dbProduct->scientificNameId = null;
                }
                $dbProduct->madeInCountryId = $madeInCountryId;
                $dbProduct->name_en = $name_en;
                $dbProduct->name_fr = $name_en;
                // $dbProduct->name_fr = $name_fr;
                $dbProduct->name_ar = $name_ar;
                $dbProduct->image = $image;
                $dbProduct->subtitle_ar = $subtitle_ar;
                $dbProduct->subtitle_en = $subtitle_en;
                $dbProduct->subtitle_fr = $subtitle_en;
                // $dbProduct->subtitle_fr = $subtitle_fr;
                $dbProduct->description_ar = $description_ar;
                $dbProduct->description_en = $description_en;
                $dbProduct->description_fr = $description_en;
                // $dbProduct->description_fr = $description_fr;
                $dbProduct->manufacturerName = $manufacturerName;
                $dbProduct->batchNumber = $batchNumber;
                $dbProduct->itemCode = $itemCode;
                $dbProduct->categoryId = $categoryId;
                $dbProduct->subcategoryId = $subcategoryId;
                $dbProduct->expiryDate = $expiryDate;
                $dbProduct->strength = $strength;

                $dbProduct->update();

                $dbProductIngredient = new BaseModel($this->db, "productIngredient");
                $dbProductIngredient->getWhere("productId = $productId");
                while (!$dbProductIngredient->dry()) {
                    $dbProductIngredient->delete();
                    $dbProductIngredient->next();
                }

                if ($activeIngredientsId) {
                    $arrIngredientId = explode(",", $activeIngredientsId);
                    foreach ($arrIngredientId as $ingredientId) {
                        $dbProductIngredient->productId = $dbProduct->id;
                        $dbProductIngredient->ingredientId = $ingredientId;
                        $dbProductIngredient->add();
                    }
                }

                $dbProductSubimage = new BaseModel($this->db, "productSubimage");
                $dbProductSubimage->getWhere("productId = $productId");
                while (!$dbProductSubimage->dry()) {
                    $dbProductSubimage->delete();
                    $dbProductSubimage->next();
                }

                if ($subimages && count($subimages) > 0) {
                    foreach ($subimages as $subimage) {
                        $dbProductSubimage->productId = $dbProduct->id;
                        $dbProductSubimage->subimage = $subimage;
                        $dbProductSubimage->add();
                    }
                }

                $dbEntityProduct->unitPrice = $unitPrice;
                $dbEntityProduct->vat = $vat;
                $dbEntityProduct->stockUpdateDateTime = $dbEntityProduct->getCurrentDateTime();

                if (strlen($maximumOrderQuantity) > 0) {
                    $dbEntityProduct->maximumOrderQuantity = $maximumOrderQuantity;
                } else {
                    $dbEntityProduct->maximumOrderQuantity = null;
                }

                $dbEntityProduct->update();

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS_SHOW_DIALOG;
                $this->webResponse->message = $this->f3->get('vModule_productEdited');
                echo $this->webResponse->jsonResponse();
            }
        }
    }

    function postEditQuantityDistributorProduct()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/distributor/product");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $id = $this->f3->get('POST.id');

            $dbEntityProduct = new BaseModel($this->db, "entityProductSell");
            $dbEntityProduct->getWhere("productId=$id");

            $dbProduct = new BaseModel($this->db, "product");
            $dbProduct->getWhere("id=$id");

            if ($dbEntityProduct->dry() || $dbProduct->dry()) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->title = "";
                $this->webResponse->message = "No Product";
                echo $this->webResponse->jsonResponse();
            } else {
                $stock = $this->f3->get('POST.stock');
                $stockStatusId = $this->f3->get('POST.stockStatus');
                $bonusTypeId = $this->f3->get('POST.bonusType');
                $bonusRepeater = $this->f3->get('POST.bonusRepeater');
                if ($bonusRepeater == null) {
                    $bonusRepeater = [];
                }

                if (!filter_var($stock, FILTER_VALIDATE_INT) || $stock < 0) {
                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->title = "";
                    $this->webResponse->message = "Stock must be a positive number";
                    echo $this->webResponse->jsonResponse();
                    return;
                }

                $validMinOrder = true;
                $validBonus = true;
                foreach ($bonusRepeater as $bonus) {
                    if (!filter_var($bonus['minOrder'], FILTER_VALIDATE_INT) || $bonus['minOrder'] < 0) {
                        $validMinOrder = false;
                    }

                    if (!filter_var($bonus['bonus'], FILTER_VALIDATE_INT) || $bonus['bonus'] < 0) {
                        $validBonus = false;
                    }

                    if ($validMinOrder && $validBonus) {
                        break;
                    }
                }

                if (!$validMinOrder || !$validBonus) {
                    $message = "";
                    if (!$validMinOrder && !$validBonus) {
                        $message = "Min Order and Bonus must be positive numbers";
                    } else if (!$validMinOrder) {
                        $message = "Min Order must be a positive number";
                    } else {
                        $message = "Bonus must be a positive number";
                    }

                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->title = "";
                    $this->webResponse->message = $message;
                    echo $this->webResponse->jsonResponse();
                    return;
                }

                if ($stock > 0) {
                    $stockStatusId = 1;
                } else {
                    if (isset($stockStatusId) && $stockStatusId == 'on') {
                        $stockStatusId = 3;
                    } else {
                        $stockStatusId = 2;
                    }
                }

                if (isset($bonusTypeId) && $bonusTypeId == 'on') {
                    $bonusTypeId = 2;
                } else {
                    $bonusTypeId = 1;
                }

                $dbEntityProduct->stock = $stock;
                $dbEntityProduct->stockStatusId = $stockStatusId;
                $dbEntityProduct->bonusTypeId = $bonusTypeId;
                $dbEntityProduct->stockUpdateDateTime = $dbEntityProduct->getCurrentDateTime();

                $dbEntityProduct->update();

                if ($bonusTypeId != 1) {
                    $dbBonus = new BaseModel($this->db, "entityProductSellBonusDetail");
                    $dbBonus->load(array("entityProductId = $id AND isActive = 1"));
                    while (!$dbBonus->dry()) {
                        $dbBonus->delete();
                        $dbBonus->next();
                    }

                    foreach ($bonusRepeater as $bonus) {
                        $dbBonus->reset();
                        if ($bonus['minOrder'] != '' && $bonus['bonus'] != '') {
                            $dbBonus->entityProductId = $id;
                            $dbBonus->minOrder = $bonus['minOrder'];
                            $dbBonus->bonus = $bonus['bonus'];
                            $dbBonus->add();
                        }
                    }
                }

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS_SHOW_DIALOG;
                $this->webResponse->title = "";
                $this->webResponse->message = $this->f3->get('vModule_quantityEdited');
                $this->webResponse->data = $bonusRepeater;
                echo $this->webResponse->jsonResponse();
            }
        }
    }

    function postEditStockDistributorProduct()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/distributor/product");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $entityProductSellId = $this->f3->get('POST.id');

            $dbEntityProduct = new BaseModel($this->db, "entityProductSell");
            $dbEntityProduct->getWhere(["id=?", $entityProductSellId]);

            $dbProduct = new BaseModel($this->db, "product");
            $dbProduct->getWhere(["id=?", $dbEntityProduct->productId]);

            if ($dbEntityProduct->dry() || $dbProduct->dry()) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->message = $this->f3->get('vModule_product_notFound');
                echo $this->webResponse->jsonResponse();
            } else {
                $stock = $this->f3->get('POST.stock');
                $arrDefaultBonus = $this->f3->get('POST.arrDefaultBonus');
                $arrSpecialBonus = $this->f3->get('POST.arrSpecialBonus');

                if (!$arrDefaultBonus) {
                    $arrDefaultBonus = [];
                }

                if (!$arrSpecialBonus) {
                    $arrSpecialBonus = [];
                }

                if (!(is_numeric($stock) && (int) $stock == $stock) || $stock < 0) {
                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->message = $this->f3->get('vModule_product_stockInvalid');
                    echo $this->webResponse->jsonResponse();
                    return;
                }

                foreach ($arrDefaultBonus as $bonus) {
                    $bonusTypeId = $bonus['bonusTypeId'];
                    $minOrder = $bonus['minOrder'];
                    $bonusQty = $bonus['bonus'];

                    $valid = false;
                    if (
                        strlen($bonusTypeId) != 0
                        && strlen($minOrder) != 0
                        && strlen($bonusQty) != 0
                    ) {
                        if ($bonusTypeId != Constants::BONUS_TYPE_PERCENTAGE || ($bonusQty <= 100 && $bonusTypeId == Constants::BONUS_TYPE_PERCENTAGE)) {
                            $valid = true;
                        }
                    }

                    if (!$valid) {
                        $this->webResponse->errorCode = Constants::STATUS_ERROR;
                        $this->webResponse->message = $this->f3->get('vModule_product_defaultBonusInvalid');
                        echo $this->webResponse->jsonResponse();
                        return;
                    }

                    if ($minOrder > Constants::MAX_INT_VALUE) {
                        $this->webResponse->errorCode = Constants::STATUS_ERROR;
                        $this->webResponse->message = $this->f3->get('vModule_product_quantityTooBig');
                        echo $this->webResponse->jsonResponse();
                        return;
                    }

                    if ($bonusQty > Constants::MAX_INT_VALUE) {
                        $this->webResponse->errorCode = Constants::STATUS_ERROR;
                        $this->webResponse->message = $this->f3->get('vModule_product_bonusTooBig');
                        echo $this->webResponse->jsonResponse();
                        return;
                    }
                }

                foreach ($arrSpecialBonus as $bonus) {
                    $bonusTypeId = $bonus['bonusTypeId'];
                    $minOrder = $bonus['minOrder'];
                    $bonusQty = $bonus['bonus'];
                    $arrRelationGroup = $bonus['arrRelationGroup'];

                    $valid = false;
                    if (
                        strlen($bonusTypeId) != 0
                        && strlen($minOrder) != 0
                        && strlen($bonusQty) != 0
                        && $arrRelationGroup && count($arrRelationGroup) > 0
                    ) {
                        if ($bonusTypeId != Constants::BONUS_TYPE_PERCENTAGE || ($bonusQty <= 100 && $bonusTypeId == Constants::BONUS_TYPE_PERCENTAGE)) {
                            $valid = true;
                        }
                    }

                    if (!$valid) {
                        $this->webResponse->errorCode = Constants::STATUS_ERROR;
                        $this->webResponse->message = $this->f3->get('vModule_product_specialBonusInvalid');
                        echo $this->webResponse->jsonResponse();
                        return;
                    }

                    if ($minOrder > Constants::MAX_INT_VALUE) {
                        $this->webResponse->errorCode = Constants::STATUS_ERROR;
                        $this->webResponse->message = $this->f3->get('vModule_product_quantityTooBig');
                        echo $this->webResponse->jsonResponse();
                        return;
                    }

                    if ($bonusQty > Constants::MAX_INT_VALUE) {
                        $this->webResponse->errorCode = Constants::STATUS_ERROR;
                        $this->webResponse->message = $this->f3->get('vModule_product_bonusTooBig');
                        echo $this->webResponse->jsonResponse();
                        return;
                    }
                }

                $arrBonus = array_merge($arrDefaultBonus, $arrSpecialBonus);

                $mapBonusIdBonus = [];
                foreach ($arrBonus as $bonus) {
                    if ($bonus['id']) {
                        $mapBonusIdBonus[$bonus['id']] = $bonus;
                    }
                }

                $dbBonus = new BaseModel($this->db, "entityProductSellBonusDetail");
                $dbBonusRelationGroup = new BaseModel($this->db, "entityProductSellBonusDetailRelationGroup");

                $dbBonus->getWhere(["isActive = 1 AND entityProductId=?", $entityProductSellId]);
                while (!$dbBonus->dry()) {
                    $bonusId = $dbBonus['id'];
                    if (array_key_exists($bonusId, $mapBonusIdBonus)) {
                        $newBonus = $mapBonusIdBonus[$bonusId];
                        $dbBonus->bonusTypeId = $newBonus['bonusTypeId'];
                        $dbBonus->minOrder = $newBonus['minOrder'];
                        $dbBonus->bonus = $newBonus['bonus'];
                        $dbBonus->update();

                        $arrRelationGroup = $newBonus['arrRelationGroup'];
                        $dbBonusRelationGroup->getWhere("bonusId=$bonusId");
                        while (!$dbBonusRelationGroup->dry()) {
                            $dbBonusRelationGroup->delete();
                            $dbBonusRelationGroup->next();
                        }
                        if ($arrRelationGroup) {
                            foreach ($arrRelationGroup as $relationGroupId) {
                                $dbBonusRelationGroup->bonusId = $bonusId;
                                $dbBonusRelationGroup->relationGroupId = $relationGroupId;
                                $dbBonusRelationGroup->add();
                            }
                        }
                    } else {
                        $dbBonus->delete();
                    }
                    $dbBonus->next();
                }

                foreach ($arrBonus as $bonus) {
                    if (!$bonus['id']) {
                        $dbBonus->entityProductId = $entityProductSellId;
                        $dbBonus->bonusTypeId = $bonus['bonusTypeId'];
                        $dbBonus->minOrder = $bonus['minOrder'];
                        $dbBonus->bonus = $bonus['bonus'];
                        $dbBonus->isActive = 1;
                        $dbBonus->addReturnID();

                        $arrRelationGroup = $bonus['arrRelationGroup'];
                        if ($arrRelationGroup) {
                            foreach ($arrRelationGroup as $relationGroupId) {
                                $dbBonusRelationGroup->bonusId = $dbBonus['id'];
                                $dbBonusRelationGroup->relationGroupId = $relationGroupId;
                                $dbBonusRelationGroup->add();
                            }
                        }
                    }
                }

                $dbEntityProduct->stock = $stock;
                $dbEntityProduct->update();

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS_SHOW_DIALOG;
                $this->webResponse->message = $this->f3->get('vModule_productStockEdited');
                echo $this->webResponse->jsonResponse();
            }
        }
    }

    function postAddDistributorProduct()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/distributor/product");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $scientificNameId = $this->f3->get('POST.scientificNameId');
            $madeInCountryId = $this->f3->get('POST.madeInCountryId');
            $name_en = $this->f3->clean($this->f3->get('POST.name_en'));
            $name_ar = $this->f3->clean($this->f3->get('POST.name_ar'));
            // $name_fr = $this->f3->clean($this->f3->get('POST.name_fr'));
            $image = $this->f3->get('POST.image');
            $subimages = $this->f3->get('POST.subimages');
            $stock = $this->f3->get('POST.stock');
            $maximumOrderQuantity = $this->f3->get('POST.maximumOrderQuantity');
            $subtitle_ar = $this->f3->clean($this->f3->get('POST.subtitle_ar'));
            $subtitle_en = $this->f3->clean($this->f3->get('POST.subtitle_en'));
            // $subtitle_fr = $this->f3->clean($this->f3->get('POST.subtitle_fr'));
            $description_ar = $this->f3->clean($this->f3->get('POST.description_ar'));
            $description_en = $this->f3->clean($this->f3->get('POST.description_en'));
            // $description_fr = $this->f3->clean($this->f3->get('POST.description_fr'));
            $unitPrice = $this->f3->get('POST.unitPrice');
            $vat = $this->f3->get('POST.vat');
            $manufacturerName = $this->f3->clean($this->f3->get('POST.manufacturerName'));
            $batchNumber = $this->f3->clean($this->f3->get('POST.batchNumber'));
            $itemCode = $this->f3->clean($this->f3->get('POST.itemCode'));
            $categoryId = $this->f3->get('POST.categoryId');
            $subcategoryId = $this->f3->get('POST.subcategoryId');
            $activeIngredientsId = $this->f3->get('POST.activeIngredientsId');
            $expiryDate = $this->f3->get('POST.expiryDate');;
            $strength = $this->f3->clean($this->f3->get('POST.strength'));

            if (
                strlen($madeInCountryId) == 0
                // || strlen($scientificNameId) == 0
                || strlen($name_en) == 0 || strlen($name_ar) == 0
                // || strlen($name_fr) == 0
                || strlen($unitPrice) == 0 || strlen($vat) == 0 || strlen($stock) == 0
                || strlen($categoryId) == 0 || strlen($subcategoryId) == 0
            ) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->message = $this->f3->get('vModule_product_missingFields');
                echo $this->webResponse->jsonResponse();
                return;
            }

            if ((!(is_numeric($stock) && (int) $stock == $stock) || $stock < 0)
                || (strlen($maximumOrderQuantity) > 0 && !(is_numeric($maximumOrderQuantity) && (int) $maximumOrderQuantity == $maximumOrderQuantity) || $maximumOrderQuantity < 0)
                || (!is_numeric($unitPrice) || $unitPrice <= 0)
                || (!is_numeric($vat) || $vat < 0 || $vat > 100)
            ) {
                $arrError = [];
                if (!(is_numeric($stock) && (int) $stock == $stock) || $stock < 0) {
                    array_push($arrError, $this->f3->get('vModule_product_stockInvalid'));
                }
                if (strlen($maximumOrderQuantity) > 0 && !(is_numeric($maximumOrderQuantity) && (int) $maximumOrderQuantity == $maximumOrderQuantity) || $maximumOrderQuantity < 0) {
                    array_push($arrError, $this->f3->get('vModule_product_maximumOrderQuantityInvalid'));
                }
                if (!is_numeric($unitPrice) || $unitPrice <= 0) {
                    array_push($arrError, $this->f3->get('vModule_product_unitPriceInvalid'));
                }
                if (!is_numeric($vat) || $vat < 0 || $vat > 100) {
                    array_push($arrError, $this->f3->get('vModule_product_vatInvalid'));
                }

                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->message = implode("<br>", $arrError);
                echo $this->webResponse->jsonResponse();
                return;
            }

            $dbSubcategory = new BaseModel($this->db, "subcategory");
            $dbSubcategory->getWhere("id = $subcategoryId AND categoryId = $categoryId");
            if ($dbSubcategory->dry()) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->message = $this->f3->get('vModule_product_subcategoryInvalid');
                echo $this->webResponse->jsonResponse();
                return;
            }

            $this->checkLength($name_en, 'nameEn', 200, 4);
            $this->checkLength($name_ar, 'nameAr', 200, 4);
            // $this->checkLength($name_fr, 'nameFr', 200, 4);


            if ($description_ar) {
                $this->checkLength($description_ar, 'descriptionAr', 5000, 4);
            }

            if ($description_en) {
                $this->checkLength($description_en, 'descriptionEn', 5000, 4);
            }

            // if ($description_fr) {
            //     $this->checkLength($description_fr, 'descriptionFr', 5000, 4);
            // }

            if ($subtitle_ar) {
                $this->checkLength($subtitle_ar, 'subtitleAr', 200, 4);
            }

            if ($subtitle_en) {
                $this->checkLength($subtitle_en, 'subtitleEn', 200, 4);
            }

            // if ($subtitle_fr) {
            //     $this->checkLength($subtitle_fr, 'subtitleFr', 200, 4);
            // }

            if ($manufacturerName) {
                $this->checkLength($manufacturerName, 'manufacturerName', 200, 4);
            }

            if ($strength) {
                $this->checkLength($strength, 'strength', 200, 4);
            }


            $dbProduct = new BaseModel($this->db, "product");
            if ($scientificNameId) {
                $dbProduct->scientificNameId = $scientificNameId;
            } else {
                $dbProduct->scientificNameId = null;
            }
            $dbProduct->madeInCountryId = $madeInCountryId;
            $dbProduct->name_en = $name_en;
            // $dbProduct->name_fr = $name_fr;
            $dbProduct->name_fr = $name_en;
            $dbProduct->name_ar = $name_ar;
            $dbProduct->image = $image;
            $dbProduct->subtitle_ar = $subtitle_ar;
            $dbProduct->subtitle_en = $subtitle_en;
            // $dbProduct->subtitle_fr = $subtitle_fr;
            $dbProduct->subtitle_fr = $subtitle_en;
            $dbProduct->description_ar = $description_ar;
            $dbProduct->description_en = $description_en;
            // $dbProduct->description_fr = $description_fr;
            $dbProduct->description_fr = $description_en;
            $dbProduct->manufacturerName = $manufacturerName;
            $dbProduct->batchNumber = $batchNumber;
            $dbProduct->itemCode = $itemCode;
            $dbProduct->categoryId = $categoryId;
            $dbProduct->subcategoryId = $subcategoryId;
            $dbProduct->expiryDate = $expiryDate;
            $dbProduct->strength = $strength;

            $dbProduct->addReturnID();

            if ($activeIngredientsId) {
                $arrIngredientId = explode(",", $activeIngredientsId);
                $dbProductIngredient = new BaseModel($this->db, "productIngredient");
                foreach ($arrIngredientId as $ingredientId) {
                    $dbProductIngredient->productId = $dbProduct->id;
                    $dbProductIngredient->ingredientId = $ingredientId;
                    $dbProductIngredient->add();
                }
            }

            if ($subimages && count($subimages) > 0) {
                $dbProductSubimage = new BaseModel($this->db, "productSubimage");
                foreach ($subimages as $subimage) {
                    $dbProductSubimage->productId = $dbProduct->id;
                    $dbProductSubimage->subimage = $subimage;
                    $dbProductSubimage->add();
                }
            }

            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $entityId = $arrEntityId;


            $dbEntityProduct = new BaseModel($this->db, "entityProductSell");
            $dbEntityProduct->productId = $dbProduct->id;
            $dbEntityProduct->entityId = $entityId;
            $dbEntityProduct->unitPrice = $unitPrice;
            $dbEntityProduct->vat = $vat;
            $dbEntityProduct->stock = $stock;
            $dbEntityProduct->statusId = 1;
            $dbEntityProduct->stockStatusId = 1;
            $dbEntityProduct->bonusTypeId = 1;
            $dbEntityProduct->stockUpdateDateTime = $dbEntityProduct->getCurrentDateTime();

            if (strlen($maximumOrderQuantity) > 0) {
                $dbEntityProduct->maximumOrderQuantity = $maximumOrderQuantity;
            } else {
                $dbEntityProduct->maximumOrderQuantity = null;
            }

            $dbEntityProduct->add();

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS_SHOW_DIALOG;
            $this->webResponse->message = $this->f3->get('vModule_productAdded');
            echo $this->webResponse->jsonResponse();
        }
    }

    function getStockUpload()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "Stock Update"; //$this->f3->get('vModule_stock_title');
            $this->webResponse->data = View::instance()->render('app/products/stock/upload.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function getStockDownload()
    {
        if ($this->f3->ajax()) {
            ini_set('max_execution_time', 600);

            // Get all related products
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $query = "entityId IN ($arrEntityId)";
            $dbProducts = new BaseModel($this->db, "vwEntityProductSell");
            $allProducts = $dbProducts->findWhere($query);

            // Setup excel sheet
            $sheetnameUserInput = 'User Input';
            $sheetnameDatabaseInput = 'Database Input';
            $sheetnameVariables = 'Variables';

            // Prepare data for variables sheet
            $arrProducts = [
                ['Name', 'Value']
            ];
            $arrStockAvailability = [
                ['Name', 'Value']
            ];

            $mapProductIdName = [];
            $productsNum = 2;
            $nameField = "productName_" . $this->objUser->language;
            foreach ($allProducts as $product) {
                $productsNum++;
                $arrProducts[] = array($product[$nameField], $product['productId']);
                $mapProductIdName[$product['productId']] = $product[$nameField];
            }

            $dbStockStatus = new BaseModel($this->db, "stockStatus");
            $dbStockStatus->name = "name_" . $this->objUser->language;
            $allStockStatus = $dbStockStatus->findAll("id asc");

            $mapStockIdName = [];
            $stockAvailabilityNum = 2;
            foreach ($allStockStatus as $stockStatus) {
                $stockAvailabilityNum++;
                $arrStockAvailability[] = array($stockStatus['name'], $stockStatus['id']);
                $mapStockIdName[$stockStatus['id']] = $stockStatus['name'];
            }

            $sampleFilePath = 'app/files/samples/products-stock-sample.xlsx';
            $spreadsheet = Excel::loadFile($sampleFilePath);

            // Change active sheet to variables
            $sheet = $spreadsheet->setActiveSheetIndex(2);

            // Set products and stock availability in excel
            $sheet->fromArray($arrProducts, NULL, 'A2', true);
            $sheet->fromArray($arrStockAvailability, NULL, 'D2', true);

            // Change active sheet to database input
            $sheet = $spreadsheet->setActiveSheetIndex(1);

            // Set validation and formula
            Excel::setCellFormulaVLookup($sheet, 'A3', $productsNum, "'User Input'!A", 'Variables!$A$3:$B$' . $productsNum);
            Excel::setCellFormulaVLookup($sheet, 'D3', $productsNum, "'User Input'!D", 'Variables!$D$3:$E$' . $stockAvailabilityNum);

            // Hide database and variables sheet
            Excel::hideSheetByName($spreadsheet, $sheetnameDatabaseInput);
            Excel::hideSheetByName($spreadsheet, $sheetnameVariables);

            // Change active sheet to user input
            $sheet = $spreadsheet->setActiveSheetIndex(0);

            // Set data validation for products and stock availability
            Excel::setDataValidation($sheet, 'A3', 'A' . $productsNum, 'TYPE_LIST', 'Variables!$A$3:$A$' . $productsNum);
            Excel::setDataValidation($sheet, 'D3', 'D' . $productsNum, 'TYPE_LIST', 'Variables!$D$3:$D$' . $stockAvailabilityNum);

            // Add all products to multidimensional array
            $multiProducts = [];
            $fields = [
                "productId",
                "unitPrice",
                "vat",
                "stockStatusId",
                "stock",
                "expiryDate"
            ];
            $i = 3;
            foreach ($allProducts as $product) {
                $singleProduct = [];
                foreach ($fields as $field) {
                    if ($field == "productId") {
                        $cellValue = $mapProductIdName[$product[$field]];
                    } else if ($field == "stockStatusId") {
                        $cellValue = $mapStockIdName[$product[$field]];
                    } else {
                        $cellValue = $product[$field];
                    }
                    array_push($singleProduct, $cellValue);
                }
                array_push($multiProducts, $singleProduct);
                $i++;
            }

            // Fill rows with products
            $sheet->fromArray($multiProducts, NULL, 'A3', true);

            // Create excel sheet
            $productsSheetUrl = "files/downloads/reports/products-stock/products-stock-" . $this->objUser->id . "-" . time() . ".xlsx";
            Excel::saveSpreadsheetToPath($spreadsheet, $productsSheetUrl);

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "Stock Download";
            $this->webResponse->data = "/" . $productsSheetUrl;
            echo $this->webResponse->jsonResponse();
        }
    }

    function postStockUpload()
    {
        $fileName = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_FILENAME);
        $ext = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_EXTENSION);
        // basename($_FILES["file"]["name"])

        $targetFile = "files/uploads/reports/products-stock/" . $this->objUser->id . "-" . $fileName . "-" . time() . ".$ext";

        if ($ext == "xlsx" || $ext == "xls" || $ext == "csv") {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                $dbStockUpdateUpload = new BaseModel($this->db, "stockUpdateUpload");
                $dbStockUpdateUpload->userId = $this->objUser->id;
                $dbStockUpdateUpload->filePath = $targetFile;
                $dbStockUpdateUpload->entityId = $this->objUser->entityId;
                $dbStockUpdateUpload->addReturnID();
                echo "OK";
            }
        }
    }

    function postStockUploadProcess()
    {
        ini_set('max_execution_time', 1000);
        ini_set('mysql.connect_timeout', 1000);

        $dbStockUpdateUpload = new BaseModel($this->db, "stockUpdateUpload");

        $dbStockUpdateUpload->getByField("userId", $this->objUser->id, "insertDateTime desc");

        // $inputFileType = Excel::identifyFileType($dbStockUpdateUpload->filePath);
        try {
            // $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            // $spreadsheet = $reader->load($dbStockUpdateUpload->filePath);
            $spreadsheet = Excel::loadFile($dbStockUpdateUpload->filePath);

            // Change active sheet to database input
            $sheet = $spreadsheet->setActiveSheetIndex(1);

            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $dbEntityProductSell = new BaseModel($this->db, "entityProductSell");

            // Get all related products
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $query = "entityId IN ($arrEntityId)";
            $dbProducts = new BaseModel($this->db, "vwEntityProductSell");
            $allProducts = $dbProducts->findWhere($query);

            // Get all stock status
            $dbStockStatus = new BaseModel($this->db, "stockStatus");
            $dbStockStatus->name = "name_" . $this->objUser->language;
            $allStockStatus = $dbStockStatus->findAll("id asc");

            $allStockStatusId = [];
            foreach ($allStockStatus as $stockStatus) {
                array_push($allStockStatusId, $stockStatus['id']);
            }

            $fields = [
                "A" => "productId",
                "B" => "unitPrice",
                "C" => "vat",
                "D" => "stockStatusId",
                "E" => "stock",
                "F" => "expiryDate"
            ];

            $successProducts = [];
            $failedProducts = [];

            $allErrors = [];

            $dbStockUpdateUpload->recordsCount = 0;
            $successRecords = 0;
            $failedRecords = 0;
            $unchangedRecords = 0;

            $firstRow = true;
            $secondRow = false;
            $finished = false;
            foreach ($sheet->getRowIterator() as $row) {
                $product = [];

                if ($firstRow) {
                    $firstRow = false;
                    $secondRow = true;
                    continue;
                } else if ($secondRow) {
                    $secondRow = false;
                    continue;
                }

                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);

                $errors = [];

                $fieldsChanged = false;
                $stockFieldsChanged = false;
                foreach ($cellIterator as $cell) {
                    $cellLetter = $cell->getColumn();
                    $cellValue = $cell->getCalculatedValue();

                    array_push($product, $cellValue);

                    switch ($cellLetter) {
                        case "A":
                            if (!is_numeric($cellValue)) {
                                $finished = true;
                                break;
                            } else {
                                $dbEntityProductSell->getWhere("productId=$cellValue and entityId IN ($arrEntityId)");
                                if ($dbEntityProductSell->dry()) {
                                    array_push($errors, "Product not found");
                                }
                            }
                            break;
                        case "B":
                            if (!is_numeric($cellValue) || (float)$cellValue < 0) {
                                array_push($errors, "Price must be a positive number");
                            } else {
                                $unitPrice = round((float)$cellValue, 2);
                                if ($dbEntityProductSell->unitPrice != $unitPrice) {
                                    $fieldsChanged = true;
                                    $dbEntityProductSell->unitPrice = $unitPrice;
                                }
                            }
                            break;
                        case "C":
                            if (!is_numeric($cellValue) || (float)$cellValue < 0) {
                                array_push($errors, "VAT must be a positive number");
                            } else {
                                $vat = round((float)$cellValue, 2);
                                if ($dbEntityProductSell->vat != $vat) {
                                    $fieldsChanged = true;
                                    $dbEntityProductSell->vat = $vat;
                                }
                            }
                            break;
                        case "D":
                            if (!in_array($cellValue, $allStockStatusId)) {
                                array_push($errors, "Stock Availability invalid");
                            } else {
                                $stockStatusId = $cellValue;
                                if ($dbEntityProductSell->stockStatusId != $stockStatusId) {
                                    $stockFieldsChanged = true;
                                    $dbEntityProductSell->stockStatusId = $stockStatusId;
                                }
                            }
                            break;
                        case "E":
                            if (!filter_var($cellValue, FILTER_VALIDATE_INT) || (float)$cellValue < 0) {
                                array_push($errors, "Stock Quantity must be a positive whole number");
                            } else {
                                $stock = (int)$cellValue;
                                if ($dbEntityProductSell->stock != $stock) {
                                    $stockFieldsChanged = true;
                                    $dbEntityProductSell->stock = $stock;
                                }
                            }
                            break;
                        case "F":
                            if (!is_null($cellValue)) {
                                if (!is_int($cellValue) && (count(explode("-", $cellValue)) !== 3)) {
                                    array_push($errors, "Expiry Date must fit a date format (YYYY-MM-DD)");
                                } else {
                                    if (is_int($cellValue)) {
                                        $expiryDate = Excel::excelDateToRegularDate($cellValue);
                                    } else {
                                        $expiryDate = $cellValue;
                                    }
                                    if ($dbEntityProductSell->expiryDate != $expiryDate) {
                                        $fieldsChanged = true;
                                        $dbEntityProductSell->expiryDate = $expiryDate;
                                    }
                                }
                            }
                            break;
                    }
                }

                if ($finished) {
                    break;
                }

                $dbStockUpdateUpload->recordsCount++;

                if (!$dbEntityProductSell->dry() && ($fieldsChanged || $stockFieldsChanged) && count($errors) === 0) {
                    $currentDate = date("Y-m-d H:i:s");
                    if ($fieldsChanged) {
                        $dbEntityProductSell->updateDateTime = $currentDate;
                    }

                    if ($stockFieldsChanged) {
                        $dbEntityProductSell->stockUpdateDateTime = $currentDate;
                    }

                    array_push($successProducts, $product);

                    $dbEntityProductSell->update();
                    $successRecords++;
                } else if (count($errors) > 0) {
                    array_push($failedProducts, $product);
                    array_push($allErrors, $errors);
                    $failedRecords++;
                } else {
                    $unchangedRecords++;
                }

                $dbEntityProductSell->reset();
            }

            if (count($failedProducts) > 0) {
                // Setup excel sheet
                $sheetnameUserInput = 'User Input';
                $sheetnameDatabaseInput = 'Database Input';
                $sheetnameVariables = 'Variables';

                // Prepare data for variables sheet
                $arrProducts = [
                    ['Name', 'Value']
                ];
                $arrStockAvailability = [
                    ['Name', 'Value']
                ];

                $mapProductIdName = [];
                $productsNum = 2;
                $nameField = "productName_" . $this->objUser->language;
                foreach ($allProducts as $product) {
                    $productsNum++;
                    $arrProducts[] = array($product[$nameField], $product['productId']);
                    $mapProductIdName[$product['productId']] = $product[$nameField];
                }

                $mapStockIdName = [];
                $stockAvailabilityNum = 2;
                foreach ($allStockStatus as $stockStatus) {
                    $stockAvailabilityNum++;
                    $arrStockAvailability[] = array($stockStatus['name'], $stockStatus['id']);
                    $mapStockIdName[$stockStatus['id']] = $stockStatus['name'];
                }

                $sampleFilePath = 'app/files/samples/products-stock-sample.xlsx';
                $spreadsheet = Excel::loadFile($sampleFilePath);

                // Change active sheet to variables
                $sheet = $spreadsheet->setActiveSheetIndex(2);

                // Set products and stock availability in excel
                $sheet->fromArray($arrProducts, NULL, 'A2', true);
                $sheet->fromArray($arrStockAvailability, NULL, 'D2', true);

                // Change active sheet to database input
                $sheet = $spreadsheet->setActiveSheetIndex(1);

                // Set validation and formula
                Excel::setCellFormulaVLookup($sheet, 'A3', (count($failedProducts) + 2), "'User Input'!A", 'Variables!$A$3:$B$' . $productsNum);
                Excel::setCellFormulaVLookup($sheet, 'D3', (count($failedProducts) + 2), "'User Input'!D", 'Variables!$D$3:$E$' . $stockAvailabilityNum);

                // Hide database and variables sheet
                Excel::hideSheetByName($spreadsheet, $sheetnameDatabaseInput);
                Excel::hideSheetByName($spreadsheet, $sheetnameVariables);

                // Change active sheet to user input
                $sheet = $spreadsheet->setActiveSheetIndex(0);

                // Set data validation for products and stock availability
                Excel::setDataValidation($sheet, 'A3', 'A' . (count($failedProducts) + 2), 'TYPE_LIST', 'Variables!$A$3:$A$' . $productsNum);
                Excel::setDataValidation($sheet, 'D3', 'D' . (count($failedProducts) + 2), 'TYPE_LIST', 'Variables!$D$3:$D$' . $stockAvailabilityNum);

                $sheet->setCellValue('G2', 'Error');
                $sheet->getStyle('G2')->applyFromArray(Excel::STYlE_CENTER_BOLD_BORDER_THICK);

                // Add all products to multidimensional array
                $multiProducts = [];
                $fields = [
                    "productId",
                    "unitPrice",
                    "vat",
                    "stockStatusId",
                    "stock",
                    "expiryDate"
                ];
                $i = 3;
                for ($i = 0; $i < count($failedProducts); $i++) {
                    $product = $failedProducts[$i];
                    $singleProduct = [];
                    $j = 0;
                    foreach ($fields as $field) {
                        if ($field == "productId") {
                            $cellValue = $mapProductIdName[$product[$j]];
                        } else if ($field == "stockStatusId") {
                            $cellValue = $mapStockIdName[$product[$j]];
                        } else {
                            $cellValue = $product[$j];
                        }
                        array_push($singleProduct, $cellValue);
                        $j++;
                    }
                    $errors = $allErrors[$i];
                    $error = join(", ", $errors);
                    array_push($singleProduct, $error);

                    array_push($multiProducts, $singleProduct);
                }
                // Fill rows with products
                $sheet->fromArray($multiProducts, NULL, 'A3', true);

                // Create excel sheet
                $failedProductsSheetUrl = "files/downloads/reports/products-stock/products-stock-" . $this->objUser->id . "-" . time() . ".xlsx";
                Excel::saveSpreadsheetToPath($spreadsheet, $failedProductsSheetUrl);
            }

            // Update logs
            if (count($successProducts) > 0) {
                $dbStockUpdateUpload->successLog = json_encode($successProducts);
            }

            if (count($failedProducts) > 0) {
                $dbStockUpdateUpload->failedLog = json_encode($failedProducts);
            }

            // Update counts and rates
            $dbStockUpdateUpload->completedCount = $successRecords;
            $dbStockUpdateUpload->failedCount = $failedRecords;
            $dbStockUpdateUpload->unchangedCount = $unchangedRecords;

            if ($successRecords + $failedRecords !== 0) {
                $dbStockUpdateUpload->importSuccessRate = round($successRecords / ($successRecords + $failedRecords), 2) * 100;
                $dbStockUpdateUpload->importFailureRate = round($failedRecords / ($successRecords + $failedRecords), 2) * 100;
            } else {
                $dbStockUpdateUpload->importSuccessRate = 0;
                $dbStockUpdateUpload->importFailureRate = 0;
            }

            $this->f3->set("objStockUpdateUpload", $dbStockUpdateUpload);
            if (!is_null($failedProductsSheetUrl)) {
                $this->f3->set("failedProductsSheetUrl", "/" . $failedProductsSheetUrl);
            }

            $dbStockUpdateUpload->update();

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "";
            $this->webResponse->data = View::instance()->render('app/products/stock/uploadResult.php');
            echo $this->webResponse->jsonResponse();;
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
        }
    }

    function getBonusUpload()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vModule_bonus_title');
            $this->webResponse->data = View::instance()->render('app/products/bonus/upload.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function getBonusDownload()
    {
        if ($this->f3->ajax()) {
            ini_set('max_execution_time', 600);

            // Setup excel sheet
            $sheetnameUserInput = 'User Input';
            $sheetnameDatabaseInput = 'Database Input';
            $sheetnameVariables = 'Variables';

            // Prepare data for variables sheet
            $arrProduct = [
                ['Name', 'Value']
            ];
            $arrBonusType = [
                ['Name', 'Value']
            ];
            $arrRelationGroup = [
                ['Name', 'Value']
            ];

            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $dbProduct = new BaseModel($this->db, "vwEntityProductSellSummary");
            $dbProduct->name = "productName_" . $this->objUser->language;
            $allProduct = $dbProduct->findWhere("entityId IN ($arrEntityId)");

            $arrProductId = [];
            $productNum = 2;
            foreach ($allProduct as $product) {
                $productNum++;
                $arrProduct[] = array($product['name'], $product['productId']);
                array_push($arrProductId, $product['productId']);
            }

            $dbBonusType = new BaseModel($this->db, "bonusType");
            $dbBonusType->name = "name_" . $this->objUser->language;
            $allBonusType = $dbBonusType->findAll("name ASC");

            $bonusTypeNum = 2;
            foreach ($allBonusType as $bonusType) {
                $bonusTypeNum++;
                $arrBonusType[] = array($bonusType['name'], $bonusType['id']);
            }

            $dbRelationGroup = new BaseModel($this->db, "entityRelationGroup");
            $dbRelationGroup->name = "name_" . $this->objUser->language;
            $allRelationGroup = $dbRelationGroup->getWhere("entityId IN ($arrEntityId)", "name ASC");

            $mapRelationGroupIdName = [];
            $relationGroupNum = 2;
            foreach ($allRelationGroup as $relationGroup) {
                $relationGroupNum++;
                $arrRelationGroup[] = array($relationGroup['name'], $relationGroup['id']);
                $mapRelationGroupIdName[$relationGroup['id']] = $relationGroup['name'];
            }

            $sampleFilePath = 'app/files/samples/products-bonus-sample.xlsx';
            $spreadsheet = Excel::loadFile($sampleFilePath);

            // Change active sheet to variables
            $sheet = $spreadsheet->setActiveSheetIndex(2);

            // Set dropdown variables in excel
            $sheet->fromArray($arrProduct, NULL, 'A2', true);
            $sheet->fromArray($arrBonusType, NULL, 'D2', true);
            $sheet->fromArray($arrRelationGroup, NULL, 'G2', true);

            // Change active sheet to database input
            $sheet = $spreadsheet->setActiveSheetIndex(1);

            // Set validation and formula
            Excel::setCellFormulaVLookup($sheet, 'A3', 2505, "'User Input'!A", 'Variables!$A$3:$B$' . $productNum);
            Excel::setCellFormulaVLookup($sheet, 'B3', 2505, "'User Input'!B", 'Variables!$D$3:$E$' . $bonusTypeNum);
            Excel::setCellFormulaVLookup($sheet, 'E3', 2505, "'User Input'!E", 'Variables!$G$3:$H$' . $relationGroupNum);

            // Hide database and variables sheet
            Excel::hideSheetByName($spreadsheet, $sheetnameDatabaseInput);
            Excel::hideSheetByName($spreadsheet, $sheetnameVariables);

            // Change active sheet to user input
            $sheet = $spreadsheet->setActiveSheetIndex(0);

            // Set data validation for dropdowns
            Excel::setDataValidation($sheet, 'A3', 'A2505', 'TYPE_LIST', 'Variables!$A$3:$A$' . $productNum);
            Excel::setDataValidation($sheet, 'B3', 'B2505', 'TYPE_LIST', 'Variables!$D$3:$D$' . $bonusTypeNum);
            Excel::setDataValidation($sheet, 'E3', 'E2505', 'TYPE_LIST', 'Variables!$G$3:$G$' . $relationGroupNum);

            // Prepare data for initial bonuses
            $arrProductIdStr = implode(",", $arrProductId);
            $dbBonus = new BaseModel($this->db, "vwEntityProductSellBonusDetail");
            $arrBonus = $dbBonus->findWhere("entityProductId IN (" . $arrProductIdStr . ") AND isActive = 1");

            $arrBonusId = [];
            foreach ($arrBonus as $bonus) {
                array_push($arrBonusId, $bonus['id']);
            }

            $mapBonusIdRelationGroupName = [];
            if(count($arrBonusId) > 0) {
                $arrBonusIdStr = implode(",", $arrBonusId);
                $dbBonusRelationGroup = new BaseModel($this->db, "entityProductSellBonusDetailRelationGroup");
                $arrBonusRelationGroup = $dbBonusRelationGroup->findWhere("bonusId IN (" . $arrBonusIdStr . ")");

                foreach($arrBonusRelationGroup as $bonusRelationGroup) {
                    $bonusId = $bonusRelationGroup['bonusId'];

                    $relationGroupId = $bonusRelationGroup['relationGroupId'];
                    $relationGroupName = $mapRelationGroupIdName[$relationGroupId];
                    if(array_key_exists($bonusId, $mapBonusIdRelationGroupName)) {
                        $arrRelationGroupName = $mapBonusIdRelationGroupName[$bonusId];
                        array_push($arrRelationGroupName, $relationGroupName);
                        $mapBonusIdRelationGroupName[$bonusId] = $arrRelationGroupName;
                    } else {
                        $mapBonusIdRelationGroupName[$bonusId] = [$relationGroupName];
                    }
                }
            }

            $arrBonusExcel = [];

            $fields = [
                "productName_" . $this->objUser->language,
                "bonusTypeName_" . $this->objUser->language,
                "minOrder",
                "bonus"
            ];
            foreach ($arrBonus as $bonus) {
                // Fill bonus fields
                $bonusExcel = [];
                foreach ($fields as $field) {
                    $cellValue = $bonus[$field];
                    array_push($bonusExcel, $cellValue);
                }

                // Fill relation group field
                $arrRelationGroupName = $mapBonusIdRelationGroupName[$bonus["id"]];
                if(!$arrRelationGroupName) {
                    array_push($bonusExcel, "");
                    array_push($arrBonusExcel, $bonusExcel);
                } else {
                    // Duplicate row and add each time a relation group
                    foreach($arrRelationGroupName as $relationGroupName) {
                        $bonusExcelCopy = array_merge([], $bonusExcel);
                        array_push($bonusExcelCopy, $relationGroupName);
                        array_push($arrBonusExcel, $bonusExcelCopy);
                    }
                }
            }

            // Change active sheet to user input
            $sheet = $spreadsheet->setActiveSheetIndex(0);

            // Fill bonuses in user input sheet
            $sheet->fromArray($arrBonusExcel, NULL, 'A3', true);

            // Create excel sheet
            $productsSheetUrl = "files/downloads/reports/products-bonus/products-bonus-" . $this->objUser->id . "-" . time() . ".xlsx";
            Excel::saveSpreadsheetToPath($spreadsheet, $productsSheetUrl);

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "Bonus Download";
            $this->webResponse->data = "/" . $productsSheetUrl;
            echo $this->webResponse->jsonResponse();
        }
    }

    function postBonusUpload()
    {
        $fileName = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_FILENAME);
        $ext = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_EXTENSION);
        // basename($_FILES["file"]["name"])

        $targetFile = "files/uploads/reports/products-bonus/" . $this->objUser->id . "-" . $fileName . "-" . time() . ".$ext";

        if ($ext == "xlsx") {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                $dbStockUpdateUpload = new BaseModel($this->db, "stockUpdateUpload");
                $dbStockUpdateUpload->userId = $this->objUser->id;
                $dbStockUpdateUpload->filePath = $targetFile;
                $dbStockUpdateUpload->entityId = $this->objUser->entityId;
                $dbStockUpdateUpload->addReturnID();
                echo "OK";
            }
        }
    }

    function postBonusUploadProcess()
    {
        ini_set('max_execution_time', 1000);
        ini_set('mysql.connect_timeout', 1000);

        $dbBonusUpdateUpload = new BaseModel($this->db, "stockUpdateUpload");

        $dbBonusUpdateUpload->getByField("userId", $this->objUser->id, "insertDateTime desc");

        // $inputFileType = Excel::identifyFileType($dbBonusUpdateUpload->filePath);
        try {
            // $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            // $spreadsheet = $reader->load($dbBonusUpdateUpload->filePath);
            $spreadsheet = Excel::loadFile($dbBonusUpdateUpload->filePath);

            // Change active sheet to database input
            $sheet = $spreadsheet->setActiveSheetIndex(1);

            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));

            $dbProduct = new BaseModel($this->db, "vwEntityProductSellSummary");
            $dbProduct->name = "productName_" . $this->objUser->language;
            $allProduct = $dbProduct->findWhere("entityId IN ($arrEntityId)");

            $arrProductId = [];
            foreach ($allProduct as $product) {
                array_push($arrProductId, $product['productId']);
                $mapProductIdName[$product['productId']] = $product['name'];
            }

            $dbBonusType = new BaseModel($this->db, "bonusType");
            $dbBonusType->name = "name_" . $this->objUser->language;
            $allBonusType = $dbBonusType->findAll("name ASC");

            $arrBonusTypeId = [];
            foreach ($allBonusType as $bonusType) {
                array_push($arrBonusTypeId, $bonusType['id']);
                $mapBonusTypeIdName[$bonusType['id']] = $bonusType['name'];
            }

            $dbRelationGroup = new BaseModel($this->db, "entityRelationGroup");
            $dbRelationGroup->name = "name_" . $this->objUser->language;
            $allRelationGroup = $dbRelationGroup->getWhere("entityId IN ($arrEntityId)", "name ASC");

            $arrRelationGroupId = [];
            $mapRelationGroupIdName = [];
            foreach ($allRelationGroup as $relationGroup) {
                array_push($arrRelationGroupId, $relationGroup['id']);
                $mapRelationGroupIdName[$relationGroup['id']] = $relationGroup['name'];
            }

            $fields = [
                "A" => "entityProductId",
                "B" => "bonusTypeId",
                "C" => "minOrder",
                "D" => "bonus",
                "E" => "arrRelationGroup"
            ];

            $arrBonus = [];
            $allErrors = [];

            $dbBonusUpdateUpload->recordsCount = 0;

            $firstRow = true;
            $secondRow = false;
            $finished = false;
            foreach ($sheet->getRowIterator() as $row) {
                $bonus = [];
                $bonusTypeId = null;

                if ($firstRow) {
                    $firstRow = false;
                    $secondRow = true;
                    continue;
                } else if ($secondRow) {
                    $secondRow = false;
                    continue;
                }

                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);

                $errors = [];

                foreach ($cellIterator as $cell) {
                    $cellLetter = $cell->getColumn();
                    $cellValue = $cell->getCalculatedValue();

                    switch ($cellLetter) {
                        case "A":
                            if (!in_array($cellValue, $arrProductId)) {
                                $finished = true;
                            } else {
                                array_push($bonus, $cellValue);
                            }
                            break;
                        case "B":
                            if (!in_array($cellValue, $arrBonusTypeId)) {
                                array_push($errors, "Bonus Type invalid");
                            } else {
                                $bonusTypeId = $cellValue;
                            }
                            array_push($bonus, $cellValue);
                            break;
                        case "C":
                            if (!(is_numeric($cellValue) && (int) $cellValue == $cellValue) || $cellValue < 0) {
                                array_push($errors, "Quantity must be a positive whole number");
                            }
                            array_push($bonus, $cellValue);
                            break;
                        case "D":
                            if (!(is_numeric($cellValue) && (int) $cellValue == $cellValue) || $cellValue < 0) {
                                array_push($errors, "Bonus must be a positive whole number");
                            } else {
                                if ($cellValue > 100 && $bonusTypeId == Constants::BONUS_TYPE_PERCENTAGE) {
                                    array_push($errors, "Percentage Bonus must be between 0 and 100");
                                }
                            }
                            array_push($bonus, $cellValue);
                            break;
                        case "E":
                            if($cellValue != "#N/A") {
                                if (!in_array($cellValue, $arrRelationGroupId)) {
                                    array_push($errors, "Customer Group invalid");
                                }
                            }
                            array_push($bonus, $cellValue);
                            break;
                    }
                }

                if ($finished) {
                    break;
                }

                $dbBonusUpdateUpload->recordsCount++;

                array_push($arrBonus, $bonus);
                array_push($allErrors, $errors);

                if (count($errors) > 0) {
                    $failedSheet = true;
                }
            }

            if ($failedSheet) {
                // Setup excel sheet
                $sheetnameUserInput = 'User Input';
                $sheetnameDatabaseInput = 'Database Input';
                $sheetnameVariables = 'Variables';

                // Prepare data for variables sheet
                $arrProduct = [
                    ['Name', 'Value']
                ];
                $arrBonusType = [
                    ['Name', 'Value']
                ];
                $arrRelationGroup = [
                    ['Name', 'Value']
                ];

                $productNum = 2;
                foreach ($allProduct as $product) {
                    $productNum++;
                    $arrProduct[] = array($product['name'], $product['productId']);
                }

                $bonusTypeNum = 2;
                foreach ($allBonusType as $bonusType) {
                    $bonusTypeNum++;
                    $arrBonusType[] = array($bonusType['name'], $bonusType['id']);
                }

                $relationGroupNum = 2;
                foreach ($allRelationGroup as $relationGroup) {
                    $relationGroupNum++;
                    $arrRelationGroup[] = array($relationGroup['name'], $relationGroup['id']);
                }

                $sampleFilePath = 'app/files/samples/products-bonus-sample.xlsx';
                $spreadsheet = Excel::loadFile($sampleFilePath);

                // Change active sheet to variables
                $sheet = $spreadsheet->setActiveSheetIndex(2);

                // Set dropdown variables in excel
                $sheet->fromArray($arrProduct, NULL, 'A2', true);
                $sheet->fromArray($arrBonusType, NULL, 'D2', true);
                $sheet->fromArray($arrRelationGroup, NULL, 'G2', true);

                // Change active sheet to database input
                $sheet = $spreadsheet->setActiveSheetIndex(1);

                // Set validation and formula
                Excel::setCellFormulaVLookup($sheet, 'A3', 2505, "'User Input'!A", 'Variables!$A$3:$B$' . $productNum);
                Excel::setCellFormulaVLookup($sheet, 'B3', 2505, "'User Input'!B", 'Variables!$D$3:$E$' . $bonusTypeNum);
                Excel::setCellFormulaVLookup($sheet, 'E3', 2505, "'User Input'!E", 'Variables!$G$3:$H$' . $relationGroupNum);

                // Hide database and variables sheet
                Excel::hideSheetByName($spreadsheet, $sheetnameDatabaseInput);
                Excel::hideSheetByName($spreadsheet, $sheetnameVariables);

                // Change active sheet to user input
                $sheet = $spreadsheet->setActiveSheetIndex(0);

                // Set data validation for dropdowns
                Excel::setDataValidation($sheet, 'A3', 'A2505', 'TYPE_LIST', 'Variables!$A$3:$A$' . $productNum);
                Excel::setDataValidation($sheet, 'B3', 'B2505', 'TYPE_LIST', 'Variables!$D$3:$D$' . $bonusTypeNum);
                Excel::setDataValidation($sheet, 'E3', 'E2505', 'TYPE_LIST', 'Variables!$G$3:$G$' . $relationGroupNum);

                $sheet->setCellValue('F2', 'Error');
                $sheet->getStyle('F2')->applyFromArray(Excel::STYlE_CENTER_BOLD_BORDER_THICK);

                // Prepare data for returned bonuses
                $arrBonusExcel = [];

                $fields = [
                    "entityProductId",
                    "bonusTypeId",
                    "minOrder",
                    "bonus",
                    "relationGroupId",
                    "error"
                ];
                $i = 0;
                foreach ($arrBonus as $bonus) {
                    $bonusExcel = [];

                    $j = 0;
                    foreach ($fields as $field) {
                        switch ($field) {
                            case "entityProductId":
                                $cellValue = $mapProductIdName[$bonus[$j]];
                                break;
                            case "bonusTypeId":
                                $cellValue = $mapBonusTypeIdName[$bonus[$j]];
                                break;
                            case "relationGroupId":
                                $cellValue = $mapRelationGroupIdName[$bonus[$j]];
                                break;
                            case "error":
                                $cellValue = implode(", ", $allErrors[$i]);
                                break;
                            default:
                                $cellValue = $bonus[$j];
                        }
                        array_push($bonusExcel, $cellValue);
                        $j++;
                    }
                    array_push($arrBonusExcel, $bonusExcel);
                    $i++;
                }

                // Change active sheet to user input
                $sheet = $spreadsheet->setActiveSheetIndex(0);

                // Fill bonuses in user input sheet
                $sheet->fromArray($arrBonusExcel, NULL, 'A3', true);

                // Create excel sheet
                $failedProductsSheetUrl = "files/downloads/reports/products-bonus/products-bonus-" . $this->objUser->id . "-" . time() . ".xlsx";
                Excel::saveSpreadsheetToPath($spreadsheet, $failedProductsSheetUrl);
            } else {
                $arrProductIdStr = implode(",", $arrProductId);
                $dbBonus = new BaseModel($this->db, "entityProductSellBonusDetail");
                $dbBonusRelationGroup = new BaseModel($this->db, "entityProductSellBonusDetailRelationGroup");
                $dbBonus->getWhere("entityProductId IN (" . $arrProductIdStr . ") AND isActive = 1");
                while(!$dbBonus->dry()) {
                    $dbBonus->isActive = 0;
                    $dbBonus->update();
                    $dbBonus->next();
                }

                $arrBonusDb = [];
                $arrMergedIndex = [];
                for($i = 0; $i < count($arrBonus); $i++) {
                    if(in_array($i, $arrMergedIndex)) {
                        continue;
                    }
                    $bonus = $arrBonus[$i];

                    $entityProductId = $bonus[0];
                    $bonusTypeId = $bonus[1];
                    $minOrder = $bonus[2];
                    $bonusBonus = $bonus[3];
                    $relationGroupId = $bonus[4];

                    $arrRelationGroupId = [];
                    if($relationGroupId != "#N/A") {
                        array_push($arrRelationGroupId, $relationGroupId);
                        for($j = 0; $j < count($arrBonus); $j++) {
                            if(in_array($j, $arrMergedIndex)) {
                                continue;
                            }
                            $mergeBonus = $arrBonus[$j];
                            $mergeEntityProductId = $mergeBonus[0];
                            $mergeBonusTypeId = $mergeBonus[1];
                            $mergeMinOrder = $mergeBonus[2];
                            $mergeBonusBonus = $mergeBonus[3];
                            $mergeRelationGroupId = $mergeBonus[4];

                            if($entityProductId == $mergeEntityProductId
                                && $bonusTypeId == $mergeBonusTypeId
                                && $minOrder == $mergeMinOrder
                                && $bonusBonus == $mergeBonusBonus
                                && $mergeRelationGroupId != "#N/A") {
                                array_push($arrMergedIndex, $j);
                                if(!in_array($mergeRelationGroupId, $arrRelationGroupId)) {
                                    array_push($arrRelationGroupId, $mergeRelationGroupId);
                                }
                            }
                        }
                    }

                    $bonusDb = [
                        $entityProductId,
                        $bonusTypeId,
                        $minOrder,
                        $bonusBonus,
                        $arrRelationGroupId
                    ];
                    array_push($arrBonusDb, $bonusDb);
                }

                foreach($arrBonusDb as $bonusDb) {
                    $dbBonus->entityProductId = $bonusDb[0];
                    $dbBonus->bonusTypeId = $bonusDb[1];
                    $dbBonus->minOrder = $bonusDb[2];
                    $dbBonus->bonus = $bonusDb[3];
                    $dbBonus->addReturnID();

                    $arrRelationGroupId = $bonusDb[4];
                    if(count($arrRelationGroupId) > 0) {
                        foreach($arrRelationGroupId as $relationGroupId) {
                            $dbBonusRelationGroup->bonusId = $dbBonus['id'];
                            $dbBonusRelationGroup->relationGroupId = $relationGroupId;
                            $dbBonusRelationGroup->add();
                        }
                    }
                }
            }

            // Update logs
            if ($failedSheet) {
                $dbBonusUpdateUpload->failedLog = json_encode($arrBonus);
            } else {
                $dbBonusUpdateUpload->successLog = json_encode($arrBonus);
            }

            $this->f3->set("objBonusUpdateUpload", $dbBonusUpdateUpload);
            if (!is_null($failedProductsSheetUrl)) {
                $this->f3->set("failedProductsSheetUrl", "/" . $failedProductsSheetUrl);
            }

            $dbBonusUpdateUpload->update();

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "";
            $this->webResponse->data = View::instance()->render('app/products/bonus/uploadResult.php');
            echo $this->webResponse->jsonResponse();;
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
        }
    }

    function getBulkAddUpload()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vModule_bulk_add_title');
            $this->webResponse->data = View::instance()->render('app/products/bulkAdd/upload.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function getBulkAddDownload()
    {
        if ($this->f3->ajax()) {
            // Setup excel sheet
            $sheetnameUserInput = 'User Input';
            $sheetnameDatabaseInput = 'Database Input';
            $sheetnameVariables = 'Variables';

            // Prepare data for variables sheet
            $arrScientificName = [
                ['Name', 'Value']
            ];
            $arrCountry = [
                ['Name', 'Value']
            ];
            $arrSubcategory = [
                ['Name', 'Value']
            ];
            $arrIngredient = [
                ['Name', 'Value']
            ];

            $dbScientificName = new BaseModel($this->db, "scientificName");
            $allScientificName = $dbScientificName->findAll("name asc");

            $scientificNum = 2;
            foreach ($allScientificName as $scientificName) {
                $scientificNum++;
                $arrScientificName[] = array($scientificName['name'], $scientificName['id']);
            }

            $dbCountry = new BaseModel($this->db, "country");
            $dbCountry->name = "name_" . $this->objUser->language;
            $allCountry = $dbCountry->findAll("name asc");

            $countryNum = 2;
            foreach ($allCountry as $country) {
                $countryNum++;
                $arrCountry[] = array($country['name'], $country['id']);
            }

            $dbSubcategory = new BaseModel($this->db, "subcategory");
            $dbSubcategory->name = "name_" . $this->objUser->language;
            $allSubcategory = $dbSubcategory->findAll("name asc");

            $subcategoryNum = 2;
            foreach ($allSubcategory as $subcategory) {
                $subcategoryNum++;
                $arrSubcategory[] = array($subcategory['name'], $subcategory['id']);
            }

            $dbIngredient = new BaseModel($this->db, "ingredient");
            $dbIngredient->name = "name_" . $this->objUser->language;
            $allIngredient = $dbIngredient->findAll("name asc");

            $ingredientNum = 2;
            foreach ($allIngredient as $ingredient) {
                $ingredientNum++;
                $arrIngredient[] = array($ingredient['name'], $ingredient['id']);
            }

            $sampleFilePath = 'app/files/samples/products-add-sample.xlsm';
            $spreadsheet = Excel::loadFile($sampleFilePath);

            // Change active sheet to variables
            $sheet = $spreadsheet->setActiveSheetIndex(2);

            // Set dropdown variables in excel
            $sheet->fromArray($arrScientificName, NULL, 'A2', true);
            $sheet->fromArray($arrCountry, NULL, 'D2', true);
            $sheet->fromArray($arrSubcategory, NULL, 'G2', true);
            $sheet->fromArray($arrIngredient, NULL, 'J2', true);

            // Change active sheet to database input
            $sheet = $spreadsheet->setActiveSheetIndex(1);

            // Set validation and formula
            Excel::setCellFormulaVLookup($sheet, 'A3', 2505, "'User Input'!A", 'Variables!$A$3:$B$' . $scientificNum);
            Excel::setCellFormulaVLookup($sheet, 'B3', 2505, "'User Input'!B", 'Variables!$D$3:$E$' . $countryNum);
            Excel::setCellFormulaVLookup($sheet, 'P3', 2505, "'User Input'!P", 'Variables!$G$3:$H$' . $subcategoryNum);
            // Excel::setCellFormulaVLookup($sheet, 'Q3', 2505, "'User Input'!Q", 'Variables!$J$3:$K$' . $ingredientNum);

            // Hide database and variables sheet
            Excel::hideSheetByName($spreadsheet, $sheetnameDatabaseInput);
            Excel::hideSheetByName($spreadsheet, $sheetnameVariables);

            // Change active sheet to user input
            $sheet = $spreadsheet->setActiveSheetIndex(0);

            // Set data validation for dropdowns
            Excel::setDataValidation($sheet, 'A3', 'A2505', 'TYPE_LIST', 'Variables!$A$3:$A$' . $scientificNum);
            Excel::setDataValidation($sheet, 'B3', 'B2505', 'TYPE_LIST', 'Variables!$D$3:$D$' . $countryNum);
            Excel::setDataValidation($sheet, 'P3', 'P2505', 'TYPE_LIST', 'Variables!$G$3:$G$' . $subcategoryNum);
            Excel::setDataValidation($sheet, 'Q3', 'Q2505', 'TYPE_LIST', 'Variables!$J$3:$J$' . $ingredientNum);

            // Create excel sheet
            $productsSheetUrl = "files/downloads/reports/products-add/products-add-" . $this->objUser->id . "-" . time() . ".xlsm";
            Excel::saveSpreadsheetToPath($spreadsheet, $productsSheetUrl);

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "Bulk Download";
            $this->webResponse->data = "/" . $productsSheetUrl;
            echo $this->webResponse->jsonResponse();
        }
    }

    function postBulkAddUpload()
    {
        $fileName = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_FILENAME);
        $ext = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_EXTENSION);
        // basename($_FILES["file"]["name"])

        $targetFile = "files/uploads/reports/products-add/" . $this->objUser->id . "-" . $fileName . "-" . time() . ".$ext";

        if ($ext == "xlsm" || $ext == "xlsx") {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                $dbBulkAddUpload = new BaseModel($this->db, "bulkAddUpload");
                $dbBulkAddUpload->userId = $this->objUser->id;
                $dbBulkAddUpload->filePath = $targetFile;
                $dbBulkAddUpload->entityId = $this->objUser->entityId;
                $dbBulkAddUpload->addReturnID();
                echo "OK";
            }
        }
    }

    function postBulkAddUploadProcess()
    {
        ini_set('memory_limit', -1);
        ini_set('max_execution_time', 1000);
        ini_set('mysql.connect_timeout', 1000);

        $dbBulkAddUpload = new BaseModel($this->db, "bulkAddUpload");

        $dbBulkAddUpload->getByField("userId", $this->objUser->id, "insertDateTime desc");

        try {
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $entityId = $arrEntityId;

            $spreadsheet = Excel::loadFile($dbBulkAddUpload->filePath);

            // Change active sheet to database input
            $sheet = $spreadsheet->setActiveSheetIndex(1);

            // Get all scientific names
            $dbScientificName = new BaseModel($this->db, "scientificName");
            $allScientificName = $dbScientificName->findAll("name asc");

            $allScientificId = [];
            $mapScientificIdName = [];
            foreach ($allScientificName as $scientificName) {
                array_push($allScientificId, $scientificName['id']);
                $mapScientificIdName[$scientificName['id']] = $scientificName['name'];
            }

            // Get all countries
            $dbCountry = new BaseModel($this->db, "country");
            $dbCountry->name = "name_" . $this->objUser->language;
            $allCountry = $dbCountry->findAll("name asc");

            $allCountryId = [];
            $mapCountryIdName = [];
            foreach ($allCountry as $country) {
                array_push($allCountryId, $country['id']);
                $mapCountryIdName[$country['id']] = $country['name'];
            }

            // Get all subcategories
            $dbSubcategory = new BaseModel($this->db, "subcategory");
            $dbSubcategory->name = "name_" . $this->objUser->language;
            $allSubcategory = $dbSubcategory->findAll("name asc");

            $allSubcategoryId = [];
            $mapSubcategoryIdName = [];
            $mapSubcategoryIdCategoryId = [];
            foreach ($allSubcategory as $subcategory) {
                array_push($allSubcategoryId, $subcategory['id']);
                $mapSubcategoryIdCategoryId[$subcategory['id']] = $subcategory['categoryId'];
                $mapSubcategoryIdName[$subcategory['id']] = $subcategory['name'];
            }

            // Get all ingredients
            $dbIngredient = new BaseModel($this->db, "ingredient");
            $dbIngredient->name = "name_" . $this->objUser->language;
            $allIngredient = $dbIngredient->findAll("name asc");

            $allIngredientId = [];
            $mapIngredientIdName = [];
            foreach ($allIngredient as $ingredient) {
                array_push($allIngredientId, $ingredient['id']);
                $mapIngredientIdName[$ingredient['id']] = $ingredient['name'];
            }

            $fields = [
                "A" => "scientificNameId",
                "B" => "madeInCountryId",
                "C" => "name_en",
                "D" => "name_ar",
                "E" => "subtitle_ar",
                "F" => "subtitle_en",
                "G" => "description_ar",
                "H" => "description_en",
                "I" => "unitPrice",
                "J" => "vat",
                "K" => "stock",
                "L" => "maximumOrderQuantity",
                "M" => "manufacturerName",
                "N" => "batchNumber",
                "O" => "itemCode",
                "P" => "subcategoryId",
                "Q" => "activeIngredientsId",
                "R" => "expiryDate",
                "S" => "strength"
            ];

            $successProducts = [];
            $failedProducts = [];

            $allErrors = [];
            $multiActiveIngredients = [];

            $dbBulkAddUpload->recordsCount = 0;
            $successRecords = 0;
            $failedRecords = 0;
            $unchangedRecords = 0;

            $firstRow = true;
            $secondRow = false;
            $finished = false;
            foreach ($sheet->getRowIterator() as $row) {
                if ($firstRow) {
                    $firstRow = false;
                    $secondRow = true;
                    continue;
                } else if ($secondRow) {
                    $secondRow = false;
                    continue;
                }

                $dbProduct = new BaseModel($this->db, "product");
                $dbEntityProduct = new BaseModel($this->db, "entityProductSell");
                $dbProductIngredient = new BaseModel($this->db, "productIngredient");

                $product = [];

                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);

                $errors = [];

                foreach ($cellIterator as $cell) {
                    $cellLetter = $cell->getColumn();
                    $cellValue = $cell->getCalculatedValue();

                    if ($cellValue === "#REF!") {
                        $cellValue = $cell->getOldCalculatedValue();
                    }

                    array_push($product, $cellValue);

                    switch ($cellLetter) {
                        case "A":
                            if ($cellValue != "#N/A") {
                                if (!in_array($cellValue, $allScientificId)) {
                                    array_push($errors, "Scientific Name is invalid");
                                } else {
                                    $dbProduct->scientificNameId = $cellValue;
                                }
                            }
                            break;
                        case "B":
                            if (!in_array($cellValue, $allCountryId)) {
                                $finished = true;
                            } else {
                                $dbProduct->madeInCountryId = $cellValue;
                            }
                            break;
                        case "C":
                            if (strlen($cellValue) == 0) {
                                array_push($errors, "Brand Name AR required");
                            } else {
                                if (strlen($cellValue) < 4 || strlen($cellValue) > 200) {
                                    array_push($errors, "Brand Name AR should be between 4 and 200 characters");
                                } else {
                                    $dbProduct->name_ar = $cellValue;
                                }
                            }
                            break;
                        case "D":
                            if (strlen($cellValue) == 0) {
                                array_push($errors, "Brand Name EN required");
                            } else {
                                if (strlen($cellValue) < 4 || strlen($cellValue) > 200) {
                                    array_push($errors, "Brand Name EN should be between 4 and 200 characters");
                                } else {
                                    $dbProduct->name_en = $cellValue;
                                    $dbProduct->name_fr = $cellValue;
                                }
                            }
                            break;
                        case "E":
                            if (strlen($cellValue) != 0) {
                                if (strlen($cellValue) < 4 || strlen($cellValue) > 200) {
                                    array_push($errors, "Subtitle AR should be between 4 and 200 characters");
                                } else {
                                    $dbProduct->subtitle_ar = $cellValue;
                                }
                            }
                            break;
                        case "F":
                            if (strlen($cellValue) != 0) {
                                if (strlen($cellValue) < 4 || strlen($cellValue) > 200) {
                                    array_push($errors, "Subtitle EN should be between 4 and 200 characters");
                                } else {
                                    $dbProduct->subtitle_en = $cellValue;
                                    $dbProduct->subtitle_fr = $cellValue;
                                }
                            }
                            break;
                        case "G":
                            if (strlen($cellValue) != 0) {
                                if (strlen($cellValue) < 4 || strlen($cellValue) > 5000) {
                                    array_push($errors, "Description AR should be between 4 and 5000 characters");
                                } else {
                                    $dbProduct->description_ar = $cellValue;
                                }
                            }
                            break;
                        case "H":
                            if (strlen($cellValue) != 0) {
                                if (strlen($cellValue) < 4 || strlen($cellValue) > 5000) {
                                    array_push($errors, "Description EN should be between 4 and 5000 characters");
                                } else {
                                    $dbProduct->description_en = $cellValue;
                                    $dbProduct->description_fr = $cellValue;
                                }
                            }
                            break;
                        case "I":
                            if (!is_numeric($cellValue) || (float) $cellValue <= 0) {
                                array_push($errors, "Unit Price must be a positive number not null");
                            } else {
                                $dbEntityProduct->unitPrice = round((float)$cellValue, 2);
                            }
                            break;
                        case "J":
                            if (!is_numeric($cellValue) || (float) $cellValue < 0 || (float) $cellValue > 100) {
                                array_push($errors, "VAT must be a positive number between 0 and 100");
                            } else {
                                if (strpos($cell->getFormattedValue(), '%') !== false) {
                                    $cellValue *= 100;
                                }
                                $dbEntityProduct->vat = round((float) $cellValue, 2);
                            }
                            break;
                        case "K":
                            if (!(is_numeric($cellValue) && (int) $cellValue == $cellValue) || $cellValue < 0) {
                                array_push($errors, "Available Quantity must be a positive whole number");
                            } else {
                                $dbEntityProduct->stock = (int) $cellValue;
                            }
                            break;
                        case "L":
                            if (strlen($cellValue) > 0) {
                                if (!(is_numeric($cellValue) && (int) $cellValue == $cellValue) || $cellValue < 0) {
                                    array_push($errors, "Maximum Order Quantity must be a positive whole number");
                                } else {
                                    $dbEntityProduct->maximumOrderQuantity = (int) $cellValue;
                                }
                            }
                            break;
                        case "M":
                            if (strlen($cellValue) != 0) {
                                if (strlen($cellValue) < 4 || strlen($cellValue) > 200) {
                                    array_push($errors, "Manufacturer Name should be between 4 and 200 characters");
                                } else {
                                    $dbProduct->manufacturerName = $cellValue;
                                }
                            }
                            break;
                        case "N":
                            if (strlen($cellValue) != 0) {
                                $dbProduct->batchNumber = $cellValue;
                            }
                            break;
                        case "O":
                            if (strlen($cellValue) != 0) {
                                $dbProduct->itemCode = $cellValue;
                            }
                            break;
                        case "P":
                            if (!in_array($cellValue, $allSubcategoryId)) {
                                array_push($errors, "Category - Subcategory invalid");
                            } else {
                                $dbProduct->subcategoryId = $cellValue;
                                $dbProduct->categoryId = $mapSubcategoryIdCategoryId[$cellValue];
                            }
                            break;
                        case "Q":
                            $valid = true;
                            if (strlen($cellValue) > 0) {
                                $activeIngredientsTemp = explode(", ", $cellValue);
                                foreach ($activeIngredientsTemp as $ingredient) {
                                    if (!in_array($ingredient, $allIngredientId)) {
                                        array_push($errors, "Active Ingredients invalid");
                                        $valid = false;
                                        break;
                                    }
                                }
                            }
                            $activeIngredients = $cellValue;
                            break;
                        case "R":
                            if (!is_null($cellValue)) {
                                if (!is_int($cellValue) && !is_float($cellValue)) {
                                    array_push($errors, "Expiry Date must fit a date format (mm/dd/yyyy)");
                                } else {
                                    $expiryDate = Excel::excelDateToRegularDate($cellValue, "m/d/Y");
                                    $dbProduct->expiryDate = $expiryDate;
                                }
                            }
                            break;
                        case "S":
                            if (strlen($cellValue) != 0) {
                                if (strlen($cellValue) < 4 || strlen($cellValue) > 200) {
                                    array_push($errors, "Strength should be between 4 and 200 characters");
                                } else {
                                    $dbProduct->strength = $cellValue;
                                }
                            }
                            break;
                    }
                }

                if ($finished) {
                    break;
                }

                $dbBulkAddUpload->recordsCount++;

                if (count($errors) === 0) {
                    $dbProduct->addReturnID();

                    $dbEntityProduct->productId = $dbProduct->id;
                    $dbEntityProduct->entityId = $entityId;
                    $dbEntityProduct->stockStatusId = 1;
                    $dbEntityProduct->bonusTypeId = 1;
                    $dbEntityProduct->stockUpdateDateTime = $dbEntityProduct->getCurrentDateTime();
                    $dbEntityProduct->add();

                    if (strlen($activeIngredients) > 0) {
                        $arrActiveIngredients = explode(", ", $activeIngredients);
                        foreach ($arrActiveIngredients as $ingredient) {
                            $dbProductIngredient->productId = $dbProduct->id;
                            $dbProductIngredient->ingredientId = $ingredient;
                            $dbProductIngredient->add();
                        }
                    }

                    array_push($successProducts, $product);
                    $successRecords++;
                } else {
                    array_push($failedProducts, $product);
                    array_push($allErrors, $errors);
                    array_push($multiActiveIngredients, [$activeIngredients]);
                    $failedRecords++;
                }
            }

            if (count($failedProducts) > 0) {
                // Setup excel sheet
                $sheetnameUserInput = 'User Input';
                $sheetnameDatabaseInput = 'Database Input';
                $sheetnameVariables = 'Variables';

                // Prepare data for variables sheet
                $arrScientificName = [
                    ['Name', 'Value']
                ];
                $arrCountry = [
                    ['Name', 'Value']
                ];
                $arrSubcategory = [
                    ['Name', 'Value']
                ];
                $arrIngredient = [
                    ['Name', 'Value']
                ];

                $scientificNum = 2;
                foreach ($allScientificName as $scientificName) {
                    $scientificNum++;
                    $arrScientificName[] = array($scientificName['name'], $scientificName['id']);
                }

                $countryNum = 2;
                foreach ($allCountry as $country) {
                    $countryNum++;
                    $arrCountry[] = array($country['name'], $country['id']);
                }

                $subcategoryNum = 2;
                foreach ($allSubcategory as $subcategory) {
                    $subcategoryNum++;
                    $arrSubcategory[] = array($subcategory['name'], $subcategory['id']);
                }

                $ingredientNum = 2;
                foreach ($allIngredient as $ingredient) {
                    $ingredientNum++;
                    $arrIngredient[] = array($ingredient['name'], $ingredient['id']);
                }

                $sampleFilePath = 'app/files/samples/products-add-sample.xlsm';
                $spreadsheet = Excel::loadFile($sampleFilePath);

                // Change active sheet to variables
                $sheet = $spreadsheet->setActiveSheetIndex(2);

                // Set dropdown variables in excel
                $sheet->fromArray($arrScientificName, NULL, 'A2', true);
                $sheet->fromArray($arrCountry, NULL, 'D2', true);
                $sheet->fromArray($arrSubcategory, NULL, 'G2', true);
                $sheet->fromArray($arrIngredient, NULL, 'J2', true);

                // Change active sheet to database input
                $sheet = $spreadsheet->setActiveSheetIndex(1);

                // Set validation and formula
                Excel::setCellFormulaVLookup($sheet, 'A3', 2505, "'User Input'!A", 'Variables!$A$3:$B$' . $scientificNum);
                Excel::setCellFormulaVLookup($sheet, 'B3', 2505, "'User Input'!B", 'Variables!$D$3:$E$' . $countryNum);
                Excel::setCellFormulaVLookup($sheet, 'P3', 2505, "'User Input'!P", 'Variables!$G$3:$H$' . $subcategoryNum);
                // Excel::setCellFormulaVLookup($sheet, 'Q3', 2505, "'User Input'!Q", 'Variables!$J$3:$K$' . $ingredientNum);

                // Hide database and variables sheet
                Excel::hideSheetByName($spreadsheet, $sheetnameDatabaseInput);
                Excel::hideSheetByName($spreadsheet, $sheetnameVariables);

                // Change active sheet to user input
                $sheet = $spreadsheet->setActiveSheetIndex(0);

                // Set data validation for dropdowns
                Excel::setDataValidation($sheet, 'A3', 'A2505', 'TYPE_LIST', 'Variables!$A$3:$A$' . $scientificNum);
                Excel::setDataValidation($sheet, 'B3', 'B2505', 'TYPE_LIST', 'Variables!$D$3:$D$' . $countryNum);
                Excel::setDataValidation($sheet, 'P3', 'P2505', 'TYPE_LIST', 'Variables!$G$3:$G$' . $subcategoryNum);
                Excel::setDataValidation($sheet, 'Q3', 'Q2505', 'TYPE_LIST', 'Variables!$J$3:$J$' . $ingredientNum);

                $sheet->setCellValue('T2', 'Error');
                $sheet->getStyle('T2')->applyFromArray(Excel::STYlE_CENTER_BOLD_BORDER_THICK);

                // Add all products to multidimensional array
                $multiProducts = [];
                $fields = [
                    "scientificNameId",
                    "madeInCountryId",
                    "name_en",
                    "name_ar",
                    "subtitle_ar",
                    "subtitle_en",
                    "description_ar",
                    "description_en",
                    "unitPrice",
                    "vat",
                    "stock",
                    "maximumOrderQuantity",
                    "manufacturerName",
                    "batchNumber",
                    "itemCode",
                    "subcategoryId",
                    "activeIngredientsId",
                    "expiryDate",
                    "strength"
                ];
                $i = 3;
                for ($i = 0; $i < count($failedProducts); $i++) {
                    $product = $failedProducts[$i];
                    $singleProduct = [];
                    $j = 0;
                    foreach ($fields as $field) {
                        $cellValue = "";
                        if ($field == "scientificNameId") {
                            $cellValue = $mapScientificIdName[$product[$j]];
                        } else if ($field == "madeInCountryId") {
                            $cellValue = $mapCountryIdName[$product[$j]];
                        } else if ($field == "subcategoryId") {
                            $cellValue = $mapSubcategoryIdName[$product[$j]];
                        } else if ($field == "activeIngredientsId") {
                            if (strlen($product[$j]) > 0) {
                                $activeIngredientsId = explode(", ", $product[$j]);
                                $activeIngredientsName = [];
                                foreach ($activeIngredientsId as $ingredientId) {
                                    array_push($activeIngredientsName, $mapIngredientIdName[$ingredientId]);
                                }
                                $cellValue = implode(", ", $activeIngredientsName);
                            } else {
                                $cellValue = $product[$j];
                            }
                        } else {
                            $cellValue = $product[$j];
                        }
                        array_push($singleProduct, $cellValue);
                        $j++;
                    }
                    $errors = $allErrors[$i];
                    $error = join(", ", $errors);
                    array_push($singleProduct, $error);

                    array_push($multiProducts, $singleProduct);
                }
                // Fill rows with products
                $sheet->fromArray($multiProducts, NULL, 'A3', true);

                // Change active sheet to user input
                $sheet = $spreadsheet->setActiveSheetIndex(1);

                // Fill rows with active ingredients column
                $sheet->fromArray($multiActiveIngredients, NULL, 'T3', true);

                // Change active sheet to user input
                $sheet = $spreadsheet->setActiveSheetIndex(0);

                // Create excel sheet
                $failedProductsSheetUrl = "files/downloads/reports/products-add/products-add-" . $this->objUser->id . "-" . time() . ".xlsm";
                Excel::saveSpreadsheetToPath($spreadsheet, $failedProductsSheetUrl);
            }

            // Update logs
            if (count($successProducts) > 0) {
                $dbBulkAddUpload->successLog = json_encode($successProducts);
            }

            if (count($failedProducts) > 0) {
                $dbBulkAddUpload->failedLog = json_encode($failedProducts);
            }

            // Update counts and rates
            $dbBulkAddUpload->completedCount = $successRecords;
            $dbBulkAddUpload->failedCount = $failedRecords;

            if ($successRecords + $failedRecords !== 0) {
                $dbBulkAddUpload->importSuccessRate = round($successRecords / ($successRecords + $failedRecords), 2) * 100;
                $dbBulkAddUpload->importFailureRate = round($failedRecords / ($successRecords + $failedRecords), 2) * 100;
            } else {
                $dbBulkAddUpload->importSuccessRate = 0;
                $dbBulkAddUpload->importFailureRate = 0;
            }

            $this->f3->set("objBulkAddUpload", $dbBulkAddUpload);
            if (!is_null($failedProductsSheetUrl)) {
                $this->f3->set("failedProductsSheetUrl", "/" . $failedProductsSheetUrl);
            }

            $dbBulkAddUpload->update();

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "";
            $this->webResponse->data = View::instance()->render('app/products/bulkAdd/uploadResult.php');
            echo $this->webResponse->jsonResponse();
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
        }
    }

    function getBulkAddImageUpload()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $this->webResponse->errorCode = 1;
            $this->webResponse->title = $this->f3->get('vModule_bulk_add_image_title');
            $this->webResponse->data = View::instance()->render('app/products/bulkAddImage/upload.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function postBulkAddImageUpload()
    {
        $mapNewOldFileName = [];
        $error = false;

        $fileCount = count($_FILES["file"]["name"]);
        for ($i = 0; $i < $fileCount; $i++) {
            $success = false;

            $fileName = pathinfo(basename($_FILES["file"]["name"][$i]), PATHINFO_FILENAME);
            $ext = pathinfo(basename($_FILES["file"]["name"][$i]), PATHINFO_EXTENSION);

            $newFileName = $fileName . "-" . time() . ".$ext";
            $targetFile = "assets/img/products/" . $newFileName;

            if ($ext == "png" || $ext == "jpg" || $ext == "jpeg") {
                if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], $targetFile)) {
                    $mapNewOldFileName[$newFileName] = $fileName;
                    $success = true;
                }
            }

            if (!$success) {
                $error = true;
                break;
            }
        }

        if (!$error) {
            echo json_encode($mapNewOldFileName);
        }
    }

    function postBulkAddImageUploadProcess()
    {
        $mapNewOldFileNameStr = $this->f3->get('POST.mapNewOldFileName');
        $mapNewOldFileName = json_decode($mapNewOldFileNameStr);

        // Get all product ids
        $allProductId = [];
        $dbProduct = new BaseModel($this->db, "product");
        $dbProduct->name = "name_" . $this->objUser->language;
        $allProduct = $dbProduct->findAll("id asc");

        // Check if file name starts with an existing product id
        $mapFileNameProduct = [];
        foreach ($mapNewOldFileName as $newFileName => $oldFileName) {
            $product = null;
            $allParts = explode("-", $oldFileName);
            if (count($allParts) > 1 && filter_var($allParts[0], FILTER_VALIDATE_INT)) {
                $productIdInitial = (int)$allParts[0];
                foreach ($allProduct as $prod) {
                    if ($productIdInitial == $prod['id']) {
                        $product = new stdClass();
                        $product->id = $prod['id'];
                        $product->name = $prod['name'];
                        break;
                    }
                }
            }

            $mapFileNameProduct[$newFileName] = $product;
        }

        $this->f3->set("mapFileNameProduct", $mapFileNameProduct);

        $this->webResponse->errorCode = 1;
        $this->webResponse->title = "";
        $this->webResponse->data = View::instance()->render('app/products/bulkAddImage/uploadResult.php');
        echo $this->webResponse->jsonResponse();
    }

    function getProductList()
    {
        $this->handleGetListFilters("product", ['name_en', 'name_fr', 'name_ar'], 'name_' . $this->objUser->language);
    }

    function postBulkAddImage()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/distributor/product/bulk/add/image/upload");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $mapProductIdImage = $this->f3->get('POST.mapProductIdImage');

            $dbProduct = new BaseModel($this->db, "product");
            foreach ($mapProductIdImage as $productId => $image) {
                $dbProduct->getWhere("id = $productId");
                if (!$dbProduct->dry()) {
                    $dbProduct->image = $image;
                    $dbProduct->update();
                }
            }

            echo $this->webResponse->jsonResponseV2(1, "Success", $this->f3->get('vResponse_imagesAdded'));
        }
    }
}
