<?php

class CustomersController extends Controller {

    function getOrderCustomersFeedback()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vModule_feedback_title');
            $this->webResponse->data = View::instance()->render('app/entity/feedback/list.php');
            echo $this->webResponse->jsonResponse();
        }
    }


    function postOrderCustomersFeedback()
    {
        ## Read values from Datatables
        $datatable = new Datatable($_POST);
        $query = "";

        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
        $query = "entitySellerId IN ($arrEntityId)";

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

}
