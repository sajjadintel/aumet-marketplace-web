<?php

class OrderController extends Controller
{
    function getDistributorOrdersNew()
    {
        $this->handleGetDistributorOrders('new');
    }
    function getDistributorOrdersPending()
    {
        $this->handleGetDistributorOrders('pending');
    }
    function getDistributorOrdersUnpaid()
    {
        $this->handleGetDistributorOrders('unpaid');
    }
    function getDistributorOrdersHistory()
    {
        $this->handleGetDistributorOrders('history');
    }

    function getNotifcationsDistributorOrdersNew()
    {

        global $dbConnection;

        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
        $query = "notificationFlag = 1 and entitySellerId IN ($arrEntityId)";

        $dbOrder = new BaseModel($dbConnection, "order");
        $dbOrder->getWhere($query);
        $count = 0;
        while (!$dbOrder->dry()) {
            $count++;
            $dbOrder->notificationFlag = 2;
            $dbOrder->update();
            $dbOrder->next();
        }
        $this->webResponse->errorCode = 1;
        $this->webResponse->title = "";
        $this->webResponse->data = $count;
        echo $this->webResponse->jsonResponse();
    }

    function handleGetDistributorOrders($status)
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $renderFile = 'app/sale/orders/orders.php';
            $title = $this->f3->get('vModule_order_title');

