<?php

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Firebase\Auth\Token\Exception\InvalidToken;
use Ahc\Jwt\JWT;

class AuthController extends Controller
{
    function beforeroute()
    {
        $this->f3->clear('SESSION.notAuthorized');
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
                $this->f3->set('pageSeoTitle', 'Aumet Marketplace - Sign In');

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

            $this->f3->set('companyType', ($this->f3->get('SESSION.companyType')));

            $this->f3->set('vAuthFile', 'signup');

            $dbCountry = new BaseModel($this->db, "country");
            $dbCountry->name = "name_en";
            $arrCountry = $dbCountry->findAll("name_en ASC");
            $this->f3->set('arrCountry', $arrCountry);
            $this->f3->set('pageSeoTitle', 'Aumet Marketplace - Register');
            echo View::instance()->render('public/auth/layout.php');
        }
    }

    public function getSignUpInvite()
    {
        if ($this->isAuth) {
            $this->f3->reroute('/web');
            return;
        }
        $this->f3->set('companyType', ($this->f3->get('SESSION.companyType')));
        $this->f3->set('vAuthFile', 'signup-invite');

        $token = array_key_exists('token', $_GET) ? $_GET['token'] : null;
        $invite = UserInvite::findByToken($token);
        if ($invite == false) {
            $this->f3->reroute('/web/auth/signup');
            return;
        } else if (!$invite->statusIsPending()) {
            $this->f3->set('SESSION.error', 'Invalid Invite');
            $this->f3->reroute('/web/auth/signup');
            return;
        }

        $this->f3->set('invite', $invite);
        echo View::instance()->render('public/auth/layout.php');
    }

    public function postSignUpInvite()
    {
        $invite = UserInvite::findByTokenAndStatus($this->f3->get('POST.token'), UserInvite::STATUS_PENDING);
        $this->renderErrorIfValidationFails($invite, Constants::STATUS_ERROR, implode("\n", array_values($invite->errors)));
        $invite = $invite->process();

        $user = (new User)->create($this->f3->get('POST'), true, true);
        $this->renderErrorIfValidationFails($user, Constants::STATUS_ERROR, implode("\n", array_values($user->errors)));
        $account = (new Account)->create(['entityId' => $invite->entityId, 'number' => 100, 'statusId' => Constants::ACCOUNT_STATUS_ACTIVE]);
        $this->renderErrorIfValidationFails($account, Constants::STATUS_ERROR, implode("\n", array_values($account->errors)));
        $userAccount = (new UserAccount)->create(['userId' => $user->id, 'accountId' => $account->id, 'statusId' => Constants::ACCOUNT_STATUS_ACTIVE]);
        $this->renderErrorIfValidationFails($userAccount, Constants::STATUS_ERROR, implode("\n", array_values($userAccount->errors)));
        $entity = (new EntityUserProfileView)->findone(['entityId = ?', $invite->entityId]);

        $allValues = new stdClass();
        $allValues->name = $user->fullname;
        $allValues->mobile = $user->mobile;
        $allValues->email = $user->email;
        $allValues->entityName = $entity->entityName_en;
        $allValues->tradeLicenseNumber = $entity->entityBranchTradeLicenseNumber;
        $allValues->countryId = $entity->entityCountryId;
        $allValues->countryName = $entity->entityCountryName_en;
        $allValues->cityId = $entity->entityBranchCityId;
        $allValues->cityName = $entity->entityBranchCityName_en;
        $allValues->address = $entity->entityBranchAddress_en;
        $allValues->tradeLicenseUrl = $entity->entityBranchTradeLicenseUrl;
        $allValues->roleId = $user->roleId;
        $allValues->invite = $invite;

        $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
        $this->webResponse->message = $this->f3->get("vMessage_signupSuccessful");
        $this->webResponse->data = $allValues;
        echo $this->webResponse->jsonResponse();
    }

    function postSignIn_NoFirebase()
    {
        $email = trim($this->f3->get("POST.email"));
        $password = trim($this->f3->get("POST.password"));

        $dbUser = new BaseModel($this->db, "user");
        $dbUser->getByField("email", $email);

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
                $this->webResponse->errortype = "login";
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
                    $this->webResponse->errortype = "login";
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
            $this->webResponse->errortype = "login";
            $this->webResponse->message = $e->getMessage();
        } catch (InvalidToken $e) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->errortype = "login";
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
        $objUser->cartCount = 0;
        $dbCartDetail = $this->db->exec("CALL spGetCartCount($objUser->accountId)");
        if (count($dbCartDetail) > 0) {
            $objUser->cartCount = intval($dbCartDetail[0]['cartCount']);
        }

        $this->isAuth = true;

        $dbUser->loginCounter++;
        $objUser->loginCounter = $dbUser->loginCounter;
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

        $dbUserProfile = new BaseModel($this->db, "vwEntityUserProfile");
        $userProfile = $dbUserProfile->getWhere("userId=" . $dbUser->id)[0];
        $objUser->entityImage = $userProfile->entityImage;
    }

    function getForgottenPassword()
    {
        if ($this->isAuth) {
            $this->f3->reroute('/web');
        } else {
            $this->f3->set('pageSeoTitle', 'Aumet Marketplace - Forgot Password');
            $this->f3->set('vAuthFile', 'forgot');
            echo View::instance()->render('public/auth/layout.php');
        }
    }

    function postForgottenPassword()
    {
        $email = trim($this->f3->get("POST.email"));

        $dbUser = new BaseModel($this->db, "user");
        $dbUser->getByField("email", $email);

        if ($dbUser->dry()) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->errortype = "password reset";
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
        $email = trim($this->f3->get("POST.email"));
        $password = trim($this->f3->get("POST.password"));
        $entityName = !empty($this->f3->get("POST.pharmacyName")) ? $this->f3->get("POST.pharmacyName") : $this->f3->get("POST.distributorName");
        $tradeLicenseNumber = $this->f3->get("POST.tradeLicenseNumber");
        $countryId = $this->f3->get("POST.country");
        $cityId = $this->f3->get("POST.city");
        $address = $this->f3->get("POST.address");
        $pharmacyDocument = $this->f3->get("POST.pharmacyDocument");
        $companyType = $this->f3->get("POST.companyType");
        $isDistributor = empty($this->f3->get("POST.pharmacyName"));

        if (!$name || !ltrim($mobile, "+") || !$email || !$password || !$entityName || !$countryId || !$cityId || !$address) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->errortype = "registration";
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
            $this->webResponse->errortype = "registration";
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
            $this->webResponse->errortype = "registration";
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
                $this->webResponse->errortype = "registration";
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
        $dbUser->roleId = $isDistributor ? Constants::USER_ROLE_DISTRIBUTOR_SYSTEM_ADMINISTRATOR : Constants::USER_ROLE_PHARMACY_SYSTEM_ADMINISTRATOR;
        $dbUser->language = "en";
        $dbUser->addReturnID();

        // Add entity
        $dbEntity = new BaseModel($this->db, "entity");
        $dbEntity->typeId = $isDistributor ? Constants::ENTITY_TYPE_DISTRIBUTOR : Constants::ENTITY_TYPE_PHARMACY;
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
        $allValues->roleId = $dbUser->roleId;

        $dbCountry = new BaseModel($this->db, "country");
        $dbCountry->name = "name_en";
        $country = $dbCountry->getById($allValues->countryId)[0];
        $allValues->countryName = $country['name'];

        $dbCity = new BaseModel($this->db, "city");
        $dbCity->name = "nameEn";
        $city = $dbCity->getById($allValues->cityId)[0];
        $allValues->cityName = $city['name'];

        if (Helper::isPharmacy($dbUser->roleId)) {
            NotificationHelper::sendVerificationPharmacyNotification($this->f3, $this->db, $allValues, $dbUser->id, $dbEntity->id, $dbEntityBranch->id);
        } else {
            NotificationHelper::sendVerificationDistributorNotification($this->f3, $this->db, $allValues, $dbUser->id, $dbEntity->id, $dbEntityBranch->id);
        }

        $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
        $this->webResponse->message = $this->f3->get("vMessage_signupSuccessful");
        $this->webResponse->data = $allValues;
        echo $this->webResponse->jsonResponse();
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
            "docx",
            "jpeg",
            "jpg",
            "png",
        ];

        $success = false;

        $ext = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_EXTENSION);
        if (in_array(strtolower($ext), $allValidExtensions)) {
            $success = true;
        }

        if ($success) {
            $objResult = AumetFileUploader::upload("s3", $_FILES["file"], $this->generateRandomString(64));
            echo $objResult->fileLink;
        }
    }

    function postSignUpValidateStep1()
    {
        $this->f3->set('SESSION.companyType', $this->f3->get("POST.companyType"));
        $email = $this->f3->get("POST.email");
        $mobile = $this->f3->get("POST.mobile");

        // Check if email is unique
        $dbUser = new BaseModel($this->db, "user");
        $dbUser->getByField("email", $email);

        if (!$dbUser->dry()) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->title = "";
            $this->webResponse->errortype = "registration";
            $this->webResponse->message = "Email already exists.";
            echo $this->webResponse->jsonResponse();
            return;
        }

        $dbUser->getByField("mobile", $mobile);

        if (!$dbUser->dry()) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->title = "";
            $this->webResponse->errortype = "registration";
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
            $this->f3->set('vAuthFile', 'signup-verification-invalid');
        } else if ($dbUser->statusId != Constants::USER_STATUS_WAITING_VERIFICATION) {
            $this->f3->set('vAuthFile', 'signup-verification-verified-already');
        } else {
            $dbUser->statusId = Constants::USER_STATUS_PENDING_APPROVAL;
            $dbUser->update();

            $message = "Your account has been authenticated. You will be contacted by Aumet within 24 to 48 hours to activate your account";

            if (Helper::isPharmacy($dbUser->roleId)) {
                NotificationHelper::sendAccountVerifiedPharmacyNotification($this->f3, $this->db, $dbUser, $message);
            } else {
                NotificationHelper::sendAccountVerifiedDistributorNotification($this->f3, $this->db, $dbUser, $message);
            }

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
            if (Helper::isPharmacy($dbUser->roleId)) {
                NotificationHelper::sendApprovalPharmacyNotification($this->f3, $this->db, $allValues, $dbUser);
                $this->f3->set('companyType', 'pharmacy');
            } else {
                NotificationHelper::sendApprovalDistributorNotification($this->f3, $this->db, $allValues, $dbUser);
                $this->f3->set('companyType', 'distributor');
            }

            $this->f3->set('vAuthFile', 'signup-verification-verified');
        }
        echo View::instance()->render('public/auth/layout.php');
    }

    function getApproveAccount()
    {
        $token = $_GET['token'];
        $isValid = true;

        if (!isset($token) || $token == null || $token == "") {
            $this->f3->set('vAuthFile', 'signup-approve-invalid');
        }
        $token = urldecode($token);
        try {
            $jwt = new JWT(getenv('JWT_SECRET_KEY'), 'HS256', (86400 * 30), 10);
            $accessTokenPayload = $jwt->decode($token);
        } catch (\Exception $e) {
            $isValid = false;
            $this->f3->set('vAuthFile', 'signup-approve-invalid');
        }
        if (!is_array($accessTokenPayload)) {
            $isValid = false;
            $this->f3->set('vAuthFile', 'signup-approve-invalid');
        }

        if ($isValid) {
            $userId = $accessTokenPayload["userId"];

            $dbUser = new BaseModel($this->db, "user");
            $dbUser->getById($userId);

            if ($dbUser->dry()) {
                $this->f3->set('vAuthFile', 'signup-approve-invalid');
            } else if ($dbUser->statusId != Constants::USER_STATUS_PENDING_APPROVAL) {
                $this->f3->set('vAuthFile', 'signup-approve-verified-already');
            } else {
                $dbUser->statusId = Constants::USER_STATUS_ACCOUNT_ACTIVE;
                $dbUser->update();

                if (Helper::isPharmacy($dbUser->roleId)) {
                    NotificationHelper::sendAccountApprovedPharmacyNotification($this->f3, $this->db, $dbUser);
                } else {
                    NotificationHelper::sendAccountApprovedDistributorNotification($this->f3, $this->db, $dbUser);
                }

                $this->f3->set('vAuthFile', 'signup-approve-verified');
            }
        }
        echo View::instance()->render('public/auth/layout.php');
    }

    function getProcessEmailOnboarding(){
        $token = $_GET['token'];
        $isValid = true;

        if (!isset($token) || $token == null || $token == "") {
            $this->f3->set('vAuthFile', 'signup-approve-invalid');
        }
        $token = urldecode($token);
        try {
            $jwt = new JWT(getenv('JWT_SECRET_KEY'), 'HS256', (86400 * 30), 10);
            $accessTokenPayload = $jwt->decode($token);
        } catch (\Exception $e) {
            $isValid = false;
            $this->f3->set('vAuthFile', 'signup-approve-invalid');
        }
        if (!is_array($accessTokenPayload)) {
            $isValid = false;
        }

        if ($isValid) {
            $userId = $accessTokenPayload["userId"];

            $dbUser = new BaseModel($this->db, "user");
            $dbUser->getById($userId);

            if ($dbUser->dry()) {
                $this->rerouteAuth();
            } else if ($dbUser->statusId != Constants::USER_STATUS_PENDING_APPROVAL) {
                $this->rerouteAuth();
            } else {
                $this->configUser($dbUser);

                $this->f3->reroute('/web');
            }
        }
        else{
            $this->rerouteAuth();
        }


    }

    function getProcessPharmacies()
    {
        $dbUpload = new BaseModel($this->db, "uploadpharma_1");
        $dbUpload->getByField('isProcess', 7);

        $arr = [];

        while (!$dbUpload->dry()) {
            $password = $this->generateRandomNumber(6);
            $cityId = 53765;
            switch(strtolower(trim($dbUpload->City))) {
                case "rak":
                    $cityId = 53773;
                    break;
                case "ajman":
                    $cityId = 53768;
                    break;
                case "abu dhabi":
                    $cityId = 53765;
                    break;
                case "uaq":
                case "umm al quwaim":
                    $cityId = 53767;
                    break;
                case "sharjah":
                    case "Khorfakkan":
                    $cityId = 53774;
                    break;
                case "fujeirah":
                    $cityId = 53772;
                    break;
                case "dubai":
                    $cityId = 53771;
                    break;
            }
            $this->webResponse = new WebResponse();
            $obj = $this->handleProcessPharmacies($dbUpload->name, $dbUpload->mobile, $dbUpload->email, $password, $dbUpload->pharmacyName, $cityId, $dbUpload->Address );
            if($obj) {
                $dbUpload->entityId = $obj->entityId;
                $dbUpload->userId = $obj->userId;
                $dbUpload->entityBranchId = $obj->entityBranchId;
                $dbUpload->emailCode = NotificationHelper::sendOnboardingPharmacyNotification($this->f3, $this->db, $obj->userId, $dbUpload->email, $password, $dbUpload->pharmacyName, $obj->entityId, $obj->entityBranchId);
                $dbUpload->isProcess = 2;
                $dbUpload->update();

                $obj->emailCode = $dbUpload->emailCode;

            }
            else{
                $obj = new stdClass();
                $obj->emailCode = -1;
                $obj->email = $dbUpload->email;
            }
            $obj->response = $this->webResponse;


            $arr[] = $obj;

            $dbUpload->next();
        }

        echo $this->jsonResponseRaw($arr);
    }

    function handleProcessPharmacies($name, $mobile, $email, $password, $entityName, $cityId, $address)
    {

        $res = new stdClass();

        $uid = null;
        $tradeLicenseNumber = '';
        $countryId = 29;
        $pharmacyDocument = "";
        $isDistributor = false;

        if (!$name || !ltrim($mobile, "+") || !$email || !$password || !$entityName || !$countryId || !$cityId || !$address) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->errortype = "registration";
            $this->webResponse->message = "Some mandatory fields are missing";
            return false;
        }

        // Check if email is unique
        $dbUser = new BaseModel($this->db, "user");
        $dbUser->getByField("email", $email);

        if (!$dbUser->dry()) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->title = "";
            $this->webResponse->errortype = "registration";
            $this->webResponse->message = "Email Already Exists";
            return false;
        }

        /*
        if($mobile != '' && $mobile != null){
            // Check if phone number is unique
            $dbUser = new BaseModel($this->db, "user");
            $dbUser->getByField("mobile", $mobile);

            if (!$dbUser->dry()) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->title = "";
                $this->webResponse->message = "Phone number exists!";
                return false;
            }
        }*/



        // Check if trading license is unique
        $dbEntityBranch = new BaseModel($this->db, "entityBranch");
        if ($tradeLicenseNumber != '') {
            $dbEntityBranch->getByField("tradeLicenseNumber", $tradeLicenseNumber);

            if (!$dbEntityBranch->dry()) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->title = "";
                $this->webResponse->errortype = "registration";
                $this->webResponse->message = "Trading license exists!";
                return  $this->webResponse->jsonResponse();
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
        $dbUser->statusId = Constants::USER_STATUS_ACCOUNT_ACTIVE;
        $dbUser->fullname = $name;
        $dbUser->mobile = $mobile;
        $dbUser->roleId = Constants::USER_ROLE_PHARMACY_SYSTEM_ADMINISTRATOR;
        $dbUser->language = "en";
        $dbUser->addReturnID();

        $res->userId = $dbUser->id;

        // Add entity
        $dbEntity = new BaseModel($this->db, "entity");
        $dbEntity->typeId = Constants::ENTITY_TYPE_PHARMACY;
        $dbEntity->name_ar = $entityName;
        $dbEntity->name_en = $entityName;
        $dbEntity->name_fr = $entityName;
        $dbEntity->countryId = $countryId;
        $dbEntity->currencyId = $currencyId;
        $dbEntity->addReturnID();

        $res->entityId = $dbEntity->id;

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

        $res->entityBranchId = $dbEntityBranch->id;

        // Add account
        $dbAccount = new BaseModel($this->db, "account");
        $dbAccount->entityId = $dbEntity->id;
        $dbAccount->number = 100;
        $dbAccount->statusId = Constants::ACCOUNT_STATUS_ACTIVE;
        $dbAccount->addReturnID();

        $res->accountId = $dbAccount->id;

        // Add user account
        $dbUserAccount = new BaseModel($this->db, "userAccount");
        $dbUserAccount->userId = $dbUser->id;
        $dbUserAccount->accountId = $dbAccount->id;
        $dbUserAccount->statusId = Constants::ACCOUNT_STATUS_ACTIVE;
        $dbUserAccount->addReturnID();

        return $res;
    }

    protected function renderErrorIfValidationFails(BaseModel $model, $statusCode, $message, $data = [])
    {
        if ($model->hasErrors) {
            $this->webResponse->errorCode = $statusCode;
            $this->webResponse->message = $message;
            $this->webResponse->data = $data;
            echo $this->webResponse->jsonResponse();
            die();
        }
    }
}
