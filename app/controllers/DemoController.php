<?php

class DemoController extends Controller
{

  function get()
  {
    $this->f3->set("LANGUAGE", "en");

    if (!$this->f3->ajax()) {
      $this->f3->set("pageURL", "/web/demo/editor/scientificnames");
      echo View::instance()->render('app/layout/layout.php');
    } else {
      $this->webResponse->errorCode = 1;
      $this->webResponse->title = $this->f3->get('vTitle_dashboard');
      $this->webResponse->data = View::instance()->render('app/demo/scientificNames.php');
      echo $this->webResponse->jsonResponse();
    }
  }
}
