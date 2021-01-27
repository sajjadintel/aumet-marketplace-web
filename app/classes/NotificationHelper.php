<?php

class NotificationHelper {

    /**
     * Does something interesting
     *
     * @param \Base $f3 f3 instance
     * @param BaseModel $dbConnection db connection instance
     * @param int[] $lowStockProducts Array of product Ids
     */
    public static function lowStockNotification($f3, $dbConnection, $lowStockProducts)
    {
        $dbProduct = new BaseModel($dbConnection, "vwEntityProductSell");
        $dbProduct->name = "productName_en";
        $dbProduct->getWhere("id IN (" . implode(",", $lowStockProducts) . ")");

        $emailHandler = new EmailHandler($dbConnection);
        $emailFile = "email/layout.php";
        $f3->set('domainUrl', getenv('DOMAIN_URL'));
        $f3->set('title', 'Low Stock');
        $f3->set('emailType', 'lowStock');
        $f3->set('products', $dbProduct);


        $dbEntityUserProfile = new BaseModel($dbConnection, "vwEntityUserProfile");
        $arrEntityUserProfile = $dbEntityUserProfile->getByField("entityId", $dbProduct->entityId);
        foreach ($arrEntityUserProfile as $entityUserProfile) {
            $emailHandler->appendToAddress($entityUserProfile->userEmail, $entityUserProfile->userFullName);
        }

        $htmlContent = View::instance()->render($emailFile);

        $subject = "Low Stock";
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: " . getenv('ENV') . ")";

