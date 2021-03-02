<?php

class MarketingController extends Controller
{
    function getMarketingAnnouncements()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vModule_announcements_title');
            $this->webResponse->data = View::instance()->render('app/marketing/announcements/home.php');
            echo $this->webResponse->jsonResponse();
        }
    }
}
