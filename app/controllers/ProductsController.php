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
            $productId = $this->f3->get('PARAMS.productId');

            global $dbConnection;

            $dbEntityProduct = new BaseModel($dbConnection, "vwEntityProductSell");
            $dbEntityProduct->getWhere("entityId=$entityId and productId=$productId");
            $this->f3->set('objEntityProduct', $dbEntityProduct);

            $dbEntityProductRelated = new BaseModel($dbConnection, "vwEntityProductSell");
            $arrRelatedEntityProduct = $dbEntityProductRelated->getWhere("stockStatusId=1 and scientificNameId =$dbEntityProduct->scientificNameId and id != $dbEntityProduct->id");
            $this->f3->set('arrRelatedEntityProduct', $arrRelatedEntityProduct);

            $this->webResponse->errorCode = 1;
            $this->webResponse->title = $this->f3->get('vTitle_entityProductDetail');
            $this->webResponse->data = View::instance()->render('app/products/single/entityProduct.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function getDistributorProducts()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {

            global $dbConnection;

            $dbStockStatus = new BaseModel($dbConnection, "stockStatus");
            $dbStockStatus->name = "name_" . $this->objUser->language;
            $arrStockStatus = $dbStockStatus->all("id asc");
            $this->f3->set('arrStockStatus', $arrStockStatus);

            $this->webResponse->errorCode = 1;
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
            $productId = $this->f3->get('PARAMS.productId');

            $dbProduct = new BaseModel($this->db, "vwEntityProductSell");
            $arrProduct = $dbProduct->findWhere("productId = '$productId'");

            $data['product'] = $arrProduct[0];

            echo $this->webResponse->jsonResponseV2(1, "", "", $data);
            return;
        }
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
        $where = "";
        $term = $_GET['term'];
        if (isset($term) && $term != "" && $term != null) {
            $where = "scientificName like '%$term%'";
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

        $dbNames = new BaseModel($dbConnection, "scientificName");
        $dbNames->getWhere($where, "name", $pageSize, $page * $pageSize);
        $resultsCount = 0;
        while (!$dbNames->dry()) {
            $resultsCount++;
            $select2ResultItem = new stdClass();
            $select2ResultItem->id = $dbNames->id;
            $select2ResultItem->text = $dbNames->name;
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

    function postDistributorProducts()
    {
        $query = "";
        $datatable = array_merge(array('pagination' => array(), 'sort' => array(), 'query' => array()), $_REQUEST);

        if ($datatable['query'] != "") {

            $productQuery = "";
            $productId = $datatable['query']['productId'];
            if (isset($productId)) {
                if (is_array($productId)) {
                    $productQuery = "id in (" . implode(",", $productId) . ")";
                } else {
                    $productQuery = "id = $productId";
                }
            }

            $scientificQuery = "";
            $scientificNameId = $datatable['query']['scientificNameId'];
            if (isset($scientificNameId)) {
                if (is_array($scientificNameId)) {
                    $scientificQuery = "scientificNameId in (" . implode(",", $scientificNameId) . ")";
                } else {
                    $scientificQuery = "scientificNameId = $scientificNameId";
                }
            }

            $entityQuery = "";
            $entityId = $datatable['query']['entityId'];
            if (isset($entityId)) {
                if (is_array($entityId)) {
                    $entityQuery = "entityId in (" . implode(",", $entityId) . ")";
                } else {
                    $entityQuery = "entityId = $entityId";
                }
            }

            if ($productQuery != "" && $scientificQuery != "" && $entityQuery != "") {
                $query = " $entityQuery and ($productQuery or $scientificQuery)";
            } elseif ($productQuery != "" && $scientificQuery != "" && $entityQuery == "") {
                $query = "$productQuery or $scientificQuery";
            } elseif ($productQuery != "" && $scientificQuery == "" && $entityQuery != "") {
                $query = " $entityQuery and $productQuery";
            } elseif ($productQuery != "" && $scientificQuery == "" && $entityQuery == "") {
                $query = "$productQuery";
            } elseif ($productQuery == "" && $scientificQuery != "" && $entityQuery != "") {
                $query = "$entityQuery and $scientificQuery";
            } elseif ($productQuery == "" && $scientificQuery == "" && $entityQuery != "") {
                $query = "$entityQuery";
            } elseif ($productQuery == "" && $scientificQuery != "" && $entityQuery == "") {
                $query = "$scientificQuery";
            }

            if ($datatable['query']['stockOption'] == 1) {
                if ($query == "") {
                    $query = "stockStatusId=1";
                } else {
                    $query = "stockStatusId=1 and ($query)";
                }
            }
        } else {
            $query = "stockStatusId=1";
        }

        $sort = !empty($datatable['sort']['sort']) ? $datatable['sort']['sort'] : 'asc';
        $field = !empty($datatable['sort']['field']) ? $datatable['sort']['field'] : 'id';

        $meta = array();
        $page = !empty($datatable['pagination']['page']) ? (int)$datatable['pagination']['page'] : 1;
        $perpage = !empty($datatable['pagination']['perpage']) ? (int)$datatable['pagination']['perpage'] : 10;

        $offset = ($page - 1) * $perpage;
        $total = 0;

        $dbProducts = new BaseModel($this->db, "vwEntityProductSell");

        if ($query == "") {
            $total = $dbProducts->count();
            $data = $dbProducts->findAll("$field $sort", $perpage, $offset);
        } else {
            $total = $dbProducts->count($query);
            $data = $dbProducts->findWhere($query, "$field $sort", $perpage, $offset);
        }

        $pages = 1;

        // $perpage 0; get all data
        if ($perpage > 0) {
            $pages = ceil($total / $perpage); // calculate total pages
            $page = max($page, 1); // get 1 page when $_REQUEST['page'] <= 0
            $page = min($page, $pages); // get last page when $_REQUEST['page'] > $totalPages
            $offset = ($page - 1) * $perpage;
            if ($offset < 0) {
                $offset = 0;
            }

            //$data = array_slice($data, $offset, $perpage, true);
        }

        $meta = array(
            'page' => $page,
            'pages' => $pages,
            'perpage' => $perpage,
            'total' => $total,
        );

        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition, Content-Description');

        $result = array(
            'q' => $query,
            'meta' => $meta + array(
                'sort' => $sort,
                'field' => $field,
            ),
            'data' => $data
        );

        echo json_encode($result, JSON_PRETTY_PRINT);
    }
}
