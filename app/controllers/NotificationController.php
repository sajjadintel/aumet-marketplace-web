<?php

class NotificationController extends Controller {

    function beforeroute()
    {
    }

    function support()
    {
        $supportReasonId = $this->f3->get("POST.supportReasonId");
        $email = $this->f3->get("POST.supportEmail");
        $phone = $this->f3->get("POST.supportPhone");

        if(strlen($email) == 0
            || strlen($phone) == 0
            || strlen($supportReasonId) == 0) {
            $arrError = [];

            if(strlen($email) == 0) {
                array_push($arrError, $this->f3->get('vSupport_emailMissing'));
            }

            if(strlen($phone) == 0) {
                array_push($arrError, $this->f3->get('vSupport_phoneMissing'));
            }

            if(strlen($supportReasonId) == 0) {
                array_push($arrError, $this->f3->get('vSupport_reasonMissing'));
            }

            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = implode("<br>", $arrError);
            echo $this->webResponse->jsonResponse();
            return;
        }


        $supportLog = new BaseModel($this->db, "supportLog");
        $supportLog->entityId = $this->isAuth ? Helper::idListFromArray($this->f3->get('SESSION.arrEntities'))[0] : 0;
        $supportLog->userId = $this->isAuth ? $this->f3->get('SESSION.userId') : 0;
        $supportLog->supportReasonId = $supportReasonId;
        $supportLog->email = $this->isAuth ? $this->f3->get('SESSION.objUser')->email : $email;
        $supportLog->phone = $phone;
        $supportLog->typeId = 1;
        $supportLog->add();

        NotificationHelper::customerSupportNotification($this->f3, $this->db, $supportLog);
        NotificationHelper::customerSupportConfirmNotification($this->f3, $this->db, $supportLog);

        echo $this->webResponse->jsonResponseV2(Constants::STATUS_SUCCESS_SHOW_DIALOG, "Success", $this->f3->get('vSupport_requestSent'));
    }

    public function index()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $renderFile = 'app/notifications/index.php';
            $title = $this->f3->get('vTitle_notifications');
            $offset = array_key_exists('page', $_GET) ? $_GET['page'] - 1 : 0;
            $this->f3->set('notifications', (new Notification)->paginate($offset, 10, ['user_id = ?', $this->objUser->id], ['order' => 'created_at DESC']));
            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $title;
            $this->webResponse->data = View::instance()->render($renderFile);
            echo $this->webResponse->jsonResponse();
        }
    }

    public function markAsRead()
    {
        $notification = (new Notification)->findone([
            'id = ? AND user_id = ?',
            $this->f3->get('PARAMS.notificationId'),
            $this->objUser->id
        ]);

        if ($notification) {
            $notification->read = true;
            $notification->save();
            return $this->jsonResponse([]);
        }
    }
}
