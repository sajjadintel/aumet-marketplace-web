<?php

class LandingController extends Controller
{

    function beforeroute()
    {
        if ($this->isAuth) {
            $this->f3->reroute('/web');
        } else {
            $this->rerouteAuth();
        }
    }

    function get()
    {
        echo "Landing...";
    }
}
