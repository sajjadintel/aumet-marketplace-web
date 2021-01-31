<?php

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Firebase\Auth\Token\Exception\InvalidToken;
use Ahc\Jwt\JWT;

class AuthController extends Controller
{
    function beforeroute()
    {
    }

    function getSignIn()
    {
        if ($this->isAuth) {
            $this->f3->reroute('/web');
        } else {
            $activationId = $this->f3->get("PARAMS._a");

            if ($activationId) {

                $dbUser = new BaseModel($this->db, "users");
                $dbUser->getByField("activationId", $activationId);

                if (!$dbUser->dry()) {
                    echo "get started";
                } else {
                    echo "activation ID not found";
                }
            } else {

                $lang = $this->f3->get("PARAMS.lang");
                if (!$lang) {
                    $lang = 'en';
                }
                $this->f3->set('SESSION.userLang', $lang);
                $this->f3->set('SESSION.userLangDirection', $lang == "ar" ? "rtl" : "ltr");
                $this->f3->set('LANGUAGE', $lang);

                $this->f3->set('vAuthFile', 'signin');

                echo View::instance()->render('public/auth/layout.php');
            }
        }
    }

    function getSignOut()
    {
        try {
            $factory = (new Factory)->withServiceAccount($this->getRootDirectory() . '/config/aumet-com-firebase-adminsdk-2nsnx-64efaf5c39.json');

            $auth = $factory->createAuth();

            $idTokenString = $this->f3->get("SESSION.token");

            $verifiedIdToken = $auth->verifyIdToken($idTokenString);
            $uid = $verifiedIdToken->getClaim('sub');

            $auth->revokeRefreshTokens($uid);

            $verifiedIdToken = $auth->verifyIdToken($idTokenString, $checkIfRevoked = true);
        } catch (RevokedIdToken $e) {
            //$e->getMessage();
        } catch (Exception $e) {
        }
        $this->clearUserSession();
        $this->rerouteAuth();
    }

    function getSignUp()
    {
        if ($this->isAuth) {
            $this->f3->reroute('/web');
        } else {

            $this->f3->set('companyType',($this->f3->get('SESSION.companyType')));

            $this->f3->set('vAuthFile', 'signup');

            $dbCountry = new BaseModel($this->db, "country");
            $dbCountry->name = "name_en";
            $arrCountry = $dbCountry->findAll("name_en ASC");
            $this->f3->set('arrCountry', $arrCountry);

            echo View::instance()->render('public/auth/layout.php');
        }
    }

    function postSignIn_NoFirebase()
    {
        $email = $this->f3->get("POST.email");
        $password = $this->f3->get("POST.password");

        $dbUser = new BaseModel($this->db, "user");
        $dbUser->getByField("email", $email);
        if ($dbUser->statusId == 1) {
            echo $this->jsonResponse(false, null, $this->f3->get("vMessage_verifyAccount"));
            return;
        } else if ($dbUser->statusId == 2) {
            echo $this->jsonResponse(false, null, $this->f3->get("vMessage_waitForVerify"));
            return;
        }

        if ($dbUser->dry()) {
            echo $this->jsonResponse(false, null, $this->f3->get("vMessage_invalidLogin"));
        } else {
            if (password_verify($password, $dbUser->password)) {
                if ($dbUser->statusId == Constants::USER_STATUS_WAITING_VERIFICATION) {
                    echo $this->jsonResponse(false, null, $this->f3->get("vMessage_verifyAccount"));
                } else if ($dbUser->statusId == Constants::USER_STATUS_PENDING_APPROVAL) {
                    echo $this->jsonResponse(false, null, $this->f3->get("vMessage_waitForVerify"));
                } else if ($dbUser->statusId == Constants::USER_STATUS_ACCOUNT_ACTIVE) {
                    $this->configUser($dbUser);
                    $this->webResponse->errorCode = Constants::STATUS_CODE_REDIRECT_TO_WEB;
                    echo $this->webResponse->jsonResponse();
                }
            } else {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->message = $this->f3->get("vMessage_invalidLogin");
                $this->webResponse->data = $dbUser;
                echo $this->webResponse->jsonResponse();
            }
        }
    }

