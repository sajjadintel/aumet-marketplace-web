<?php

class LandingController extends Controller {

    function beforeroute()
    {
        if ($this->isAuth) {
            $this->f3->reroute('/app');
        } else {
            $this->rerouteAuth();
        }
    }

    function get() {
        echo "Landing...";
    }
}