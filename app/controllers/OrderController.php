<?php

class OrderController extends Controller
{
    function getDistributorOrders()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));

            $dbOrders = new BaseModel($this->db, "vwOrderEntityUser");
            $arrOrders = $dbOrders->getWhere("entityDistributorId IN ($arrEntityId)");
            $this->f3->set('arrOrders', $arrOrders);

            $this->webResponse->errorCode = 1;
            $this->webResponse->title = $this->f3->get('vModule_order_title');
            $this->webResponse->data = View::instance()->render('app/sale/orders/orders.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function getOrderConfirmation()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", $this->f3->get('SERVER.REQUEST_URI'));
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $orderId = $this->f3->get('PARAMS.orderId');
            $statusId = $this->f3->get('PARAMS.statusId');

            $modalRoute = '';
            $modalText = '';
            $modalTitle = '';
            $modalCallback = 'DistributorOrdersDataTable.reloadDatatable';
            $modalButton = $this->f3->get('vButton_update');

            switch ($statusId) {
                case Constants::ORDER_STATUS_ONHOLD:
                    $modalTitle = $this->f3->get('vOrderStatus_OnHold');
                    $modalText = $this->f3->get('vOrderStatusConfirmation', $this->f3->get('vOrderStatus_OnHold'));
                    $modalRoute = '/web/distributor/order/onhold';
                    break;
                case Constants::ORDER_STATUS_PROCESSING:
                    $modalTitle = $this->f3->get('vOrderStatus_Processing');
                    $modalText = $this->f3->get('vOrderStatusConfirmation', $this->f3->get('vOrderStatus_Processing'));
                    $modalRoute = '/web/distributor/order/process';
                    break;
                case Constants::ORDER_STATUS_COMPLETED:
                    $modalTitle = $this->f3->get('vOrderStatus_Completed');
                    $modalText = $this->f3->get('vOrderStatusConfirmation', $this->f3->get('vOrderStatus_Completed'));
                    $modalRoute = '/web/distributor/order/complete';
                    break;
                case Constants::ORDER_STATUS_CANCELED:
                    $modalTitle = $this->f3->get('vOrderStatus_Canceled');
                    $modalText = $this->f3->get('vOrderStatusConfirmation', $this->f3->get('vOrderStatus_Canceled'));
                    $modalRoute = '/web/distributor/order/cancel';
                    break;
            }

            $modal = new stdClass();
            $modal->modalTitle = $modalTitle;
            $modal->modalText = $modalText;
            $modal->modalRoute = $modalRoute;
            $modal->modalButton = $modalButton;
            $modal->id = $orderId;
            $modal->fnCallback = $modalCallback;

            $this->f3->set('modalArr', $modal);
            echo $this->webResponse->jsonResponseV2(1, "", "", $modal);
            return;
        }
    }

    function getOrderDetails()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", $this->f3->get('SERVER.REQUEST_URI'));
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $orderId = $this->f3->get('PARAMS.orderId');

            $dbOrder = new BaseModel($this->db, "vwOrderEntityUser");
            $arrOrder = $dbOrder->findWhere("id = '$orderId'");

            $dbOrderDetail = new BaseModel($this->db, "vwOrderDetail");
            $arrOrderDetail = $dbOrderDetail->findWhere("id = '$orderId'");

            $data['order'] = $arrOrder[0];
            $data['orderDetail'] = $arrOrderDetail;

            echo $this->webResponse->jsonResponseV2(1, "", "", $data);
            return;
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

    function postOnHoldOrder()
    {
        $orderId = $this->f3->get("POST.id");
        $statusId = Constants::ORDER_STATUS_ONHOLD;
        $this->handleUpdateOrderStatus($orderId, $statusId);
    }

    function postProcessOrder()
    {
        $orderId = $this->f3->get("POST.id");
        $statusId = Constants::ORDER_STATUS_PROCESSING;
        $this->handleUpdateOrderStatus($orderId, $statusId);
    }

    function postCancelOrder()
    {
        $orderId = $this->f3->get("POST.id");
        $statusId = Constants::ORDER_STATUS_CANCELED;
        $this->handleUpdateOrderStatus($orderId, $statusId);
    }

    function postCompleteOrder()
    {
        $orderId = $this->f3->get("POST.id");
        $statusId = Constants::ORDER_STATUS_COMPLETED;
        $this->handleUpdateOrderStatus($orderId, $statusId);
    }

    function postReceivedOrder()
    {
        $orderId = $this->f3->get("POST.id");
        $statusId = Constants::ORDER_STATUS_RECEIVED;
        $this->handleUpdateOrderStatus($orderId, $statusId);
    }

    function handleUpdateOrderStatus($orderId, $statusId)
    {
        $dbOrder = new BaseModel($this->db, "order");
        $dbOrder->getById($orderId);

        if ($dbOrder->dry()) {
            echo $this->webResponse->jsonResponseV2(1, $this->f3->get('vResponse_notFound', $this->f3->get('vEntity_order')), '', null);
            return;
        }

        $dbOrder->statusId = $statusId;

        if (!$dbOrder->update()) {
            echo $this->webResponse->jsonResponseV2(1, $this->f3->get('vResponse_notUpdated', $this->f3->get('vEntity_order')), $dbOrder->exception(), null);
            return;
        }

        $dbOrderLog = new BaseModel($this->db, "orderLog");
        $dbOrderLog->orderId = $orderId;
        $dbOrderLog->userId = $this->objUser->id;
        $dbOrderLog->statusId = $statusId;

        if (!$dbOrderLog->add()) {
            echo $this->webResponse->jsonResponseV2(1, $this->f3->get('vResponse_notAdded', $this->f3->get('vEntity_orderLog')), $dbOrderLog->exception(), null);
            return;
        }

        echo $this->webResponse->jsonResponseV2(1, $this->f3->get('vResponse_updated', $this->f3->get('vEntity_order')), null, null);
        return;
    }
}
