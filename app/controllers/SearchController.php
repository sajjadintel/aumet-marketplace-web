<?php

class SearchController extends Controller
{
    function getSearchProducts()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {

            global $dbConnection;

            $dbEntities = new BaseModel($dbConnection, "entity");
            $dbEntities->name = "name_ar";
            $arrEntities = $dbEntities->getWhere("typeId=10", "name_ar");
            $this->f3->set('arrEntities', $arrEntities);

            $dbStockStatus = new BaseModel($dbConnection, "stockStatus");
            $dbStockStatus->name = "name_" . $this->objUser->language;
            $arrStockStatus = $dbStockStatus->all("id asc");
            $this->f3->set('arrStockStatus', $arrStockStatus);

            $this->webResponse->errorCode = 1;
            $this->webResponse->title = $this->f3->get('vModule_search_title');
            $this->webResponse->data = View::instance()->render('app/products/search/search.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function handleGetListFilters($table, $queryTerms, $queryDisplay, $queryId = 'id', $additionalQuery = null)
    {
        $where = "";
        if ($additionalQuery != null) {
            $where = $additionalQuery;
        }
        $term = $_GET['term'];
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
        $dbNames->getWhere($where, $queryDisplay, $pageSize, $page * $pageSize);
        $resultsCount = 0;
        while (!$dbNames->dry()) {
            $resultsCount++;
            $select2ResultItem = new stdClass();
            $select2ResultItem->id = $dbNames[$queryId];
            $select2ResultItem->text = $dbNames[$queryDisplay];
            $select2Result->results[] = $select2ResultItem;
            $dbNames->next();
        }

        if ($resultsCount >= $pageSize) {
            $select2Result->pagination = true;
        }

        $this->webResponse->errorCode = 1;
        $this->webResponse->title = "";
        $this->webResponse->data = $select2Result;
        echo $this->webResponse->jsonResponse();
    }

    function getProductBrandNameList()
    {
        if ($this->f3->ajax()) {
            $where = "";
            $term = $_GET['term'];
            if (isset($term) && $term != "" && $term != null) {
                $where = "name_en like '%$term%'";
            }
            $page = $_GET['page'];
            if (isset($page) && $page != "" && $page != null && is_numeric($page)) {
                $page = $page - 1;
            } else {
                $page = 0;
            }

            $pageSize = 10;

            global $dbConnection;

            $select2Result = new stdClass();
            $select2Result->results = [];
            $select2Result->pagination = false;

            $dbProducts = new BaseModel($dbConnection, "product");
            $dbProducts->name = "name_en";
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

            $this->webResponse->errorCode = 1;
            $this->webResponse->title = "";
            $this->webResponse->data = $select2Result;
        } else {
            $this->webResponse->errorCode = 1;
        }
        echo $this->webResponse->jsonResponse();
    }

    function getProductScientificNameList()
    {
        $this->handleGetListFilters("scientificName", 'name', 'name');
    }

    function getProductCountryList()
    {
        $this->handleGetListFilters("country", ['name_en', 'name_fr', 'name_ar'], 'name_' . $this->objUser->language);
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

    function postSearchProducts()
    {
        ## Read values from Datatables
        $datatable = new Datatable($_POST);
        $query = "1=1 ";

        $fullQuery = $query;

        if (is_array($datatable->query)) {
            $productId = $datatable->query['productId'];
            if (isset($productId) && is_array($productId)) {
                $query .= " AND id in (" . implode(",", $productId) . ")";
            }

            $scientificNameId = $datatable->query['scientificNameId'];
            if (isset($scientificNameId) && is_array($scientificNameId)) {
                $query .= " AND scientificNameId in (" . implode(",", $scientificNameId) . ")";
            }

            $entityId = $datatable->query['entityId'];
            if (isset($entityId) && is_array($entityId)) {
                $query .= " AND entityId in (" . implode(",", $entityId) . ")";
            }

            $stockOption = $datatable->query['stockOption'];
            if (isset($stockOption) && $stockOption == 1) {
                $query .= " AND stockStatusId = 1 ";
            }
        }

        $dbProducts = new BaseModel($this->db, "vwEntityProductSell");

        $data = [];

        $totalRecords = $dbProducts->count($fullQuery);
        $totalFiltered = $dbProducts->count($query);
        $data = $dbProducts->findWhere($query, "$datatable->sortBy $datatable->sortByOrder", $datatable->limit, $datatable->offset);

        $allProductId = [];
        foreach($data as $product) {
            array_push($allProductId, $product['id']);
        }
        $allProductId = implode(",", $allProductId);

        $dbCartDetail = new BaseModel($this->db, "cartDetail");
        $arrCartDetail = $dbCartDetail->getByField("accountId", $this->objUser->accountId);

        $dbBonus = new BaseModel($this->db, "entityProductSellBonusDetail");
        $arrBonus = $dbBonus->findWhere("entityProductId IN ($allProductId) AND isActive = 1");

        $mapProductIdBonuses = [];

        foreach($arrBonus as $bonus) {
            $productId = $bonus['entityProductId'];
            $allBonuses = [];
            if(array_key_exists($productId, $mapProductIdBonuses)) {
                $allBonuses = $mapProductIdBonuses[$productId];
            }
            array_push($allBonuses, $bonus);
            $mapProductIdBonuses[$productId] = $allBonuses;
        }

        for ($i = 0; $i < count($data); $i++) {
            if ($data[$i]['bonusTypeId'] == 2) {
                $data[$i]['bonusOptions'] = json_decode($data[$i]['bonusConfig']);
                $data[$i]['bonuses'] = $mapProductIdBonuses[$data[$i]['id']];
            }

            $quantityFree = 0;
            $data[$i]['cart'] = 0;
            if (is_array($arrCartDetail) || is_object($arrCartDetail)) {
                foreach ($arrCartDetail as $objCartItem) {
                    if ($objCartItem['entityProductId'] == $data[$i]['id']) {
                        $data[$i]['cart'] += $objCartItem['quantity'];
                        $data[$i]['cart'] += $objCartItem['quantityFree'];
                        $quantityFree = $objCartItem['quantityFree'];
                        break;
                    }
                }
            }

            $data[$i]['activeBonus'] = null;
            if($quantityFree > 0) {
                $allBonuses = $data[$i]['bonuses'];
                foreach($allBonuses as $bonus) {
                    if($bonus['bonus'] === $quantityFree) {
                        $data[$i]['activeBonus'] = $bonus;
                        break;
                    }
                }
            }
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
}
