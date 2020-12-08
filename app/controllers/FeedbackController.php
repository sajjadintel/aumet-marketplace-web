<?php

class FeedbackController extends Controller {
    function getOrderCustomersFeedback()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));

            $this->webResponse->errorCode = 1;
            $this->webResponse->title = $this->f3->get('vModule_feedback_title');
            $this->webResponse->data = View::instance()->render('app/entity/feedback/list.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function postOrderCustomersFeedback()
    {
        $query = "";
        $datatable = array_merge(array('pagination' => array(), 'sort' => array(), 'query' => array()), $_REQUEST);

        $sort = !empty($datatable['sort']['sort']) ? $datatable['sort']['sort'] : 'desc';
        $field = !empty($datatable['sort']['field']) ? $datatable['sort']['field'] : 'id';

        $meta = array();
        $page = !empty($datatable['pagination']['page']) ? (int)$datatable['pagination']['page'] : 1;
        $perpage = !empty($datatable['pagination']['perpage']) ? (int)$datatable['pagination']['perpage'] : 10;

        $total = 0;
        $offset = ($page - 1) * $perpage;

        global $dbConnection;

        $dbData = new BaseModel($dbConnection, "vwOrderUserRate");
        $dbData->entityName = "entityName_en";
        $dbData->rateName = "rateName_en";
        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));

        $query = "entitySellerId IN ($arrEntityId)";
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


    function getPharmacyFeedbacksPending()
    {
        $this->handleGetPharmacyFeedbacks('pending');
    }

    function getPharmacyFeedbacksHistory()
    {
        $this->handleGetPharmacyFeedbacks('history');
    }

    function handleGetPharmacyFeedbacks($status)
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $title = $this->f3->get('vModule_pharmacy_feedback_title');

            switch ($status) {
                case 'pending':
                    $renderFile = 'app/entity/feedback/listPharmacy.php';
                    $this->f3->set('vModule_order_header', $this->f3->get('vModule_pharmacy_feedback_header_pending'));
                    break;
                case 'history':
                    $renderFile = 'app/entity/feedback/listFeedbackHistoryPharmacy.php';
                    $this->f3->set('vModule_order_header', $this->f3->get('vModule_pharmacy_feedback_header_history'));
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


    function postPharmacyFeedbacksPending()
    {
        $datatable = new Datatable($_POST);
        $query = "";

        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
        $query = "entityBuyerId IN ($arrEntityId)";
        $query .= " AND statusId IN (6,7)";

        $fullQuery = $query;

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

    function postPharmacyFeedbacksHistory()
    {
        ## Read values from Datatables
        $datatable = new Datatable($_POST);
        $query = "";

        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
        $query = "entityBuyerId IN ($arrEntityId)";
//                $query .= " AND statusId IN (6,7)";

        $fullQuery = $query;

        $dbData = new BaseModel($this->db, "vwOrderUserRate");

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




    function getPharmacyFeedback()
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

    function postPharmacyFeedback()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/pharmacy/feedback/pending");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $orderId = $this->f3->get('POST.id');
            $rating = $this->f3->get('POST.rating');
            $comment = $this->f3->get('POST.comment');
            $userId = $this->objUser->id;

            $orderRating = new BaseModel($this->db, "orderRating");
            $orderRating = $orderRating->findWhere("orderId = '$orderId' AND userId= '$userId'");

            if ($orderRating != null) {
                $this->webResponse->errorCode = 1;
                $this->webResponse->title = $this->f3->get("alreadySentFeedback");
                echo $this->webResponse->jsonResponse();
                return;
            }

            $dbProduct = new BaseModel($this->db, "orderRating");
            $dbProduct->orderId = $orderId;
            $dbProduct->userId = $userId;
            $dbProduct->rateId = $rating;
            $dbProduct->feedback = $comment;

            $dbProduct->add();

            $this->webResponse->errorCode = 1;
            $this->webResponse->title = $this->f3->get("feedbackSaved");
            echo $this->webResponse->jsonResponse();
        }
    }

}
