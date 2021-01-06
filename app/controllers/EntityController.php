<?php

class EntityController extends Controller
{
    function getEntityCustomers()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $dbStockStatus = new BaseModel($this->db, "stockStatus");
            $dbStockStatus->name = "name_" . $this->objUser->language;
            $arrStockStatus = $dbStockStatus->all("id asc");
            $this->f3->set('arrStockStatus', $arrStockStatus);

            $this->webResponse->errorCode = 1;
            $this->webResponse->title = $this->f3->get('vModule_customer_title');
            $this->webResponse->data = View::instance()->render('app/entity/customers/customers.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function getEntityCustomerDetails()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", $this->f3->get('SERVER.REQUEST_URI'));
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $id = $this->f3->get('PARAMS.id');

            $dbRelation = new BaseModel($this->db, "vwEntityRelation");
            $arrRelation = $dbRelation->findWhere("id = '$id'");

            $data['relation'] = $arrRelation[0];

            echo $this->webResponse->jsonResponseV2(1, "", "", $data);
            return;
        }
    }

    function postEntityCustomers()
    {
        ## Read values from Datatables
        $datatable = new Datatable($_POST);
        $query = "";

        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
        $query = "entitySellerId IN ($arrEntityId)";

        $fullQuery = $query;

        $dbData = new BaseModel($this->db, "vwEntityRelation");
        $dbData->buyerName = "buyerName_".$this->f3->get("LANGUAGE");
        $dbData->sellerName = "sellerName_".$this->f3->get("LANGUAGE");
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

    function getEntityUsers()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {

            $dbStockStatus = new BaseModel($this->db, "stockStatus");
            $dbStockStatus->name = "name_" . $this->objUser->language;
            $arrStockStatus = $dbStockStatus->all("id asc");
            $this->f3->set('arrStockStatus', $arrStockStatus);

            $this->webResponse->errorCode = 1;
            $this->webResponse->title = $this->f3->get('vModule_users_title');
            $this->webResponse->data = View::instance()->render('app/users/list.php');
            echo $this->webResponse->jsonResponse();
        }
    }
}
