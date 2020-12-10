<?php

class FeedbackController extends Controller {

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

        $dbData = new BaseModel($this->db, "vwOrderEntityUserFeedbackPending");

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