    function postSignIn()
    {
        $factory = (new Factory)->withServiceAccount($this->getRootDirectory() . '/config/aumet-com-firebase-adminsdk-2nsnx-64efaf5c39.json');

        $auth = $factory->createAuth();

        $idTokenString = $this->f3->get("POST.token");

        try {
            $verifiedIdToken = $auth->verifyIdToken($idTokenString);
            $uid = $verifiedIdToken->getClaim('sub');
            $user = $auth->getUser($uid);

            $validUser = false;

            $dbUser = new BaseModel($this->db, "user");
            $dbUser->getWhere("uid = '$uid'");
            if ($dbUser->dry()) {
                $dbUser->getWhere("email = '$user->email'");
                if ($dbUser->dry()) {
                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->message = $this->f3->get("vMessage_invalidLogin");
                    $this->webResponse->data = $user;
                } else {
                    $dbUser->uid = $uid;
                    $dbUser->update();
                    $validUser = true;
                }
            } else {
                $validUser = true;
            }

            if ($validUser) {
                if ($dbUser->statusId == Constants::USER_STATUS_WAITING_VERIFICATION) {
                    echo $this->jsonResponse(false, null, $this->f3->get("vMessage_verifyAccount"));
                    return;
                } else if ($dbUser->statusId == Constants::USER_STATUS_PENDING_APPROVAL) {
                    echo $this->jsonResponse(false, null, $this->f3->get("vMessage_waitForVerify"));
                    return;
                } else if ($dbUser->statusId == Constants::USER_STATUS_ACCOUNT_ACTIVE) {
                    $this->configUser($dbUser);
                    $this->webResponse->errorCode = Constants::STATUS_CODE_REDIRECT_TO_WEB;
                }
            }
        } catch (\InvalidArgumentException $e) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = $e->getMessage();
        } catch (InvalidToken $e) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = $e->getMessage();
        }

        echo $this->webResponse->jsonResponse();
    }

    function configUser($dbUser)
    {
        $objUser = new stdClass();

        $objUser->id = $dbUser->id;
        $objUser->email = $dbUser->email;
        $objUser->mobile = $dbUser->mobile;
        $objUser->fullname = $dbUser->fullname;
        $objUser->roleId = $dbUser->roleId;
        $objUser->statusId = $dbUser->statusId;
        $objUser->language = $dbUser->language;

        $this->f3->set('LANGUAGE', $objUser->language);

        $dbUserRole = new BaseModel($this->db, "userRole");
        $dbUserRole->name = "name_" . $objUser->language;
        $dbUserRole->getById($objUser->roleId);
        $objUser->roleName = $dbUserRole->name;
        $objUser->isSystemRole = $dbUserRole->isSystem;

        $dbMenuConfig = new BaseModel($this->db, "menuConfig");
        if ($objUser->isSystemRole == 1) {
            $dbMenuConfig->getWhere("userRoleId=$objUser->roleId and entityTypeId=0");
        } else {
            $dbMenuConfig->getWhere("userRoleId=$objUser->roleId and entityTypeId=0");
        }
        $objUser->menuId = $dbMenuConfig->menuId;
        $objUser->entityTypeId = $dbMenuConfig->entityTypeId;

        $dbMenu = new BaseModel($this->db, "menu");
        $dbMenu->getById($objUser->menuId);
        $objUser->menuCode = $dbMenu->code;

        $dbUserAccount = new BaseModel($this->db, "userAccount");
        $dbUserAccount->getByField("userId", $objUser->id);
        $objUser->accountId = $dbUserAccount->accountId;

        // Get cart count
        $dbCartDetail = new BaseModel($this->db, "cartDetail");
        $arrCartDetail = $dbCartDetail->getByField("accountId", $objUser->accountId);
        $cartCount = 0;
        foreach ($arrCartDetail as $cartDetail) {
            $cartCount += $cartDetail->quantity;
            $cartCount += $cartDetail->quantityFree;
        }
        $objUser->cartCount = $cartCount;

        $this->isAuth = true;

        $dbUser->loginCounter++;
        $dbUser->loginDateTime = date('Y-m-d H:i:s');
        $dbUser->update();

        $this->f3->set('SESSION.objUser', $objUser);
        $this->f3->set('SESSION.userLang', $objUser->language);
        $this->f3->set('LANGUAGE', $objUser->language);
        $_SESSION['userLang'] = $objUser->language;
        switch ($_SESSION['userLang']) {
            case "en":
            case "fr":
                $_SESSION['userLangDirection'] = "ltr";
                break;
            default:
                $_SESSION['userLangDirection'] = "rtl";
                break;
        }

        $dbAccount = new BaseModel($this->db, "account");
        $dbAccount->getByField("id", $dbUserAccount->accountId);

        $dbEntity = new BaseModel($this->db, "entity");
        $dbEntity->name = "name_" . $objUser->language;
        $dbEntity->getByField("id", $dbAccount->entityId);
        $arrEntities = [];
        while (!$dbEntity->dry()) {
            $arrEntities[$dbEntity->id] = $dbEntity->name;
            $dbEntity->next();
        }

        $this->f3->set('SESSION.arrEntities', $arrEntities);
    }

    function getForgottenPassword()
    {
        if ($this->isAuth) {
            $this->f3->reroute('/web');
        } else {
            $this->f3->set('vAuthFile', 'forgot');
            echo View::instance()->render('public/auth/layout.php');
        }
    }

    function postForgottenPassword()
    {
        $email = $this->f3->get("POST.email");

        $dbUser = new BaseModel($this->db, "user");
        $dbUser->getByField("email", $email);

        if ($dbUser->dry()) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->title = "";
            $this->webResponse->message = "Email doesn't exist!";
            echo $this->webResponse->jsonResponse();
            exit;
        }
        $userResetToken = new BaseModel($this->db, "userResetToken");
        $userResetToken->getWhere('userId=' . $dbUser->id . " and userResetTokenStatusId=1");
        if (!$userResetToken->dry()) {
            $userResetToken->userResetTokenStatusId = 3;
            $userResetToken->update();
        }

        $userResetToken = new BaseModel($this->db, "userResetToken");
        $userResetToken->userId = $dbUser->id;
        $userResetToken->userResetTokenStatusId = 1;
        $userResetToken->token = Helper::generateRandomString(20);
        $userResetToken->createdAt = date('Y-m-d H:i:s');
        $userResetToken->updatedAt = date('Y-m-d H:i:s');
        $userResetToken->addReturnID();


        $payload = [
            'id' => $userResetToken->id,
            'userId' => $userResetToken->userId,
            'token' => $userResetToken->token,
        ];
        $jwt = new JWT(getenv('JWT_SECRET_KEY'), 'HS256', (86400 * 1), 10);
        $token = $jwt->encode($payload);

        NotificationHelper::resetPasswordNotification($this->f3, $this->db, $dbUser, $token);

        $this->webResponse->errorCode = Constants::STATUS_SUCCESS_SHOW_DIALOG;
        $this->webResponse->message = $this->f3->get("vForgot_emailSent");
        echo $this->webResponse->jsonResponse();
    }

    function clearUserSession()
    {
        $this->isAuth = false;
        $this->f3->clear('SESSION.objUser');
    }

    function postSignUp()
    {
        $uid = $this->f3->get("POST.uid");
        $name = $this->f3->get("POST.name");
        $mobile = $this->f3->get("POST.mobile");
        $email = $this->f3->get("POST.email");
        $password = $this->f3->get("POST.password");
        $entityName = !empty($this->f3->get("POST.pharmacyName")) ? $this->f3->get("POST.pharmacyName") :  $this->f3->get("POST.distributorName");
        $tradeLicenseNumber = $this->f3->get("POST.tradeLicenseNumber");
        $countryId = $this->f3->get("POST.country");
        $cityId = $this->f3->get("POST.city");
        $address = $this->f3->get("POST.address");
        $pharmacyDocument = $this->f3->get("POST.pharmacyDocument");
        $companyType = $this->f3->get("POST.companyType");

        if (!$name || !ltrim($mobile, "+") || !$email || !$password || !$entityName || !$countryId || !$cityId || !$address) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = "Some mandatory fields are missing";
            echo $this->webResponse->jsonResponse();
            return;
        }

        // Check if email is unique
        $dbUser = new BaseModel($this->db, "user");
        $dbUser->getByField("email", $email);

        if (!$dbUser->dry()) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->title = "";
            $this->webResponse->message = "Email Already Exists";
            echo $this->webResponse->jsonResponse();
            return;
        }

        // Check if phone number is unique
        $dbUser = new BaseModel($this->db, "user");
        $dbUser->getByField("mobile", $mobile);

        if (!$dbUser->dry()) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->title = "";
            $this->webResponse->message = "Phone number exists!";
            echo $this->webResponse->jsonResponse();
            return;
        }

        // Check if trading license is unique
        $dbEntityBranch = new BaseModel($this->db, "entityBranch");
        if ($tradeLicenseNumber != '') {
            $dbEntityBranch->getByField("tradeLicenseNumber", $tradeLicenseNumber);

            if (!$dbEntityBranch->dry()) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->title = "";
                $this->webResponse->message = "Trading license exists!";
                echo $this->webResponse->jsonResponse();
                return;
            }
        }

        // Get currency symbol
        $dbCountry = new BaseModel($this->db, "country");
        $country = $dbCountry->getById($countryId)[0];
        $currencySymbol = $country['currency'];

        // Get currency id
        $dbCurrency = new BaseModel($this->db, "currency");
        $currency = $dbCurrency->getByField("symbol", $currencySymbol)[0];
        $currencyId = $currency['id'];

        // Add user
        if ($uid != NULL && trim($uid) != '') {
            $dbUser->uid = $uid;
        }
        $dbUser->email = $email;
        $dbUser->password = password_hash($password, PASSWORD_DEFAULT);
        $dbUser->statusId = Constants::USER_STATUS_WAITING_VERIFICATION;
        $dbUser->fullname = $name;
        $dbUser->mobile = $mobile;
        $dbUser->roleId = ($companyType == 'distributor') ? Constants::USER_ROLE_DISTRIBUTOR_SYSTEM_ADMINISTRATOR : Constants::USER_ROLE_PHARMACY_SYSTEM_ADMINISTRATOR;
        $dbUser->language = "en";
        $dbUser->addReturnID();

        // Add entity
        $dbEntity = new BaseModel($this->db, "entity");
        $dbEntity->typeId = ($companyType == 'distributor') ? Constants::ENTITY_TYPE_DISTRIBUTOR : Constants::ENTITY_TYPE_PHARMACY;
        $dbEntity->name_ar = $entityName;
        $dbEntity->name_en = $entityName;
        $dbEntity->name_fr = $entityName;
        $dbEntity->countryId = $countryId;
        $dbEntity->currencyId = $currencyId;
        $dbEntity->addReturnID();

        // Add entity branch
        $dbEntityBranch = new BaseModel($this->db, "entityBranch");
        $dbEntityBranch->entityId = $dbEntity->id;
        $dbEntityBranch->name_ar = $entityName;
        $dbEntityBranch->name_en = $entityName;
        $dbEntityBranch->name_fr = $entityName;
        $dbEntityBranch->cityId = $cityId;
        $dbEntityBranch->address_ar = $address;
        $dbEntityBranch->address_en = $address;
        $dbEntityBranch->address_fr = $address;
        $dbEntityBranch->tradeLicenseNumber = $tradeLicenseNumber;
        $dbEntityBranch->tradeLicenseUrl = $pharmacyDocument;
        $dbEntityBranch->addReturnID();

        // Add account
        $dbAccount = new BaseModel($this->db, "account");
        $dbAccount->entityId = $dbEntity->id;
        $dbAccount->number = 100;
        $dbAccount->statusId = Constants::ACCOUNT_STATUS_ACTIVE;
        $dbAccount->addReturnID();

        // Add user account
        $dbUserAccount = new BaseModel($this->db, "userAccount");
        $dbUserAccount->userId = $dbUser->id;
        $dbUserAccount->accountId = $dbAccount->id;
        $dbUserAccount->statusId = Constants::ACCOUNT_STATUS_ACTIVE;
        $dbUserAccount->addReturnID();

        // Send verification email
        $allValues = new stdClass();
        $allValues->name = $name;
        $allValues->mobile = $mobile;
        $allValues->email = $email;
        $allValues->entityName = $entityName;
        $allValues->tradeLicenseNumber = $tradeLicenseNumber;
        $allValues->countryId = $countryId;
        $allValues->cityId = $cityId;
        $allValues->address = $address;
        $allValues->tradeLicenseUrl = $pharmacyDocument;
        $this->sendVerificationEmail($allValues, $dbUser->id, $dbEntity->id, $dbEntityBranch->id);

        $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
        $this->webResponse->message = $this->f3->get("vMessage_signupSuccessful");
        echo $this->webResponse->jsonResponse();
    }

    function sendVerificationEmail($allValues, $userId, $entityId, $entityBranchId)
    {
        $emailHandler = new EmailHandler($this->db);
        $emailFile = "email/layout.php";
        $this->f3->set('domainUrl', getenv('DOMAIN_URL'));
        $this->f3->set('title', 'Pharmacy Account Verification');
        $this->f3->set('emailType', 'pharmacyAccountVerification');

        $dbCountry = new BaseModel($this->db, "country");
        $dbCountry->name = "name_en";
        $country = $dbCountry->getById($allValues->countryId)[0];
        $countryName = $country['name'];

        $dbCity = new BaseModel($this->db, "city");
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

        $this->f3->set('arrFields', $arrFields);

        $this->f3->set('tradeLicenseUrl', $allValues->tradeLicenseUrl);

        $payload = [
            'userId' => $userId,
            'entityId' => $entityId,
            'entityBranchId' => $entityBranchId
        ];
        $jwt = new JWT(getenv('JWT_SECRET_KEY'), 'HS256', (86400 * 30), 10);
        $token = $jwt->encode($payload);
        $this->f3->set('token', $token);

        $emailHandler->appendToAddress($allValues->email, $allValues->name);
        $htmlContent = View::instance()->render($emailFile);

        $subject = "Aumet - Pharmacy Account Verification";
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: " . getenv('ENV') . ")";

            if (getenv('ENV') == Constants::ENV_LOC) {
                $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
                $emailHandler->appendToAddress("n.javaid@aumet.com", "Naveed Javaid");
            }
        }

        $emailHandler->sendEmail(Constants::EMAIL_PHARMACY_ACCOUNT_VERIFICATION, $subject, $htmlContent);
    }

    function sendApprovalEmail($allValues, $userId)
    {
        $emailHandler = new EmailHandler($this->db);
        $emailFile = "email/layout.php";
        $this->f3->set('domainUrl', getenv('DOMAIN_URL'));
        $this->f3->set('title', 'Pharmacy Account Approval');
        $this->f3->set('emailType', 'pharmacyAccountApproval');

        $dbCountry = new BaseModel($this->db, "country");
        $dbCountry->name = "name_en";
        $country = $dbCountry->getById($allValues->countryId)[0];
        $countryName = $country['name'];

        $dbCity = new BaseModel($this->db, "city");
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

        $this->f3->set('arrFields', $arrFields);

        $this->f3->set('tradeLicenseUrl', $allValues->tradeLicenseUrl);

        $payload = [
            'userId' => $userId
        ];
        $jwt = new JWT(getenv('JWT_SECRET_KEY'), 'HS256', (86400 * 30), 10);
        $token = $jwt->encode($payload);
        $this->f3->set('token', $token);

        $emailList = explode(';', getenv('ADMIN_SUPPORT_EMAIL'));
        for ($i = 0; $i < count($emailList); $i++) {
        	if(!$emailList[$i]) {
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
            }
        }
        $emailHandler->sendEmail(Constants::EMAIL_PHARMACY_ACCOUNT_APPROVAL, $subject, $htmlContent);
    }

    function postForgotPassword()
    {
        $email = $this->f3->get("POST.email");

        $dbUser = new BaseModel($this->db, "users");
        $dbUser->getByField("email", $email);
        if ($dbUser->dry()) {
            echo $this->jsonResponse(false, $this->f3->get('vMessage_emailExistError'));
        } else {

            if ($dbUser->stateId == 1) {

                $objEmail = new EmailHandler($this->f3, $this->db);

                $dbUserResetPasswordRequest = new BaseModel($this->db, "usersResetPasswordRequests");
                $dbUserResetPasswordRequest->getByField("id", $dbUser->resetPasswordRequestId);
                if (!$dbUserResetPasswordRequest->dry()) {

                    if ($dbUserResetPasswordRequest->isEmailSent == 0) {
                        $dbUserResetPasswordRequest->token = $this->generateRandomString(32);
                        $dbUserResetPasswordRequest->tokenSecure = password_hash($dbUserResetPasswordRequest->token, PASSWORD_DEFAULT);

                        if ($objEmail->sendResetPassword($dbUser, $dbUserResetPasswordRequest->tokenSecure)) {

                            $dbUserResetPasswordRequest->isEmailSent = 1;
                            $dbUserResetPasswordRequest->update();

                            $dbUser->isResetPassword = 1;
                            $dbUser->update();

                            echo $this->jsonResponse(true, $this->f3->get('vMessage_resetPasswordSent'));
                        } else {
                            echo $this->jsonResponse(false, $this->f3->get('vMessage_generalError'));
                        }
                    } else {
                        echo $this->jsonResponse(true, $this->f3->get('vMessage_resetPasswordSent'));
                    }
                } else {
                    $dbUserResetPasswordRequest->userId = $dbUser->id;

                    $dbUserResetPasswordRequest->token = $this->generateRandomString(32);
                    $dbUserResetPasswordRequest->tokenSecure = password_hash($dbUserResetPasswordRequest->token, PASSWORD_DEFAULT);

                    if ($objEmail->sendResetPassword($dbUser, $dbUserResetPasswordRequest->tokenSecure)) {

                        $dbUserResetPasswordRequest->isEmailSent = 1;
                        $dbUserResetPasswordRequest->addReturnID();

                        $dbUser->resetPasswordRequestId = $dbUserResetPasswordRequest->id;
                        $dbUser->isResetPassword = 1;
                        $dbUser->update();

                        echo $this->jsonResponse(true, $this->f3->get('vMessage_resetPasswordSent'));
                    } else {
                        echo $this->jsonResponse(false, $dbUserResetPasswordRequest->token);
                    }
                }
            } else {
                echo $this->jsonResponse(false, $this->f3->get('vMessage_emailExistError'));
            }
        }
    }

    function getResetPassword()
    {
        $token = $_GET['token'];

        if (!isset($token) || $token == null || $token == "") {
            $this->rerouteAuth();
        }
        $token = urldecode($token);
        try {
            $jwt = new JWT(getenv('JWT_SECRET_KEY'), 'HS256', (86400 * 1), 10);
            $accessTokenPayload = $jwt->decode($token);
        } catch (\Exception $e) {
            $this->rerouteAuth();
        }
        if (!is_array($accessTokenPayload)) {
            $this->rerouteAuth();
        }

        $dbRequest = new BaseModel($this->db, "userResetToken");
        $dbRequest->getByField("id", $accessTokenPayload['id']);

        if ($dbRequest->dry()) {
            $this->rerouteAuth();
        }

        if ($dbRequest->userResetTokenStatusId != 1) {
            $this->rerouteAuth();
        }


        $this->f3->set('token', $token);
        $this->f3->set('vAuthFile', 'reset');
        echo View::instance()->render('public/auth/layout.php');
    }

    function postResetPassword()
    {
        $token = $this->f3->get('POST.token');
        $password = $this->f3->get('POST.password');
        $passwordConfirm = $this->f3->get('POST.passwordConfirmation');

        if (!isset($token) || $token == null || $token == "" || !isset($password) || $password == null || $password == "") {
            echo $this->webResponse->jsonResponseV2(Constants::STATUS_ERROR, null, $this->f3->get('vMessage_generalError') . '1', null);
            return;
        }

        if ($password !== $passwordConfirm) {
            echo $this->jsonResponse(false, null, $this->f3->get('vMessage_passwordRulesError'));
            return;
        }

        if (strlen($password) < 6) {
            echo $this->jsonResponse(false, null, $this->f3->get('vMessage_passwordNotStrong'));
            return;
        }

        try {
            $jwt = new JWT(getenv('JWT_SECRET_KEY'), 'HS256', (86400 * 1), 10);
            $accessTokenPayload = $jwt->decode($token);
        } catch (\Exception $e) {
            echo $this->webResponse->jsonResponseV2(Constants::STATUS_ERROR, null, $this->f3->get('vMessage_generalError') . '2', null);
            return;
        }
        if (!is_array($accessTokenPayload)) {
            echo $this->webResponse->jsonResponseV2(Constants::STATUS_ERROR, null, $this->f3->get('vMessage_generalError') . '3', null);
            return;
        }

        $dbRequest = new BaseModel($this->db, "userResetToken");
        $dbRequest->getByField("id", $accessTokenPayload['id']);

        if ($dbRequest->dry()) {
            echo $this->webResponse->jsonResponseV2(Constants::STATUS_ERROR, null, $this->f3->get('vMessage_generalError') . '4', null);
            return;
        }

        if ($dbRequest->userResetTokenStatusId != 1) {
            echo $this->webResponse->jsonResponseV2(Constants::STATUS_ERROR, null, $this->f3->get('vMessage_generalError') . '5', null);
            return;
        }


        $dbUser = new BaseModel($this->db, "user");
        $dbUser->getByField("id", $dbRequest->userId);

        if ($dbUser->dry()) {
            echo $this->webResponse->jsonResponseV2(Constants::STATUS_ERROR, null, $this->f3->get('vMessage_generalError') . '6', null);
            return;
        }

        $dbUser->password = password_hash($password, PASSWORD_DEFAULT);
        $dbRequest->updatedAt = date('Y-m-d H:i:s');

        if (!$dbUser->edit()) {
            $dbRequest->userResetTokenStatusId = 1;
            $dbRequest->edit();
            echo $this->webResponse->jsonResponseV2(Constants::STATUS_ERROR, null, $this->f3->get('vMessage_generalError') . '7', null);
            return;
        }

        $dbRequest->userResetTokenStatusId = 3;
        $dbRequest->edit();
        echo $this->webResponse->jsonResponseV2(Constants::STATUS_SUCCESS, $this->f3->get('vForgot_passwordChanged'), $this->f3->get('vForgot_passwordChanged'), null);
    }

    function getEncryptedPassword()
    {
        echo password_hash("atrash", PASSWORD_DEFAULT);
    }

    function postSignUpDocumentUpload()
    {
        $allValidExtensions = [
            "pdf",
            "ppt",
            "xcl",
            "docx",
            "jpeg",
            "jpg",
            "png",
        ];
        $success = false;

        $fileName = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_FILENAME);
        $ext = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_EXTENSION);

        $newFileName = urlencode($fileName . "-" . time() . ".$ext");
        $targetFile = "files/uploads/documents/" . $newFileName;

        if (in_array($ext, $allValidExtensions)) {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                $success = true;
            }
        }

        if ($success) {
            echo $targetFile;
        }
    }

    function postSignUpValidateStep1()
    {
        $this->f3->set('SESSION.companyType',$this->f3->get("POST.companyType"));
        $email = $this->f3->get("POST.email");
        $mobile = $this->f3->get("POST.mobile");

        // Check if email is unique
        $dbUser = new BaseModel($this->db, "user");
        $dbUser->getByField("email", $email);

        if (!$dbUser->dry()) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->title = "";
            $this->webResponse->message = "Email already exists.";
            echo $this->webResponse->jsonResponse();
            return;
        }

        $dbUser->getByField("mobile", $mobile);

        if (!$dbUser->dry()) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->title = "";
            $this->webResponse->message = "Mobile already exists.";
            echo $this->webResponse->jsonResponse();
            return;
        }

        $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
        echo $this->webResponse->jsonResponse();
    }

    function getCityByCountryList()
    {
        $countryId = $this->f3->get("PARAMS.countryId");

        $dbCity = new BaseModel($this->db, "city");
        $dbCity->name = "nameEn";
        $dbCity->getWhere("countryId=$countryId", "nameEn ASC");

        $arrCities = [];
        while (!$dbCity->dry()) {
            $city = new stdClass();
            $city->id = $dbCity["id"];
            $city->name = $dbCity["name"];

            array_push($arrCities, $city);

            $dbCity->next();
        }

        $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
        $this->webResponse->data = $arrCities;
        echo $this->webResponse->jsonResponse();
    }

    function getVerifyAccount()
    {
        $token = $_GET['token'];

        if (!isset($token) || $token == null || $token == "") {
            $this->rerouteAuth();
        }
        $token = urldecode($token);
        try {
            $jwt = new JWT(getenv('JWT_SECRET_KEY'), 'HS256', (86400 * 30), 10);
            $accessTokenPayload = $jwt->decode($token);
        } catch (\Exception $e) {
            $this->rerouteAuth();
        }
        if (!is_array($accessTokenPayload)) {
            $this->rerouteAuth();
        }

        $userId = $accessTokenPayload["userId"];
        $dbUser = new BaseModel($this->db, "user");
        $dbUser->getById($userId);

        $entityId = $accessTokenPayload["entityId"];
        $dbEntity = new BaseModel($this->db, "entity");
        $dbEntity->name = "name_en";
        $dbEntity->getById($entityId);

        $entityBranchId = $accessTokenPayload["entityBranchId"];
        $dbEntityBranch = new BaseModel($this->db, "entityBranch");
        $dbEntityBranch->address = "address_en";
        $dbEntityBranch->getById($entityBranchId);


        if ($dbUser->dry() || $dbEntity->dry() || $dbEntityBranch->dry()) {
            echo "Invalid";
        } else if ($dbUser->statusId != Constants::USER_STATUS_WAITING_VERIFICATION) {
            echo "Already Verified";
        } else {
            $dbUser->statusId = Constants::USER_STATUS_PENDING_APPROVAL;
            $dbUser->update();

            $emailHandler = new EmailHandler($this->db);
            $emailFile = "email/layout.php";
            $this->f3->set('domainUrl', getenv('DOMAIN_URL'));
            $this->f3->set('title', 'Pharmacy Account Verified');
            $this->f3->set('emailType', 'pharmacyAccountVerified');

            $message = "Your account has been authenticated. You will be contacted by Aumet within 24 to 48 hours to activate your account";
            $this->f3->set('message', $message);

            $htmlContent = View::instance()->render($emailFile);

            $emailHandler->appendToAddress($dbUser->email, $dbUser->fullname);
            $subject = "Aumet - Pharmacy Account Verified";
            if (getenv('ENV') != Constants::ENV_PROD) {
                $subject .= " - (Test: " . getenv('ENV') . ")";

                if (getenv('ENV') == Constants::ENV_LOC) {
                    $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                    $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
                    $emailHandler->appendToAddress("n.javaid@aumet.com", "Naveed Javaid");
                }
            }

            $emailHandler->sendEmail(Constants::EMAIL_PHARMACY_ACCOUNT_VERIFIED, $subject, $htmlContent);

            // Send approval email
            $allValues = new stdClass();
            $allValues->name = $dbUser->fullname;
            $allValues->mobile = $dbUser->mobile;
            $allValues->email = $dbUser->email;
            $allValues->entityName = $dbEntity->name;
            $allValues->tradeLicenseNumber = $dbEntityBranch->tradeLicenseNumber;
            $allValues->countryId = $dbEntity->countryId;
            $allValues->cityId = $dbEntityBranch->cityId;
            $allValues->address = $dbEntityBranch->address;
            $allValues->tradeLicenseUrl = $dbEntityBranch->tradeLicenseUrl;
            $this->sendApprovalEmail($allValues, $dbUser->id);

            echo $message;
        }
    }

    function getApproveAccount()
    {
        $token = $_GET['token'];

        if (!isset($token) || $token == null || $token == "") {
            $this->rerouteAuth();
        }
        $token = urldecode($token);
        try {
            $jwt = new JWT(getenv('JWT_SECRET_KEY'), 'HS256', (86400 * 30), 10);
            $accessTokenPayload = $jwt->decode($token);
        } catch (\Exception $e) {
            $this->rerouteAuth();
        }
        if (!is_array($accessTokenPayload)) {
            $this->rerouteAuth();
        }

        $userId = $accessTokenPayload["userId"];

        $dbUser = new BaseModel($this->db, "user");
        $dbUser->getById($userId);

        if ($dbUser->dry()) {
            echo "Invalid";
        } else if($dbUser->statusId != Constants::USER_STATUS_PENDING_APPROVAL) {
            echo "Already Approved";
        } else {
            $dbUser->statusId = Constants::USER_STATUS_ACCOUNT_ACTIVE;
            $dbUser->update();

            $emailHandler = new EmailHandler($this->db);
            $emailFile = "email/layout.php";
            $this->f3->set('domainUrl', getenv('DOMAIN_URL'));
            $this->f3->set('title', 'Pharmacy Account Approved');
            $this->f3->set('emailType', 'pharmacyAccountApproved');

            $message = "Your account has been approved. You can now login to our platform !";
            $this->f3->set('message', $message);

            $htmlContent = View::instance()->render($emailFile);

            $emailHandler->appendToAddress($dbUser->email, $dbUser->fullname);
            $subject = "Aumet - Pharmacy Account Approved";
            if (getenv('ENV') != Constants::ENV_PROD) {
                $subject .= " - (Test: " . getenv('ENV') . ")";

                if (getenv('ENV') == Constants::ENV_LOC) {
                    $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                    $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
                    $emailHandler->appendToAddress("n.javaid@aumet.com", "Naveed Javaid");
                }
            }

            $emailHandler->sendEmail(Constants::EMAIL_PHARMACY_ACCOUNT_APPROVED, $subject, $htmlContent);

            echo "Approved";
        }
    }
}
