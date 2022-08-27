<?php

use Ahc\Jwt\JWT;

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
        $arrProducts = $dbProduct->findWhere("id IN (" . implode(",", array_column($lowStockProducts, 'id')) . ")");

        for ($i = 0; $i < sizeof($arrProducts); $i++) {
            $arrProducts[$i]['reason'] = self::getProductFromArrayById($arrProducts[$i]['id'], $lowStockProducts)['reason'];
        }

        $emailHandler = new EmailHandler($dbConnection);
        $emailFile = "email/layout.php";
        $f3->set('domainUrl', getenv('DOMAIN_URL'));
        $f3->set('title', 'Low Stock');
        $f3->set('emailType', 'lowStock');
        $f3->set('products', $arrProducts);


        $dbEntityUserProfile = new BaseModel($dbConnection, "vwEntityUserProfile");
        $arrEntityUserProfile = $dbEntityUserProfile->getByField("entityId", $arrProducts[0]['entityId']);
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

    static function getProductFromArrayById($productId, $products)
    {
        foreach ($products as $product) {
            if ($product['id'] == $productId)
                return $product;
        }
        return null;
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
    public static function orderModifyShippedQuantityNotification($f3, $dbConnection, $orderId, $modifiedOrderDetailIds, $userId, $entityBuyerId)
    {
        $dbProduct = new BaseModel($dbConnection, "vwOrderDetail");
        $dbProduct->name = "productNameEn";
        $dbProduct->getWhere("id = $orderId AND orderDetailId IN (" . implode(",", $modifiedOrderDetailIds) . ")");

        $emailHandler = new EmailHandler($dbConnection);
        $emailFile = "email/layout.php";
        $title = "Modify Shipped Quantity in Order #" . $orderId;
        $f3->set('domainUrl', getenv('DOMAIN_URL'));
        $f3->set('title', $title);
        $f3->set('orderId', $orderId);
        $f3->set('emailType', 'modifiedOrderProducts');
        $f3->set('products', $dbProduct);


        // get user email
        $dbEntityUserProfile = new BaseModel($dbConnection, "vwEntityUserProfile");
        $arrEntityUserProfile = $dbEntityUserProfile->getByField("entityId", $entityBuyerId);

        // get distributor entity name
        $dbUser = new BaseModel($dbConnection, "vwEntityUserProfile");
        $user = $dbUser->getWhere("userId=" . $userId)[0];
        $entityName = $user->entityName_en;
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
        if($supportLog->entityBuyerId > 0){
            $dbData = new BaseModel($dbConnection, 'entity');
            $dbData->getWhere('id = '.$supportLog->entityBuyerId.'');
            $f3->set('supportCustomer', $dbData->name_ar);
        }

        if($supportLog->orderId > 0){
            $f3->set('supportOrder', $supportLog->orderId);
        }

        if($supportLog->requestCall > 0) {
            $f3->set('requestCall', 'Yes');
        }

        if(!empty($supportLog->message)) {
            $f3->set('message', $supportLog->message);
        }

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
                $emailHandler->appendToAddress("n.javaid@aumet.com", "Naveed Javaid");
                $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
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
                $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                $emailHandler->appendToAddress("n.javaid@aumet.com", "Naveed Javaid");
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
                $emailHandler->appendToAddress("n.javaid@aumet.com", "Naveed Javaid");
            }
        }

        $emailHandler->sendEmail(Constants::EMAIL_RESET_PASSWORD, $subject, $htmlContent);
        $emailHandler->resetTos();
    }


    /**
     * sendApprovalPharmacyNotification
     *
     * @param \Base $f3 f3 instance
     * @param BaseModel $dbConnection db connection instance
     * @param stdClass $allValues allValues
     * @param BaseModel $dbUser User Model
     */
    public static function sendApprovalPharmacyNotification($f3, $dbConnection, $allValues, $dbUser)
    {
        $emailHandler = new EmailHandler($dbConnection);
        $emailFile = "email/layout.php";
        $f3->set('domainUrl', getenv('DOMAIN_URL'));
        $f3->set('title', 'Pharmacy Account Approval');
        $f3->set('emailType', 'pharmacyAccountApproval');


        $dbCountry = new BaseModel($dbConnection, "country");
        $dbCountry->name = "name_en";
        $country = $dbCountry->getById($allValues->countryId)[0];
        $countryName = $country['name'];

        $dbCity = new BaseModel($dbConnection, "city");
        $dbCity->name = "nameEn";
        $city = $dbCity->getById($allValues->cityId)[0];
        $cityName = $city['name'];

        $arrFields = [
            "Name" => $allValues->name,
            "Mobile" => $allValues->mobile,
            "Email" => $allValues->email,
            "Pharmacy Name" => $allValues->entityName,
            "Trade License Number" => $allValues->tradeLicenseNumber,
            "Country" => $countryName,
            "City" => $cityName,
            "Address" => $allValues->address,
        ];

        $f3->set('arrFields', $arrFields);

        $f3->set('tradeLicenseUrl', $allValues->tradeLicenseUrl);

        $payload = [
            'userId' => $dbUser->id
        ];
        $jwt = new JWT(getenv('JWT_SECRET_KEY'), 'HS256', (86400 * 30), 10);
        $token = $jwt->encode($payload);
        $f3->set('token', $token);

        $emailList = explode(';', getenv('ADMIN_SUPPORT_EMAIL'));
        for ($i = 0; $i < count($emailList); $i++) {
            if (!$emailList[$i]) {
                continue;
            }

            $currentEmail = explode(',', $emailList[$i]);
            if (count($currentEmail) == 2) {
                $emailHandler->appendToAddress($currentEmail[0], $currentEmail[1]);
            } else {
                $emailHandler->appendToAddress($currentEmail[0], $currentEmail[0]);
            }
        }

        $htmlContent = View::instance()->render($emailFile);

        $subject = "Aumet - Pharmacy Account Approval";
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: " . getenv('ENV') . ")";

            if (getenv('ENV') == Constants::ENV_LOC) {
                $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
                $emailHandler->appendToAddress("n.javaid@aumet.com", "Naveed Javaid");
                $emailHandler->appendToAddress("sajjadintel@gmail.com", "Sajad Abbasi");
            }
        }
        $emailHandler->sendEmail(Constants::EMAIL_PHARMACY_ACCOUNT_APPROVAL, $subject, $htmlContent);
    }


    /**
     * sendApprovalDistributorNotification
     *
     * @param \Base $f3 f3 instance
     * @param BaseModel $dbConnection db connection instance
     * @param stdClass $allValues allValues
     * @param BaseModel $dbUser User Model
     */
    public static function sendApprovalDistributorNotification($f3, $dbConnection, $allValues, $dbUser)
    {
        $emailHandler = new EmailHandler($dbConnection);
        $emailFile = "email/layout.php";
        $f3->set('domainUrl', getenv('DOMAIN_URL'));
        $f3->set('title', 'Pharmacy Account Approval');
        $f3->set('emailType', 'distributorAccountApproval');


        $dbCountry = new BaseModel($dbConnection, "country");
        $dbCountry->name = "name_en";
        $country = $dbCountry->getById($allValues->countryId)[0];
        $countryName = $country['name'];

        $dbCity = new BaseModel($dbConnection, "city");
        $dbCity->name = "nameEn";
        $city = $dbCity->getById($allValues->cityId)[0];
        $cityName = $city['name'];

        $arrFields = [
            "Name" => $allValues->name,
            "Mobile" => $allValues->mobile,
            "Email" => $allValues->email,
            "Distributor Name" => $allValues->entityName,
            "Trade License Number" => $allValues->tradeLicenseNumber,
            "Country" => $countryName,
            "City" => $cityName,
            "Address" => $allValues->address,
        ];

        $f3->set('arrFields', $arrFields);

        $f3->set('tradeLicenseUrl', $allValues->tradeLicenseUrl);

        $payload = [
            'userId' => $dbUser->id
        ];
        $jwt = new JWT(getenv('JWT_SECRET_KEY'), 'HS256', (86400 * 30), 10);
        $token = $jwt->encode($payload);
        $f3->set('token', $token);

        $emailList = explode(';', getenv('ADMIN_SUPPORT_EMAIL'));
        for ($i = 0; $i < count($emailList); $i++) {
            if (!$emailList[$i]) {
                continue;
            }

            $currentEmail = explode(',', $emailList[$i]);
            if (count($currentEmail) == 2) {
                $emailHandler->appendToAddress($currentEmail[0], $currentEmail[1]);
            } else {
                $emailHandler->appendToAddress($currentEmail[0], $currentEmail[0]);
            }
        }

        $htmlContent = View::instance()->render($emailFile);

        $subject = "Aumet - Distributor Account Approval";
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: " . getenv('ENV') . ")";

            if (getenv('ENV') == Constants::ENV_LOC) {
                $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
                $emailHandler->appendToAddress("n.javaid@aumet.com", "Naveed Javaid");
                $emailHandler->appendToAddress("sajjadintel@gmail.com", "Sajad Abbasi");
            }
        }
        $emailHandler->sendEmail(Constants::EMAIL_DISTRIBUTOR_ACCOUNT_APPROVAL, $subject, $htmlContent);
    }


    /**
     * sendAccountVerifiedPharmacyNotification
     *
     * @param \Base $f3 f3 instance
     * @param BaseModel $dbConnection db connection instance
     * @param BaseModel $dbUser User Model
     */
    public static function sendAccountVerifiedPharmacyNotification($f3, $dbConnection, $dbUser, $message)
    {
        $emailHandler = new EmailHandler($dbConnection);
        $emailFile = "email/layout.php";
        $f3->set('domainUrl', getenv('DOMAIN_URL'));
        $f3->set('title', 'Pharmacy Account Verified');
        $f3->set('emailType', 'pharmacyAccountVerified');

        $f3->set('message', $message);

        $htmlContent = View::instance()->render($emailFile);

        $emailHandler->appendToAddress($dbUser->email, $dbUser->fullname);
        $subject = "Aumet - Pharmacy Account Verified";
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: " . getenv('ENV') . ")";

            if (getenv('ENV') == Constants::ENV_LOC) {
                $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
                $emailHandler->appendToAddress("n.javaid@aumet.com", "Naveed Javaid");
                $emailHandler->appendToAddress("sajjadintel@gmail.com", "Sajad Abbasi");
            }
        }

        $emailHandler->sendEmail(Constants::EMAIL_PHARMACY_ACCOUNT_VERIFIED, $subject, $htmlContent);
    }

    /**
     * sendAccountVerifiedDistributorNotification
     *
     * @param \Base $f3 f3 instance
     * @param BaseModel $dbConnection db connection instance
     * @param BaseModel $dbUser User Model
     */
    public static function sendAccountVerifiedDistributorNotification($f3, $dbConnection, $dbUser, $message)
    {
        $emailHandler = new EmailHandler($dbConnection);
        $emailFile = "email/layout.php";
        $f3->set('domainUrl', getenv('DOMAIN_URL'));
        $f3->set('title', 'Distributor Account Verified');
        $f3->set('emailType', 'distributorAccountVerified');

        $f3->set('message', $message);

        $htmlContent = View::instance()->render($emailFile);

        $emailHandler->appendToAddress($dbUser->email, $dbUser->fullname);
        $subject = "Aumet - Distributor Account Verified";
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: " . getenv('ENV') . ")";

            if (getenv('ENV') == Constants::ENV_LOC) {
                $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
                $emailHandler->appendToAddress("n.javaid@aumet.com", "Naveed Javaid");
                $emailHandler->appendToAddress("sajjadintel@gmail.com", "Sajad Abbasi");
            }
        }

        $emailHandler->sendEmail(Constants::EMAIL_DISTRIBUTOR_ACCOUNT_VERIFIED, $subject, $htmlContent);
    }


    /**
     * sendAccountVerifiedPharmacyNotification
     *
     * @param \Base $f3 f3 instance
     * @param BaseModel $dbConnection db connection instance
     * @param BaseModel $dbUser User Model
     */
    public static function sendAccountApprovedPharmacyNotification($f3, $dbConnection, $dbUser)
    {
        $emailHandler = new EmailHandler($dbConnection);
        $emailFile = "email/layout.php";
        $f3->set('domainUrl', getenv('DOMAIN_URL'));
        $f3->set('title', 'Pharmacy Account Approved');
        $f3->set('emailType', 'pharmacyAccountApproved');

        $message = "Your account has been approved. You can now login to our platform !";
        $f3->set('message', $message);

        $htmlContent = View::instance()->render($emailFile);

        $emailHandler->appendToAddress($dbUser->email, $dbUser->fullname);
        $subject = "Aumet - Pharmacy Account Approved";
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: " . getenv('ENV') . ")";

            if (getenv('ENV') == Constants::ENV_LOC) {
                $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
                $emailHandler->appendToAddress("n.javaid@aumet.com", "Naveed Javaid");
                $emailHandler->appendToAddress("sajjadintel@gmail.com", "Sajad Abbasi");
            }
        }

        $emailHandler->sendEmail(Constants::EMAIL_PHARMACY_ACCOUNT_APPROVED, $subject, $htmlContent);
    }

    /**
     * sendAccountVerifiedPharmacyNotification
     *
     * @param \Base $f3 f3 instance
     * @param BaseModel $dbConnection db connection instance
     * @param BaseModel $dbUser User Model
     */
    public static function sendAccountApprovedDistributorNotification($f3, $dbConnection, $dbUser)
    {
        $emailHandler = new EmailHandler($dbConnection);
        $emailFile = "email/layout.php";
        $f3->set('domainUrl', getenv('DOMAIN_URL'));
        $f3->set('title', 'Distributor Account Approved');
        $f3->set('emailType', 'distributorAccountApproved');

        $message = "Your account has been approved. You can now login to our platform !";
        $f3->set('message', $message);

        $htmlContent = View::instance()->render($emailFile);

        $emailHandler->appendToAddress($dbUser->email, $dbUser->fullname);
        $subject = "Aumet - Distributor Account Approved";
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: " . getenv('ENV') . ")";

            if (getenv('ENV') == Constants::ENV_LOC) {
                $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
                $emailHandler->appendToAddress("n.javaid@aumet.com", "Naveed Javaid");
                $emailHandler->appendToAddress("sajjadintel@gmail.com", "Sajad Abbasi");
            }
        }

        $emailHandler->sendEmail(Constants::EMAIL_DISTRIBUTOR_ACCOUNT_APPROVED, $subject, $htmlContent);
    }


    /**
     * sendVerificationPharmacyNotification
     *
     * @param \Base $f3 f3 instance
     * @param BaseModel $dbConnection db connection instance
     * @param stdClass $allValues all values
     * @param int $userId User id
     * @param int $entityId entity id
     * @param int $entityBranchId branch id
     */
    public static function sendVerificationPharmacyNotification($f3, $dbConnection, $allValues, $userId, $entityId, $entityBranchId)
    {
        $emailHandler = new EmailHandler($dbConnection);
        $emailFile = "email/layout.php";
        $f3->set('domainUrl', getenv('DOMAIN_URL'));
        $f3->set('title', 'Pharmacy Account Verification');
        $f3->set('emailType', 'pharmacyAccountVerification');


        $arrFields = [
            "Name" => $allValues->name,
            "Mobile" => $allValues->mobile,
            "Email" => $allValues->email,
            "Pharmacy Name" => $allValues->entityName,
            "Trade License Number" => $allValues->tradeLicenseNumber,
            "Country" => $allValues->countryName,
            "City" => $allValues->cityName,
            "Address" => $allValues->address,
        ];

        $f3->set('arrFields', $arrFields);

        $f3->set('tradeLicenseUrl', $allValues->tradeLicenseUrl);

        $payload = [
            'userId' => $userId,
            'entityId' => $entityId,
            'entityBranchId' => $entityBranchId
        ];
        $jwt = new JWT(getenv('JWT_SECRET_KEY'), 'HS256', (86400 * 30), 10);
        $token = $jwt->encode($payload);
        $f3->set('token', $token);

        $emailHandler->appendToAddress($allValues->email, $allValues->name);
        $htmlContent = View::instance()->render($emailFile);

        $subject = "Aumet - Pharmacy Account Verification";
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: " . getenv('ENV') . ")";

            if (getenv('ENV') == Constants::ENV_LOC) {
                $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
                $emailHandler->appendToAddress("n.javaid@aumet.com", "Naveed Javaid");
                $emailHandler->appendToAddress("sajjadintel@gmail.com", "Sajad Abbasi");
            }
        }

        $emailHandler->sendEmail(Constants::EMAIL_PHARMACY_ACCOUNT_VERIFICATION, $subject, $htmlContent);
    }

    /**
     * sendVerificationDistributorNotification
     *
     * @param \Base $f3 f3 instance
     * @param BaseModel $dbConnection db connection instance
     * @param stdClass $allValues all values
     * @param int $userId User id
     * @param int $entityId entity id
     * @param int $entityBranchId branch id
     */
    public static function sendVerificationDistributorNotification($f3, $dbConnection, $allValues, $userId, $entityId, $entityBranchId)
    {
        $emailHandler = new EmailHandler($dbConnection);
        $emailFile = "email/layout.php";
        $f3->set('domainUrl', getenv('DOMAIN_URL'));
        $f3->set('title', 'Distributor Account Verification');
        $f3->set('emailType', 'distributorAccountVerification');


        $dbCity = new BaseModel($dbConnection, "city");
        $dbCity->name = "nameEn";
        $city = $dbCity->getById($allValues->cityId)[0];
        $cityName = $city['name'];

        $arrFields = [
            "Name" => $allValues->name,
            "Mobile" => $allValues->mobile,
            "Email" => $allValues->email,
            "Distributor Name" => $allValues->entityName,
            "Trade License Number" => $allValues->tradeLicenseNumber,
            "Country" => $allValues->countryName,
            "City" => $allValues->cityName,
            "Address" => $allValues->address,
        ];

        $f3->set('arrFields', $arrFields);

        $f3->set('tradeLicenseUrl', $allValues->tradeLicenseUrl);

        $payload = [
            'userId' => $userId,
            'entityId' => $entityId,
            'entityBranchId' => $entityBranchId
        ];
        $jwt = new JWT(getenv('JWT_SECRET_KEY'), 'HS256', (86400 * 30), 10);
        $token = $jwt->encode($payload);
        $f3->set('token', $token);

        $emailHandler->appendToAddress($allValues->email, $allValues->name);
        $htmlContent = View::instance()->render($emailFile);

        $subject = "Aumet - Distributor Account Verification";
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: " . getenv('ENV') . ")";

            if (getenv('ENV') == Constants::ENV_LOC) {
                $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
                $emailHandler->appendToAddress("n.javaid@aumet.com", "Naveed Javaid");
                $emailHandler->appendToAddress("sajjadintel@gmail.com", "Sajad Abbasi");
            }
        }

        $emailHandler->sendEmail(Constants::EMAIL_DISTRIBUTOR_ACCOUNT_VERIFICATION, $subject, $htmlContent);
    }

    /**
     * sendOnboardingPharmacyNotification
     *
     * @param \Base $f3 f3 instance
     * @param BaseModel $dbConnection db connection instance
     * @param int $userId User id
     * @param int $entityId entity id
     * @param int $entityBranchId branch id
     */
    public static function sendOnboardingPharmacyNotification($f3, $dbConnection, $userId, $userEmail, $userPassword, $userFullname, $entityId, $entityBranchId)
    {
        $emailHandler = new EmailHandler($dbConnection);
        $emailFile = "email/layout.php";
        $f3->set('domainUrl', getenv('DOMAIN_URL'));
        $f3->set('title', Constants::EMAIL_WELCOME_PHARMACY);
        $f3->set('emailType', 'welcomePharmacy');

        $payload = [
            'userId' => $userId,
            'entityId' => $entityId,
            'entityBranchId' => $entityBranchId
        ];
        $jwt = new JWT(getenv('JWT_SECRET_KEY'), 'HS256', (86400 * 30), 10);
        $token = $jwt->encode($payload);
        $f3->set('token', $token);
        $f3->set('password', $userPassword);

        $emailHandler->appendToAddress($userEmail, $userFullname);

        $emailHandler->appendToBcc('d.nofal@aumet.com', 'Diana');
        $emailHandler->appendToBcc('a.atrash@aumet.com', 'Alaa');
        $emailHandler->appendToBcc('a.abdullah@aumet.com', 'Ahmad');
        $emailHandler->appendToBcc('s.jaber@aumet.com', 'Shahed');
        $emailHandler->appendToBcc('m.shaaban@aumet.com', 'Mustafa');
        $emailHandler->appendToBcc('l.abueisheh@aumet.com', 'Luna');

        $htmlContent = View::instance()->render($emailFile);

        $subject = Constants::EMAIL_WELCOME_PHARMACY;
        return $emailHandler->sendEmail(Constants::EMAIL_WELCOME_PHARMACY  , $subject, $htmlContent);
    }


}