            if (getenv('ENV') == Constants::ENV_LOC) {
                $emailHandler->resetTos();
                $emailHandler->appendToAddress("sajjadintel@gmail.com", "Sajjad intel");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
            }
        }

        $emailHandler->sendEmail(Constants::EMAIL_LOW_STOCK, $subject, $htmlContent);
        $emailHandler->resetTos();
    }

    /**
     * Does something interesting
     *
     * @param \Base $f3 f3 instance
     * @param BaseModel $dbConnection db connection instance
     * @param int $orderId order id
     */
    public static function orderMissingProductsNotification($f3, $dbConnection, $orderId)
    {
        $dbMissingProduct = new BaseModel($dbConnection, "vwOrderMissingProductDetail");
        $dbMissingProduct->name = "productNameEn";
        $dbMissingProduct->getWhere("orderId = $orderId");

        $emailHandler = new EmailHandler($dbConnection);
        $emailFile = "email/layout.php";
        $title = "Missing Product in Order #" . $orderId;
        $f3->set('domainUrl', getenv('DOMAIN_URL'));
        $f3->set('title', $title);
        $f3->set('emailType', 'missingProducts');
        $f3->set('products', $dbMissingProduct);


        $dbEntityUserProfile = new BaseModel($dbConnection, "vwEntityUserProfile");
        $arrEntityUserProfile = $dbEntityUserProfile->getByField("entityId", $dbMissingProduct->entityId);
        $entityName = $arrEntityUserProfile[0]->entityName_en;
        $f3->set('entityName', $entityName);

        foreach ($arrEntityUserProfile as $entityUserProfile) {
            $emailHandler->appendToAddress($entityUserProfile->userEmail, $entityUserProfile->userFullName);
        }

        $htmlContent = View::instance()->render($emailFile);

        $subject = $title;
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: " . getenv('ENV') . ")";

            if (getenv('ENV') == Constants::ENV_LOC) {
                $emailHandler->resetTos();
                $emailHandler->appendToAddress("sajjadintel@gmail.com", "Sajjad intel");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
            }
        }


        $emailHandler->sendEmail(Constants::EMAIL_MISSING_PRODUCTS, $subject, $htmlContent);
        $emailHandler->resetTos();
    }

    /**
     * Does something interesting
     *
     * @param \Base $f3 f3 instance
     * @param \Db\SQL $dbConnection db connection instance
     * @param int $orderId order id
     * @param int[] $modifiedOrderDetailIds array of modified order detail ids
     * @param int $entityBuyerId entitySellerId
     */
    public static function orderModifyShippedQuantityNotification($f3, $dbConnection, $orderId, $modifiedOrderDetailIds, $entityBuyerId)
    {
        $dbProduct = new BaseModel($dbConnection, "vwOrderDetail");
        $dbProduct->name = "productNameEn";
        $dbProduct->getWhere("id = $orderId AND orderDetailId IN (" . implode(",", $modifiedOrderDetailIds) . ")");

        $emailHandler = new EmailHandler($dbConnection);
        $emailFile = "email/layout.php";
        $title = "Modify Shipped Quantity in Order #" . $orderId;
        $f3->set('domainUrl', getenv('DOMAIN_URL'));
        $f3->set('title', $title);
        $f3->set('emailType', 'modifiedOrderProducts');
        $f3->set('products', $dbProduct);


        $dbEntityUserProfile = new BaseModel($dbConnection, "vwEntityUserProfile");
        $arrEntityUserProfile = $dbEntityUserProfile->getByField("entityId", $entityBuyerId);
        $entityName = $arrEntityUserProfile[0]->entityName_en;
        $f3->set('entityName', $entityName);

        foreach ($arrEntityUserProfile as $entityUserProfile) {
            $emailHandler->appendToAddress($entityUserProfile->userEmail, $entityUserProfile->userFullName);
        }

        $htmlContent = View::instance()->render($emailFile);

        $subject = $title;
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: " . getenv('ENV') . ")";

            if (getenv('ENV') == Constants::ENV_LOC) {
                $emailHandler->resetTos();
                $emailHandler->appendToAddress("sajjadintel@gmail.com", "Sajjad intel");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
            }
        }


        $emailHandler->sendEmail(Constants::EMAIL_MODIFY_SHIPPED_QUANTITY, $subject, $htmlContent);
        $emailHandler->resetTos();
    }

    public static function customerSupportNotification($f3, $dbConnection, $supportLog)
    {
        $supportReason = new BaseModel($dbConnection, 'supportReason');
        $supportReason->getWhere('id=' . $supportLog->supportReasonId);

        $emailHandler = new EmailHandler($dbConnection);
        $emailFile = "email/layout.php";
        $f3->set('domainUrl', getenv('DOMAIN_URL'));
        $f3->set('title', 'Customer Support Request');
        $f3->set('emailType', 'customerSupport');
        $f3->set('email', $supportLog->email);
        $f3->set('phone', $supportLog->phone);
        $f3->set('reason', $supportReason->name_en);

        $emailList = explode(';', getenv('SUPPORT_EMAIL'));
        for ($i = 0; $i < count($emailList); $i++) {
            $currentEmail = explode(',', $emailList[$i]);
            if (count($currentEmail) == 2) {
                $emailHandler->appendToAddress($currentEmail[0], $currentEmail[1]);
            } else {
                $emailHandler->appendToAddress($currentEmail[0], $currentEmail[0]);
            }
        }

        $htmlContent = View::instance()->render($emailFile);

        $subject = "Customer Support Request";
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: " . getenv('ENV') . ")";

            if (getenv('ENV') == Constants::ENV_LOC) {
                $emailHandler->resetTos();
                $emailHandler->appendToAddress("sajjadintel@gmail.com", "Sajjad intel");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
            }
        }

        $emailHandler->sendEmail(Constants::EMAIL_CUSTOMER_SUPPORT_REQUEST, $subject, $htmlContent);
        $emailHandler->resetTos();
    }

    public static function customerSupportConfirmNotification($f3, $dbConnection, $supportLog)
    {

        $supportReason = new BaseModel($dbConnection, 'supportReason');
        $supportReason->getWhere('id=' . $supportLog->supportReasonId);

        $emailHandler = new EmailHandler($dbConnection);
        $emailFile = "email/layout.php";
        $f3->set('domainUrl', getenv('DOMAIN_URL'));
        $f3->set('title', 'Customer Support Confirmation');
        $f3->set('emailType', 'customerSupport');
        $f3->set('email', $supportLog->email);
        $f3->set('phone', $supportLog->phone);
        $f3->set('reason', $supportReason->name_en);


        // if not logged in
        if (!$supportLog->entityId) {
            $emailHandler->appendToAddress($supportLog->email, '');
        } else {
            $dbEntityUserProfile = new BaseModel($dbConnection, "vwEntityUserProfile");
            $arrEntityUserProfile = $dbEntityUserProfile->getByField("entityId", $supportLog->entityId);
            foreach ($arrEntityUserProfile as $entityUserProfile) {
                $emailHandler->appendToAddress($entityUserProfile->userEmail, $entityUserProfile->userFullName);
            }
        }


        $htmlContent = View::instance()->render($emailFile);

        $subject = "Customer Support Confirmation";
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: " . getenv('ENV') . ")";

            if (getenv('ENV') == Constants::ENV_LOC) {
                $emailHandler->resetTos();
                $emailHandler->appendToAddress("sajjadintel@gmail.com", "Sajjad intel");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
            }
        }

        $emailHandler->sendEmail(Constants::EMAIL_CUSTOMER_SUPPORT_CONFIRMATION, $subject, $htmlContent);
        $emailHandler->resetTos();
    }


    /**
     * Does something interesting
     *
     * @param \Base $f3 f3 instance
     * @param BaseModel $dbConnection db connection instance
     * @param \BaseModel $user user Model
     * @param string $token reset password token
     */
    public static function resetPasswordNotification($f3, $dbConnection, $user, $token)
    {
        $emailHandler = new EmailHandler($dbConnection);
        $emailFile = "email/layout.php";
        $title = 'Reset Password';
        $f3->set('domainUrl', getenv('DOMAIN_URL'));
        $f3->set('title', $title);
        $f3->set('emailType', 'resetPassword');
        $f3->set('token', $token);


        $emailHandler->appendToAddress($user->email, $user->fullname);

        $htmlContent = View::instance()->render($emailFile);

        $subject = $title;
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: " . getenv('ENV') . ")";

            if (getenv('ENV') == Constants::ENV_LOC) {
                $emailHandler->resetTos();
                $emailHandler->appendToAddress("sajjadintel@gmail.com", "Sajjad intel");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
            }
        }

        $emailHandler->sendEmail(Constants::EMAIL_RESET_PASSWORD, $subject, $htmlContent);
        $emailHandler->resetTos();
    }

}
