<?php

class OrderController extends Controller
{

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

    function getNotificationsDistributorOrdersNew()
    {

        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
        $query = "notificationFlag = 1 and entitySellerId IN ($arrEntityId)";

        $dbOrder = new BaseModel($this->db, "order");
        $dbOrder->getWhere($query);
        $count = 0;
        while (!$dbOrder->dry()) {
            $count++;
            $dbOrder->notificationFlag = 2;
            $dbOrder->update();
            $dbOrder->next();
        }
        $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
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

            $customerId = $_GET['customer'];
            if ($customerId) {
                $dbEntity = new BaseModel($this->db, "entity");
                $dbEntity->name = "name_" . $this->objUser->language;
                $dbEntity->getWhere("id=$customerId");
                $this->f3->set('customerName', $dbEntity['name']);
            }

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
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
            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
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

            $pendingOrderReason = '';
            if (in_array($statusId, [Constants::ORDER_STATUS_ONHOLD, Constants::ORDER_STATUS_PROCESSING, Constants::ORDER_STATUS_CANCELED])) {
                $pendingOrderReason =
                    '<div class="form-group row mb-0">' .
                    '  <label for="reasonId" class="col-sm-2 col-form-label col-form-label-sm">Reason</label>' .
                    '  <div class="col-sm-10">' .
                    '    <select class="custom-select" name="reasonId" id="reasonId" ' . ($this->objUser->language === 'ar' ? 'lang="ar" dir="rtl"' : '') . ' required>' .
                    '      <option value="-1" selected disabled>Select...</option>';

                foreach ($this->f3->get('vOrderPending_reasons') as $index => $reason) {
                    $pendingOrderReason .= '<option value="' . $index . '">' . $reason . '</option>';
                }

                $pendingOrderReason .=
                    '    </select>' .
                    '  </div>' .
                    '</div>';
            }

            switch ($statusId) {
                case Constants::ORDER_STATUS_ONHOLD:
                    $modalTitle = $this->f3->get('vOrderStatus_OnHold');
                    $modalText = '<p>' . $this->f3->get('vOrderStatusConfirmation', $this->f3->get('vOrderStatus_OnHold')) . '</p>' . $pendingOrderReason;
                    $modalRoute = '/web/distributor/order/onhold';
                    break;
                case Constants::ORDER_STATUS_PROCESSING:
                    $modalTitle = $this->f3->get('vOrderStatus_Processing');
                    $modalText = '<p>' . $this->f3->get('vOrderStatusConfirmation', $this->f3->get('vOrderStatus_Processing')) . '</p>' . $pendingOrderReason;
                    $modalRoute = '/web/distributor/order/process';
                    break;
                case Constants::ORDER_STATUS_COMPLETED:
                    $modalTitle = $this->f3->get('vOrderStatus_Completed');
                    $modalText = $this->f3->get('vOrderStatusConfirmation', $this->f3->get('vOrderStatus_Completed'));
                    $modalRoute = '/web/distributor/order/complete';
                    break;
                case Constants::ORDER_STATUS_CANCELED:
                    $modalTitle = $this->f3->get('vOrderStatus_Canceled');
                    $modalText = '<p>' . $this->f3->get('vOrderStatusConfirmation', $this->f3->get('vOrderStatus_Canceled')) . '</p>' . $pendingOrderReason;
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
            $dbOrder->orderPaymentMethodName = "orderPaymentMethodName_" . $this->objUser->language;
            $arrOrder = $dbOrder->findWhere("id = '$orderId'");

            $dbOrderDetail = new BaseModel($this->db, "vwOrderDetail");
            $dbOrderDetail->productName = "productName" . ucfirst($this->objUser->language);
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

            $dbOrderDetail = new BaseModel($this->db, "vwOrderMissingProductDetail");
            $dbOrderDetail->productName = "productName" . ucfirst($this->objUser->language);
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
        $this->handlePostDistributorOrders('pending');
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
            case 'unpaid':
                $query .= " AND statusId IN (6,8)";
                break;
            case 'pending':
                $query .= " AND statusId IN (1)";
                break;
            case 'history':
                $query .= " AND statusId IN (1,2,3,4,5,6,7,8,9)";
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
                $query .= " AND insertDateTime >= '$startDate 00:00:00'";
            }

            $endDate = $datatable->query['endDate'];
            if (isset($endDate) && $endDate != "") {
                $query .= " AND insertDateTime <= '$endDate 23:59:59'";
            }
        }

        $dbData = new BaseModel($this->db, "vwOrderEntityUser");

