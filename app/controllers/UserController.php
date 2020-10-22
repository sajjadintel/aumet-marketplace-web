<?php

class UserController extends Controller
{

  function getMenu()
  {
  }

  function switchLanguage(){
      $lang = $this->f3->get("PARAMS.lang");
      $this->f3->set('SESSION.userLang', $lang);
      $this->f3->set('SESSION.userLangDirection', $lang == "ar" ? "rtl" : "ltr");
      $this->f3->set('LANGUAGE', $lang);

      $this->f3->reroute("/web");
  }
}
