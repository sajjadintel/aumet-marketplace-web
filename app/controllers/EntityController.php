<?php

class EntityController extends Controller
{
    function getEntityCustomers()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $dbStockStatus = new BaseModel($this->db, "stockStatus");
            $dbStockStatus->name = "name_" . $this->objUser->language;
            $arrStockStatus = $dbStockStatus->all("id asc");
            $this->f3->set('arrStockStatus', $arrStockStatus);

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vModule_customer_title');
            $this->webResponse->data = View::instance()->render('app/entity/customers/customers.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function getEntityCustomerDetails()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", $this->f3->get('SERVER.REQUEST_URI'));
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $customerId = $this->f3->get('PARAMS.customerId');

            $dbRelation = new BaseModel($this->db, "vwEntityRelation");
            $dbRelation->relationGroupName = "relationGroupName_" . $this->objUser->language;
            $relation = $dbRelation->findWhere("id = '$customerId'")[0];

            $data['customer'] = $relation;
            
            $dbEntityRelationGroup = new BaseModel($this->db, "entityRelationGroup");
            $dbEntityRelationGroup->name = "name_" . $this->objUser->language;
            $arrRelationGroup = $dbEntityRelationGroup->findWhere("entityId=".$relation['entitySellerId']);
            $data['arrRelationGroup'] = $arrRelationGroup;

            echo $this->webResponse->jsonResponseV2(1, "", "", $data);
            return;
        }
    }

    function getEntityCustomerRelationDetails()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", $this->f3->get('SERVER.REQUEST_URI'));
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $entityBuyerId = $this->f3->get('PARAMS.entityBuyerId');
            $entitySellerId = $this->f3->get('PARAMS.entitySellerId');

            $dbRelation = new BaseModel($this->db, "vwEntityRelation");
            $dbRelation->relationGroupName = "relationGroupName_" . $this->objUser->language;
            $relation = $dbRelation->findWhere("entityBuyerId = $entityBuyerId AND entitySellerId = $entitySellerId")[0];

            $data['customer'] = $relation;
            
            $dbEntityRelationGroup = new BaseModel($this->db, "entityRelationGroup");
            $dbEntityRelationGroup->name = "name_" . $this->objUser->language;
            $arrRelationGroup = $dbEntityRelationGroup->findWhere("entityId=".$relation['entitySellerId']);
            $data['arrRelationGroup'] = $arrRelationGroup;

            echo $this->webResponse->jsonResponseV2(1, "", "", $data);
            return;
        }
    }

    function postEntityCustomers()
    {
        ## Read values from Datatables
        $datatable = new Datatable($_POST);
        $query = "";

        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
        $query = "entitySellerId IN ($arrEntityId)";

        $fullQuery = $query;

        $dbData = new BaseModel($this->db, "vwEntityRelation");
        $dbData->buyerName = "buyerName_" . $this->objUser->language;
        $dbData->sellerName = "sellerName_" . $this->objUser->language;
        $dbData->relationGroupName = "relationGroupName_" . $this->objUser->language;
        $data = [];

        $totalRecords = $dbData->count($fullQuery);
        $totalFiltered = $dbData->count($query);
        $data = $dbData->findWhere($query, "$datatable->sortBy $datatable->sortByOrder", $datatable->limit, $datatable->offset);

        ## Response
        $response = array(
            "draw" => intval($datatable->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        );

        $this->jsonResponseAPI($response);
    }

    function getEntityUsers()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {

            $dbStockStatus = new BaseModel($this->db, "stockStatus");
            $dbStockStatus->name = "name_" . $this->objUser->language;
            $arrStockStatus = $dbStockStatus->all("id asc");
            $this->f3->set('arrStockStatus', $arrStockStatus);

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vModule_users_title');
            $this->webResponse->data = View::instance()->render('app/users/list.php');
            echo $this->webResponse->jsonResponse();
        }
    }
    
    function postEntityCustomersEditGroup()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/distributor/customer");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $entityRelationId = $this->f3->get('POST.id');

            $dbEntityRelation = new BaseModel($this->db, "entityRelation");
            $dbEntityRelation->getWhere("id=$entityRelationId");

            if ($dbEntityRelation->dry()) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->title = "";
                $this->webResponse->message = "No Customer";
                echo $this->webResponse->jsonResponse();
            } else {
                $relationGroupId = $this->f3->get('POST.relationGroupId');
                $this->checkLength($relationGroupId, 'customerGroupName', 150);

                if($relationGroupId) {
                    $dbEntityRelationGroup = new BaseModel($this->db, "entityRelationGroup");
                    $dbEntityRelationGroup->getWhere(["id = ? AND entityId = ?", $relationGroupId, $dbEntityRelation->entitySellerId]);
                    if($dbEntityRelationGroup->dry()) {
                        $dbEntityRelationGroup->entityId = $dbEntityRelation->entitySellerId;
                        $dbEntityRelationGroup->name_en = $relationGroupId;
                        $dbEntityRelationGroup->name_fr = $relationGroupId;
                        $dbEntityRelationGroup->name_ar = $relationGroupId;
                        $dbEntityRelationGroup->addReturnID();
                    }
                    $dbEntityRelation->relationGroupId = $dbEntityRelationGroup->id;
                } else {
                    $dbEntityRelation->relationGroupId = null;
                }
                $dbEntityRelation->update();

                $dbEntity = new BaseModel($this->db, "entity");
                $dbEntity->getWhere("id = $dbEntityRelation->entitySellerId");

                $emailHandler = new EmailHandler($this->db);
                $message = "You now have access to special prices and bonuses with " . $dbEntity->name_en;
                
                $dbEntityUserProfile = new BaseModel($this->db, "vwEntityUserProfile");
                $arrEntityUserProfile = $dbEntityUserProfile->findWhere("entityId = $dbEntityRelation->entityBuyerId");

                foreach($arrEntityUserProfile as $userProfile) {
                    $emailHandler->appendToAddress($userProfile['userEmail'], $userProfile['userFullName']);
                }
                $subject = "Aumet - New Bonuses";
                if (getenv('ENV') != Constants::ENV_PROD) {
                    $subject .= " - (Test: " . getenv('ENV') . ")";

                    if (getenv('ENV') == Constants::ENV_LOC) {
                        $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                        $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
                    }
                }

                $emailHandler->sendEmail(Constants::EMAIL_NEW_CUSTOMER_GROUP, $subject, $message);

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS_SHOW_DIALOG;
                $this->webResponse->title = "";
                $this->webResponse->message = $this->f3->get('vModule_customerEdited');
                echo $this->webResponse->jsonResponse();
            }
        }
    }
}
