<?php

class AppController extends Controller {

    function get() {
        echo View::instance()->render('app/layout/layout.php');
    }
}