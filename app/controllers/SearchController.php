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
                    $where .= "LOWER($queryTerm) LIKE '%$term%'";
                    $i++;
                }
            } else {
                $where .= "LOWER($queryTerms) LIKE '%$term%'";
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

            /*$where .= " AND stockStatusId = 1 ";

            $where .= " AND statusId = 1";*/

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

    function getAvailableSellerList()
    {
        $this->handleGetListFilters("vwEntityProductSell", ['entityName_en', 'entityName_fr', 'entityName_ar'], 'entityName_' . $this->objUser->language, 'entityId');
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

    function getCountryByEntityList()
    {
        if ($this->f3->ajax()) {
            $arrEntityId = $this->f3->get("PARAMS.entityId");
            $dbEntityRelation = new BaseModel($this->db, "vwEntityRelation");
            $arrEntityRelation = $dbEntityRelation->findWhere("entitySellerId IN ($arrEntityId)");

            $arrCityId = [];
            foreach($arrEntityRelation as $entityRelation) {
                $buyerCityId = $entityRelation['buyerCityId'];
                if(!in_array($buyerCityId, $arrCityId)) {
                    array_push($arrCityId, $buyerCityId);
                }
            }
            
            $arrCityIdStr = "-1";
            if(count($arrCityId) > 0) {
                $arrCityIdStr = implode(",", $arrCityId);
            }

            $where = "id IN ($arrCityIdStr)";
            $term = addslashes(trim($_GET['term']));
            if (isset($term) && $term != "" && $term != null) {
                $where .= " name_" . $this->objUser->language . " like '%$term%'";
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

            $dbCountry = new BaseModel($this->db, "vwCountry");
            $dbCountry->name = "name_" . $this->objUser->language;
            $dbCountry->parent_name = "parent_name_" . $this->objUser->language;
            $select2Result->results = $dbCountry->findWhere($where, "parent_id ASC, name_{$this->objUser->language} ASC", $pageSize, $page * $pageSize);
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

    function getCustomerNameByEntityList()
    {
        if ($this->f3->ajax()) {
            $arrEntityId = $this->f3->get("PARAMS.entityId");

            $where = "entitySellerId IN ($arrEntityId)";
            $term = addslashes(trim($_GET['term']));
            if (isset($term) && $term != "" && $term != null) {
                $where .= " AND buyerName_" . $this->objUser->language . " like '%$term%'";
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

            $dbEntity = new BaseModel($this->db, "vwEntityRelation");
            $dbEntity->entityName = "buyerName_" . $this->objUser->language;
            $arrResult = $dbEntity->findWhere($where, "buyerName_{$this->objUser->language} ASC", $pageSize, $page * $pageSize);

            $resultsCount = 0;
            foreach($arrResult as $result) {
                $resultsCount++;
                $select2ResultItem = new stdClass();
                $select2ResultItem->id = trim($result['entityName']);
                $select2ResultItem->text = trim($result['entityName']);
                $select2Result->results[] = $select2ResultItem;
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

        $dbProducts = new EntityProductSell;

        $totalRecords = $dbProducts->count($fullQuery);
        $totalFiltered = $dbProducts->count($query);
        $data = $dbProducts->findWhere($query, $order, $datatable->limit, $datatable->offset);
        $data = $dbProducts->getRelatedBonuses($data, $this->objUser);

        ## Response
        $response = array(
            "draw" => intval($datatable->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $data,
            "query" => $query
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