        $totalRecords = $dbData->count($fullQuery);
        $totalFiltered = $dbData->count($query);
        $orders = $dbData->findWhere($query, "$datatable->sortBy $datatable->sortByOrder", $datatable->limit, $datatable->offset);

        $ordersWithOrderDetail = [];
        foreach ($orders as $order) {
            $dbOrderDetail = new BaseModel($this->db, "vwOrderDetail");
            $dbOrderDetail->productName = "productName" . ucfirst($this->objUser->language);
            $arrOrderDetail = $dbOrderDetail->findWhere("id = '{$order['id']}'");

            if (sizeof($arrOrderDetail) == 0) {
                $order['isVisible'] = true;
                $order['productCount'] = 0;
                $order['orderCount'] = 0;
                $ordersWithOrderDetail[] = $order;
                continue;
            }

            $productsCount = 0;
            for ($i = 0; $i < count($arrOrderDetail); $i++) {
                $productsCount += $arrOrderDetail[$i]['shippedQuantity'];
            }

            for ($i = 0; $i < count($arrOrderDetail); $i++) {
                $orderDetail = array_merge($order, $arrOrderDetail[$i]);
                if ($i === 0) {
                    $orderDetail['productCount'] = count($arrOrderDetail);
                    $orderDetail['orderCount'] = $productsCount;
                }
                $orderDetail['isVisible'] = $i === 0;
                $ordersWithOrderDetail[] = $orderDetail;
            }

        }

        ## Response
        $response = array(
            "draw" => intval($datatable->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $ordersWithOrderDetail
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
            case 'recent':
                $query .= " AND statusId IN (1,2,3)";
                break;
            case 'unpaid':
                $query .= " AND statusId IN (4,6,8) ";
                break;
            case 'pending':
                $query .= " AND statusId IN (1)";
                break;
            case 'history':
                $query .= " AND statusId IN (1,2,3,4,5,6,7,8,9)";
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
                $query .= " AND insertDateTime >= '$startDate 00:00:00'";
            }

            $endDate = $datatable->query['endDate'];
            if (isset($endDate) && $endDate != "") {
                $query .= " AND insertDateTime <= '$endDate 23:59:59'";
            }
        }

        $dbData = new BaseModel($this->db, "vwOrderEntityUser");

        $totalRecords = $dbData->count($fullQuery);
        $totalFiltered = $dbData->count($query);
        $orders = $dbData->findWhere($query, "$datatable->sortBy $datatable->sortByOrder", $datatable->limit, $datatable->offset);

        $ordersWithOrderDetail = [];
        foreach ($orders as $order) {
            $dbOrderDetail = new BaseModel($this->db, "vwOrderDetail");
            $dbOrderDetail->productName = "productName" . ucfirst($this->objUser->language);
            $arrOrderDetail = $dbOrderDetail->findWhere("id = '{$order['id']}'");

            if (sizeof($arrOrderDetail) == 0) {
                $order['isVisible'] = true;
                $ordersWithOrderDetail[] = $order;
                continue;
            }
            for ($i = 0; $i < count($arrOrderDetail); $i++) {
                $orderDetail = array_merge($order, $arrOrderDetail[$i]);
                $orderDetail['isVisible'] = $i === 0;
                $ordersWithOrderDetail[] = array_merge($order, $orderDetail);
            }
        }

