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

            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $this->f3->set('arrEntityId', $arrEntityId);

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

        if (is_array($datatable->query)) {
            $buyerName = $datatable->query['buyerName'];
            if (isset($buyerName) && is_array($buyerName)) {
                $query .= " AND (";
                foreach ($buyerName as $key => $value) {
                    $value = addslashes($value);
                    if ($key !== 0) {
                        $query .= " OR ";
                    }
                    $query .= "buyerName_en LIKE '%{$value}%' OR buyerName_ar LIKE '%{$value}%' OR buyerName_fr LIKE '%{$value}%'";
                }
                $query .= ")";
            }

            $countryId = $datatable->query['countryId'];
            if (isset($countryId) && is_array($countryId)) {
                $query .= " AND ( buyerCountryId in (" . implode(",", $countryId) . ") OR buyerCityId in (" . implode(",", $countryId) . ") )";
            }
        }

        $dbData = new EntityRelationView;
        $dbData->buyerName = "buyerName_" . $this->objUser->language;
        $dbData->sellerName = "sellerName_" . $this->objUser->language;
        $dbData->relationGroupName = "relationGroupName_" . $this->objUser->language;
        $dbData->buyerCountryName = "buyerCountryName_" . $this->objUser->language;
        $dbData->buyerCityName = "buyerCityName_" . $this->objUser->language;
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
                $this->checkLength($relationGroupId, 'relationGroupName', 150);

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
                $emailFile = "email/layout.php";
                $this->f3->set('domainUrl', getenv('DOMAIN_URL'));
                $this->f3->set('title', 'New Bonuses');
                $this->f3->set('emailType', 'newBonuses');
                
                $message = "You now have access to special prices and bonuses with " . $dbEntity->name_en;
                $this->f3->set('message', $message);
    
                $htmlContent = View::instance()->render($emailFile);
                
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

                $emailHandler->sendEmail(Constants::EMAIL_NEW_CUSTOMER_GROUP, $subject, $htmlContent);

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS_SHOW_DIALOG;
                $this->webResponse->title = "";
                $this->webResponse->message = $this->f3->get('vModule_customerEdited');
                echo $this->webResponse->jsonResponse();
            }
        }
    }

    public function postEntityCustomersIdentifier()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/distributor/customer");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $entityRelationId = $this->f3->get('POST.id');

            $dbEntityRelation = new EntityRelation;
            $entityId = EntityUserProfileView::getEntityIdFromUser($this->objUser->id);
            $entityRelation = $dbEntityRelation->findone(['id = ? AND entitySellerId = ?', $entityRelationId, $entityId]);

            if ($dbEntityRelation === false) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->title = "";
                $this->webResponse->message = "No Customer";
                echo $this->webResponse->jsonResponse();
                return;
            }

            $entityRelation = $entityRelation->saveIdentifier($this->f3->get('POST.customerIdentifier'));
            if ($entityRelation->hasErrors) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->title = "";
                $this->webResponse->message = implode("\n", array_values($entityRelation->errors));
                echo $this->webResponse->jsonResponse();
                return;
            }

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS_SHOW_DIALOG;
            $this->webResponse->title = "";
            $this->webResponse->message = $this->f3->get('vModule_customerEdited');
            echo $this->webResponse->jsonResponse();
        }
    }

    function getEntityCustomerGroup()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vModule_customerGroup_title');
            $this->webResponse->data = View::instance()->render('app/entity/customer-group/customer-group.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function postEntityCustomerGroup()
    {
        ## Read values from Datatables
        $datatable = new Datatable($_POST);

        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));

        $dbData = new EntityRelationGroup($this->db, $this->objUser->language);
        $data = $dbData->getDetailedData($arrEntityId, "$datatable->sortBy $datatable->sortByOrder", $datatable->limit, $datatable->offset);

        $totalRecords = count($data);
        $totalFiltered = count($data);

        ## Response
        $response = array(
            "draw" => intval($datatable->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        );

        $this->jsonResponseAPI($response);
    }

    function getEntityCustomerGroupDetails()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", $this->f3->get('SERVER.REQUEST_URI'));
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $customerGroupId = $this->f3->get('PARAMS.customerGroupId');
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));

            // Get actual relation group
            $customerGroup = new stdClass();
            $customerGroup->id = $customerGroupId;
            $data['customerGroup'] = $customerGroup;

            // Get all relation groups
            $dbEntityRelationGroup = new BaseModel($this->db, "entityRelationGroup");
            $dbEntityRelationGroup->name = "name_" . $this->objUser->language;
            $arrRelationGroup = $dbEntityRelationGroup->getWhere("entityId IN ($arrEntityId)");
            $arrCustomerGroup = [];
            $mapCustomerGroupIdArrEntity = [];
            foreach($arrRelationGroup as $relationGroup) {
                $customerGroup = new stdClass();
                $customerGroup->id = $relationGroup['id'];
                $customerGroup->name = $relationGroup['name'];

                array_push($arrCustomerGroup, $customerGroup);
                $mapCustomerGroupIdArrEntity[$relationGroup['id']] = [];
            }
            $data['arrCustomerGroup'] = $arrCustomerGroup;            

            // Get all entities in each group and entities without any group
            $dbEntityRelation = new BaseModel($this->db, "vwEntityRelation");
            $arrEntityRelation = $dbEntityRelation->getWhere("entitySellerId IN ($arrEntityId)");

            $arrEntityFree = [];
            foreach($arrEntityRelation as $entityRelation) {
                $relationGroupId = $entityRelation['relationGroupId'];
                    
                $entity = new stdClass();
                $entity->id = $entityRelation['entityBuyerId'];
                $nameField = "buyerName_" . $this->objUser->language;
                $entity->name = $entityRelation[$nameField];

                if(strlen($relationGroupId) == 0) {
                    array_push($arrEntityFree, $entity);
                } else {
                    $arrEntity = $mapCustomerGroupIdArrEntity[$relationGroupId];
                    array_push($arrEntity, $entity);
                    $mapCustomerGroupIdArrEntity[$relationGroupId] = $arrEntity;
                }
            }
            $data['mapCustomerGroupIdArrEntity'] = $mapCustomerGroupIdArrEntity;
            $data['arrEntityFree'] = $arrEntityFree;

            echo $this->webResponse->jsonResponseV2(1, "", "", $data);
            return;
        }
    }

    function postEntityCustomerGroupEdit()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/distributor/customer");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $customerGroupId = $this->f3->get('POST.id');

            $dbEntityRelationGroup = new BaseModel($this->db, "entityRelationGroup");
            $dbEntityRelationGroup->getWhere("id=$customerGroupId");

            if ($dbEntityRelationGroup->dry()) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->message = $this->f3->get('vModule_customerGroup_noCustomerGroup');
                echo $this->webResponse->jsonResponse();
            } else {
                $arrEntityId = $this->f3->get('POST.arrEntityId');
                $sellerId = $dbEntityRelationGroup['entityId'];

                $dbEntity = new BaseModel($this->db, "entity");
                $dbEntity->getWhere("id=$sellerId");

                $emailHandler = new EmailHandler($this->db);
                $emailFile = "email/layout.php";
                $this->f3->set('domainUrl', getenv('DOMAIN_URL'));
                $this->f3->set('title', 'New Bonuses');
                $this->f3->set('emailType', 'newBonuses');
                
                $message = "You now have access to special prices and bonuses with " . $dbEntity->name_en;
                $this->f3->set('message', $message);
                
                $htmlContent = View::instance()->render($emailFile);

                $dbEntityRelation = new BaseModel($this->db, "entityRelation");

                if(!$arrEntityId || count($arrEntityId) == 0) {
                    $dbEntityRelation->getWhere("relationGroupId=$customerGroupId AND entitySellerId=$sellerId");
                    while(!$dbEntityRelation->dry()) {
                        $dbEntityRelation->relationGroupId = null;
                        $dbEntityRelation->update();
                        $dbEntityRelation->next();
                    }
                } else {
                    $arrEntityIdStr = implode(",", $arrEntityId);
                    $dbEntityRelation->getWhere("relationGroupId=$customerGroupId AND entityBuyerId NOT IN ($arrEntityIdStr) AND entitySellerId=$sellerId");
                    while(!$dbEntityRelation->dry()) {
                        $dbEntityRelation->relationGroupId = null;
                        $dbEntityRelation->update();
                        $dbEntityRelation->next();
                    }
                    
                    foreach($arrEntityId as $entityId) {
                        $dbEntityRelation->getWhere("entityBuyerId=$entityId AND entitySellerId=$sellerId");
                        if($dbEntityRelation->relationGroupId == $customerGroupId) {
                            continue;
                        }
                        $emailHandler->resetTos();
    
                        $dbEntityRelation->relationGroupId = $customerGroupId;
                        $dbEntityRelation->update();
    
                        $dbEntityUserProfile = new BaseModel($this->db, "vwEntityUserProfile");
                        $arrEntityUserProfile = $dbEntityUserProfile->findWhere("entityId=$entityId");
    
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
    
                        $emailHandler->sendEmail(Constants::EMAIL_NEW_CUSTOMER_GROUP, $subject, $htmlContent);
                    }
                }
            

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
                $this->webResponse->data = $this->f3->get('vModule_customerGroup_saved');
                echo $this->webResponse->jsonResponse();
            }
        }
    }
}
