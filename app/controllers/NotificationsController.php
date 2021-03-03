<?php

class NotificationsController extends Controller
{
    public function index()
    {
        $this->f3->set('notifications', (new Notification)->paginate(0, 10, ['user_id' => $this->objUser->id]));

        $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
        $this->webResponse->title = $this->f3->get('vTitle_cart');
        $this->webResponse->data = View::instance()->render('app/notifications/index.php');
        $this->jsonResponse();
    }
}