        ## Response
        $response = array(
            "draw" => intval($datatable->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $ordersWithOrderDetail
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
            if (!(is_numeric($missingProduct['productId']) || $missingProduct['productId'] < 0)) {
                echo $this->webResponse->jsonResponseV2(2, "Error", "Invalid Product id");
                return;
            }
            $serverProduct = $this->getProductFromArrayById($missingProduct['productId'], $arrOrderDetail);
            if ($missingProduct['quantity'] > $serverProduct['requestedQuantity'] || $missingProduct['quantity'] <= 0) {
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

        NotificationHelper::orderMissingProductsNotification($this->f3, $this->db, $orderId);

        echo $this->webResponse->jsonResponseV2(1, "Success", "");
    }

    function postEditQuantityOrder()
    {
        $orderId = $this->f3->get("POST.orderId");
        $editQuantityProducts = $this->f3->get("POST.modifyQuantityOrderRepeater");
        if ($this->checkForProductsDuplication($editQuantityProducts, 'productCode')) {
            echo $this->webResponse->jsonResponseV2(2, "Error", $this->f3->get('vMissingProduct_ErrorDuplicateProducts'));
            return;
        }

        $dbOrderDetail = new BaseModel($this->db, "vwOrderDetail");
        $dbOrderDetail->productName = "productName" . ucfirst($this->objUser->language);
        $arrOrderDetail = $dbOrderDetail->findWhere("id = '$orderId'");

        $dbOrder = new BaseModel($this->db, "order");
        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
        $dbOrder->getWhere("id=$orderId AND entitySellerId IN ($arrEntityId)");
        // check if order is for this user
        if ($dbOrder->dry()) {
            echo $this->webResponse->jsonResponseV2(2, "Error", "Invalid order Id");
            return;
        }

        foreach ($editQuantityProducts as $editQuantityProduct) {
            if (!(is_numeric($editQuantityProduct['productCode']) || $editQuantityProduct['productCode'] < 0)) {
                echo $this->webResponse->jsonResponseV2(2, "Error", "Invalid Product id");
                return;
            }
            $serverProduct = $this->getProductFromArrayById($editQuantityProduct['productCode'], $arrOrderDetail);
            if ($editQuantityProduct['shippedQuantity'] > $serverProduct['requestedQuantity'] || $editQuantityProduct['shippedQuantity'] < 0) {
                echo $this->webResponse->jsonResponseV2(2, "Error", $this->f3->get('vMissingProduct_ErrorInvalidQuantity') . $serverProduct['productName']);
                return;
            }
        }

        $modifiedOrderDetailIds = [];
        $priceDifference = 0;
        $priceDifferenceVat = 0;
        $commands = [];
        foreach ($editQuantityProducts as $editQuantityProduct) {
            $orderDetail = $this->getProductFromArrayById($editQuantityProduct['productCode'], $arrOrderDetail);

            if ($orderDetail['shippedQuantity'] == $editQuantityProduct['shippedQuantity']) {
                continue;
            }
            // calculate order entity quantity free based on new quantity and free ratio
            $quantityFree = floor($editQuantityProduct['shippedQuantity'] * $orderDetail['freeRatio']);
            $quantity = $editQuantityProduct['shippedQuantity'] - $quantityFree;

            // get product vat
            $dbEntityProductSell = new BaseModel($this->db, "entityProductSell");
            $dbEntityProductSell->getWhere("id={$editQuantityProduct['productCode']}");
            // calculate product price difference caused by quantity change
            $priceDifference += ($orderDetail['quantity'] - $quantity) * $orderDetail['unitPrice'];
            // calculate product vat difference caused by quantity change
            $priceDifferenceVat += ($orderDetail['quantity'] - $quantity) * $orderDetail['unitPrice'] * $dbEntityProductSell->vat / 100;

            // set order detail new quantity
            $query = "UPDATE orderDetail SET quantity='$quantity',  quantityFree='$quantityFree', shippedQuantity='{$editQuantityProduct['shippedQuantity']}' "
                . "WHERE orderId={$dbOrder->id} AND entityProductId={$editQuantityProduct['productCode']};";
            array_push($commands, $query);

            $modifiedOrderDetailIds[] = $orderDetail['orderDetailId'];
        }

        if (sizeof($modifiedOrderDetailIds) == 0) {
            echo $this->webResponse->jsonResponseV2(2, "Error", $this->f3->get('vModalEditQuantity_nothingModified'));
            return;
        }

        $this->db->exec($commands);

        $dbOrder->subtotal = round($dbOrder->subtotal - $priceDifference, 2);
        $dbOrder->vat = round($dbOrder->vat - $priceDifferenceVat, 2);
        $dbOrder->total = round($dbOrder->subtotal + $dbOrder->vat, 2);
        $dbOrder->update();

        $dbRelation = new BaseModel($this->db, "entityRelation");
        $dbRelation->getWhere("entityBuyerId = $dbOrder->entityBuyerId AND entitySellerId = $dbOrder->entitySellerId");
        $dbRelation->orderTotal -= ($priceDifference + $priceDifferenceVat);
        $dbRelation->updatedAt = date('Y-m-d H:i:s');
        $dbRelation->update();

        NotificationHelper::orderModifyShippedQuantityNotification($this->f3, $this->db, $dbOrder->id, $modifiedOrderDetailIds, $this->objUser->id, $dbOrder->entityBuyerId);

        echo $this->webResponse->jsonResponseV2(Constants::STATUS_SUCCESS_SHOW_DIALOG, "Success", $this->f3->get('responseSuccess_modifyQuantity'));
    }

    private function getProductFromArrayById($productId, $products)
    {
        foreach ($products as $product) {
            if ($product['productCode'] == $productId)
                return $product;
        }
        return null;
    }

    private function checkForProductsDuplication($missingProducts, $key = 'productId')
    {
        $dupe_array = array();
        foreach ($missingProducts as $val) {
            if (++$dupe_array[$val[$key]] > 1) {
                return true;
            }
        }
        return false;
    }


    function postOnHoldOrder()
    {
        $orderId = $this->f3->get("POST.id");
        $statusId = Constants::ORDER_STATUS_ONHOLD;
        $reasonId = $this->f3->get('POST.reasonId');
        $this->handleUpdateOrderStatus($orderId, $statusId, $reasonId);
    }

    function postProcessOrder()
    {
        $orderId = $this->f3->get("POST.id");
        $statusId = Constants::ORDER_STATUS_PROCESSING;
        $reasonId = $this->f3->get('POST.reasonId');
        $this->handleUpdateOrderStatus($orderId, $statusId, $reasonId);
    }

    function postCancelOrder()
    {
        $orderId = $this->f3->get("POST.id");
        $statusId = Constants::ORDER_STATUS_CANCELED;
        $reasonId = $this->f3->get('POST.reasonId');
        $this->handleUpdateOrderStatus($orderId, $statusId, $reasonId);
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

    function handleUpdateOrderStatus($orderId, $statusId, $reasonId = null)
    {
        $dbOrder = new BaseModel($this->db, "order");
        $dbOrder->getById($orderId);

        if ($dbOrder->dry()) {
            echo $this->webResponse->jsonResponseV2(2, $this->f3->get('vResponse_notFound', $this->f3->get('vEntity_order')), '', null);
            return;
        }

        $missingProductsMsg = [];

        // Add conditions for checks on Order
        // If Order to change to Complete, check if Stock is available for all the items
        if ($statusId == Constants::ORDER_STATUS_COMPLETED) {
            $dbOrderItems = new BaseModel($this->db, "orderDetail");
            $dbProduct = new BaseModel($this->db, "entityProductSell");
            $dbProductSummary = new BaseModel($this->db, "product");
            $dbProductSummary->name = "name_" . $this->objUser->language;
            // check all items if stock is available
            $dbOrderItems->getWhere("orderId = $orderId");

            while (!$dbOrderItems->dry()) {
                $dbProduct->getWhere("id = $dbOrderItems->entityProductId");
                $dbProductSummary->getWhere("id = $dbProduct->productId");

                if ($dbProduct->dry() || $dbProduct->stockStatusId != 1 || $dbProduct->stock < $dbOrderItems->quantity) {
                    $productMsg = $dbProductSummary->name . " - requested " . $dbOrderItems->quantity . ", only " . $dbProduct->stock . " available";
                    array_push($missingProductsMsg, $productMsg);
                }

                $dbOrderItems->next();
            }
        }

        if (count($missingProductsMsg) > 0) {
            $msg = $this->f3->get('vEntity_order') . "<br>" . implode("<br>", $missingProductsMsg);
            echo $this->webResponse->jsonResponseV2(2, $this->f3->get('vResponse_notUpdated', $this->f3->get('vEntity_order')), $msg, null);
            return;
        }

        $dbOrder->updateDateTime = date("Y-m-d H:i:s");
        $dbOrder->statusId = $statusId;
        $dbOrder->userSellerId = $this->objUser->id;

        if (!$dbOrder->update()) {
            echo $this->webResponse->jsonResponseV2(2, $this->f3->get('vResponse_notUpdated', $this->f3->get('vEntity_order')), $dbOrder->exception(), null);
            return;
        }

        $dbOrderLog = new BaseModel($this->db, "orderLog");
        $dbOrderLog->orderId = $orderId;
        $dbOrderLog->userId = $this->objUser->id;
        $dbOrderLog->statusId = $statusId;
        if (!is_null($reasonId)) {
            $dbOrderLog->reasonId = $reasonId;
        }


        if (!$dbOrderLog->add()) {
            echo $this->webResponse->jsonResponseV2(2, $this->f3->get('vResponse_notAdded', $this->f3->get('vEntity_orderLog')), $dbOrderLog->exception(), null);
            return;
        }

        $lowStockProducts = [];

        // Add other management information
        // If order is Complete, adjust stocks and relation (unpaid total)
        // If order is Paid, adjust relation (paid total)
        if ($statusId == Constants::ORDER_STATUS_COMPLETED) {
            $dbOrderItems->getWhere("orderId = $orderId");

            while (!$dbOrderItems->dry()) {
                $dbProduct->getWhere("id = $dbOrderItems->entityProductId");

                $dbProduct->stock -= $dbOrderItems->shippedQuantity;
                $dbProduct->totalOrderCount += 1;
                $dbProduct->totalOrderQuantity += $dbOrderItems->shippedQuantity;
                $dbProduct->update();


                $arrProductId = [$dbOrderItems->entityProductId];
                $dbBonus = new BaseModel($this->db, "vwEntityProductSellBonusDetail");
                $dbBonus->bonusTypeName = "bonusTypeName_" . $this->objUser->language;
                $arrBonus = $dbBonus->getWhere("entityProductId IN (" . implode(",", $arrProductId) . ") AND isActive = 1");
                $arrBonusId = [];
                foreach ($arrBonus as $bonus) {
                    array_push($arrBonusId, $bonus['id']);
                }


                $lowStockByBonus = [];

                foreach ($arrBonus as $bonus) {
                    $bonusType = $bonus['bonusTypeName'];
                    $bonusTypeId = $bonus['bonusTypeId'];
                    $bonusMinOrder = $bonus['minOrder'];
                    $bonusBonus = $bonus['bonus'];

                    $totalBonus = 0;
                    if ($bonusTypeId == Constants::BONUS_TYPE_FIXED) {
                        $totalBonus = $bonusMinOrder + $bonusBonus;
                    } else if ($bonusTypeId == Constants::BONUS_TYPE_DYNAMIC) {
                        $totalBonus = $bonusMinOrder + $bonusBonus;
                    } else if ($bonusTypeId == Constants::BONUS_TYPE_PERCENTAGE) {
                        $totalBonus = $bonusMinOrder + floor($bonusMinOrder * $bonusBonus / 100);
                    }

                    if ($totalBonus > $dbProduct->stock) {
                        $lowStockByBonus[$bonusTypeId][] = $bonusMinOrder . ':' . ($bonusTypeId == Constants::BONUS_TYPE_PERCENTAGE ? '%' . $bonusBonus : $bonusBonus);
                    }
                }


                $averageQuantity = $dbProduct->totalOrderQuantity / $dbProduct->totalOrderCount;
                $lowStockReasons = [];
                if ($dbProduct->stock <= 5 * $averageQuantity)
                    $lowStockReasons[] = 'Average quantity per order is ' . ceil($averageQuantity);

                foreach ($lowStockByBonus as $key => $lowStockByBonusItems) {
                    $bonusName = '';
                    if ($key == Constants::BONUS_TYPE_FIXED) {
                        $bonusName = 'Fixed';
                    } else if ($key == Constants::BONUS_TYPE_DYNAMIC) {
                        $bonusName = 'Dynamic';
                    } else if ($key == Constants::BONUS_TYPE_PERCENTAGE) {
                        $bonusName = 'Percentage';
                    }
                    $lowStockReasons[] = 'You have a ' . $bonusName . ' bonus (' . implode(', ', $lowStockByBonusItems) . ')';
                }

                if (sizeof($lowStockReasons) > 0) {
                    $lowStockProducts[] = ['id' => $dbProduct->id, 'reason' => $lowStockReasons];
                }

                $dbOrderItems->next();
            }

            if (sizeof($lowStockProducts) > 0)
                NotificationHelper::lowStockNotification($this->f3, $this->db, $lowStockProducts);

            $dbRelation = new BaseModel($this->db, "entityRelation");
            $dbRelation->getWhere("entityBuyerId = $dbOrder->entityBuyerId AND entitySellerId = $dbOrder->entitySellerId");

            if ($dbRelation->dry()) {
                $dbRelation->entityBuyerId = $dbOrder->entityBuyerId;
                $dbRelation->entitySellerId = $dbOrder->entitySellerId;
                $dbRelation->currencyId = $dbOrder->currencyId;
                $dbRelation->orderCount = 1;
                $dbRelation->orderCountPaid = 1;
                $dbRelation->orderTotal = $dbOrder->total;
                $dbRelation->orderTotalPaid = $dbOrder->total;
                $dbRelation->add();
            } else {
                $dbRelation->orderCountPaid++;
                $dbRelation->orderTotalPaid += $dbOrder->total;
                $dbRelation->updatedAt = date('Y-m-d H:i:s');
                $dbRelation->update();
            }
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

        $orderStatusUpdateTitle = "Order # " . $dbOrder->id . " status has changed to " . $mapStatusIdName[strval($statusId)];
        $this->f3->set('orderStatusUpdateTitle', $orderStatusUpdateTitle);

        $dbCurrency = new BaseModel($this->db, "currency");
        $currency = $dbCurrency->getWhere("id = $dbOrder->currencyId");

        $dbOrderDetail = new BaseModel($this->db, "vwOrderDetail");
        $dbOrderDetail->name = "productName" . ucfirst($this->objUser->language);
        $arrOrderDetail = $dbOrderDetail->getByField("id", $orderId);

        $this->f3->set('products', $arrOrderDetail);
        $this->f3->set('currencySymbol', $currency->symbol);
        $this->f3->set('subTotal', $dbOrder->subtotal);
        $this->f3->set('tax', $dbOrder->vat);
        $this->f3->set('total', $dbOrder->total);

        $ordersUrl = "web/pharmacy/order/";
        switch ($statusId) {
            case Constants::ORDER_STATUS_PENDING:
            case Constants::ORDER_STATUS_COMPLETED:
            case Constants::ORDER_STATUS_CANCELED:
            case Constants::ORDER_STATUS_PAID:
            case Constants::ORDER_STATUS_MISSING_PRODUCTS:
            case Constants::ORDER_STATUS_CANCELED_PHARMACY:
                $ordersUrl .= "history";
                break;
            case Constants::ORDER_STATUS_ONHOLD:
            case Constants::ORDER_STATUS_PROCESSING:
                $ordersUrl .= "pending";
                break;
            case Constants::ORDER_STATUS_RECEIVED:
                $ordersUrl .= "unpaid";
                break;
        }

        $this->f3->set('ordersUrl', $ordersUrl);

        $htmlContent = View::instance()->render($emailFile);

        $dbEntityUserProfile = new BaseModel($this->db, "vwEntityUserProfile");

        $arrEntityUserProfile = $dbEntityUserProfile->getByField("entityId", $dbOrder->entityBuyerId);
        foreach ($arrEntityUserProfile as $entityUserProfile) {
            $emailHandler->appendToAddress($entityUserProfile->userEmail, $entityUserProfile->userFullName);
        }

        $subject = "Order Status Update";
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: " . getenv('ENV') . ")";
            if (getenv('ENV') == Constants::ENV_LOC) {
                $emailHandler->resetTos();
                $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
            }
        }
        $emailHandler->sendEmail(Constants::EMAIL_ORDER_STATUS_UPDATE, $subject, $htmlContent);
        $emailHandler->resetTos();


        $ordersUrl = "web/distributor/order/";
        switch ($statusId) {
            case Constants::ORDER_STATUS_PENDING:
                $ordersUrl .= "new";
                break;
            case Constants::ORDER_STATUS_ONHOLD:
            case Constants::ORDER_STATUS_PROCESSING:
                $ordersUrl .= "pending";
                break;
            case Constants::ORDER_STATUS_COMPLETED:
            case Constants::ORDER_STATUS_CANCELED:
            case Constants::ORDER_STATUS_PAID:
            case Constants::ORDER_STATUS_CANCELED_PHARMACY:
                $ordersUrl .= "history";
                break;
            case Constants::ORDER_STATUS_RECEIVED:
            case Constants::ORDER_STATUS_MISSING_PRODUCTS:
                $ordersUrl .= "unpaid";
                break;
        }

        $this->f3->set('ordersUrl', $ordersUrl);

        $arrEntityUserProfile = $dbEntityUserProfile->getByField("entityId", $dbOrder->entitySellerId);
        foreach ($arrEntityUserProfile as $entityUserProfile) {
            $emailHandler->appendToAddress($entityUserProfile->userEmail, $entityUserProfile->userFullName);
        }

        $subject = "Order Status Update";
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: " . getenv('ENV') . ")";
            if (getenv('ENV') == Constants::ENV_LOC) {
                $emailHandler->resetTos();
                $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
            }
        }
        $emailHandler->sendEmail(Constants::EMAIL_ORDER_STATUS_UPDATE, $subject, $htmlContent);

        echo $this->webResponse->jsonResponseV2(3, $this->f3->get('vResponse_updated', $this->f3->get('vEntity_order')), $this->f3->get('vResponse_updated', $this->f3->get('vEntity_order')), null);
    }

