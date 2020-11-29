<?php

class DashboardController extends Controller
{

    function get()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/dashboard");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            if ($this->objUser->menuId == 1) {
                $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
                $query = "entitySellerId IN ($arrEntityId)";

                $dbData = new BaseModel($this->db, "vwDashboardToday");
                $dbData->getWhere($query);

                $dbDataNewCustomer = new BaseModel($this->db, "vwNewCustomerToday");
                $dbDataNewCustomer->getWhere($query);

                $dbDataYesterday = new BaseModel($this->db, "vwDashboardYesterday");
                $dbDataYesterday->getWhere($query);

                $dbDataNewCustomerYesterday = new BaseModel($this->db, "vwNewCustomerYesterday");
                $dbDataNewCustomerYesterday->getWhere($query);
                // $data = $data[0];

                $this->f3->set('dashboard_revenue', is_null($dbData['revenue']) ? 0 : $dbData['revenue']);
                $this->f3->set('dashboard_order', is_null($dbData['orderCount']) ? 0 : $dbData['orderCount']);
                $this->f3->set('dashboard_customer', is_null($dbData['customerCount']) ? 0 : $dbData['customerCount']);
                $this->f3->set('dashboard_new_customer', is_null($dbDataNewCustomer['newCustomerCount']) ? 0 : $dbDataNewCustomer['newCustomerCount']);

                $this->f3->set('dashboard_revenueYesterday', is_null($dbDataYesterday['revenue']) ? 0 : $dbDataYesterday['revenue']);
                $this->f3->set('dashboard_order', is_null($dbDataYesterday['orderCount']) ? 0 : $dbDataYesterday['orderCount']);
                $this->f3->set('dashboard_customerYesterday', is_null($dbDataYesterday['customerCount']) ? 0 : $dbDataYesterday['customerCount']);
                $this->f3->set('dashboard_new_customerYesterday', is_null($dbDataNewCustomerYesterday['newCustomerCount']) ? 0 : $dbDataNewCustomerYesterday['newCustomerCount']);

                $this->webResponse->errorCode = 1;
                $this->webResponse->title = $this->f3->get('vTitle_dashboard');
                $this->webResponse->data = View::instance()->render('app/dashboard/seller.php');
                echo $this->webResponse->jsonResponse();
            } else {
                $this->webResponse->errorCode = 1;
                $this->webResponse->title = $this->f3->get('vTitle_dashboard');
                $this->webResponse->data = View::instance()->render('app/dashboard/buyer.php');
                echo $this->webResponse->jsonResponse();
            }
        }
    }
}
