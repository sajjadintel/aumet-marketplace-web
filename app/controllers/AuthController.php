<?php

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Firebase\Auth\Token\Exception\InvalidToken;

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
            $this->f3->set('vAuthFile', 'signup');

            $dbCountry = new BaseModel($this->db, "country");
            $dbCountry->name = "name_en"; 
            $arrCountry = $dbCountry->findAll();
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
        if ($dbUser->dry()) {
            echo $this->jsonResponse(false, null, $this->f3->get("vMessage_invalidLogin"));
        } else {
            if (password_verify($password, $dbUser->password)) {

                $this->configUser($dbUser);
                $this->webResponse->errorCode = Constants::STATUS_CODE_REDIRECT_TO_WEB;
                echo $this->webResponse->jsonResponse();
            } else {
                $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
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

            $dbUser = new BaseModel($this->db, "user");
            $dbUser->getWhere("uid = '$uid' AND statusId = 3");
            if ($dbUser->dry()) {

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
                $this->webResponse->message = $this->f3->get("vMessage_invalidLogin");
                $this->webResponse->data = $user;
            } else {
                $this->configUser($dbUser);
                $this->webResponse->errorCode = Constants::STATUS_CODE_REDIRECT_TO_WEB;
            }
        } catch (\InvalidArgumentException $e) {
            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->message = $e->getMessage();
        } catch (InvalidToken $e) {
            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
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
        foreach($arrCartDetail as $cartDetail) {
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

    function clearUserSession()
    {
        $this->isAuth = false;
        $this->f3->clear('SESSION.objUser');
    }

    function postSignUp()
    {
        $name = $this->f3->get("POST.name");
		$mobile = $this->f3->get("POST.mobile");
		$email = $this->f3->get("POST.email");
		$password = $this->f3->get("POST.password");
		$entityName = $this->f3->get("POST.entityName");
		$tradeLicenseNumber = $this->f3->get("POST.tradeLicenseNumber");
		$countryId = $this->f3->get("POST.country");
		$city = $this->f3->get("POST.city");
		$address = $this->f3->get("POST.address");
        $pharmacyDocuments = $this->f3->get("POST.pharmacyDocuments");

        // Check if email is unique
        $dbUser = new BaseModel($this->db, "user");
        $dbUser->getByField("email", $email);

        if(!$dbUser->dry()) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->title = "";
            $this->webResponse->message = "Email address exists, Please signin instead";
            echo $this->webResponse->jsonResponse();
        } else {
            // Get currency symbol
            $dbCountry = new BaseModel($this->db, "country");
            $country = $dbCountry->getById($countryId)[0];
            $currencySymbol = $country['currency'];
            
            // Get currency id
            $dbCurrency = new BaseModel($this->db, "currency");
            $currency = $dbCurrency->getByField("symbol", $currencySymbol)[0];
            $currencyId = $currency['id'];

            // Add user
            $dbUser->email = $email;
            $dbUser->password = password_hash($password, PASSWORD_DEFAULT);
            $dbUser->statusId = Constants::USER_STATUS_WAITING_VERIFICATION;
            $dbUser->fullname = $name;
            $dbUser->mobile = $mobile;
            $dbUser->roleId = Constants::USER_ROLE_PHARMACY_SYSTEM_ADMINISTRATOR;
            $dbUser->language = "en";
            $dbUser->addReturnID();

            // Add entity
            $dbEntity = new BaseModel($this->db, "entity");
            $dbEntity->typeId = Constants::ENTITY_TYPE_PHARMACY;
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
            $dbEntityBranch->address_ar = $address;
            $dbEntityBranch->address_en = $address;
            $dbEntityBranch->address_fr = $address;
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
            
            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->message = $this->f3->get("vMessage_signupSuccessful");
            echo $this->webResponse->jsonResponse();
        }
    }

    function sendWelcomeEmail($dbUser)
    {
        if ($dbUser->isWelcomeEmail == 0) {

            $emailTitle = "أهلا بك في نظام النجوم العالمي لتصنيف الخدمات";

            $bccEmails = [
                "alaa@blueoceans.io" => "Alaa Al Atrash",
                "Ahmed.Shaban@pmo.gov.ae" => "Ahmed Shaban",
                "Hamda.AlAli@pmo.gov.ae" => "Hamda Al Ali"
            ];

            $this->f3->set('vEmail_fullname', $dbUser->fullname);
            $this->f3->set('vEmail_username', $dbUser->email);
            $this->f3->set('vEmail_password', $dbUser->plainPassword);

            $htmlContent = View::instance()->render('emails/ar/welcome.php');

            $arrTo = [
                $dbUser->email => $dbUser->fullname
            ];

            $emailStatusCode = $this->sendEmail($emailTitle, $htmlContent, $arrTo, null, $bccEmails);

            $dbTransaction = new BaseModel($this->db, "userEmailTransactions");
            $dbTransaction->userId = $dbUser->id;
            $dbTransaction->email = $dbUser->email;
            $dbTransaction->typeId = 1;
            $dbTransaction->content = $htmlContent;
            $dbTransaction->status = $emailStatusCode;

            $dbTransaction->addReturnID();

            $dbUser->isWelcomeEmail = $emailStatusCode;
            $dbUser->update();
        }
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

        if (isset($token) && $token != null && $token != "") {
            $token = urldecode($token);
            $dbRequest = new BaseModel($this->db, "usersResetPasswordRequests");
            $dbRequest->getByField("tokenSecure", $token);


            if (!$dbRequest->dry()) {
                if ($dbRequest->stateId == 1) {
                    $this->f3->set('passwordToken', $token);

                    echo View::instance()->render('resetPassword.php');
                } else {
                    $this->rerouteAuth();
                }
            } else {
                $this->rerouteAuth();
            }
        } else {
            $this->rerouteAuth();
        }
    }

    function postResetPassword()
    {
        $token = $this->f3->get('POST.token');
        $password = $this->f3->get('POST.password');
        $passwordConfirm = $this->f3->get('POST.passwordConfirm');

        if (
            isset($token) && $token != null && $token != "" &&
            isset($password) && $password != null && $password != ""
        ) {

            $uppercase = preg_match('@[A-Z]@', $password);
            $lowercase = preg_match('@[a-z]@', $password);
            $number = preg_match('@[0-9]@', $password);
            $specialChars = preg_match('@[^\w]@', $password);

            if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8 || $password !== $passwordConfirm) {
                echo $this->jsonResponse(false, $this->f3->get('vMessage_passwordRulesError'));
            } else {
                $dbRequest = new BaseModel($this->db, "usersResetPasswordRequests");
                $dbRequest->getByField("tokenSecure", $token);

                if (!$dbRequest->dry()) {
                    if ($dbRequest->stateId == 1) {

                        $dbUser = new BaseModel($this->db, "users");
                        $dbUser->getByField("resetPasswordRequestId", $dbRequest->id);

                        if (!$dbUser->dry()) {
                            if ($dbUser->stateId == 1 && $dbUser->id == $dbRequest->userId && $dbUser->isResetPassword == 1) {
                                $dbUser->isResetPassword = 0;
                                $dbUser->resetPasswordRequestId = 0;

                                $dbUser->password = password_hash($password, PASSWORD_DEFAULT);

                                $dbRequest->stateId = 2;
                                $dbRequest->updateDateTime = date('Y-m-d H:i:s');

                                if ($dbRequest->edit()) {
                                    if ($dbUser->edit()) {
                                        echo $this->jsonResponse(true, "");
                                    } else {
                                        $dbRequest->stateId = 1;
                                        $dbRequest->edit();
                                        echo $this->jsonResponse(false, $this->f3->get('vMessage_generalError'));
                                    }
                                } else {
                                    echo $this->jsonResponse(false, $this->f3->get('vMessage_generalError'));
                                }
                            } else {
                                echo $this->jsonResponse(false, $this->f3->get('vMessage_generalError'));
                            }
                        } else {
                            $dbRequest->stateId = 3;
                            $dbRequest->edit();
                            echo $this->jsonResponse(false, $this->f3->get('vMessage_generalError'));
                        }
                    } else {
                        echo $this->jsonResponse(false, $this->f3->get('vMessage_generalError'));
                    }
                } else {
                    echo $this->jsonResponse(false, $this->f3->get('vMessage_generalError'));
                }
            }
        } else {
            echo $this->jsonResponse(false, $this->f3->get('vMessage_generalError'));
        }
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
        
        $newFileName = $fileName . "-" . time() . ".$ext";
        $targetFile = "files/uploads/documents/" . $newFileName;
        
        if (in_array($ext, $allValidExtensions)) {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                $success = true;
            }
        }

        if($success) {
            echo $targetFile;
        }
    }

    function postSignUpValidateEmail()
    {
        $email = $this->f3->get("POST.email");
        
        // Check if email is unique
        $dbUser = new BaseModel($this->db, "user");
        $dbUser->getByField("email", $email);

        if(!$dbUser->dry()) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->title = "";
            $this->webResponse->message = "Email address exists, Please signin instead";
            echo $this->webResponse->jsonResponse();
        } else {
            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            echo $this->webResponse->jsonResponse();
        }
    }
}