<?php

use Ahc\Jwt\JWT;

class ProfileController extends Controller {

    function getProfile()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/profile");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            if ($this->objUser->menuId == Constants::MENU_DISTRIBUTOR) {
                // Get logged in user
                $dbUser = new BaseModel($this->db, "vwEntityUserProfile");
                $dbUser->entityName = "entityName_" . $this->objUser->language;
                $dbUser->entityCountryName = "entityCountryName_" . $this->objUser->language;
                $dbUser->entityBranchCityName = "entityBranchCityName_" . $this->objUser->language;
                $dbUser->entityBranchAddress = "entityBranchAddress_" . $this->objUser->language;
                $user = $dbUser->getWhere("userId=".$this->objUser->id)[0];
                $this->f3->set('user', $user);
                $this->f3->set('entityBranchTradeLicenseUrlDecoded', urldecode($user->entityBranchTradeLicenseUrl));

                // Get all payment methods
                $dbPaymentMethod = new BaseModel($this->db, "paymentMethod");
                $dbPaymentMethod->name = "name_" . $this->objUser->language;
                $arrPaymentMethod = $dbPaymentMethod->findAll();
                $this->f3->set('arrPaymentMethod', $arrPaymentMethod);

                $entityId = $dbUser->entityId;

                // Get all entity payment methods
                $dbEntityPaymentMethod = new BaseModel($this->db, "entityPaymentMethod");
                $arrEntityPaymentMethod = $dbEntityPaymentMethod->getWhere("entityId = $entityId");
                $arrEntityPaymentMethodId = [];
                foreach($arrEntityPaymentMethod as $entityPaymentMethod) {
                    array_push($arrEntityPaymentMethodId, $entityPaymentMethod['paymentMethodId']);
                }
                $this->f3->set('arrEntityPaymentMethodId', $arrEntityPaymentMethodId);

                // Get all entity minimum value orders
                $dbEntityMinimumValueOrder = new BaseModel($this->db, "entityMinimumValueOrder");
                $arrEntityMinimumValueOrder = $dbEntityMinimumValueOrder->getWhere("entityId = $entityId");
                $arrEntityMinimumValueOrderId = [];
                foreach($arrEntityMinimumValueOrder as $entityMinimumValueOrder) {
                    array_push($arrEntityMinimumValueOrderId, $entityMinimumValueOrder['id']);
                }

