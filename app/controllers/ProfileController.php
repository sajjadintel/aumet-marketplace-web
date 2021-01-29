<?php

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

        $newFileName = $fileName . "-" . time() . ".$ext";
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

    function postPharmacyProfileMyProfile()
    {
        $userId = $this->f3->get("POST.userId");
        $entityName = $this->f3->get("POST.entityName");
        $tradeLicenseNumber = $this->f3->get("POST.tradeLicenseNumber");
        $address = $this->f3->get("POST.address");
        $entityDocument = $this->f3->get("POST.entityDocument");

        $this->checkLength($entityName, 'entityName', 100, 4);
        $this->checkLength($address, 'address', 500, 4);
        if($tradeLicenseNumber) {
            $this->checkLength($tradeLicenseNumber, 'tradeLicenseNumber', 200, 4);
        }

        if(!$entityName || !$address) {
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
            // Update entity
            $dbEntity = new BaseModel($this->db, "entity");
            $dbEntity->getByField("id", $dbUser->entityId);
            $dbEntity->name_ar = $entityName;
            $dbEntity->name_en = $entityName;
            $dbEntity->name_fr = $entityName;
            $dbEntity->update();

            // Update entity branch
            $dbEntityBranch = new BaseModel($this->db, "entityBranch");
            $dbEntityBranch->getByField("id", $dbUser->entityBranchId);
            $dbEntityBranch->name_ar = $entityName;
            $dbEntityBranch->name_en = $entityName;
            $dbEntityBranch->name_fr = $entityName;
            $dbEntityBranch->address_ar = $address;
            $dbEntityBranch->address_en = $address;
            $dbEntityBranch->address_fr = $address;
            $dbEntityBranch->tradeLicenseNumber = $tradeLicenseNumber;
            $dbEntityBranch->tradeLicenseUrl = $entityDocument;
            $dbEntityBranch->update();

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->message = $this->f3->get("vModule_profile_myProfileSaved");
            echo $this->webResponse->jsonResponse();
        }
    }

    function postPharmacyProfileAccountSetting()
    {
        $userId = $this->f3->get("POST.userId");
        $oldPassword = $this->f3->get("POST.oldPassword");
        $newPassword = $this->f3->get("POST.newPassword");

        if(!$oldPassword || !$newPassword) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = $this->f3->get("vModule_profile_missingFields");
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
        if($tradeLicenseNumber) {
            $this->checkLength($tradeLicenseNumber, 'tradeLicenseNumber', 200, 4);
        }

        if(!$entityName || !$address) {
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
            // Update entity
            $dbEntity = new BaseModel($this->db, "entity");
            $dbEntity->getByField("id", $dbUser->entityId);
            $dbEntity->name_ar = $entityName;
            $dbEntity->name_en = $entityName;
            $dbEntity->name_fr = $entityName;
            $dbEntity->update();

            // Update entity branch
            $dbEntityBranch = new BaseModel($this->db, "entityBranch");
            $dbEntityBranch->getByField("id", $dbUser->entityBranchId);
            $dbEntityBranch->name_ar = $entityName;
            $dbEntityBranch->name_en = $entityName;
            $dbEntityBranch->name_fr = $entityName;
            $dbEntityBranch->address_ar = $address;
            $dbEntityBranch->address_en = $address;
            $dbEntityBranch->address_fr = $address;
            $dbEntityBranch->tradeLicenseNumber = $tradeLicenseNumber;
            $dbEntityBranch->tradeLicenseUrl = $entityDocument;
            $dbEntityBranch->update();

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->message = $this->f3->get("vModule_profile_myProfileSaved");
            echo $this->webResponse->jsonResponse();
        }
    }

    function postDistributorProfileAccountSetting()
    {
        $userId = $this->f3->get("POST.userId");
        $oldPassword = $this->f3->get("POST.oldPassword");
        $newPassword = $this->f3->get("POST.newPassword");

        if(!$oldPassword || !$newPassword) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = $this->f3->get("vModule_profile_missingFields");
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
}