<?php

class NotificationHelper {

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

    public static function orderMissingProductsNotification($f3, $dbConnection, $orderId)
    {
        $dbMissingProduct = new BaseModel($dbConnection, "vwOrderMissingProductDetail");
        $dbMissingProduct->name = "productNameEn";
        $dbMissingProduct->getWhere("orderId = $orderId");

        $emailHandler = new EmailHandler($dbConnection);
        $emailFile = "email/layout.php";
        $f3->set('domainUrl', getenv('DOMAIN_URL'));
        $f3->set('title', 'Missing Products');
        $f3->set('emailType', 'missingProducts');
        $f3->set('products', $dbMissingProduct);


        $dbEntityUserProfile = new BaseModel($dbConnection, "vwEntityUserProfile");
        $arrEntityUserProfile = $dbEntityUserProfile->getByField("entityId", $dbMissingProduct->entityId);
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


        $emailHandler->sendEmail(Constants::EMAIL_MISSING_PRODUCTS, $subject, $htmlContent);
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


        $supportEmails = getenv('SUPPORT_EMAIL');
        $supportEmails = explode($supportEmails, ',');
        foreach ($supportEmails as $supportEmail) {
            $emailHandler->appendToAddress($supportEmail, $supportEmail);
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


        $dbEntityUserProfile = new BaseModel($dbConnection, "vwEntityUserProfile");
        $arrEntityUserProfile = $dbEntityUserProfile->getByField("entityId", $supportLog->entityId);
        foreach ($arrEntityUserProfile as $entityUserProfile) {
            $emailHandler->appendToAddress($entityUserProfile->userEmail, $entityUserProfile->userFullName);
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

}