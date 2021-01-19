<?php

class NotificationController extends Controller {

    function beforeroute()
    {
    }

    function support()
    {
        $supportReasonId = $this->f3->get("POST.supportReasonId");
        $email = $this->f3->get("POST.email");
        $phone = $this->f3->get("POST.phone");


        $supportLog = new BaseModel($this->db, "supportLog");
        $supportLog->entityId = $this->isAuth ? Helper::idListFromArray($this->f3->get('SESSION.arrEntities'))[0] : 0;
        $supportLog->userId = $this->isAuth ? $this->f3->get('SESSION.userId') : 0;
        $supportLog->supportReasonId = $supportReasonId;
        $supportLog->email = $email;
        $supportLog->phone = $phone;
        $supportLog->typeId = 1;
        $supportLog->add();

        NotificationHelper::customerSupportNotification($this->f3, $this->db, $supportLog);
        NotificationHelper::customerSupportConfirmNotification($this->f3, $this->db, $supportLog);

        echo $this->webResponse->jsonResponseV2(Constants::STATUS_SUCCESS_SHOW_DIALOG, "Success", $this->f3->get('vSupport_requestSent'));
    }

}
