<?php

class ProfileController extends Controller {

    function getProfile()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/profile");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            // Get logged in user
            $dbUser = new BaseModel($this->db, "vwEntityUserProfile");
            $dbUser->entityName = "entityName_" . $this->objUser->language;
            $dbUser->entityCountryName = "entityCountryName_" . $this->objUser->language;
            $dbUser->entityBranchCityName = "entityBranchCityName_" . $this->objUser->language;
            $dbUser->entityBranchAddress = "entityBranchAddress_" . $this->objUser->language;
            $user = $dbUser->getWhere("userId=".$this->objUser->id)[0];
            $this->f3->set('user', $user);

            // Get all countries
            $dbCountry = new BaseModel($this->db, "country");
            $dbCountry->name = "name_" . $this->objUser->language;
            $arrCountry = $dbCountry->findAll();
            $this->f3->set('arrCountry', $arrCountry);

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vTitle_profile');
            $this->webResponse->data = View::instance()->render('app/profile/home.php');
            echo $this->webResponse->jsonResponse();
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

    function postProfileMyProfile()
    {
        $userId = $this->f3->get("POST.userId");
        $entityName = $this->f3->get("POST.entityName");
        $tradeLicenseNumber = $this->f3->get("POST.tradeLicenseNumber");
        $countryId = $this->f3->get("POST.country");
        $cityId = $this->f3->get("POST.city");
        $address = $this->f3->get("POST.address");
        $pharmacyDocument = $this->f3->get("POST.pharmacyDocument");

        // Check if user exists
        $dbUser = new BaseModel($this->db, "vwEntityUserProfile");
        $dbUser->getWhere("userId=$userId");

        if ($dbUser->dry()) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = $this->f3->get("vModule_profile_userNotFound");
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

            // Update entity
            $dbEntity = new BaseModel($this->db, "entity");
            $dbEntity->getByField("id", $dbUser->entityId);
            $dbEntity->name_ar = $entityName;
            $dbEntity->name_en = $entityName;
            $dbEntity->name_fr = $entityName;
            $dbEntity->countryId = $countryId;
            $dbEntity->currencyId = $currencyId;
            $dbEntity->update();

            // Update entity branch
            $dbEntityBranch = new BaseModel($this->db, "entityBranch");
            $dbEntityBranch->getByField("id", $dbUser->entityBranchId);
            $dbEntityBranch->name_ar = $entityName;
            $dbEntityBranch->name_en = $entityName;
            $dbEntityBranch->name_fr = $entityName;
            $dbEntityBranch->cityId = $cityId;
            $dbEntityBranch->address_ar = $address;
            $dbEntityBranch->address_en = $address;
            $dbEntityBranch->address_fr = $address;
            $dbEntityBranch->tradeLicenseNumber = $tradeLicenseNumber;
            $dbEntityBranch->tradeLicenseUrl = $pharmacyDocument;
            $dbEntityBranch->update();

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->message = $this->f3->get("vModule_profile_myProfileSaved");
            echo $this->webResponse->jsonResponse();
        }
    }

    function postProfileAccountSetting()
    {
        $userId = $this->f3->get("POST.userId");
        $email = $this->f3->get("POST.email");
        $mobile = $this->f3->get("POST.mobile");
        $oldPassword = $this->f3->get("POST.oldPassword");
        $newPassword = $this->f3->get("POST.newPassword");
        
        // Check if user exists
        $dbUser = new BaseModel($this->db, "user");
        $dbUser->getWhere("id=$userId");
        if ($dbUser->dry()) {
            $this->webResponse->errorCode = Constants::STATUS_ERROR;
            $this->webResponse->message = $this->f3->get("vModule_profile_userNotFound");
            echo $this->webResponse->jsonResponse();
        } else {
            // Update user password if oldPassword provided
            if($oldPassword) {
                if(!password_verify($oldPassword, $dbUser->password)) {
                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->message = $this->f3->get("vModule_profile_wrongPassword");
                    echo $this->webResponse->jsonResponse();
                    return;
                } else {
                    $dbUser->password = password_hash($newPassword, PASSWORD_DEFAULT);
                }
            }
                
            // Update user
            $dbUser->email = $email;
            $dbUser->mobile = $mobile;
            $dbUser->update();

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->message = $this->f3->get("vModule_profile_accountSettingSaved");
            echo $this->webResponse->jsonResponse();
        }
    }
}