            switch ($status) {
                case 'new':
                    $this->f3->set('vModule_order_header', $this->f3->get('vModule_order_header_new'));
                    break;
                case 'pending':
                    $this->f3->set('vModule_order_header', $this->f3->get('vModule_order_header_pending'));
                    break;
                case 'unpaid':
                    $this->f3->set('vModule_order_header', $this->f3->get('vModule_order_header_unpaid'));
                    break;
                case 'history':
                    $this->f3->set('vModule_order_header', $this->f3->get('vModule_order_header_history'));
                    break;
                default:
                    $this->f3->set('vModule_order_header', 'Unknown List');
                    break;
            }
            $this->webResponse->errorCode = 1;
            $this->webResponse->title = $title;
            $this->webResponse->data = View::instance()->render($renderFile);
            echo $this->webResponse->jsonResponse();
        }
    }

    function getOrderConfirmationDashboard()
    {
        $this->getOrderConfirmation(true);
    }

    function getOrderConfirmation($fromDashboard = false)
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
            if ($fromDashboard) {
                $modalCallback = 'DistributorDashboardDataTable.reloadDatatable';
            } else {
                $modalCallback = 'DistributorOrdersDataTable.reloadDatatable';
            }
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
                case Constants::ORDER_STATUS_PAID:
                    $modalTitle = $this->f3->get('vOrderStatus_Paid');
                    $modalText = $this->f3->get('vOrderStatusConfirmation', $this->f3->get('vOrderStatus_Paid'));
                    $modalRoute = '/web/distributor/order/paid';
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

            $dbOrderLog = new BaseModel($this->db, "vwOrderLog");
            $arrOrderLog = $dbOrderLog->findWhere("orderId = '$orderId'");

            $data['order'] = $arrOrder[0];
            $data['orderDetail'] = $arrOrderDetail;
            $data['orderLog'] = $arrOrderLog;

            echo $this->webResponse->jsonResponseV2(1, "", "", $data);
            return;
        }
    }

    function postDistributorOrdersRecent()
    {
        $this->handlePostDistributorOrders('new');
    }

    function postDistributorOrdersNew()
    {
        $this->handlePostDistributorOrders('new');
    }

    function postDistributorOrdersPending()
    {
        $this->handlePostDistributorOrders('pending');
    }

    function postDistributorOrdersUnpaid()
    {
        $this->handlePostDistributorOrders('unpaid');
    }

    function postDistributorOrdersHistory()
    {
        $this->handlePostDistributorOrders('history');
    }

    function handlePostDistributorOrders($status)
    {
        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
        $query = "entitySellerId IN ($arrEntityId)";
        switch ($status) {
            case 'new':
                $query .= " AND statusId = 1";
                break;
            case 'unpaid':
                $query .= " AND statusId = 6";
                break;
            case 'pending':
                $query .= " AND statusId IN (2,3)";
                break;
            case 'history':
                $query .= " AND statusId IN (4,5,6,7)";
                break;
            default:
                break;
        }

        $datatable = array_merge(array('pagination' => array(), 'sort' => array(), 'query' => array()), $_REQUEST);

        if (is_array($datatable['query'])) {
            $entityBuyerId = $datatable['query']['entityBuyerId'];
            if (isset($entityBuyerId) && is_array($entityBuyerId)) {
                $query .= " AND entityBuyerId in (" . implode(",", $entityBuyerId) . ")";
            }

            $startDate = $datatable['query']['startDate'];
            if (isset($startDate) && $startDate != "") {
                $query .= " AND insertDateTime >= '$startDate'";
            }

            $endDate = $datatable['query']['endDate'];
            if (isset($endDate) && $endDate != "") {
                $query .= " AND insertDateTime <= '$endDate'";
            }
        }

        $sort = !empty($datatable['sort']['sort']) ? $datatable['sort']['sort'] : 'asc';
        $field = !empty($datatable['sort']['field']) ? $datatable['sort']['field'] : 'id';

        $meta = array();
        $page = !empty($datatable['pagination']['page']) ? (int)$datatable['pagination']['page'] : 1;
        $perpage = !empty($datatable['pagination']['perpage']) ? (int)$datatable['pagination']['perpage'] : 10;

        $total = 0;
        $offset = ($page - 1) * $perpage;

        $dbData = new BaseModel($this->db, "vwOrderEntityUser");

        $data = [];

        if (!$dbData->exists($field)) {
            $field = 'id';
        }
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

    function postPaidOrder()
    {
        $orderId = $this->f3->get("POST.id");
        $statusId = Constants::ORDER_STATUS_PAID;
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

        // Add conditions for checks on Order
        // If Order to change to Complete, check if Stock is available for all the items
        if ($statusId == Constants::ORDER_STATUS_COMPLETED) {
            $dbOrderItems = new BaseModel($this->db, "orderDetail");
            $dbProduct = new BaseModel($this->db, "entityProductSell");
            // check all items if stock is available
            $dbOrderItems->getWhere("orderId = $orderId");

            while (!$dbOrderItems->dry()) {
                $dbProduct->getWhere("id = $dbOrderItems->entityProductId");

                if ($dbProduct->dry() || $dbProduct->stockStatusId != 1 || $dbProduct->stock < $dbOrderItems->quantity) {
                    echo $this->webResponse->jsonResponseV2(1, $this->f3->get('vResponse_notUpdated', $this->f3->get('vEntity_order')), $this->f3->get('vResponse_productsMissing'), null);
                    return;
                }

                $dbProduct->next();
            }
        }

        $dbOrder->updateDateTime = date("Y-m-d H:i:s");
        $dbOrder->statusId = $statusId;
        $dbOrder->userSellerId = $this->objUser->id;

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

        // Add other management information
        // If order is Complete, adjust stocks and relation (unpaid total)
        // If order is Paid, adjust relation (paid total)
        if ($statusId == Constants::ORDER_STATUS_COMPLETED) {
            $dbOrderItems->getWhere("orderId = $orderId");

            while (!$dbOrderItems->dry()) {
                $dbProduct->getWhere("id = $dbOrderItems->entityProductId");

                $dbProduct->stock -= $dbOrderItems->quantity;
                $dbProduct->update();

                $dbProduct->next();
            }

            $dbRelation = new BaseModel($this->db, "entityRelation");
            $dbRelation->getWhere("entityBuyerId = $dbOrder->entityBuyerId AND entitySellerId = $dbOrder->entitySellerId");

            if ($dbRelation->dry()) {
                $dbRelation->entityBuyerId = $dbOrder->entityBuyerId;
                $dbRelation->entitySellerId = $dbOrder->entitySellerId;
                $dbRelation->currencyId = $dbOrder->currencyId;
                $dbRelation->orderCount = 1;
                $dbRelation->orderTotal = $dbOrder->total;
                $dbRelation->add();
            } else {
                $dbRelation->orderCount++;
                $dbRelation->orderTotal += $dbOrder->total;
                $dbRelation->updatedAt = date('Y-m-d H:i:s');
                $dbRelation->update();
            }
        } elseif ($statusId == Constants::ORDER_STATUS_PAID) {
            $dbRelation = new BaseModel($this->db, "entityRelation");
            $dbRelation->getWhere("entityBuyerId = $dbOrder->entityBuyerId AND entitySellerId = $dbOrder->entitySellerId");

            $dbRelation->orderCountPaid++;
            $dbRelation->orderTotalPaid += $dbOrder->total;
            $dbRelation->updatedAt = date('Y-m-d H:i:s');
            $dbRelation->update();
        }

        echo $this->webResponse->jsonResponseV2(1, $this->f3->get('vResponse_updated', $this->f3->get('vEntity_order')), null, null);
        return;
    }

    function getPrintOrderInvoice()
    {
        $font = 'dejavusans';
        $orderId = $this->f3->get('PARAMS.orderId');

        $dbOrder = new BaseModel($this->db, 'vwOrderEntityUser');
        $arrOrder = $dbOrder->findWhere("id = $orderId");
        $arrOrder = $arrOrder[0];
        $pdf = new PDF();
        // create new PDF document
        $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->AddPage();

        // Title
        $pdf->SetFont($font, 'B', 14);
        $pdf->Cell(0, 10, 'Order #' . $arrOrder['id'], 0, 0, 'R');
        $pdf->Ln(6);
        $pdf->Cell(0, 10, $arrOrder['entitySeller'], 0, 0, 'R');
        $pdf->Ln(10);

        $pdf->SetFont($font, '', 14);
        $pdf->Cell(0, 10, $arrOrder['insertDateTime'], 0, 0, 'R');
        $pdf->Ln(20);

        $pdf->SetFont($font, '', 11);

        $pharmacyTableHeader = array('Customer ID', 'Customer Name', 'Email');
        $pharmacyTableData = array(array($arrOrder['entityBuyerId'], $arrOrder['entityBuyer'], $arrOrder['userBuyerEmail']));
        $pdf->FancyTable($pharmacyTableHeader, $pharmacyTableData);
        $pdf->Ln(20);

        $orderDetailHeader = array('Code', 'Name', 'Quantity', 'Price', 'VAT', 'Total');
        $dbOrderDetail = new BaseModel($this->db, 'vwOrderDetail');
        $arrOrderDetail = $dbOrderDetail->findWhere("id = $orderId");

        $orderDetailData = array();
        foreach ($arrOrderDetail as $item) {
            array_push($orderDetailData, array($item['productCode'], $item['productNameEn'], $item['quantity'], $item['currency'] . " " . $item['unitPrice'], $item['tax'] . "%", $item['currency'] . " " . ($item['unitPrice'] * $item['quantity'])));
        }

        $pdf->FancyTableOrderDetail($orderDetailHeader, $orderDetailData);

        $pdf->Ln(20);

        $pdf->Cell(0, 0, 'Order: AED ' . $arrOrder['total'], 0, 0, 'R');
        $pdf->Ln(10);
        $pdf->Cell(0, 0, 'VAT: AED ' . round($arrOrder['tax'] * $arrOrder['total'], 2), 0, 0, 'R');

        $pdf->Ln(10);
        $pdf->Cell(0, 0, 'Total: AED ' . $arrOrder['total'], 0, 0, 'R');

        $pdf->Output();
    }
}
