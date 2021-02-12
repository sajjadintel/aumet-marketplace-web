<?php

use Ahc\Jwt\JWT;

class ReviewController extends Controller
{
    function beforeroute()
    {
    }

    function getReviewPharmacyProfileApprove()
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
        } else if ($dbEntityChangeApproval->isApproved) {
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
            while (!$dbEntityChangeApprovalField->dry()) {
                $allParts = explode(".", $dbEntityChangeApprovalField->fieldName);
                $table = $allParts[0];
                $name = end($allParts);

                // Update information
                if ($table == "entity") {
                    $dbEntity[$name] = $dbEntityChangeApprovalField->newValue;
                } else if ($table == "entityBranch") {
                    $dbEntityBranch[$name] = $dbEntityChangeApprovalField->newValue;
                }

                // Fill map used to display data in the mail approval
                if ($name == "name_en") {
                    $displayName = "Pharmacy Name";
                } else if ($name == "tradeLicenseNumber") {
                    $displayName = "Trade License Number";
                } else {
                    $displayName = "";
                }

                if ($displayName) {
                    $mapDisplayNameOldNewValue[$displayName] = [
                        $dbEntityChangeApprovalField->oldValue,
                        $dbEntityChangeApprovalField->newValue
                    ];
                }

                if($name == "tradeLicenseUrl") {
                    $oldTradeLicenseUrl = $dbEntityChangeApprovalField->oldValue;
                }

                $dbEntityChangeApprovalField->next();
            }

            $tradeLicenseUrl = $dbEntityChangeApproval->tradeLicenseUrl;
            if(strlen($oldTradeLicenseUrl) == 0) {
                $oldTradeLicenseUrl = $tradeLicenseUrl;
            }

            $dbEntity->update();
            $dbEntityBranch->update();

            $this->sendChangeApprovedEmail($entityChangeApprovalId, $mapDisplayNameOldNewValue, $oldTradeLicenseUrl, $tradeLicenseUrl);
            echo "Approved";
        }
    }

    function getReviewDistributorProfileApprove()
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
        } else if ($dbEntityChangeApproval->isApproved) {
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
            while (!$dbEntityChangeApprovalField->dry()) {
                $allParts = explode(".", $dbEntityChangeApprovalField->fieldName);
                $table = $allParts[0];
                $name = end($allParts);

                // Update information
                if ($table == "entity") {
                    $dbEntity[$name] = $dbEntityChangeApprovalField->newValue;
                } else if ($table == "entityBranch") {
                    $dbEntityBranch[$name] = $dbEntityChangeApprovalField->newValue;
                }

                // Fill map used to display data in the mail approval
                if ($name == "name_en") {
                    $displayName = "Distributor Name";
                } else if ($name == "tradeLicenseNumber") {
                    $displayName = "Trade License Number";
                } else {
                    $displayName = "";
                }

                if ($displayName) {
                    $mapDisplayNameOldNewValue[$displayName] = [
                        $dbEntityChangeApprovalField->oldValue,
                        $dbEntityChangeApprovalField->newValue
                    ];
                }

                if($name == "tradeLicenseUrl") {
                    $oldTradeLicenseUrl = $dbEntityChangeApprovalField->oldValue;
                }

                $dbEntityChangeApprovalField->next();
            }

            $tradeLicenseUrl = $dbEntityChangeApproval->tradeLicenseUrl;
            if(strlen($oldTradeLicenseUrl) == 0) {
                $oldTradeLicenseUrl = $tradeLicenseUrl;
            }

            $dbEntity->update();
            $dbEntityBranch->update();

            $this->sendChangeApprovedEmail($entityChangeApprovalId, $mapDisplayNameOldNewValue, $oldTradeLicenseUrl, $tradeLicenseUrl);
            echo "Approved";
        }
    }

    function sendChangeApprovedEmail($entityChangeApprovalId, $mapDisplayNameOldNewValue, $oldTradeLicenseUrl, $tradeLicenseUrl)
    {
        $emailHandler = new EmailHandler($this->db);
        $emailFile = "email/layout.php";
        $this->f3->set('domainUrl', getenv('DOMAIN_URL'));
        $this->f3->set('title', 'Change Profile Approved');
        $this->f3->set('emailType', 'changeProfileApproved');

        $this->f3->set('mapDisplayNameOldNewValue', $mapDisplayNameOldNewValue);
        if($oldTradeLicenseUrl != $tradeLicenseUrl) {
            $this->f3->set('oldTradeLicenseUrl', $oldTradeLicenseUrl);
        }
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