    function setLocale()
    {
        switch ($this->f3->get('LANGUAGE')) {
            case 'ar':
                \Moment\Moment::setLocale('ar_TN');
                break;
            case 'fr':
                \Moment\Moment::setLocale('fr_FR');
                break;
            case 'en':
            default:
                \Moment\Moment::setLocale('en_US');
                break;
        }
    }

    function getDistributorPendingOrderLog()
    {
        $this->setLocale();
        $timezone = $this->f3->get('PARAMS.timezone');
        $html_content = '';
        $reasons = $this->f3->get('vOrderPending_reasons');

        $pendingOrderLogs = $this->db->exec("CALL spGetDistributorPendingOrdersLog({$this->objUser->id})");

        foreach ($pendingOrderLogs as $index => $pendingOrderLog) {
            $html_content .=
                '<!--begin::Item-->
                <div class="d-flex align-items-start border-bottom mb-4">
                    <!--begin::Symbol-->
                    <div class="symbol symbol-40 symbol-light-primary mr-5">
                        <span class="symbol-label">';

            // show entityBuyerImage if present, otherwise show default svg icon
            if (!is_null($pendingOrderLog['entityBuyerImage'])) {
                $html_content .=
                    '<!-- begin::Image -->
                    <img src="' . $pendingOrderLog['entityBuyerImage'] . '" alt="msg-' . ($index + 1) . '" width="40px" height="40px" style="border-radius: 0.42rem;">
                    <!-- end::Image -->';
            } else {
                $html_content .=
                    '<span class="svg-icon svg-icon-lg svg-icon-primary">
                        <!--begin::Svg Icon | path:assets/media/svg/icons/Home/Library.svg-->
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                            <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                <rect x="0" y="0" width="24" height="24" />
                                <path d="M5,3 L6,3 C6.55228475,3 7,3.44771525 7,4 L7,20 C7,20.5522847 6.55228475,21 6,21 L5,21 C4.44771525,21 4,20.5522847 4,20 L4,4 C4,3.44771525 4.44771525,3 5,3 Z M10,3 L11,3 C11.5522847,3 12,3.44771525 12,4 L12,20 C12,20.5522847 11.5522847,21 11,21 L10,21 C9.44771525,21 9,20.5522847 9,20 L9,4 C9,3.44771525 9.44771525,3 10,3 Z" fill="#000000" />
                                <rect fill="#000000" opacity="0.3" transform="translate(17.825568, 11.945519) rotate(-19.000000) translate(-17.825568, -11.945519)" x="16.3255682" y="2.94551858" width="3" height="18" rx="1" />
                            </g>
                        </svg>
                        <!--end::Svg Icon-->
                    </span>';
            }

            $html_content .=
                '       </span>
                    </div>
                    <!--end::Symbol-->
                    <!--begin::Text-->
                    <div class="d-flex flex-column flex-fill font-weight-bold">
                        <a href="/web/distributor/order/history" class="text-dark text-hover-primary mb-1 font-size-lg">' . $pendingOrderLog['entityBuyer'] . '</a>
                        <div class="text-muted">' . $pendingOrderLog['name_' . $this->objUser->language] . (empty($reasons[$pendingOrderLog['reasonId']]) ? '' : ': ' . $reasons[$pendingOrderLog['reasonId']]) . '</div>
                        <div class="d-flex align-self-end text-muted font-size-sm my-2">' . (new \Moment\Moment(str_replace(' ', 'T', $pendingOrderLog['updatedAt']), $timezone))->calendar() . '</div>
                    </div>
                    <!--end::Text-->
                    
                </div>
                <!--end::Item-->';
        }

        echo $this->webResponse->jsonResponseV2(Constants::STATUS_SUCCESS, $this->f3->get('vOrderPending_logTitle'), $html_content, $pendingOrderLogs);
    }

