<?php

class CustomerCareController extends Controller
{

    function get()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/customercare");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $this->webResponse->errorCode = 1;
            $this->webResponse->title = $this->f3->get('vTitle_customerCare');
            $this->webResponse->data = View::instance()->render('app/customerCare/home.php');
            echo $this->webResponse->jsonResponse();
        }
    }
}
