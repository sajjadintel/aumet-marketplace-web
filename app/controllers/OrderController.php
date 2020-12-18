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


    function getPharmacyOrdersUnpaid()
    {
        $this->handleGetPharmacyOrders('unpaid');
    }

    function getPharmacyOrdersPending()
    {
        $this->handleGetPharmacyOrders('pending');
    }

    function getPharmacyOrdersHistory()
    {
        $this->handleGetPharmacyOrders('history');
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

    function handleGetPharmacyOrders($status)
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $renderFile = 'app/sale/orders/ordersPharmacy.php';
            $title = $this->f3->get('vModule_order_title');

            switch ($status) {
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
            $modalCallback = 'WebApp.reloadDatatable';
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

    function getOrderMissingProducts()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", $this->f3->get('SERVER.REQUEST_URI'));
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $orderId = $this->f3->get('PARAMS.orderId');

            $dbOrder = new BaseModel($this->db, "vwOrderEntityUser");
            $arrOrder = $dbOrder->findWhere("id = '$orderId'");

            //            $dbOrderDetail = new BaseModel($this->db, "vwOrderDetail");
            $dbOrderDetail = new BaseModel($this->db, "vwOrderMissingProductDetail");
            $arrOrderDetail = $dbOrderDetail->findWhere("id = '$orderId'");


            $data['order'] = $arrOrder[0];
            $data['orderDetail'] = $arrOrderDetail;

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
        ## Read values from Datatables
        $datatable = new Datatable($_POST);
        $query = "";

        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
        $query = "entitySellerId IN ($arrEntityId)";
        switch ($status) {
            case 'new':
                $query .= " AND statusId = 1";
                break;
            case 'unpaid':
                $query .= " AND statusId IN (6,8)";
                break;
            case 'pending':
                $query .= " AND statusId IN (2,3)";
                break;
            case 'history':
                $query .= " AND statusId IN (4,5,6,7,8,9)";
                break;
            default:
                break;
        }

        $fullQuery = $query;

        // $datatable = array_merge(array('pagination' => array(), 'sort' => array(), 'query' => array()), $_REQUEST);

        if (is_array($datatable->query)) {
            $entityBuyerId = $datatable->query['entityBuyerId'];
            if (isset($entityBuyerId) && is_array($entityBuyerId)) {
                $query .= " AND entityBuyerId in (" . implode(",", $entityBuyerId) . ")";
            }

            $startDate = $datatable->query['startDate'];
            if (isset($startDate) && $startDate != "") {
                $query .= " AND insertDateTime >= '$startDate'";
            }

            $endDate = $datatable->query['endDate'];
            if (isset($endDate) && $endDate != "") {
                $query .= " AND insertDateTime <= '$endDate'";
            }
        }

        $dbData = new BaseModel($this->db, "vwOrderEntityUser");

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

    function postPharmacyOrdersRecent()
    {
        $this->handlePostPharmacyOrders('recent');
    }

    function postPharmacyOrdersPending()
    {
        $this->handlePostPharmacyOrders('pending');
    }

    function postPharmacyOrdersUnpaid()
    {
        $this->handlePostPharmacyOrders('unpaid');
    }

    function postPharmacyOrdersHistory()
    {
        $this->handlePostPharmacyOrders('history');
    }

    function handlePostPharmacyOrders($status)
    {
        ## Read values from Datatables
        $datatable = new Datatable($_POST);
        $query = "";

        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
        $query = "entityBuyerId IN ($arrEntityId)";
        switch ($status) {
            case 'new':
                $query .= " AND statusId = 1";
                break;
            case 'recent':
                $query .= " AND statusId IN (1, 2)";
                break;
            case 'unpaid':
                $query .= " AND statusId IN (6,8) ";
                break;
            case 'pending':
                $query .= " AND statusId IN (2,3)";
                break;
            case 'history':
                $query .= " AND statusId IN (4,5,6,7,8,9)";
                break;
            default:
                break;
        }

        $fullQuery = $query;

        // $datatable = array_merge(array('pagination' => array(), 'sort' => array(), 'query' => array()), $_REQUEST);

        if (is_array($datatable->query)) {
            $entitySellerId = $datatable->query['entitySellerId'];
            if (isset($entitySellerId) && is_array($entitySellerId)) {
                $query .= " AND entitySellerId in (" . implode(",", $entitySellerId) . ")";
            }

            $startDate = $datatable->query['startDate'];
            if (isset($startDate) && $startDate != "") {
                $query .= " AND insertDateTime >= '$startDate'";
            }

            $endDate = $datatable->query['endDate'];
            if (isset($endDate) && $endDate != "") {
                $query .= " AND insertDateTime <= '$endDate'";
            }
        }

        $dbData = new BaseModel($this->db, "vwOrderEntityUser");

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

    function postPharmacyMissingProducts()
    {
        $orderId = $this->f3->get("POST.orderId");
        $missingProducts = $this->f3->get("POST.missingProductsRepeater");
        if ($this->checkForProductsDuplication($missingProducts)) {
            echo $this->webResponse->jsonResponseV2(2, "Error", $this->f3->get('vMissingProduct_ErrorDuplicateProducts'));
            return;
        }

        $dbOrder = new BaseModel($this->db, "order");
        $dbOrder->getByField("id", $orderId);

        $dbOrderDetail = new BaseModel($this->db, "vwOrderDetail");
        $arrOrderDetail = $dbOrderDetail->findWhere("id = '$orderId'");


        foreach ($missingProducts as $missingProduct) {
            if (!(is_numeric($missingProduct['productId']) && $missingProduct['productId'] > 0)) {
                echo $this->webResponse->jsonResponseV2(2, "Error", "Invalid Product id");
                return;
            }
            $serverProduct = $this->getProductFromArrayById($missingProduct['productId'], $arrOrderDetail);
            if ($missingProduct['quantity'] > $serverProduct['quantity'] || $missingProduct['quantity'] <= 0) {
                echo $this->webResponse->jsonResponseV2(2, "Error", $this->f3->get('vMissingProduct_ErrorInvalidQuantity') . $serverProduct['productNameEn']);
                return;
            }
        }

        foreach ($missingProducts as $missingProduct) {
            $dbMissingProduct = new BaseModel($this->db, "orderMissingProduct");
            $dbMissingProduct->orderId = $orderId;
            $dbMissingProduct->statusId = 1;
            $dbMissingProduct->buyerUserId = $this->objUser->id;
            $dbMissingProduct->productId = $missingProduct['productId'];
            $dbMissingProduct->quantity = $missingProduct['quantity'];
            $dbMissingProduct->add();
        }


        $dbOrder->statusId = 8; // Missing Products
        $dbOrder->edit();

        $missingProductsToEmail = $missingProducts;
        // TODO: Email To Distributor

        echo $this->webResponse->jsonResponseV2(1, "Success", "");
    }

    private function getProductFromArrayById($productId, $products)
    {
        foreach ($products as $product) {
            if ($product['productCode'] == $productId)
                return $product;
        }
        return null;
    }

    private function checkForProductsDuplication($missingProducts)
    {
        $dupe_array = array();
        foreach ($missingProducts as $val) {
            if (++$dupe_array[$val['productId']] > 1) {
                return true;
            }
        }
        return false;
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

        // Send mails to notify about order status update
        $emailHandler = new EmailHandler($this->db);
        $emailFile = "email/layout.php";
        $this->f3->set('domainUrl', getenv('DOMAIN_URL'));
        $this->f3->set('title', 'Order Status Update');
        $this->f3->set('emailType', 'orderStatusUpdate');

        $dbOrderStatus = new BaseModel($this->db, "orderStatus");

        $nameField = "name_" . $this->objUser->language;
        $dbOrderStatus->name = $nameField;

        $allOrderStatus = $dbOrderStatus->all();

        $mapStatusIdName = [];
        foreach ($allOrderStatus as $orderStatus) {
            $mapStatusIdName[$orderStatus->id] = $orderStatus->name;
        }

        $orderStatusUpdateTitle = "Order with serial " . $dbOrder->serial . " status has changed to " . $mapStatusIdName[strval($statusId)];
        $this->f3->set('orderStatusUpdateTitle', $orderStatusUpdateTitle);

        $dbCurrency = new BaseModel($this->db, "currency");
        $currency = $dbCurrency->getWhere("id = $dbOrder->currencyId");

        $dbOrderDetail = new BaseModel($this->db, "vwOrderDetail");
        $dbOrderDetail->name = "productName" . ucfirst($this->objUser->language);
        $arrOrderDetail = $dbOrderDetail->getByField("id", $orderId);

        $this->f3->set('products', $arrOrderDetail);
        $this->f3->set('currencySymbol', $currency->symbol);
        $this->f3->set('subTotal', $subTotal);
        $this->f3->set('tax', $tax);
        $this->f3->set('total', $total);

        $htmlContent = View::instance()->render($emailFile);

        $dbEntityUserProfile = new BaseModel($this->db, "vwEntityUserProfile");

        $arrEntityUserProfile = $dbEntityUserProfile->getByField("entityId", $dbOrder->entityBuyerId);
        foreach ($arrEntityUserProfile as $entityUserProfile) {
            $emailHandler->appendToAddress($entityUserProfile->userEmail, $entityUserProfile->userFullName);
        }

        $subject = "Order Status Update";
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: ".getenv('ENV').")";
            if (getenv('ENV') == Constants::ENV_LOC){
                $emailHandler->resetTos();
                $emailHandler->appendToAddress("antoineaboucherfane@gmail.com", "Antoine Abou Cherfane");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
            }
        }
        $emailHandler->sendEmail(Constants::EMAIL_ORDER_STATUS_UPDATE, $subject, $htmlContent);
        $emailHandler->resetTos();

        $arrEntityUserProfile = $dbEntityUserProfile->getByField("entityId", $dbOrder->entitySellerId);
        foreach ($arrEntityUserProfile as $entityUserProfile) {
            $emailHandler->appendToAddress($entityUserProfile->userEmail, $entityUserProfile->userFullName);
        }

        $subject = "Order Status Update";
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: ".getenv('ENV').")";
            if (getenv('ENV') == Constants::ENV_LOC){
                $emailHandler->resetTos();
                $emailHandler->appendToAddress("antoineaboucherfane@gmail.com", "Antoine Abou Cherfane");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
            }
        }
        $emailHandler->sendEmail(Constants::EMAIL_ORDER_STATUS_UPDATE, $subject, $htmlContent);

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

    function getPrintOrderPharmacyInvoice()
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
        $pdf->Cell(0, 10, $arrOrder['entityBuyer'], 0, 0, 'R');
        $pdf->Ln(10);

        $pdf->SetFont($font, '', 14);
        $pdf->Cell(0, 10, $arrOrder['insertDateTime'], 0, 0, 'R');
        $pdf->Ln(20);

        $pdf->SetFont($font, '', 11);

        $pharmacyTableHeader = array('Distributor ID', 'Distributor Name', 'Email');
        $pharmacyTableData = array(array($arrOrder['entitySellerId'], $arrOrder['entitySeller'], $arrOrder['userSellerEmail']));
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