    function getPrintOrderInvoice()
    {
        $font = 'dejavusans';
        $orderId = $this->f3->get('PARAMS.orderId');

        $dbOrder = new BaseModel($this->db, 'vwOrderEntityUser');
        $arrOrder = $dbOrder->findWhere("id = $orderId");
        $arrOrder = $arrOrder[0];

        $dbEntityBranch = new BaseModel($this->db, "entityBranch");
        $entityBranch = $dbEntityBranch->getByField("id", $arrOrder['branchBuyerId'])[0];

        $dbCity = new BaseModel($this->db, "city");
        $city = $dbCity->getByField("id", $dbEntityBranch['cityId'])[0];

        $dbCountry = new BaseModel($this->db, "country");
        $country = $dbCountry->getByField("id", $city['countryId'])[0];
        $buyerAddress = $country['name_en'].' - '.$city['nameEn'].' - '.$entityBranch['address_en'];

        $pdf = new PDF();
        // create new PDF document
        $pdf = new PDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        // set margin for second page
        $pdf->SetMargins(15, $pdf->top_margin, 15);
        $pdf->AddPage();
        $pdf->SetMargins(15, $pdf->top_margin, 15);

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


        $pharmacyTableHeader = array('Buyer Info');
        if ($arrOrder['userBuyerEmail'] != null) {
            $pharmacyTableData = array(array('#' . $arrOrder['entityBuyerId'] . ' - ' . $arrOrder['entityBuyer']), array($buyerAddress) , array($arrOrder['userBuyerEmail']));
        } else {
            $pharmacyTableData = array(array('#' . $arrOrder['entityBuyerId'] . ' - ' . $arrOrder['entityBuyer']), array($buyerAddress));
        }
        $pdf->FancyOneTitleHeader($pharmacyTableHeader, $pharmacyTableData);
        $pdf->Ln(20);

        $orderDetailHeader = array('ID', 'Name', 'Quantity', 'Unit', 'Total');
        $dbOrderDetail = new BaseModel($this->db, 'vwOrderDetail');
        $arrOrderDetail = $dbOrderDetail->findWhere("id = $orderId");

        $orderDetailData = array();
        foreach ($arrOrderDetail as $item) {
            $quantity = $item['quantity'];
            if ($item['quantityFree'] > 0) {
                $quantity .= " (+" . $item['quantityFree'] . ")";
            }
            array_push($orderDetailData, array(
                $item['productCode'],
                $item['productNameEn'],
                $quantity,
                $item['currency'] . " " . Helper::formatMoney($item['unitPrice'], 2) . ($item['tax'] == 0 ? '' : ' +' . $item['tax'] . "%"),
                $item['currency'] . " " . Helper::formatMoney($item['unitPrice'] * $item['quantity'] * (1.0 + $item['tax'] / 100.0))
            ));
        }

        $pdf->FancyTableOrderDetail($orderDetailHeader, $orderDetailData);

        $pdf->Ln(20);

        if ($arrOrder['subtotal'] != $arrOrder['total']) {
            $pdf->Cell(0, 0, "Subtotal: {$item['currency']} " . Helper::formatMoney($arrOrder['subtotal'], 2), 0, 0, 'R');
            $pdf->Ln(10);
            $pdf->Cell(0, 0, "VAT: {$item['currency']} " . Helper::formatMoney($arrOrder['total'] - $arrOrder['subtotal'], 2), 0, 0, 'R');
        }

        $pdf->Ln(10);
        $pdf->Cell(0, 0, "Total: {$item['currency']} " . Helper::formatMoney($arrOrder['total'], 2), 0, 0, 'R');

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
        // set margin for second page
        $pdf->SetMargins(15, $pdf->top_margin, 15);
        $pdf->AddPage();
        $pdf->SetMargins(15, $pdf->top_margin, 15);

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

        $pharmacyTableHeader = array('Seller Info');
        if ($arrOrder['userSellerEmail'] != null) {
            $pharmacyTableData = array(array('#' . $arrOrder['entitySellerId'] . ' - ' . $arrOrder['entitySeller']), array($arrOrder['userSellerEmail']));
        } else {
            $pharmacyTableData = array(array('#' . $arrOrder['entitySellerId'] . ' - ' . $arrOrder['entitySeller']));
        }
        $pdf->FancyOneTitleHeader($pharmacyTableHeader, $pharmacyTableData);
        $pdf->Ln(20);

        $orderDetailHeader = array('ID', 'Name', 'Quantity', 'Unit', 'Total');
        $dbOrderDetail = new BaseModel($this->db, 'vwOrderDetail');
        $arrOrderDetail = $dbOrderDetail->findWhere("id = $orderId");

        $orderDetailData = array();
        foreach ($arrOrderDetail as $item) {
            $quantity = $item['quantity'];
            if ($item['quantityFree'] > 0) {
                $quantity .= " (+" . $item['quantityFree'] . ")";
            }
            array_push($orderDetailData, array(
                $item['productCode'],
                $item['productNameEn'],
                $quantity,
                $item['currency'] . " " . Helper::formatMoney($item['unitPrice']) . ($item['tax'] == 0 ? '' : ' +' . $item['tax'] . "%"),
                $item['currency'] . " " . Helper::formatMoney($item['unitPrice'] * $item['quantity'] * (1.0 + $item['tax'] / 100.0))
            ));
        }

        $pdf->FancyTableOrderDetail($orderDetailHeader, $orderDetailData);

        $pdf->Ln(20);

        if ($arrOrder['subtotal'] != $arrOrder['total']) {
            $pdf->Cell(0, 0, "Subtotal: {$item['currency']} " . Helper::formatMoney($arrOrder['subtotal'], 2), 0, 0, 'R');
            $pdf->Ln(10);
            $pdf->Cell(0, 0, "VAT: {$item['currency']} " . Helper::formatMoney($arrOrder['total'] - $arrOrder['subtotal'], 2), 0, 0, 'R');
        }

        $pdf->Ln(10);
        $pdf->Cell(0, 0, "Total: {$item['currency']} " . Helper::formatMoney($arrOrder['total'], 2), 0, 0, 'R');

        $pdf->Output();
    }
}
