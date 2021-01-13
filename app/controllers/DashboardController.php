<?php

class DashboardController extends Controller {

    function get()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/dashboard");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            if ($this->objUser->menuId == 1) {
                $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
                $query = "entitySellerId IN ($arrEntityId)";

                $dbData = new BaseModel($this->db, "vwDashboardSellerToday");
                $dbData->getWhere($query);

                $dbDataNewCustomer = new BaseModel($this->db, "vwNewCustomerToday");
                $dbDataNewCustomer->getWhere($query);

                $dbDataYesterday = new BaseModel($this->db, "vwDashboardSellerYesterday");
                $dbDataYesterday->getWhere($query);

                $dbDataNewCustomerYesterday = new BaseModel($this->db, "vwNewCustomerYesterday");
                $dbDataNewCustomerYesterday->getWhere($query);
                // $data = $data[0];

                $this->f3->set('dashboard_revenue', is_null($dbData['revenue']) ? 0 : $dbData['revenue']);
                $this->f3->set('dashboard_order', is_null($dbData['orderCount']) ? 0 : $dbData['orderCount']);
                $this->f3->set('dashboard_customer', is_null($dbData['customerCount']) ? 0 : $dbData['customerCount']);
                $this->f3->set('dashboard_new_customer', is_null($dbDataNewCustomer['newCustomerCount']) ? 0 : $dbDataNewCustomer['newCustomerCount']);

                $this->f3->set('dashboard_revenueYesterday', is_null($dbDataYesterday['revenue']) ? 0 : $dbDataYesterday['revenue']);
                $this->f3->set('dashboard_orderYesterday', is_null($dbDataYesterday['orderCount']) ? 0 : $dbDataYesterday['orderCount']);
                $this->f3->set('dashboard_customerYesterday', is_null($dbDataYesterday['customerCount']) ? 0 : $dbDataYesterday['customerCount']);
                $this->f3->set('dashboard_new_customerYesterday', is_null($dbDataNewCustomerYesterday['newCustomerCount']) ? 0 : $dbDataNewCustomerYesterday['newCustomerCount']);

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
                $this->webResponse->title = $this->f3->get('vTitle_dashboard');
                $this->webResponse->data = View::instance()->render('app/dashboard/seller.php');
                echo $this->webResponse->jsonResponse();
            } else {
                $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
                $query = "entityBuyerId IN ($arrEntityId)";

                $dbData = new BaseModel($this->db, "vwDashboardBuyerToday");
                $dbData->getWhere($query);

                $dbDataYesterday = new BaseModel($this->db, "vwDashboardBuyerYesterday");
                $dbDataYesterday->getWhere($query);

                $this->f3->set('dashboard_order', is_null($dbData['orderCount']) ? 0 : $dbData['orderCount']);
                $this->f3->set('dashboard_invoice', is_null($dbData['invoice']) ? 0 : $dbData['invoice']);

                $this->f3->set('dashboard_orderYesterday', is_null($dbDataYesterday['orderCount']) ? 0 : $dbDataYesterday['orderCount']);
                $this->f3->set('dashboard_invoiceYesterday', is_null($dbDataYesterday['invoice']) ? 0 : $dbDataYesterday['invoice']);

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
                $this->webResponse->title = $this->f3->get('vTitle_dashboard');
                $this->webResponse->data = View::instance()->render('app/dashboard/buyer.php');
                echo $this->webResponse->jsonResponse();
            }
        }
    }

    function support()
    {
        $supportReasonId = $this->f3->get("POST.supportReasonId");
        $email = $this->f3->get("POST.email");
        $phone = $this->f3->get("POST.phone");
        $supportReasonId = 1;
        $email = 'asdfasdf@asddf.asdf';
        $phone = '2341234';

        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));

        $supportLog = new BaseModel($this->db, "supportLog");
        $supportLog->entityId = $arrEntityId[0];
        $supportLog->userId = $this->f3->get('SESSION.userId');
        $supportLog->supportReasonId = $supportReasonId;
        $supportLog->email = $email;
        $supportLog->phone = $phone;
        $supportLog->typeId = 1;
//        $supportLog->add();

        NotificationHelper::customerSupportNotification($this->f3, $this->db, $supportLog);
        NotificationHelper::customerSupportConfirmNotification($this->f3, $this->db, $supportLog);

        echo $this->webResponse->jsonResponseV2(Constants::STATUS_SUCCESS_SHOW_DIALOG, "Success", $this->f3->get('vSupport_requestSent'));
    }

}
