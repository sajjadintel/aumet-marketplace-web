<?php

class OrderController extends Controller
{
    function getDistributorOrders()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {

            global $dbConnection;

            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));

            $dbOrders = new BaseModel($dbConnection, "vwOrderEntityUser");
            $arrOrders = $dbOrders->getWhere("entityDistributorId IN ($arrEntityId)");
            $this->f3->set('arrOrders', $arrOrders);

            $this->webResponse->errorCode = 1;
            $this->webResponse->title = $this->f3->get('vModule_order_title');
            $this->webResponse->data = View::instance()->render('app/sale/orders/orders.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function postDistributorOrders()
    {
        $query = "";
        $datatable = array_merge(array('pagination' => array(), 'sort' => array(), 'query' => array()), $_REQUEST);

        // if ($datatable['query'] != "") {

        //     $productQuery = "";
        //     $productId = $datatable['query']['productId'];
        //     if (isset($productId)) {
        //         if (is_array($productId)) {
        //             $productQuery = "id in (" . implode(",", $productId) . ")";
        //         } else {
        //             $productQuery = "id = $productId";
        //         }
        //     }

        //     $scientificQuery = "";
        //     $scientificNameId = $datatable['query']['scientificNameId'];
        //     if (isset($scientificNameId)) {
        //         if (is_array($scientificNameId)) {
        //             $scientificQuery = "scientificNameId in (" . implode(",", $scientificNameId) . ")";
        //         } else {
        //             $scientificQuery = "scientificNameId = $scientificNameId";
        //         }
        //     }

        //     $entityQuery = "";
        //     $entityId = $datatable['query']['entityId'];
        //     if (isset($entityId)) {
        //         if (is_array($entityId)) {
        //             $entityQuery = "entityId in (" . implode(",", $entityId) . ")";
        //         } else {
        //             $entityQuery = "entityId = $entityId";
        //         }
        //     }

        //     if ($productQuery != "" && $scientificQuery != "" && $entityQuery != "") {
        //         $query = " $entityQuery and ($productQuery or $scientificQuery)";
        //     } elseif ($productQuery != "" && $scientificQuery != "" && $entityQuery == "") {
        //         $query = "$productQuery or $scientificQuery";
        //     } elseif ($productQuery != "" && $scientificQuery == "" && $entityQuery != "") {
        //         $query = " $entityQuery and $productQuery";
        //     } elseif ($productQuery != "" && $scientificQuery == "" && $entityQuery == "") {
        //         $query = "$productQuery";
        //     } elseif ($productQuery == "" && $scientificQuery != "" && $entityQuery != "") {
        //         $query = "$entityQuery and $scientificQuery";
        //     } elseif ($productQuery == "" && $scientificQuery == "" && $entityQuery != "") {
        //         $query = "$entityQuery";
        //     } elseif ($productQuery == "" && $scientificQuery != "" && $entityQuery == "") {
        //         $query = "$scientificQuery";
        //     }

        //     if ($datatable['query']['stockOption'] == 1) {
        //         if ($query == "") {
        //             $query = "stockStatusId=1";
        //         } else {
        //             $query = "stockStatusId=1 and ($query)";
        //         }
        //     }
        // } else {
        //     $query = "stockStatusId=1";
        // }

        $sort = !empty($datatable['sort']['sort']) ? $datatable['sort']['sort'] : 'asc';
        $field = !empty($datatable['sort']['field']) ? $datatable['sort']['field'] : 'id';

        $meta = array();
        $page = !empty($datatable['pagination']['page']) ? (int)$datatable['pagination']['page'] : 1;
        $perpage = !empty($datatable['pagination']['perpage']) ? (int)$datatable['pagination']['perpage'] : 10;

        $total = 0;
        $offset = ($page - 1) * $perpage;

        global $dbConnection;

        $dbData = new BaseModel($dbConnection, "vwOrderEntityUser");
        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));

        $query = "entityDistributorId IN ($arrEntityId)";
        $data = [];

        if ($query == "") {
            $total = $dbData->count();
            $data = $dbData->findAll("$field $sort", $perpage, $offset);
        } else {
            $total = $dbData->count($query);
            $data = $dbData->findWhere($query, "$field $sort", $perpage, $offset);
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
