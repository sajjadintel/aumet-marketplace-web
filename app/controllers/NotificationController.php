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
        $orderId = $this->f3->get("POST.supportOrder");
        $entityBuyerId = $this->f3->get("POST.supportCustomer");
        $requestCall = $this->f3->get("POST.requestCall");
        $message = $this->f3->get("POST.message");

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
        $supportLog->orderId = $orderId;
        $supportLog->entityBuyerId = $entityBuyerId;
        $supportLog->requestCall = $requestCall;
        $supportLog->message = $message;
        $supportLog->typeId = 1;
        $supportLog->add();

        NotificationHelper::customerSupportNotification($this->f3, $this->db, $supportLog);
        NotificationHelper::customerSupportConfirmNotification($this->f3, $this->db, $supportLog);

        echo $this->webResponse->jsonResponseV2(Constants::STATUS_SUCCESS_SHOW_DIALOG, "Success", $this->f3->get('vSupport_requestSent'));
    }

}
