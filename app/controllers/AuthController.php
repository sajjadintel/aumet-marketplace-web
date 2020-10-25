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
        $this->clearUserSession();
        $this->rerouteAuth();
    }

    function postSignIn_NoFirbase()
    {
        $email = $this->f3->get("POST.email");
        $password = $this->f3->get("POST.password");

        global $dbConnection;

        $dbUser = new BaseModel($dbConnection, "user");
        $dbUser->getByField("email", $email);
        if ($dbUser->dry()) {
            echo $this->jsonResponse(false, null, $this->f3->get("vMessage_invalidLogin"));
        } else {
            if (password_verify($password, $dbUser->password)) {

                $this->configUser($dbUser);

                $this->webResponse->errorCode = 1;
                echo $this->webResponse->jsonResponse();
            } else {
                $this->webResponse->errorCode = 2;
                $this->webResponse->message = $this->f3->get("vMessage_invalidLogin");
                echo $this->webResponse->jsonResponse();
            }
        }
    }

    function postSignIn()
    {
        $factory = (new Factory)->withServiceAccount($this->getRootDirectory() . '/config/aumet-marketplace-firebase-adminsdk-xw4gn-6075919bd7.json');

        $auth = $factory->createAuth();

        $idTokenString = $this->f3->get("POST.token");

        try {
            $verifiedIdToken = $auth->verifyIdToken($idTokenString);
            $uid = $verifiedIdToken->getClaim('sub');
            $user = $auth->getUser($uid);

            global $dbConnection;

            $dbUser = new BaseModel($dbConnection, "user");
            $dbUser->getByField("uid", $uid);
            if ($dbUser->dry()) {


                $this->webResponse->errorCode = 1;
                $this->webResponse->message = $this->f3->get("vMessage_invalidLogin");
                $this->webResponse->data = $user;
            } else {
                $this->configUser($dbUser);
                $this->webResponse->errorCode = 0;
            }
        } catch (\InvalidArgumentException $e) {
            $this->webResponse->errorCode = 1;
            $this->webResponse->message = $e->getMessage();
        } catch (InvalidToken $e) {
            $this->webResponse->errorCode = 1;
            $this->webResponse->message = $e->getMessage();
        }

        echo $this->webResponse->jsonResponse();
    }

    function configUser($dbUser)
    {
        $objUser = new stdClass();

        global $dbConnection;

        $objUser->id = $dbUser->id;
        $objUser->email = $dbUser->email;
        $objUser->mobile = $dbUser->mobile;
        $objUser->fullname = $dbUser->fullname;
        $objUser->roleId = $dbUser->roleId;
        $objUser->statusId = $dbUser->statusId;
        $objUser->language = $dbUser->language;

        $this->f3->set('LANGUAGE', $objUser->language);

        $dbUserRole = new BaseModel($dbConnection, "userRole");
        $dbUserRole->name = "name_" . $objUser->language;
        $dbUserRole->getById($objUser->roleId);
        $objUser->roleName = $dbUserRole->name;
        $objUser->isSystemRole = $dbUserRole->isSystem;

        $dbMenuConfig = new BaseModel($dbConnection, "menuConfig");
        if ($objUser->isSystemRole == 1) {
            $dbMenuConfig->getWhere("userRoleId=$objUser->roleId and entityTypeId=0");
        } else {
            $dbMenuConfig->getWhere("userRoleId=$objUser->roleId and entityTypeId=0");
        }
        $objUser->menuId = $dbMenuConfig->menuId;
        $objUser->entityTypeId = $dbMenuConfig->entityTypeId;

        $dbMenu = new BaseModel($dbConnection, "menu");
        $dbMenu->getById($objUser->menuId);
        $objUser->menuCode = $dbMenu->code;

        $dbUserAccount = new BaseModel($dbConnection, "userAccount");
        $dbUserAccount->getByField("userId", $objUser->id);
        $objUser->accountId = $dbUserAccount->accountId;

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

        $dbAccount = new BaseModel($dbConnection, "account");
        $dbAccount->getByField("id", $dbUserAccount->accountId);

        $dbEntity = new BaseModel($dbConnection, "entity");
        $dbEntity->name = "name_" . $objUser->language;
        $dbEntity->getByField("id", $dbAccount->entityId);
        $arrEntities = [];
        while (!$dbEntity->dry()) {
            $arrEntities[$dbEntity->id] = $dbEntity->name;
            $dbEntity->next();
        }

        $this->f3->set('SESSION.arrEntities', $arrEntities);
    }

    function setLanguage()
    {
    }

    function getSignUp()
    {
        if ($this->isAuth) {
            $this->f3->reroute('/web');
        } else {
            $this->f3->set('vAuthFile', 'signup');

            echo View::instance()->render('public/auth/layout.php');
        }
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

    function refreshUserData()
    {
        $dbUser = new BaseModel($this->db, "users");
        $dbUser->getByField("id", $this->f3->get('SESSION.userId'));

        if ($dbUser->dry()) {
            return FALSE;
        } else {
            $this->setUserSession($dbUser);
            return TRUE;
        }
    }



    function postSignUp()
    {
        $code = $this->f3->get("POST.code");

        $dbCode = new BaseModel($this->db, "codes");
        $dbCode->getByField("code", $code);

        if ($dbCode->dry()) {
            echo $this->jsonResponse(false, "Code is invalid, Please signup with a valid code");
        } else if ($dbCode->isActive == 0) {
            echo $this->jsonResponse(false, "Code is invalid, Please signup with a valid code");
        } else {
            $fullname = $this->f3->get("POST.fullname");
            $email = $this->f3->get("POST.email");
            $password = $this->f3->get("POST.password");

            $dbUser = new BaseModel($this->db, "users");
            $dbUser->getByField("email", $email);
            if (!$dbUser->dry()) {
                echo $this->jsonResponse(false, "Email address exists, Please signin instead");
            } else {
                $dbUser->fullname = $fullname;
                $dbUser->email = $email;
                $dbUser->password = password_hash($password, PASSWORD_DEFAULT);
                $dbUser->codeId = $dbCode->id;
                $dbUser->plainPassword = $password;
                $dbUser->typeId = 5;
                if ($dbUser->addReturnID()) {

                    $this->sendWelcomeEmail($dbUser);

                    $this->setUserSession($dbUser);

                    echo $this->jsonResponse(true);
                } else {
                    echo $this->jsonResponse(false, "Something went wrong, Try again later");
                }
            }
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
}
