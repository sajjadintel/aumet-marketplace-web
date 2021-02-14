<?php

class SearchController extends Controller
{
    function getSearchProducts()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $dbStockStatus = new BaseModel($this->db, "stockStatus");
            $dbStockStatus->name = "name_" . $this->objUser->language;
            $arrStockStatus = $dbStockStatus->all("id asc");
            $this->f3->set('arrStockStatus', $arrStockStatus);
            $this->f3->set('objUser', $this->objUser);

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vModule_search_title');
            $this->webResponse->data = View::instance()->render('app/products/search/search.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function handleSearchBar()
    {
        $where = "1=1 ";
        $term = addslashes($_GET['query']);
        if (isset($term) && $term != "" && $term != null) {
            $where .= "AND ( scientificName LIKE '%{$term}%'";
            $where .= " OR entityName_ar LIKE '%{$term}%'";
            $where .= " OR entityName_en LIKE '%{$term}%'";
            $where .= " OR entityName_fr LIKE '%{$term}%'";
            $where .= " OR productName_ar LIKE '%{$term}%'";
            $where .= " OR productName_en LIKE '%{$term}%'";
            $where .= " OR productName_fr LIKE '%{$term}%' ) ";
        }

        $roleId = $this->f3->get('SESSION.objUser')->roleId;

        // if distributor
        if (Helper::isDistributor($roleId)) {
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $where .= " AND entityId IN ($arrEntityId)";
        }

        $page = $_GET['page'];
        if (isset($page) && $page != "" && $page != null && is_numeric($page)) {
            $page = $page - 1;
        } else {
            $page = 0;
        }

        $pageSize = 10;

        $select2Result = [];

        $queryDisplay = 'productName_' . $this->objUser->language;

        $dbNames = new BaseModel($this->db, 'vwEntityProductSellSummary');
        $dbNames->load(array($where), array('order' => $queryDisplay, 'limit' => $pageSize, 'offset' => $page * $pageSize, 'group' => $queryDisplay));
        $resultsCount = 0;
        while (!$dbNames->dry()) {
            $resultsCount++;
            $select2ResultItem = new stdClass();
            $select2ResultItem->data = $dbNames['id'];
            $select2ResultItem->value = $dbNames[$queryDisplay];
            $select2Result[] = $select2ResultItem;
            $dbNames->next();
        }

        $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
        $this->webResponse->title = "";
        $this->webResponse->suggestions = $select2Result;
        echo $this->webResponse->jsonResponse();
    }

    function handleGetListFilters($table, $queryTerms, $queryDisplay, $queryId = 'id', $additionalQuery = null)
    {
        $where = "";
        $nameAsValue = isset($_GET['nameAsValue']);
        if ($additionalQuery != null) {
            $where = $additionalQuery;
        }
        $term = addslashes(trim($_GET['term']));
        if (isset($term) && $term != "" && $term != null) {
            if ($additionalQuery != null) {
                $where .= " AND (";
            }
            if (is_array($queryTerms)) {
                $i = 0;
                foreach ($queryTerms as $queryTerm) {
                    if ($i != 0) {
                        $where .= ' OR ';
                    }
                    $where .= "$queryTerm LIKE '%$term%'";
                    $i++;
                }
            } else {
                $where .= "$queryTerms LIKE '%$term%'";
            }
            if ($additionalQuery != null) {
                $where .= ")";
            }
        }
        $page = $_GET['page'];
        if (isset($page) && $page != "" && $page != null && is_numeric($page)) {
            $page = $page - 1;
        } else {
            $page = 0;
        }

        $pageSize = 10;

        $select2Result = new stdClass();
        $select2Result->results = [];
        $select2Result->pagination = false;

        $dbNames = new BaseModel($this->db, $table);
        $dbNames->load(array($where), array('order' => $queryDisplay, 'limit' => $pageSize, 'offset' => $page * $pageSize, 'group' => $queryDisplay));

        $resultsCount = 0;
        while (!$dbNames->dry()) {
            $resultsCount++;
            $select2ResultItem = new stdClass();
            $select2ResultItem->id = $nameAsValue ? $dbNames[$queryDisplay] : $dbNames[$queryId];
            $select2ResultItem->text = $dbNames[$queryDisplay];
            $select2Result->results[] = $select2ResultItem;
            $dbNames->next();
        }

        if ($resultsCount >= $pageSize) {
            $select2Result->pagination = true;
        }

        $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
        $this->webResponse->title = "";
        $this->webResponse->data = $select2Result;
        echo $this->webResponse->jsonResponse();
    }

    function getProductBrandNameList()
    {
        if ($this->f3->ajax()) {
            $nameAsValue = isset($_GET['nameAsValue']);

            $where = "1=1";
            $term = addslashes(trim($_GET['term']));
            if (isset($term) && $term != "" && $term != null) {
                $where .= " AND productName_" . $this->objUser->language . " like '%$term%'";
            }
            $page = $_GET['page'];
            if (isset($page) && $page != "" && $page != null && is_numeric($page)) {
                $page = $page - 1;
            } else {
                $page = 0;
            }
            $pageSize = 10;

            $roleId = $this->f3->get('SESSION.objUser')->roleId;
            if (Helper::isDistributor($roleId)) {
                $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
                $where .= " AND entityId IN ($arrEntityId)";
            }


            $select2Result = new stdClass();
            $select2Result->results = [];
            $select2Result->pagination = false;

            $dbProducts = new BaseModel($this->db, "vwEntityProductSell");
            $dbProducts->productName = "productName_" . $this->objUser->language;

            $dbProducts->load(array($where), array('order' => "productName_" . $this->objUser->language, 'limit' => $pageSize, 'offset' => $page * $pageSize, 'group' => "productName_" . $this->objUser->language));

            $resultsCount = 0;
            while (!$dbProducts->dry()) {
                $resultsCount++;
                $select2ResultItem = new stdClass();
                $select2ResultItem->id = $nameAsValue ? trim($dbProducts->productName) : $dbProducts->id;
                $select2ResultItem->text = trim($dbProducts->productName);
                $select2Result->results[] = $select2ResultItem;
                $dbProducts->next();
            }

            if ($resultsCount >= $pageSize) {
                $select2Result->pagination = true;
            }

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "";
            $this->webResponse->data = $select2Result;
        } else {
            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
        }
        echo $this->webResponse->jsonResponse();
    }

    function getProductScientificNameList()
    {
        $this->handleGetListFilters("scientificNameWithProduct", 'name', 'name');
    }

    function getProductCountryList()
    {
        $this->handleGetListFilters("country", ['name_en', 'name_fr', 'name_ar'], 'name_' . $this->objUser->language);
    }

    function getProductCategoryList()
    {
        $this->handleGetListFilters("category", ['name_en', 'name_fr', 'name_ar'], 'name_' . $this->objUser->language);
    }

    function getProductSubcategoryByCategoryList()
    {
        $categoryId = $this->f3->get("PARAMS.categoryId");
        $this->handleGetListFilters("subcategory", ['name_en', 'name_fr', 'name_ar'], 'name_' . $this->objUser->language, 'id', 'categoryId = ' . $categoryId);
    }

    function getProductIngredientList()
    {
        $this->handleGetListFilters("ingredient", ['name_en', 'name_fr', 'name_ar'], 'name_' . $this->objUser->language);
    }

    function getOrderBuyerList()
    {
        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
        $this->handleGetListFilters("vwEntityRelation", ['buyerName_en', 'buyerName_fr', 'buyerName_ar'], 'buyerName_' . $this->objUser->language, 'entityBuyerId', "entitySellerId IN ($arrEntityId)");
    }

    function getOrderSellerList()
    {
        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
        $this->handleGetListFilters("vwEntityRelation", ['sellerName_en', 'sellerName_fr', 'sellerName_ar'], 'sellerName_' . $this->objUser->language, 'entitySellerId', "entityBuyerId IN ($arrEntityId)");
    }

    function getAllSellerList()
    {
        $this->handleGetListFilters("entity", ['name_en', 'name_fr', 'name_ar'], 'name_' . $this->objUser->language, 'id', 'typeId=10');
    }

    function getRelationGroupByEnitityList()
    {
        $entityId = $this->f3->get("PARAMS.entityId");
        $this->handleGetListFilters("entityRelationGroup", ['name_en', 'name_fr', 'name_ar'], 'name_' . $this->objUser->language, 'id', 'entityId = ' . $entityId);
    }

    function getCategoryList()
    {
        if ($this->f3->ajax()) {
            $where = "";
            $term = addslashes(trim($_GET['term']));
            if (isset($term) && $term != "" && $term != null) {
                $where = "name_" . $this->objUser->language . " like '%$term%' AND parent_id IS NULL";
            } else {
                $where = " parent_id IS NULL";
            }

            $page = $_GET['page'];
            if (isset($page) && $page != "" && $page != null && is_numeric($page)) {
                $page = $page - 1;
            } else {
                $page = 0;
            }

            $pageSize = 10;

            $select2Result = new stdClass();
            $select2Result->results = [];
            $select2Result->pagination = false;

            $dbProducts = new BaseModel($this->db, "category");
            $dbProducts->name = "name_" . $this->objUser->language;
            $dbProducts->getWhere($where, "name_en", $pageSize, $page * $pageSize);
            $resultsCount = 0;
            while (!$dbProducts->dry()) {
                $resultsCount++;
                $select2ResultItem = new stdClass();
                $select2ResultItem->id = $dbProducts->id;
                $select2ResultItem->text = $dbProducts->name;
                $select2Result->results[] = $select2ResultItem;
                $dbProducts->next();
            }

            if ($resultsCount >= $pageSize) {
                $select2Result->pagination = true;
            }

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "";
            $this->webResponse->data = $select2Result;
        } else {
            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
        }
        echo $this->webResponse->jsonResponse();
    }

    function getAllCategoryList()
    {
        if ($this->f3->ajax()) {
            $where = "";
            $term = addslashes(trim($_GET['term']));
            if (isset($term) && $term != "" && $term != null) {
                $where = "name_" . $this->objUser->language . " like '%$term%'";
            }

            $page = $_GET['page'];
            if (isset($page) && $page != "" && $page != null && is_numeric($page)) {
                $page = $page - 1;
            } else {
                $page = 0;
            }

            $pageSize = 10;

            $select2Result = new stdClass();
            $select2Result->results = [];
            $select2Result->pagination = false;

            $dbProducts = new BaseModel($this->db, "vwCategory");
            $dbProducts->name = "name_" . $this->objUser->language;
            $dbProducts->parent_name = "parent_name_" . $this->objUser->language;
            $select2Result->results = $dbProducts->findWhere($where, "parent_id ASC, name_{$this->objUser->language} ASC", $pageSize, $page * $pageSize);
            $resultsCount = count($select2Result->results);
            //            while (!$dbProducts->dry()) {
            //                $resultsCount++;
            //                $select2ResultItem = new stdClass();
            //                $select2ResultItem->id = $dbProducts->id;
            //                $select2ResultItem->text = $dbProducts->name;
            //                $select2Result->results[] = $select2ResultItem;
            //                $select2Result->results[] = $dbProducts;
            //                $dbProducts->next();
            //            }

            if ($resultsCount >= $pageSize) {
                $select2Result->pagination = true;
            }

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "";
            $this->webResponse->data = $select2Result;
        } else {
            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
        }
        echo $this->webResponse->jsonResponse();
    }

    function postSearchProducts()
    {
        $sortParam = $this->f3->get('PARAMS.sort');

        ## Read values from Datatables
        $datatable = new Datatable($_POST);
        $query = "1=1 ";

        $fullQuery = $query;

        $roleId = $this->f3->get('SESSION.objUser')->roleId;

        // if distributor
        $isDistributor = false;
        if (Helper::isDistributor($roleId)) {
            $isDistributor = true;
        }

        if (is_array($datatable->query)) {
            $productName = $datatable->query['productName'];
            if (isset($productName) && is_array($productName)) {
                $query .= " AND (";
                foreach ($productName as $key => $value) {
                    $value = addslashes($value);
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
                    $value = addslashes($value);
                    if ($key !== 0) {
                        $query .= " OR ";
                    }
                    $query .= "scientificName LIKE '%{$value}%'";
                }
                $query .= ")";
            }

            if (!$isDistributor) {
                $entityId = $datatable->query['entityId'];
                if (isset($entityId) && is_array($entityId)) {
                    $query .= " AND entityId in (" . implode(",", $entityId) . ")";
                }
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

        $order = "$datatable->sortBy $datatable->sortByOrder";

        if ($order == "productName_en asc") {
            if ($sortParam == "newest") {
                $order = "insertDateTime DESC";
            } else if ($sortParam == "top-selling") {
                $order = "totalOrderQuantity DESC";
            }
        }

        if ($isDistributor) {
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $query .= " AND entityId IN ($arrEntityId)";
        }

        $queryParam = addslashes($datatable->query['query']);
        if ($queryParam != null && $queryParam != 'null' && trim($queryParam) != '') {
            $queryParam = trim($queryParam);
            $query .= " AND ( scientificName LIKE '%{$queryParam}%'";
            $query .= " OR productName_ar LIKE '%{$queryParam}%'";
            $query .= " OR productName_en LIKE '%{$queryParam}%'";
            $query .= " OR productName_fr LIKE '%{$queryParam}%' ) ";
        }

        $dbProducts = new BaseModel($this->db, "vwEntityProductSell");

        $totalRecords = $dbProducts->count($fullQuery);
        $totalFiltered = $dbProducts->count($query);
        $data = $dbProducts->findWhere($query, $order, $datatable->limit, $datatable->offset);

        $allProductId = [];
        foreach ($data as $product) {
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


        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['cart'] = 0;
            if (is_array($arrCartDetail) || is_object($arrCartDetail)) {
                foreach ($arrCartDetail as $objCartItem) {
                    if ($objCartItem['entityProductId'] == $data[$i]['id']) {
                        $data[$i]['cartDetailId'] += $objCartItem['id'];
                        $data[$i]['quantity'] = $objCartItem['quantity'];
                        $data[$i]['cart'] += $objCartItem['quantity'];
                        $data[$i]['cart'] += $objCartItem['quantityFree'];
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

    function getCityByCountryList()
    {
        $countryId = $this->f3->get("PARAMS.countryId");

        $dbCity = new BaseModel($this->db, "city");
        $dbCity->name = "name" . ucfirst($this->objUser->language);
        $dbCity->getWhere("countryId=$countryId", "name" . ucfirst($this->objUser->language) . " ASC");

        $arrCities = [];
        while (!$dbCity->dry()) {
            $city = new stdClass();
            $city->id = $dbCity["id"];
            $city->name = $dbCity["name"];

            array_push($arrCities, $city);

            $dbCity->next();
        }

        $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
        $this->webResponse->data = $arrCities;
        echo $this->webResponse->jsonResponse();
    }
}
