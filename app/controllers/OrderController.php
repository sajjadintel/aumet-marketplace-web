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

            $data['order'] = $arrOrder[0];
            $data['orderDetail'] = $arrOrderDetail;

            echo $this->webResponse->jsonResponseV2(1, "", "", $data);
            return;
        }
    }

    function postDistributorOrdersNew()
    {
        $status = 'new';
        $this->handlePostDistributorOrders($status);
    }

    function postDistributorOrdersPending()
    {
        $status = 'pending';
        $this->handlePostDistributorOrders($status);
    }

    function postDistributorOrdersUnpaid()
    {
        $status = 'unpaid';
        $this->handlePostDistributorOrders($status);
    }

    function postDistributorOrdersHistory()
    {
        $status = 'history';
        $this->handlePostDistributorOrders($status);
    }

    function handlePostDistributorOrders($status)
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

    function postPaidOrder()
    {
        $orderId = $this->f3->get("POST.id");
        $statusId = Constants::ORDER_STATUS_PAID;
        $this->handleUpdateOrderStatus($orderId, $statusId);

        // TODO: Update entityRelation table with new details
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

    function getPrintOrderInvoice()
    {
        $orderId = $this->f3->get('PARAMS.orderId');
        $pdf = new PDF();
        $pdf->AddPage();
        $pdf->AliasNbPages();

        // Title
        $pdf->SetFont('Times', 'B', 14);
        $pdf->Cell(0, 10, 'Order #111', 0, 0, 'R');
        $pdf->Ln(5);
        $pdf->Cell(0, 10, 'Violet Drug Store', 0, 0, 'R');
        $pdf->Ln(10);

        $pdf->SetFont('Times', '', 14);
        $pdf->Cell(0, 10, '2020-10-08 22:21:49', 0, 0, 'R');
        $pdf->Ln(20);

        $pdf->SetFont('Times', '', 11);

        $pharmacyTableHeader = array('Code', 'Customer Name', 'Email');
        $pharmacyTableData = array(array('123', 'Pharmacy Salam', 'd.daoud@aumet.me'));
        $pdf->FancyTable($pharmacyTableHeader, $pharmacyTableData);
        $pdf->Ln(20);

        $orderDetailHeader = array('Brand Name', 'Scientific Name', 'Quantity', 'Unit Price', 'VAT', 'Total');
        $orderDetailData = array(
            array('Bone Holding Forceps', 'Bone Holding Forceps', '1 (8336)', '$30.95', '0%', '$30.95'), array('Tonsil Tissue Forceps', 'Tissue Forceps', '1 (9510)', '$49.47', '0%', '$49.47'),
            array('Medicine Cup', 'Medicine Cups', '1 (4213)', '27.61', '0%', '$27.61'), array('Collin Specula', 'Collin Specula', '1 (6120)', '$27.61', '0%', '$55.22'),
            array('Catheter Mount', 'Catheter Mount', '1 (257)', '$28.92', '0%', '$28.92')
        );
        $pdf->FancyTableOrderDetail($orderDetailHeader, $orderDetailData);

        $pdf->Ln(10);

        $pdf->Cell(0, 0, 'Order Total: $192.17', 0, 0, 'R');

        $pdf->Output();
    }
}
