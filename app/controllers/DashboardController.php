<?php

class DashboardController extends Controller
{

    function get()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/dashboard");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $this->webResponse->errorCode = 1;
            $this->webResponse->title = $this->f3->get('vTitle_dashboard');
            $this->webResponse->data = View::instance()->render('app/dashboard/seller.php');
            echo $this->webResponse->jsonResponse();
        }
    }
}