                // Group all minimum value orders' cities
                $arrEntityMinimumValueOrderGrouped = [];
                if(count($arrEntityMinimumValueOrderId) > 0) {
                    $dbEntityMinimumValueOrderCity = new BaseModel($this->db, "entityMinimumValueOrderCity");
                    $strEntityMinimumValueOrderId = implode(",", $arrEntityMinimumValueOrderId);
                    $arrEntityMinimumValueOrderCity = $dbEntityMinimumValueOrderCity->getWhere("entityMinimumValueOrderId IN ($strEntityMinimumValueOrderId)");

                    $mapEntityMinimumValueOrderIdCityId = [];
                    foreach($arrEntityMinimumValueOrderCity as $entityMinimumValueOrderCity) {
                        $entityMinimumValueOrderId = $entityMinimumValueOrderCity['entityMinimumValueOrderId'];
                        $cityId = $entityMinimumValueOrderCity['cityId'];
                        if(array_key_exists($entityMinimumValueOrderId, $mapEntityMinimumValueOrderIdCityId)) {
                            $allCityId = $mapEntityMinimumValueOrderIdCityId[$entityMinimumValueOrderId];
                            array_push($allCityId, $cityId);
                            $mapEntityMinimumValueOrderIdCityId[$entityMinimumValueOrderId] = $allCityId;
                        } else {
                            $mapEntityMinimumValueOrderIdCityId[$entityMinimumValueOrderId] = [$cityId];
                        }
                    }

                    foreach($arrEntityMinimumValueOrder as $entityMinimumValueOrder) {
                        $entityMinimumValueOrderId = $entityMinimumValueOrder['id'];

                        $entityMinimumValueOrderGrouped = new stdClass();
                        $entityMinimumValueOrderGrouped->entityMinimumValueOrderId = $entityMinimumValueOrderId;
                        $entityMinimumValueOrderGrouped->minimumValueOrder = $entityMinimumValueOrder['minimumValueOrder'];
                        $entityMinimumValueOrderGrouped->allCity = $mapEntityMinimumValueOrderIdCityId[$entityMinimumValueOrderId];
                        array_push($arrEntityMinimumValueOrderGrouped, $entityMinimumValueOrderGrouped);
                    }
                }
                $this->f3->set('arrEntityMinimumValueOrderGrouped', $arrEntityMinimumValueOrderGrouped);

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
                $this->webResponse->title = $this->f3->get('vTitle_profile');
                $this->webResponse->data = View::instance()->render('app/profile/distributor.php');
                echo $this->webResponse->jsonResponse();
            } else {
                // Get logged in user
                $dbUser = new BaseModel($this->db, "vwEntityUserProfile");
                $dbUser->entityName = "entityName_" . $this->objUser->language;
                $dbUser->entityCountryName = "entityCountryName_" . $this->objUser->language;
                $dbUser->entityBranchCityName = "entityBranchCityName_" . $this->objUser->language;
                $dbUser->entityBranchAddress = "entityBranchAddress_" . $this->objUser->language;
                $user = $dbUser->getWhere("userId=".$this->objUser->id)[0];
                $this->f3->set('user', $user);
                $this->f3->set('entityBranchTradeLicenseUrlDecoded', urldecode($user->entityBranchTradeLicenseUrl));

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
                $this->webResponse->title = $this->f3->get('vTitle_profile');
                $this->webResponse->data = View::instance()->render('app/profile/pharmacy.php');
                echo $this->webResponse->jsonResponse();
            }
        }
    }

    function postProfileDocumentUpload()
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

        $targetFile = Helper::createUploadedFileName($fileName,$ext,"files/uploads/documents/");

        if (in_array($ext, $allValidExtensions)) {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                $success = true;
            }
        }

        if ($success) {
            echo $targetFile;
        }
    }

    function postPharmacyProfileMyProfile()
    {
        $userId = $this->f3->get("POST.userId");
        $entityName = $this->f3->get("POST.entityName");
        $tradeLicenseNumber = $this->f3->get("POST.tradeLicenseNumber");
        $address = $this->f3->get("POST.address");
        $entityDocument = $this->f3->get("POST.entityDocument");

        $this->checkLength($entityName, 'entityName', 100, 4);
        $this->checkLength($address, 'address', 500, 4);
        if(strlen($tradeLicenseNumber) > 0) {
            $this->checkLength($tradeLicenseNumber, 'tradeLicenseNumber', 200, 4);
        }

        if(strlen($address) == 0) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = $this->f3->get("vModule_profile_missingFields");
            echo $this->webResponse->jsonResponse();
            return;
        }

        // Check if user exists
        $dbUser = new BaseModel($this->db, "vwEntityUserProfile");
        $dbUser->getWhere("userId=$userId");

        if ($dbUser->dry()) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = $this->f3->get("vModule_profile_userNotFound");
            echo $this->webResponse->jsonResponse();
        } else {
            $dbEntity = new BaseModel($this->db, "entity");
            $dbEntity->getByField("id", $dbUser->entityId);
            
            $dbEntityBranch = new BaseModel($this->db, "entityBranch");
            $dbEntityBranch->getByField("id", $dbUser->entityBranchId);
            $dbEntityBranch->address_ar = $address;
            $dbEntityBranch->address_en = $address;
            $dbEntityBranch->address_fr = $address;
            $dbEntityBranch->tradeLicenseUrl = $entityDocument;
            
            if($dbEntity->name_en != $entityName || $dbEntityBranch->tradeLicenseNumber != $tradeLicenseNumber) {
                if(strlen($entityDocument) == 0) {
                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->message = $this->f3->get("vModule_profile_missingDocumentApproval");
                    echo $this->webResponse->jsonResponse();
                    return;
                }

                $mapFieldNameOldNewValue = [];
                if($dbEntity->name_en != $entityName) {
                    $mapFieldNameOldNewValue["entity.name_ar"] = [ $dbEntity->name_ar, $entityName ];
                    $mapFieldNameOldNewValue["entity.name_en"] = [ $dbEntity->name_en, $entityName ];
                    $mapFieldNameOldNewValue["entity.name_fr"] = [ $dbEntity->name_fr, $entityName ];
                    $mapFieldNameOldNewValue["entityBranch.name_ar"] = [ $dbEntity->name_ar, $entityName ];
                    $mapFieldNameOldNewValue["entityBranch.name_en"] = [ $dbEntity->name_en, $entityName ];
                    $mapFieldNameOldNewValue["entityBranch.name_fr"] = [ $dbEntity->name_fr, $entityName ];
                }
                
                if($dbEntityBranch->tradeLicenseNumber != $tradeLicenseNumber) {
                    $mapFieldNameOldNewValue["entityBranch.tradeLicenseNumber"] = [ $dbEntityBranch->tradeLicenseNumber, $tradeLicenseNumber ];
                }
            
                $dbEntityChangeApproval = new BaseModel($this->db, "entityChangeApproval");
                $dbEntityChangeApproval->tradeLicenseUrl = $entityDocument;
                $dbEntityChangeApproval->entityId = $dbUser->entityId;
                $dbEntityChangeApproval->userId = $userId;
                $dbEntityChangeApproval->addReturnID();
            
                $dbEntityChangeApprovalField = new BaseModel($this->db, "entityChangeApprovalField");
                $mapDisplayNameOldNewValue = [];
                foreach($mapFieldNameOldNewValue as $fieldName => $oldNewValue) {
                    $oldValue = $oldNewValue[0];
                    $newValue = $oldNewValue[1];

                    // Add row in entityChangeApprovalField
                    $dbEntityChangeApprovalField->entityChangeApprovalId = $dbEntityChangeApproval->id;
                    $dbEntityChangeApprovalField->fieldName = $fieldName;
                    $dbEntityChangeApprovalField->oldValue = $oldValue;
                    $dbEntityChangeApprovalField->newValue = $newValue;
                    $dbEntityChangeApprovalField->add();

                    // Fill map used to display data in the mail approval
                    $allParts = explode(".", $fieldName);
                    $name = end($allParts);
                    if($name == "name_en") {
                        $displayName = "Pharmacy Name";
                    } else if($name == "tradeLicenseNumber") {
                        $displayName = "Trade License Number";
                    }

                    if($displayName) {
                        $mapDisplayNameOldNewValue[$displayName] = $oldNewValue;
                    }
                }
                $message = $this->f3->get("vModule_profile_requestSent");

                $approvalUrl = "web/pharmacy/profile/approve";
                $this->sendChangeApprovalEmail($dbEntityChangeApproval->id, $mapDisplayNameOldNewValue, $entityDocument, $approvalUrl);
            } else {
                $message = $this->f3->get("vModule_profile_myProfileSaved");
            }

            $dbEntityBranch->update();

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->message = $message;
            echo $this->webResponse->jsonResponse();
        }
    }

    function postPharmacyProfileAccountSetting()
    {
        $userId = $this->f3->get("POST.userId");
        $oldPassword = $this->f3->get("POST.oldPassword");
        $newPassword = $this->f3->get("POST.newPassword");
        $newPasswordConfirmation = $this->f3->get("POST.newPasswordConfirmation");

        if(strlen($oldPassword) == 0
        || strlen($newPassword) == 0
        || strlen($newPasswordConfirmation) == 0) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = $this->f3->get("vModule_profile_missingFields");
            echo $this->webResponse->jsonResponse();
            return;
        }

        if($newPassword != $newPasswordConfirmation) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = $this->f3->get("vModule_profile_wrongPasswordConfirmation");
            echo $this->webResponse->jsonResponse();
            return;
        }

        // Check if user exists
        $dbUser = new BaseModel($this->db, "user");
        $dbUser->getWhere("id=$userId");
        if ($dbUser->dry()) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = $this->f3->get("vModule_profile_userNotFound");
            echo $this->webResponse->jsonResponse();
        } else {
            // Check if oldPassword is correct
            if(!password_verify($oldPassword, $dbUser->password)) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->message = $this->f3->get("vModule_profile_wrongPassword");
                echo $this->webResponse->jsonResponse();
            } else {
                if(password_verify($newPassword, $dbUser->password)) {
                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->message = $this->f3->get("vModule_profile_samePassword");
                    echo $this->webResponse->jsonResponse();
                    return;
                }

                $dbUser->password = password_hash($newPassword, PASSWORD_DEFAULT);
                $dbUser->update();

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
                $this->webResponse->message = $this->f3->get("vModule_profile_accountSettingSaved");
                echo $this->webResponse->jsonResponse();
            }
        }
    }

    function postDistributorProfileMyProfile()
    {
        $userId = $this->f3->get("POST.userId");
        $entityName = $this->f3->get("POST.entityName");
        $tradeLicenseNumber = $this->f3->get("POST.tradeLicenseNumber");
        $address = $this->f3->get("POST.address");
        $entityDocument = $this->f3->get("POST.entityDocument");

        $this->checkLength($entityName, 'entityName', 100, 4);
        $this->checkLength($address, 'address', 500, 4);
        if(strlen($tradeLicenseNumber) > 0) {
            $this->checkLength($tradeLicenseNumber, 'tradeLicenseNumber', 200, 4);
        }

        if(strlen($address) == 0) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = $this->f3->get("vModule_profile_missingFields");
            echo $this->webResponse->jsonResponse();
            return;
        }

        // Check if user exists
        $dbUser = new BaseModel($this->db, "vwEntityUserProfile");
        $dbUser->getWhere("userId=$userId");

        if ($dbUser->dry()) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = $this->f3->get("vModule_profile_userNotFound");
            echo $this->webResponse->jsonResponse();
        } else {
            $dbEntity = new BaseModel($this->db, "entity");
            $dbEntity->getByField("id", $dbUser->entityId);
            
            $dbEntityBranch = new BaseModel($this->db, "entityBranch");
            $dbEntityBranch->getByField("id", $dbUser->entityBranchId);
            $dbEntityBranch->address_ar = $address;
            $dbEntityBranch->address_en = $address;
            $dbEntityBranch->address_fr = $address;
            $dbEntityBranch->tradeLicenseUrl = $entityDocument;
            
            if($dbEntity->name_en != $entityName || $dbEntityBranch->tradeLicenseNumber != $tradeLicenseNumber) {
                if(strlen($entityDocument) == 0) {
                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->message = $this->f3->get("vModule_profile_missingDocumentApproval");
                    echo $this->webResponse->jsonResponse();
                    return;
                }

                $mapFieldNameOldNewValue = [];
                if($dbEntity->name_en != $entityName) {
                    $mapFieldNameOldNewValue["entity.name_ar"] = [ $dbEntity->name_ar, $entityName ];
                    $mapFieldNameOldNewValue["entity.name_en"] = [ $dbEntity->name_en, $entityName ];
                    $mapFieldNameOldNewValue["entity.name_fr"] = [ $dbEntity->name_fr, $entityName ];
                    $mapFieldNameOldNewValue["entityBranch.name_ar"] = [ $dbEntity->name_ar, $entityName ];
                    $mapFieldNameOldNewValue["entityBranch.name_en"] = [ $dbEntity->name_en, $entityName ];
                    $mapFieldNameOldNewValue["entityBranch.name_fr"] = [ $dbEntity->name_fr, $entityName ];
                }
                
                if($dbEntityBranch->tradeLicenseNumber != $tradeLicenseNumber) {
                    $mapFieldNameOldNewValue["entityBranch.tradeLicenseNumber"] = [ $dbEntityBranch->tradeLicenseNumber, $tradeLicenseNumber ];
                }
            
                $dbEntityChangeApproval = new BaseModel($this->db, "entityChangeApproval");
                $dbEntityChangeApproval->tradeLicenseUrl = $entityDocument;
                $dbEntityChangeApproval->entityId = $dbUser->entityId;
                $dbEntityChangeApproval->userId = $userId;
                $dbEntityChangeApproval->addReturnID();
            
                $dbEntityChangeApprovalField = new BaseModel($this->db, "entityChangeApprovalField");
                $mapDisplayNameOldNewValue = [];
                foreach($mapFieldNameOldNewValue as $fieldName => $oldNewValue) {
                    $oldValue = $oldNewValue[0];
                    $newValue = $oldNewValue[1];

                    // Add row in entityChangeApprovalField
                    $dbEntityChangeApprovalField->entityChangeApprovalId = $dbEntityChangeApproval->id;
                    $dbEntityChangeApprovalField->fieldName = $fieldName;
                    $dbEntityChangeApprovalField->oldValue = $oldValue;
                    $dbEntityChangeApprovalField->newValue = $newValue;
                    $dbEntityChangeApprovalField->add();

                    // Fill map used to display data in the mail approval
                    $allParts = explode(".", $fieldName);
                    $name = end($allParts);
                    if($name == "name_en") {
                        $displayName = "Distributor Name";
                    } else if($name == "tradeLicenseNumber") {
                        $displayName = "Trade License Number";
                    }

                    if($displayName) {
                        $mapDisplayNameOldNewValue[$displayName] = $oldNewValue;
                    }
                }
                $message = $this->f3->get("vModule_profile_requestSent");

                $approvalUrl = "web/distributor/profile/approve";
                $this->sendChangeApprovalEmail($dbEntityChangeApproval->id, $mapDisplayNameOldNewValue, $entityDocument, $approvalUrl);
            } else {
                $message = $this->f3->get("vModule_profile_myProfileSaved");
            }

            $dbEntityBranch->update();

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->message = $message;
            echo $this->webResponse->jsonResponse();
        }
    }

    function postDistributorProfileAccountSetting()
    {
        $userId = $this->f3->get("POST.userId");
        $oldPassword = $this->f3->get("POST.oldPassword");
        $newPassword = $this->f3->get("POST.newPassword");
        $newPasswordConfirmation = $this->f3->get("POST.newPasswordConfirmation");

        if(strlen($oldPassword) == 0
        || strlen($newPassword) == 0
        || strlen($newPasswordConfirmation) == 0) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = $this->f3->get("vModule_profile_missingFields");
            echo $this->webResponse->jsonResponse();
            return;
        }

        if($newPassword != $newPasswordConfirmation) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = $this->f3->get("vModule_profile_wrongPasswordConfirmation");
            echo $this->webResponse->jsonResponse();
            return;
        }

        // Check if user exists
        $dbUser = new BaseModel($this->db, "user");
        $dbUser->getWhere("id=$userId");
        if ($dbUser->dry()) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = $this->f3->get("vModule_profile_userNotFound");
            echo $this->webResponse->jsonResponse();
        } else {
            // Check if oldPassword is correct
            if(!password_verify($oldPassword, $dbUser->password)) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->message = $this->f3->get("vModule_profile_wrongPassword");
                echo $this->webResponse->jsonResponse();
            } else {
                if(password_verify($newPassword, $dbUser->password)) {
                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->message = $this->f3->get("vModule_profile_samePassword");
                    echo $this->webResponse->jsonResponse();
                    return;
                }

                $dbUser->password = password_hash($newPassword, PASSWORD_DEFAULT);
                $dbUser->update();

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
                $this->webResponse->message = $this->f3->get("vModule_profile_accountSettingSaved");
                echo $this->webResponse->jsonResponse();
            }
        }
    }

    function postDistributorProfilePaymentSetting()
    {
        $userId = $this->f3->get("POST.userId");
        $allPaymentMethodId = $this->f3->get("POST.allPaymentMethodId");
        $allEntityMinimumValueOrder = $this->f3->get("POST.allEntityMinimumValueOrder");

        // Check if there is a payment method
        if(!$allPaymentMethodId || count($allPaymentMethodId) == 0) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = $this->f3->get("vModule_profile_paymentMethodRequired");
            echo $this->webResponse->jsonResponse();
            return;
        }

        if(!$allEntityMinimumValueOrder) {
            $allEntityMinimumValueOrder = [];
        }

        // Check if a city is selected more than once
        $allSelectedCityId = [];
        foreach($allEntityMinimumValueOrder as $entityMinimumValueOrder) {
            $allCityId = $entityMinimumValueOrder['minimumValueOrderCityId'];
            foreach($allCityId as $cityId) {
                if(in_array($cityId, $allSelectedCityId)) {
                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->message = $this->f3->get("vModule_profile_minimumValueOrderCityError");
                    echo $this->webResponse->jsonResponse();
                    return;
                } else {
                    array_push($allSelectedCityId, $cityId);
                }
            }
        }

        // Check if user exists
        $dbUser = new BaseModel($this->db, "vwEntityUserProfile");
        $dbUser->getWhere("userId=$userId");
        if ($dbUser->dry()) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = $this->f3->get("vModule_profile_userNotFound");
            echo $this->webResponse->jsonResponse();
        } else {
            $entityId = $dbUser->entityId;

            // Update payment method
            $dbEntityPaymentMethod = new BaseModel($this->db, "entityPaymentMethod");
            $dbEntityPaymentMethod->getWhere("entityId = $entityId");
            while (!$dbEntityPaymentMethod->dry()) {
                $dbEntityPaymentMethod->delete();
                $dbEntityPaymentMethod->next();
            }

            foreach($allPaymentMethodId as $paymentMethodId) {
                $dbEntityPaymentMethod->entityId = $entityId;
                $dbEntityPaymentMethod->paymentMethodId = $paymentMethodId;
                $dbEntityPaymentMethod->add();
            }

            // Update minimum value order
            $dbEntityMinimumValueOrder = new BaseModel($this->db, "entityMinimumValueOrder");
            $dbEntityMinimumValueOrder->getWhere("entityId = $entityId");
            $oldEntityMinimumValueOrderId = [];
            while (!$dbEntityMinimumValueOrder->dry()) {
                array_push($oldEntityMinimumValueOrderId, $dbEntityMinimumValueOrder['id']);
                $dbEntityMinimumValueOrder->delete();
                $dbEntityMinimumValueOrder->next();
            }

            $dbEntityMinimumValueOrderCity = new BaseModel($this->db, "entityMinimumValueOrderCity");
            if(count($oldEntityMinimumValueOrderId) > 0) {
                $oldEntityMinimumValueOrderIdStr = implode(",", $oldEntityMinimumValueOrderId);
                $dbEntityMinimumValueOrderCity->getWhere("entityMinimumValueOrderId IN ($oldEntityMinimumValueOrderIdStr)");
                while (!$dbEntityMinimumValueOrderCity->dry()) {
                    $dbEntityMinimumValueOrderCity->delete();
                    $dbEntityMinimumValueOrderCity->next();
                }
            }

            foreach($allEntityMinimumValueOrder as $entityMinimumValueOrder) {
                $dbEntityMinimumValueOrder->entityId = $entityId;
                $dbEntityMinimumValueOrder->minimumValueOrder = $entityMinimumValueOrder['minimumValueOrder'];
                $dbEntityMinimumValueOrder->addReturnID();
                $allCityId = $entityMinimumValueOrder['minimumValueOrderCityId'];
                foreach($allCityId as $cityId) {
                    $dbEntityMinimumValueOrderCity->entityMinimumValueOrderId = $dbEntityMinimumValueOrder['id'];
                    $dbEntityMinimumValueOrderCity->cityId = $cityId;
                    $dbEntityMinimumValueOrderCity->add();
                }
            }

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->message = $this->f3->get("vModule_profile_paymentSettingSaved");
            echo $this->webResponse->jsonResponse();
        }
    }

    function sendChangeApprovalEmail($entityChangeApprovalId, $mapDisplayNameOldNewValue, $tradeLicenseUrl, $approvalUrl)
    {
        $emailHandler = new EmailHandler($this->db);
        $emailFile = "email/layout.php";
        $this->f3->set('domainUrl', getenv('DOMAIN_URL'));
        $this->f3->set('title', 'Change Profile Approval');
        $this->f3->set('emailType', 'changeProfileApproval');
        
        $this->f3->set('mapDisplayNameOldNewValue', $mapDisplayNameOldNewValue);
        $this->f3->set('tradeLicenseUrl', $tradeLicenseUrl);
        $this->f3->set('approvalUrl', $approvalUrl);

        $payload = [
            'entityChangeApprovalId' => $entityChangeApprovalId
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

        $subject = "Aumet - Change Profile Approval";
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: " . getenv('ENV') . ")";

            if (getenv('ENV') == Constants::ENV_LOC) {
                $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                $emailHandler->appendToAddress("sajjadintel@gmail.com", "Sajad Abbasi");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
            }
        }
        $emailHandler->sendEmail(Constants::EMAIL_CHANGE_PROFILE_APPROVAL, $subject, $htmlContent);
    }

    function getPharmacyProfileApprove()
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

        $entityChangeApprovalId = $accessTokenPayload["entityChangeApprovalId"];

        $dbEntityChangeApproval = new BaseModel($this->db, "entityChangeApproval");
        $dbEntityChangeApproval->getById($entityChangeApprovalId);

        if ($dbEntityChangeApproval->dry()) {
            echo "Invalid";
        } else if($dbEntityChangeApproval->isApproved) {
            echo "Already Approved";
        } else {
            $dbEntityChangeApproval->isApproved = 1;
            $dbEntityChangeApproval->updatedAt = date('Y-m-d H:i:s');
            $dbEntityChangeApproval->update();
            
            $dbEntityChangeApprovalField = new BaseModel($this->db, "entityChangeApprovalField");
            $dbEntityChangeApprovalField->getWhere("entityChangeApprovalId=$entityChangeApprovalId");
            
            $dbUser = new BaseModel($this->db, "vwEntityUserProfile");
            $dbUser->getWhere("userId=$dbEntityChangeApproval->userId");

            $dbEntity = new BaseModel($this->db, "entity");
            $dbEntity->getByField("id", $dbUser->entityId);
            
            $dbEntityBranch = new BaseModel($this->db, "entityBranch");
            $dbEntityBranch->getByField("id", $dbUser->entityBranchId);

            $mapDisplayNameOldNewValue = [];
            while(!$dbEntityChangeApprovalField->dry()) {
                $allParts = explode(".", $dbEntityChangeApprovalField->fieldName);
                $table = $allParts[0];
                $name = end($allParts);

                // Update information
                if($table == "entity") {
                    $dbEntity[$name] = $dbEntityChangeApprovalField->newValue;
                } else if($table == "entityBranch") {
                    $dbEntityBranch[$name] = $dbEntityChangeApprovalField->newValue;
                }

                // Fill map used to display data in the mail approval
                if($name == "name_en") {
                    $displayName = "Pharmacy Name";
                } else if($name == "tradeLicenseNumber") {
                    $displayName = "Trade License Number";
                }

                if($displayName) {
                    $mapDisplayNameOldNewValue[$displayName] = [
                        $dbEntityChangeApprovalField->oldValue,
                        $dbEntityChangeApprovalField->newValue
                    ];
                }

                $dbEntityChangeApprovalField->next();
            }

            $dbEntity->update();
            $dbEntityBranch->update();

            $this->sendChangeApprovedEmail($entityChangeApprovalId, $mapDisplayNameOldNewValue, $dbEntityChangeApproval->tradeLicenseUrl);
            echo "Approved";
        }
    }

    function getDistributorProfileApprove()
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

        $entityChangeApprovalId = $accessTokenPayload["entityChangeApprovalId"];

        $dbEntityChangeApproval = new BaseModel($this->db, "entityChangeApproval");
        $dbEntityChangeApproval->getById($entityChangeApprovalId);

        if ($dbEntityChangeApproval->dry()) {
            echo "Invalid";
        } else if($dbEntityChangeApproval->isApproved) {
            echo "Already Approved";
        } else {
            $dbEntityChangeApproval->isApproved = 1;
            $dbEntityChangeApproval->updatedAt = date('Y-m-d H:i:s');
            $dbEntityChangeApproval->update();
            
            $dbEntityChangeApprovalField = new BaseModel($this->db, "entityChangeApprovalField");
            $dbEntityChangeApprovalField->getWhere("entityChangeApprovalId=$entityChangeApprovalId");
            
            $dbUser = new BaseModel($this->db, "vwEntityUserProfile");
            $dbUser->getWhere("userId=$dbEntityChangeApproval->userId");

            $dbEntity = new BaseModel($this->db, "entity");
            $dbEntity->getByField("id", $dbUser->entityId);
            
            $dbEntityBranch = new BaseModel($this->db, "entityBranch");
            $dbEntityBranch->getByField("id", $dbUser->entityBranchId);

            $mapDisplayNameOldNewValue = [];
            while(!$dbEntityChangeApprovalField->dry()) {
                $allParts = explode(".", $dbEntityChangeApprovalField->fieldName);
                $table = $allParts[0];
                $name = end($allParts);

                // Update information
                if($table == "entity") {
                    $dbEntity[$name] = $dbEntityChangeApprovalField->newValue;
                } else if($table == "entityBranch") {
                    $dbEntityBranch[$name] = $dbEntityChangeApprovalField->newValue;
                }

                // Fill map used to display data in the mail approval
                if($name == "name_en") {
                    $displayName = "Distributor Name";
                } else if($name == "tradeLicenseNumber") {
                    $displayName = "Trade License Number";
                }

                if($displayName) {
                    $mapDisplayNameOldNewValue[$displayName] = [
                        $dbEntityChangeApprovalField->oldValue,
                        $dbEntityChangeApprovalField->newValue
                    ];
                }

                $dbEntityChangeApprovalField->next();
            }

            $dbEntity->update();
            $dbEntityBranch->update();

            $this->sendChangeApprovedEmail($entityChangeApprovalId, $mapDisplayNameOldNewValue, $dbEntityChangeApproval->tradeLicenseUrl);
            echo "Approved";
        }
    }

    function sendChangeApprovedEmail($entityChangeApprovalId, $mapDisplayNameOldNewValue, $tradeLicenseUrl)
    {
        $emailHandler = new EmailHandler($this->db);
        $emailFile = "email/layout.php";
        $this->f3->set('domainUrl', getenv('DOMAIN_URL'));
        $this->f3->set('title', 'Change Profile Approved');
        $this->f3->set('emailType', 'changeProfileApproved');
        
        $this->f3->set('mapDisplayNameOldNewValue', $mapDisplayNameOldNewValue);
        $this->f3->set('tradeLicenseUrl', $tradeLicenseUrl);

        $htmlContent = View::instance()->render($emailFile);

        $subject = "Aumet - Change Profile Approved";
        if (getenv('ENV') != Constants::ENV_PROD) {
            $subject .= " - (Test: " . getenv('ENV') . ")";

            if (getenv('ENV') == Constants::ENV_LOC) {
                $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
            }
        }
        $emailHandler->sendEmail(Constants::EMAIL_CHANGE_PROFILE_APPROVED, $subject, $htmlContent);
    }
}