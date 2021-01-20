<?php

class ProfileController extends Controller {

    function getProfile()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/profile");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            // Get all countries
            $dbCountry = new BaseModel($this->db, "country");
            $dbCountry->name = "name_" . $this->objUser->language;
            $arrCountry = $dbCountry->findAll();
            $this->f3->set('arrCountry', $arrCountry);

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vTitle_profile');
            $this->webResponse->data = View::instance()->render('app/profile/home.php');
            echo $this->webResponse->jsonResponse();
        }
    }
}