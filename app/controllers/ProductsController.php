<?php

class ProductsController extends Controller {

    function getEntityProduct()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", $this->f3->get('SERVER.REQUEST_URI'));
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $entityId = $this->f3->get('PARAMS.entityId');
            $productId = $this->f3->get('PARAMS.productId');

            $roleId = $this->f3->get('SESSION.objUser')->roleId;

            if ($entityId == 0)
                $query = "productId=$productId";
            else
                $query = "entityId=$entityId and productId=$productId";

            $dbEntityProduct = new BaseModel($this->db, "vwEntityProductSell");
            $dbEntityProduct->productName = "productName_" . $this->objUser->language;
            $dbEntityProduct->entityName = "entityName_" . $this->objUser->language;
            $dbEntityProduct->madeInCountryName = "madeInCountryName_" . $this->objUser->language;
            $dbEntityProduct->getWhere($query);

            $dbCartDetail = new BaseModel($this->db, "cartDetail");
            $arrCartDetail = $dbCartDetail->getByField("accountId", $this->objUser->accountId);


            if ($dbEntityProduct['bonusTypeId'] == 2) {
                $dbEntityProduct['bonusOptions'] = json_decode($dbEntityProduct->bonusConfig);
            }

            $dbEntityProduct['cart'] = 0;
            if (is_array($arrCartDetail) || is_object($arrCartDetail)) {
                foreach ($arrCartDetail as $objCartItem) {
                    if ($objCartItem['entityProductId'] == $dbEntityProduct['id']) {
                        $dbEntityProduct['cart'] += $objCartItem['quantity'];
                        $dbEntityProduct['cart'] += $objCartItem['quantityFree'];
                    }
                }
            }

            $found = false;
            if (array_key_exists((string)$dbEntityProduct->entityId, $this->f3->get('SESSION.arrEntities'))) {
                $found = true;
            }

            if (($this->objUser->menuId == Constants::MENU_PHARMACY && $dbEntityProduct['statusId'] === 0) || (!$found && $this->objUser->menuId == Constants::MENU_DISTRIBUTOR)) {
                $this->webResponse->errorCode = Constants::STATUS_CODE_REDIRECT_TO_WEB;
                echo $this->webResponse->jsonResponse();
                return;
            }

            $this->f3->set('objEntityProduct', $dbEntityProduct);

            $dbEntityProductRelated = new BaseModel($this->db, "vwEntityProductSell");
            $dbEntityProductRelated->productName = "productName_" . $this->objUser->language;
            $dbEntityProductRelated->entityName = "entityName_" . $this->objUser->language;
            $where = "stockStatusId=1 AND scientificNameId =$dbEntityProduct->scientificNameId AND id != $dbEntityProduct->id";
            if (Helper::isDistributor($roleId))
                $where .= " AND entityId=$dbEntityProduct->entityId";

            $arrRelatedEntityProduct = $dbEntityProductRelated->getWhere($where, 'id', 12);
            $this->f3->set('arrRelatedEntityProduct', $arrRelatedEntityProduct);


            $dbEntityProductFromThisDistributor = new BaseModel($this->db, "vwEntityProductSell");
            $dbEntityProductFromThisDistributor->productName = "productName_" . $this->objUser->language;
            $dbEntityProductFromThisDistributor->entityName = "entityName_" . $this->objUser->language;
            $dbEntityProductFromThisDistributor = $dbEntityProductFromThisDistributor->getWhere("stockStatusId=1 and entityId=$dbEntityProduct->entityId and id != $dbEntityProduct->id", 'id', 12);
            $this->f3->set('arrProductFromThisDistributor', $dbEntityProductFromThisDistributor);

            if (Helper::isPharmacy($roleId)) {
                $dbEntityProductOtherOffers = new BaseModel($this->db, "vwEntityProductSell");
                $dbEntityProductOtherOffers->productName = "productName_" . $this->objUser->language;
                $dbEntityProductOtherOffers->entityName = "entityName_" . $this->objUser->language;

                $where = "( TRIM(productName_ar) LIKE '" . trim($dbEntityProduct->productName_ar) . "'";
                $where .= " OR TRIM(productName_en) LIKE '" . mb_strtolower(trim($dbEntityProduct->productName_en)) . "'";
                $where .= " OR TRIM(productName_fr) LIKE '" . mb_strtolower(trim($dbEntityProduct->productName_fr)) . "' ) ";
                $where .= " AND id != $dbEntityProduct->id";

                $dbEntityProductOtherOffers = $dbEntityProductOtherOffers->findWhere($where);


                $allProductId = [];
                foreach ($dbEntityProductOtherOffers as $product) {
                    array_push($allProductId, $product['id']);
                }
                $allProductId = implode(",", $allProductId);

                $dbCartDetail = new BaseModel($this->db, "cartDetail");
                $arrCartDetail = $dbCartDetail->getByField("accountId", $this->objUser->accountId);

                if ($allProductId != null) {
                    $dbBonus = new BaseModel($this->db, "entityProductSellBonusDetail");
                    $arrBonus = $dbBonus->findWhere("entityProductId IN ($allProductId) AND isActive = 1");

                    $mapProductIdBonuses = [];

                    foreach ($arrBonus as $bonus) {
                        $productId = $bonus['entityProductId'];
                        $allBonuses = [];
                        if (array_key_exists($productId, $mapProductIdBonuses)) {
                            $allBonuses = $mapProductIdBonuses[$productId];
                        }
                        array_push($allBonuses, $bonus);
                        $mapProductIdBonuses[$productId] = $allBonuses;
                    }
                }


                for ($i = 0; $i < count($dbEntityProductOtherOffers); $i++) {
                    if ($dbEntityProductOtherOffers[$i]['bonusTypeId'] == 2) {
                        $dbEntityProductOtherOffers[$i]['bonusOptions'] = json_decode($dbEntityProductOtherOffers[$i]['bonusConfig']);
                        $dbEntityProductOtherOffers[$i]['bonusConfig'] = $dbEntityProductOtherOffers[$i]['bonusOptions'];
                        $dbEntityProductOtherOffers[$i]['bonuses'] = $mapProductIdBonuses[$dbEntityProductOtherOffers[$i]['id']];
                    }
                }

                $this->f3->set('arrProductOtherOffers', $dbEntityProductOtherOffers);
            }

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vTitle_entityProductDetail');
            $this->webResponse->data = View::instance()->render('app/products/single/entityProduct.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function getDistributorProducts()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $dbStockStatus = new BaseModel($this->db, "stockStatus");
            $dbStockStatus->name = "name_" . $this->objUser->language;
            $arrStockStatus = $dbStockStatus->findAll("id asc");
            $this->f3->set('arrStockStatus', $arrStockStatus);

            $dbScientificName = new BaseModel($this->db, "scientificName");
            $arrScientificName = $dbScientificName->findAll();

            // Find buyer currency
            $dbCurrencies = new BaseModel($this->db, "currency");
            $allCurrencies = $dbCurrencies->all();

            $mapCurrencyIdCurrency = [];
            foreach ($allCurrencies as $currency) {
                $mapCurrencyIdCurrency[$currency['id']] = $currency;
            }

            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));

            $dbEntities = new BaseModel($this->db, "entity");
            $buyerEntity = $dbEntities->getWhere("id in ($arrEntityId)")[0];

            $buyerCurrency = $mapCurrencyIdCurrency[$buyerEntity['currencyId']];
            $this->f3->set('buyerCurrency', $buyerCurrency['symbol']);

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vModule_product_title');
            $this->webResponse->data = View::instance()->render('app/products/distributor/products.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function getProductDetails()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", $this->f3->get('SERVER.REQUEST_URI'));
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $productId = $this->f3->get('PARAMS.productId');

            $dbProduct = new BaseModel($this->db, "vwEntityProductSell");
            $arrProduct = $dbProduct->findWhere("productId = $productId");

            $dbProductIngredient = new BaseModel($this->db, "vwProductIngredient");
            $arrActiveIngredients = $dbProductIngredient->findWhere("productId = $productId");

            $data['product'] = $arrProduct[0];
            $data['activeIngredients'] = $arrActiveIngredients;

            echo $this->webResponse->jsonResponseV2(1, "", "", $data);
        }
    }

    function getProductQuantityDetails()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", $this->f3->get('SERVER.REQUEST_URI'));
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $productId = $this->f3->get('PARAMS.productId');

            $dbProduct = new BaseModel($this->db, "vwEntityProductSell");
            $arrProduct = $dbProduct->findWhere("productId = '$productId'");

            $dbBonus = new BaseModel($this->db, "entityProductSellBonusDetail");
            $dbBonus->bonusId = 'id';
            $arrBonus = $dbBonus->findWhere("entityProductId = '$productId' AND isActive = 1");

            $data['product'] = $arrProduct[0];
            $data['bonus'] = $arrBonus;

            echo $this->webResponse->jsonResponseV2(1, "", "", $data);
            return;
        }
    }

    function postDistributorProducts()
    {
        ## Read values from Datatables
        $datatable = new Datatable($_POST);
        $query = "1=1 ";

        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
        $query = "entityId IN ($arrEntityId)";

        if (is_array($datatable->query)) {
            $productId = $datatable->query['productId'];
            if (isset($productId) && is_array($productId)) {
                $query .= " AND id in (" . implode(",", $productId) . ")";
            }

            $scientificNameId = $datatable->query['scientificNameId'];
            if (isset($scientificNameId) && is_array($scientificNameId)) {
                $query .= " AND scientificNameId in (" . implode(",", $scientificNameId) . ")";
            }

            $stockOption = $datatable->query['stockOption'];
            if (isset($stockOption) && $stockOption == 1) {
                $query .= " AND stockStatusId = 1 ";
            }

            $categoryId = $datatable->query['categoryId'];
            if (isset($categoryId) && is_array($categoryId)) {
                $query .= " AND ( categoryId in (" . implode(",", $categoryId) . ") OR subCategoryId in (" . implode(",", $categoryId) . ") )";
            }

        }

        $query .= " AND statusId = 1";

        $fullQuery = $query;

        $dbData = new BaseModel($this->db, "vwEntityProductSell");
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

    function postProductImage()
    {
        $imageName = $this->f3->get('POST.imageName');

        $uploadDir = "assets/img/products/";
        $this->f3->set('UPLOADS', $uploadDir);

        $overwrite = true;

        $web = \Web::instance();
        $files = $web->receive(function ($file, $formFieldName) {
            return true;
        }, $overwrite, true);

        $path = "/assets/img/products/" . $imageName;

        $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
        $this->webResponse->title = "Product Image Upload";
        $this->webResponse->data = $path;
        echo $this->webResponse->jsonResponse();
    }

    function postDistributorProductsBestSelling()
    {
        ## Read values from Datatables
        $datatable = new Datatable($_POST);

        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
        $query = "entityId IN ($arrEntityId)";
        $query .= " AND statusId = 1";
        $meta = array();
        $dbProducts = new BaseModel($this->db, "vwEntityProductSell");

        $data = [];

        $totalRecords = $dbProducts->count($query);
        $totalFiltered = 5;
        $data = $dbProducts->findWhere($query, "quantityOrdered DESC", 5, 0);

        ## Response
        $response = array(
            "draw" => intval($datatable->draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        );

        $this->jsonResponseAPI($response);
    }

    function postEditDistributorProduct()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/distributor/product");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $productId = $this->f3->get('POST.id');

            $dbEntityProduct = new BaseModel($this->db, "entityProductSell");
            $dbEntityProduct->getWhere("productId=$productId");

            $dbProduct = new BaseModel($this->db, "product");
            $dbProduct->getWhere("id=$productId");

            if ($dbEntityProduct->dry() || $dbProduct->dry()) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->title = "";
                $this->webResponse->message = "No Product";
                echo $this->webResponse->jsonResponse();
            } else {
                $scientificNameId = $this->f3->get('POST.scientificNameId');
                $madeInCountryId = $this->f3->get('POST.madeInCountryId');
                $name_en = $this->f3->get('POST.name_en');
                $name_ar = $this->f3->get('POST.name_ar');
                $name_fr = $this->f3->get('POST.name_fr');
                $image = $this->f3->get('POST.image');
                $unitPrice = $this->f3->get('POST.unitPrice');
                $maximumOrderQuantity = $this->f3->get('POST.maximumOrderQuantity');
                $subtitle_ar = $this->f3->get('POST.subtitle_ar');
                $subtitle_en = $this->f3->get('POST.subtitle_en');
                $subtitle_fr = $this->f3->get('POST.subtitle_fr');
                $description_ar = $this->f3->get('POST.description_ar');
                $description_en = $this->f3->get('POST.description_en');
                $description_fr = $this->f3->get('POST.description_fr');
                $unitPrice = $this->f3->get('POST.unitPrice');
                $manufacturerName = $this->f3->get('POST.manufacturerName');
                $batchNumber = $this->f3->get('POST.batchNumber');
                $itemCode = $this->f3->get('POST.itemCode');
                $categoryId = $this->f3->get('POST.categoryId');
                $subcategoryId = $this->f3->get('POST.subcategoryId');
                $activeIngredientsId = $this->f3->get('POST.activeIngredientsId');
                $expiryDate = $this->f3->get('POST.expiryDate');;
                $strength = $this->f3->get('POST.strength');

                if (!$scientificNameId || !$madeInCountryId || !$name_en
                    || !$name_ar || !$name_fr || !$unitPrice || !$maximumOrderQuantity
                    || !$description_ar || !$description_en || !$description_fr
                    || !$categoryId || !$subcategoryId) {
                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->title = "";
                    $this->webResponse->message = "Some mandatory fields are missing";
                    echo $this->webResponse->jsonResponse();
                    return;
                }

                if (!is_numeric($unitPrice) || $unitPrice <= 0)  {
                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->title = "";
                    $this->webResponse->message = "Unit Price must be a positive number";
                    echo $this->webResponse->jsonResponse();
                    return;
                }

                $dbSubcategory = new BaseModel($this->db, "subcategory");
                $dbSubcategory->getWhere("id = $subcategoryId AND categoryId = $categoryId");
                if($dbSubcategory->dry()) {
                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->title = "";
                    $this->webResponse->message = "Category invalid";
                    echo $this->webResponse->jsonResponse();
                    return;
                }

                $dbProduct->scientificNameId = $scientificNameId;
                $dbProduct->madeInCountryId = $madeInCountryId;
                $dbProduct->name_en = $name_en;
                $dbProduct->name_fr = $name_fr;
                $dbProduct->name_ar = $name_ar;
                $dbProduct->image = $image;
                $dbProduct->subtitle_ar = $subtitle_ar;
                $dbProduct->subtitle_en = $subtitle_en;
                $dbProduct->subtitle_fr = $subtitle_fr;
                $dbProduct->description_ar = $description_ar;
                $dbProduct->description_en = $description_en;
                $dbProduct->description_fr = $description_fr;
                $dbProduct->unitPrice = $unitPrice;
                $dbProduct->manufacturerName = $manufacturerName;
                $dbProduct->batchNumber = $batchNumber;
                $dbProduct->itemCode = $itemCode;
                $dbProduct->categoryId = $categoryId;
                $dbProduct->subcategoryId = $subcategoryId;
                $dbProduct->expiryDate = $expiryDate;
                $dbProduct->strength = $strength;

                $dbProduct->update();

                $dbProductIngredient = new BaseModel($this->db, "productIngredient");
                $dbProductIngredient->getWhere("productId = $productId");
                while (!$dbProductIngredient->dry()) {
                    $dbProductIngredient->delete();
                    $dbProductIngredient->next();
                }

                $arrIngredientId = explode(",", $activeIngredientsId);
                foreach($arrIngredientId as $ingredientId) {
                    $dbProductIngredient->productId = $productId;
                    $dbProductIngredient->ingredientId = $ingredientId;
                    $dbProductIngredient->add();
                }

                $dbEntityProduct->unitPrice = $unitPrice;
                $dbEntityProduct->stockUpdateDateTime = $dbEntityProduct->getCurrentDateTime();
                $dbEntityProduct->maximumOrderQuantity = $maximumOrderQuantity;

                $dbEntityProduct->update();

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS_SHOW_DIALOG;
                $this->webResponse->title = "";
                $this->webResponse->message = $this->f3->get('vModule_productEdited');
                $this->webResponse->data = $dbProduct->name_ar;
                echo $this->webResponse->jsonResponse();
            }
        }
    }

    function postEditQuantityDistributorProduct()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/distributor/product");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $id = $this->f3->get('POST.id');

            $dbEntityProduct = new BaseModel($this->db, "entityProductSell");
            $dbEntityProduct->getWhere("productId=$id");

            $dbProduct = new BaseModel($this->db, "product");
            $dbProduct->getWhere("id=$id");

            if ($dbEntityProduct->dry() || $dbProduct->dry()) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->title = "";
                $this->webResponse->message = "No Product";
                echo $this->webResponse->jsonResponse();
            } else {
                $stock = $this->f3->get('POST.stock');
                $stockStatusId = $this->f3->get('POST.stockStatus');
                $bonusTypeId = $this->f3->get('POST.bonusType');
                $bonusRepeater = $this->f3->get('POST.bonusRepeater');
                if ($bonusRepeater == null) {
                    $bonusRepeater = [];
                }

                if (!filter_var($stock, FILTER_VALIDATE_INT) || $stock < 0) {
                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->title = "";
                    $this->webResponse->message = "Stock must be a positive number";
                    echo $this->webResponse->jsonResponse();
                    return;
                }

                $validMinOrder = true;
                $validBonus = true;
                foreach ($bonusRepeater as $bonus) {
                    if (!filter_var($bonus['minOrder'], FILTER_VALIDATE_INT) || $bonus['minOrder'] < 0) {
                        $validMinOrder = false;
                    }

                    if (!filter_var($bonus['bonus'], FILTER_VALIDATE_INT) || $bonus['bonus'] < 0) {
                        $validBonus = false;
                    }

                    if ($validMinOrder && $validBonus) {
                        break;
                    }
                }

                if (!$validMinOrder || !$validBonus) {
                    $message = "";
                    if (!$validMinOrder && !$validBonus) {
                        $message = "Min Order and Bonus must be positive numbers";
                    } else if (!$validMinOrder) {
                        $message = "Min Order must be a positive number";
                    } else {
                        $message = "Bonus must be a positive number";
                    }

                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->title = "";
                    $this->webResponse->message = $message;
                    echo $this->webResponse->jsonResponse();
                    return;
                }

                if ($stock > 0) {
                    $stockStatusId = 1;
                } else {
                    if (isset($stockStatusId) && $stockStatusId == 'on') {
                        $stockStatusId = 3;
                    } else {
                        $stockStatusId = 2;
                    }
                }

                if (isset($bonusTypeId) && $bonusTypeId == 'on') {
                    $bonusTypeId = 2;
                } else {
                    $bonusTypeId = 1;
                }

                $dbEntityProduct->stock = $stock;
                $dbEntityProduct->stockStatusId = $stockStatusId;
                $dbEntityProduct->bonusTypeId = $bonusTypeId;
                $dbEntityProduct->stockUpdateDateTime = $dbEntityProduct->getCurrentDateTime();

                $dbEntityProduct->update();

                if ($bonusTypeId != 1) {
                    $dbBonus = new BaseModel($this->db, "entityProductSellBonusDetail");
                    $dbBonus->load(array("entityProductId = $id AND isActive = 1"));
                    while (!$dbBonus->dry()) {
                        $dbBonus->delete();
                        $dbBonus->next();
                    }

                    foreach ($bonusRepeater as $bonus) {
                        $dbBonus->reset();
                        if ($bonus['minOrder'] != '' && $bonus['bonus'] != '') {
                            $dbBonus->entityProductId = $id;
                            $dbBonus->minOrder = $bonus['minOrder'];
                            $dbBonus->bonus = $bonus['bonus'];
                            $dbBonus->add();
                        }
                    }
                }

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS_SHOW_DIALOG;
                $this->webResponse->title = "";
                $this->webResponse->message = $this->f3->get('vModule_quantityEdited');
                $this->webResponse->data = $bonusRepeater;
                echo $this->webResponse->jsonResponse();
            }
        }
    }

    function postAddDistributorProduct()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/distributor/product");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $scientificNameId = $this->f3->get('POST.scientificNameId');
            $madeInCountryId = $this->f3->get('POST.madeInCountryId');
            $name_en = $this->f3->clean($this->f3->get('POST.name_en'));
            $name_ar = $this->f3->clean($this->f3->get('POST.name_ar'));
            $name_fr = $this->f3->clean($this->f3->get('POST.name_fr'));
            $image = $this->f3->get('POST.image');
            $stock = $this->f3->get('POST.stock');
            $maximumOrderQuantity = $this->f3->get('POST.maximumOrderQuantity');
            $subtitle_ar = $this->f3->clean($this->f3->get('POST.subtitle_ar'));
            $subtitle_en = $this->f3->clean($this->f3->get('POST.subtitle_en'));
            $subtitle_fr = $this->f3->clean($this->f3->get('POST.subtitle_fr'));
            $description_ar = $this->f3->clean($this->f3->get('POST.description_ar'));
            $description_en = $this->f3->clean($this->f3->get('POST.description_en'));
            $description_fr = $this->f3->clean($this->f3->get('POST.description_fr'));
            $unitPrice = $this->f3->get('POST.unitPrice');
            $manufacturerName = $this->f3->clean($this->f3->get('POST.manufacturerName'));
            $batchNumber = $this->f3->clean($this->f3->get('POST.batchNumber'));
            $itemCode = $this->f3->clean($this->f3->get('POST.itemCode'));
            $categoryId = $this->f3->get('POST.categoryId');
            $subcategoryId = $this->f3->get('POST.subcategoryId');
            $activeIngredientsId = $this->f3->get('POST.activeIngredientsId');
            $expiryDate = $this->f3->get('POST.expiryDate');;
            $strength = $this->f3->clean($this->f3->get('POST.strength'));

            if (!$scientificNameId || !$madeInCountryId || !$name_en
                || !$name_ar || !$name_fr || !$unitPrice
                || !$stock || !$maximumOrderQuantity || !$description_ar
                || !$description_en || !$description_fr || !$categoryId
                || !$subcategoryId) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->title = "";
                $this->webResponse->message = "Some mandatory fields are missing";
                echo $this->webResponse->jsonResponse();
                return;
            }

            if ((!filter_var($stock, FILTER_VALIDATE_INT) || $stock < 0) || (!is_numeric($unitPrice) || $unitPrice <= 0)) {
                $message = "";
                if ((!filter_var($stock, FILTER_VALIDATE_INT) || $stock < 0) && (!is_numeric($unitPrice) || $unitPrice <= 0)) {
                    $message = "Stock and Unit Price must be positive numbers";
                } else if (!is_numeric($unitPrice) || $unitPrice <= 0) {
                    $message = "Unit Price must be a positive number";
                } else {
                    $message = "Stock must be a positive number";
                }

                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->title = "";
                $this->webResponse->message = $message;
                echo $this->webResponse->jsonResponse();
                return;
            }

            $dbSubcategory = new BaseModel($this->db, "subcategory");
            $dbSubcategory->getWhere("id = $subcategoryId AND categoryId = $categoryId");
            if($dbSubcategory->dry()) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->title = "";
                $this->webResponse->message = "Category invalid";
                echo $this->webResponse->jsonResponse();
                return;
            }

            $dbProduct = new BaseModel($this->db, "product");
            $dbProduct->scientificNameId = $scientificNameId;
            $dbProduct->madeInCountryId = $madeInCountryId;
            $dbProduct->name_en = $name_en;
            $dbProduct->name_fr = $name_fr;
            $dbProduct->name_ar = $name_ar;
            $dbProduct->image = $image;
            $dbProduct->subtitle_ar = $subtitle_ar;
            $dbProduct->subtitle_en = $subtitle_en;
            $dbProduct->subtitle_fr = $subtitle_fr;
            $dbProduct->description_ar = $description_ar;
            $dbProduct->description_en = $description_en;
            $dbProduct->description_fr = $description_fr;
            $dbProduct->unitPrice = $unitPrice;
            $dbProduct->manufacturerName = $manufacturerName;
            $dbProduct->batchNumber = $batchNumber;
            $dbProduct->itemCode = $itemCode;
            $dbProduct->categoryId = $categoryId;
            $dbProduct->subcategoryId = $subcategoryId;
            $dbProduct->expiryDate = $expiryDate;
            $dbProduct->strength = $strength;

            $dbProduct->addReturnID();

            $arrIngredientId = explode(",", $activeIngredientsId);
            $dbProductIngredient = new BaseModel($this->db, "productIngredient");
            foreach($arrIngredientId as $ingredientId) {
                $dbProductIngredient->productId = $dbProduct->id;
                $dbProductIngredient->ingredientId = $ingredientId;
                $dbProductIngredient->add();
            }

            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $entityId = $arrEntityId;


            $dbEntityProduct = new BaseModel($this->db, "entityProductSell");
            $dbEntityProduct->productId = $dbProduct->id;
            $dbEntityProduct->entityId = $entityId;
            $dbEntityProduct->unitPrice = $unitPrice;
            $dbEntityProduct->stock = $stock;
            $dbEntityProduct->statusId = 1;
            $dbEntityProduct->stockStatusId = 1;
            $dbEntityProduct->bonusTypeId = 1;
            $dbEntityProduct->stockUpdateDateTime = $dbEntityProduct->getCurrentDateTime();
            $dbEntityProduct->maximumOrderQuantity = $maximumOrderQuantity;

            $dbEntityProduct->add();

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS_SHOW_DIALOG;
            $this->webResponse->title = "";
            $this->webResponse->message = $this->f3->get('vModule_productAdded');
            $this->webResponse->data = $dbProduct['name_' . $this->objUser->language];
            echo $this->webResponse->jsonResponse();
        }
    }

    function getStockUpload()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "Stock Update"; //$this->f3->get('vModule_stock_title');
            $this->webResponse->data = View::instance()->render('app/products/stock/upload.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function getStockDownload()
    {
        if ($this->f3->ajax()) {
            ini_set('max_execution_time', 600);

            // Get all related products
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $query = "entityId IN ($arrEntityId)";
            $dbProducts = new BaseModel($this->db, "vwEntityProductSell");
            $allProducts = $dbProducts->findWhere($query);

            // Setup excel sheet
            $sheetnameUserInput = 'User Input';
            $sheetnameDatabaseInput = 'Database Input';
            $sheetnameVariables = 'Variables';

            // Prepare data for variables sheet
            $arrProducts = [
                ['Name', 'Value']
            ];
            $arrStockAvailability = [
                ['Name', 'Value']
            ];

            $mapProductIdName = [];
            $productsNum = 2;
            $nameField = "productName_" . $this->objUser->language;
            foreach ($allProducts as $product) {
                $productsNum++;
                $arrProducts[] = array($product[$nameField], $product['productId']);
                $mapProductIdName[$product['productId']] = $product[$nameField];
            }

            $dbStockStatus = new BaseModel($this->db, "stockStatus");
            $dbStockStatus->name = "name_" . $this->objUser->language;
            $allStockStatus = $dbStockStatus->findAll("id asc");

            $mapStockIdName = [];
            $stockAvailabilityNum = 2;
            foreach ($allStockStatus as $stockStatus) {
                $stockAvailabilityNum++;
                $arrStockAvailability[] = array($stockStatus['name'], $stockStatus['id']);
                $mapStockIdName[$stockStatus['id']] = $stockStatus['name'];
            }

            $sampleFilePath = 'app/files/samples/products-stock-sample.xlsx';
            $spreadsheet = Excel::loadFile($sampleFilePath);

            // Change active sheet to variables
            $sheet = $spreadsheet->setActiveSheetIndex(2);

            // Set products and stock availability in excel
            $sheet->fromArray($arrProducts, NULL, 'A2', true);
            $sheet->fromArray($arrStockAvailability, NULL, 'D2', true);

            // Change active sheet to database input
            $sheet = $spreadsheet->setActiveSheetIndex(1);

            // Set validation and formula
            Excel::setCellFormulaVLookup($sheet, 'A3', $productsNum, "'User Input'!A", 'Variables!$A$3:$B$' . $productsNum);
            Excel::setCellFormulaVLookup($sheet, 'D3', $productsNum, "'User Input'!D", 'Variables!$D$3:$E$' . $stockAvailabilityNum);

            // Hide database and variables sheet
            Excel::hideSheetByName($spreadsheet, $sheetnameDatabaseInput);
            Excel::hideSheetByName($spreadsheet, $sheetnameVariables);

            // Change active sheet to user input
            $sheet = $spreadsheet->setActiveSheetIndex(0);

            // Set data validation for products and stock availability
            Excel::setDataValidation($sheet, 'A3', 'A' . $productsNum, 'TYPE_LIST', 'Variables!$A$3:$A$' . $productsNum);
            Excel::setDataValidation($sheet, 'D3', 'D' . $productsNum, 'TYPE_LIST', 'Variables!$D$3:$D$' . $stockAvailabilityNum);

            // Add all products to multidimensional array
            $multiProducts = [];
            $fields = [
                "productId",
                "unitPrice",
                "vat",
                "stockStatusId",
                "stock",
                "expiryDate"
            ];
            $i = 3;
            foreach ($allProducts as $product) {
                $singleProduct = [];
                foreach ($fields as $field) {
                    if ($field == "productId") {
                        $cellValue = $mapProductIdName[$product[$field]];
                    } else if ($field == "stockStatusId") {
                        $cellValue = $mapStockIdName[$product[$field]];
                    } else {
                        $cellValue = $product[$field];
                    }
                    array_push($singleProduct, $cellValue);
                }
                array_push($multiProducts, $singleProduct);
                $i++;
            }

            // Fill rows with products
            $sheet->fromArray($multiProducts, NULL, 'A3', true);

            // Create excel sheet
            $productsSheetUrl = "files/downloads/reports/products-stock/products-stock-" . $this->objUser->id . "-" . time() . ".xlsx";
            Excel::saveSpreadsheetToPath($spreadsheet, $productsSheetUrl);

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "Stock Download";
            $this->webResponse->data = "/" . $productsSheetUrl;
            echo $this->webResponse->jsonResponse();
        }
    }

    function postStockUpload()
    {
        $fileName = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_FILENAME);
        $ext = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_EXTENSION);
        // basename($_FILES["file"]["name"])

        $targetFile = "files/uploads/reports/products-stock/" . $this->objUser->id . "-" . $fileName . "-" . time() . ".$ext";

        if ($ext == "xlsx" || $ext == "xls" || $ext == "csv") {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                $dbStockUpdateUpload = new BaseModel($this->db, "stockUpdateUpload");
                $dbStockUpdateUpload->userId = $this->objUser->id;
                $dbStockUpdateUpload->filePath = $targetFile;
                $dbStockUpdateUpload->entityId = $this->objUser->entityId;
                $dbStockUpdateUpload->addReturnID();
                echo "OK";
            }
        }
    }

    function postStockUploadProcess()
    {
        ini_set('max_execution_time', 1000);
        ini_set('mysql.connect_timeout', 1000);

        $dbStockUpdateUpload = new BaseModel($this->db, "stockUpdateUpload");

        $dbStockUpdateUpload->getByField("userId", $this->objUser->id, "insertDateTime desc");

        // $inputFileType = Excel::identifyFileType($dbStockUpdateUpload->filePath);
        try {
            // $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            // $spreadsheet = $reader->load($dbStockUpdateUpload->filePath);
            $spreadsheet = Excel::loadFile($dbStockUpdateUpload->filePath);

            // Change active sheet to database input
            $sheet = $spreadsheet->setActiveSheetIndex(1);

            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $dbEntityProductSell = new BaseModel($this->db, "entityProductSell");

            // Get all related products
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $query = "entityId IN ($arrEntityId)";
            $dbProducts = new BaseModel($this->db, "vwEntityProductSell");
            $allProducts = $dbProducts->findWhere($query);

            // Get all stock status
            $dbStockStatus = new BaseModel($this->db, "stockStatus");
            $dbStockStatus->name = "name_" . $this->objUser->language;
            $allStockStatus = $dbStockStatus->findAll("id asc");

            $allStockStatusId = [];
            foreach ($allStockStatus as $stockStatus) {
                array_push($allStockStatusId, $stockStatus['id']);
            }

            $fields = [
                "A" => "productId",
                "B" => "unitPrice",
                "C" => "vat",
                "D" => "stockStatusId",
                "E" => "stock",
                "F" => "expiryDate"
            ];

            $successProducts = [];
            $failedProducts = [];

            $allErrors = [];

            $dbStockUpdateUpload->recordsCount = 0;
            $successRecords = 0;
            $failedRecords = 0;
            $unchangedRecords = 0;

            $firstRow = true;
            $secondRow = false;
            $finished = false;
            foreach ($sheet->getRowIterator() as $row) {
                $product = [];

                if ($firstRow) {
                    $firstRow = false;
                    $secondRow = true;
                    continue;
                } else if ($secondRow) {
                    $secondRow = false;
                    continue;
                }

                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);

                $errors = [];

                $fieldsChanged = false;
                $stockFieldsChanged = false;
                foreach ($cellIterator as $cell) {
                    $cellLetter = $cell->getColumn();
                    $cellValue = $cell->getCalculatedValue();

                    array_push($product, $cellValue);

                    switch ($cellLetter) {
                        case "A":
                            if (!is_numeric($cellValue)) {
                                $finished = true;
                                break;
                            } else {
                                $dbEntityProductSell->getWhere("productId=$cellValue and entityId IN ($arrEntityId)");
                                if ($dbEntityProductSell->dry()) {
                                    array_push($errors, "Product not found");
                                }
                            }
                            break;
                        case "B":
                            if (!is_numeric($cellValue) || (float)$cellValue < 0) {
                                array_push($errors, "Price must be a positive number");
                            } else {
                                $unitPrice = round((float)$cellValue, 2);
                                if ($dbEntityProductSell->unitPrice != $unitPrice) {
                                    $fieldsChanged = true;
                                    $dbEntityProductSell->unitPrice = $unitPrice;
                                }
                            }
                            break;
                        case "C":
                            if (!is_numeric($cellValue) || (float)$cellValue < 0) {
                                array_push($errors, "VAT must be a positive number");
                            } else {
                                $vat = round((float)$cellValue, 2);
                                if ($dbEntityProductSell->vat != $vat) {
                                    $fieldsChanged = true;
                                    $dbEntityProductSell->vat = $vat;
                                }
                            }
                            break;
                        case "D":
                            if (!in_array($cellValue, $allStockStatusId)) {
                                array_push($errors, "Stock Availability invalid");
                            } else {
                                $stockStatusId = $cellValue;
                                if ($dbEntityProductSell->stockStatusId != $stockStatusId) {
                                    $stockFieldsChanged = true;
                                    $dbEntityProductSell->stockStatusId = $stockStatusId;
                                }
                            }
                            break;
                        case "E":
                            if (!filter_var($cellValue, FILTER_VALIDATE_INT) || (float)$cellValue < 0) {
                                array_push($errors, "Stock Quantity must be a positive whole number");
                            } else {
                                $stock = (int)$cellValue;
                                if ($dbEntityProductSell->stock != $stock) {
                                    $stockFieldsChanged = true;
                                    $dbEntityProductSell->stock = $stock;
                                }
                            }
                            break;
                        case "F":
                            if (!is_null($cellValue)) {
                                if (!is_int($cellValue) && (count(explode("-", $cellValue)) !== 3)) {
                                    array_push($errors, "Expiry Date must fit a date format (YYYY-MM-DD)");
                                } else {
                                    if (is_int($cellValue)) {
                                        $expiryDate = Excel::excelDateToRegularDate($cellValue);
                                    } else {
                                        $expiryDate = $cellValue;
                                    }
                                    if ($dbEntityProductSell->expiryDate != $expiryDate) {
                                        $fieldsChanged = true;
                                        $dbEntityProductSell->expiryDate = $expiryDate;
                                    }
                                }
                            }
                            break;
                    }
                }

                if ($finished) {
                    break;
                }

                $dbStockUpdateUpload->recordsCount++;

                if (!$dbEntityProductSell->dry() && ($fieldsChanged || $stockFieldsChanged) && count($errors) === 0) {
                    $currentDate = date("Y-m-d H:i:s");
                    if ($fieldsChanged) {
                        $dbEntityProductSell->updateDateTime = $currentDate;
                    }

                    if ($stockFieldsChanged) {
                        $dbEntityProductSell->stockUpdateDateTime = $currentDate;
                    }

                    array_push($successProducts, $product);

                    $dbEntityProductSell->update();
                    $successRecords++;
                } else if (count($errors) > 0) {
                    array_push($failedProducts, $product);
                    array_push($allErrors, $errors);
                    $failedRecords++;
                } else {
                    $unchangedRecords++;
                }

                $dbEntityProductSell->reset();
            }

            if (count($failedProducts) > 0) {
                // Setup excel sheet
                $sheetnameUserInput = 'User Input';
                $sheetnameDatabaseInput = 'Database Input';
                $sheetnameVariables = 'Variables';

                // Prepare data for variables sheet
                $arrProducts = [
                    ['Name', 'Value']
                ];
                $arrStockAvailability = [
                    ['Name', 'Value']
                ];

                $mapProductIdName = [];
                $productsNum = 2;
                $nameField = "productName_" . $this->objUser->language;
                foreach ($allProducts as $product) {
                    $productsNum++;
                    $arrProducts[] = array($product[$nameField], $product['productId']);
                    $mapProductIdName[$product['productId']] = $product[$nameField];
                }

                $mapStockIdName = [];
                $stockAvailabilityNum = 2;
                foreach ($allStockStatus as $stockStatus) {
                    $stockAvailabilityNum++;
                    $arrStockAvailability[] = array($stockStatus['name'], $stockStatus['id']);
                    $mapStockIdName[$stockStatus['id']] = $stockStatus['name'];
                }

                $sampleFilePath = 'app/files/samples/products-stock-sample.xlsx';
                $spreadsheet = Excel::loadFile($sampleFilePath);

                // Change active sheet to variables
                $sheet = $spreadsheet->setActiveSheetIndex(2);

                // Set products and stock availability in excel
                $sheet->fromArray($arrProducts, NULL, 'A2', true);
                $sheet->fromArray($arrStockAvailability, NULL, 'D2', true);

                // Change active sheet to database input
                $sheet = $spreadsheet->setActiveSheetIndex(1);

                // Set validation and formula
                Excel::setCellFormulaVLookup($sheet, 'A3', (count($failedProducts) + 2), "'User Input'!A", 'Variables!$A$3:$B$' . $productsNum);
                Excel::setCellFormulaVLookup($sheet, 'D3', (count($failedProducts) + 2), "'User Input'!D", 'Variables!$D$3:$E$' . $stockAvailabilityNum);

                // Hide database and variables sheet
                Excel::hideSheetByName($spreadsheet, $sheetnameDatabaseInput);
                Excel::hideSheetByName($spreadsheet, $sheetnameVariables);

                // Change active sheet to user input
                $sheet = $spreadsheet->setActiveSheetIndex(0);

                // Set data validation for products and stock availability
                Excel::setDataValidation($sheet, 'A3', 'A' . (count($failedProducts) + 2), 'TYPE_LIST', 'Variables!$A$3:$A$' . $productsNum);
                Excel::setDataValidation($sheet, 'D3', 'D' . (count($failedProducts) + 2), 'TYPE_LIST', 'Variables!$D$3:$D$' . $stockAvailabilityNum);

                $sheet->setCellValue('G2', 'Error');
                $sheet->getStyle('G2')->applyFromArray(Excel::STYlE_CENTER_BOLD_BORDER_THICK);

                // Add all products to multidimensional array
                $multiProducts = [];
                $fields = [
                    "productId",
                    "unitPrice",
                    "vat",
                    "stockStatusId",
                    "stock",
                    "expiryDate"
                ];
                $i = 3;
                for ($i = 0; $i < count($failedProducts); $i++) {
                    $product = $failedProducts[$i];
                    $singleProduct = [];
                    $j = 0;
                    foreach ($fields as $field) {
                        if ($field == "productId") {
                            $cellValue = $mapProductIdName[$product[$j]];
                        } else if ($field == "stockStatusId") {
                            $cellValue = $mapStockIdName[$product[$j]];
                        } else {
                            $cellValue = $product[$j];
                        }
                        array_push($singleProduct, $cellValue);
                        $j++;
                    }
                    $errors = $allErrors[$i];
                    $error = join(", ", $errors);
                    array_push($singleProduct, $error);

                    array_push($multiProducts, $singleProduct);
                }
                // Fill rows with products
                $sheet->fromArray($multiProducts, NULL, 'A3', true);

                // Create excel sheet
                $failedProductsSheetUrl = "files/downloads/reports/products-stock/products-stock-" . $this->objUser->id . "-" . time() . ".xlsx";
                Excel::saveSpreadsheetToPath($spreadsheet, $failedProductsSheetUrl);
            }

            // Update logs
            if (count($successProducts) > 0) {
                $dbStockUpdateUpload->successLog = json_encode($successProducts);
            }

            if (count($failedProducts) > 0) {
                $dbStockUpdateUpload->failedLog = json_encode($failedProducts);
            }

            // Update counts and rates
            $dbStockUpdateUpload->completedCount = $successRecords;
            $dbStockUpdateUpload->failedCount = $failedRecords;
            $dbStockUpdateUpload->unchangedCount = $unchangedRecords;

            if ($successRecords + $failedRecords !== 0) {
                $dbStockUpdateUpload->importSuccessRate = round($successRecords / ($successRecords + $failedRecords), 2) * 100;
                $dbStockUpdateUpload->importFailureRate = round($failedRecords / ($successRecords + $failedRecords), 2) * 100;
            } else {
                $dbStockUpdateUpload->importSuccessRate = 0;
                $dbStockUpdateUpload->importFailureRate = 0;
            }

            $this->f3->set("objStockUpdateUpload", $dbStockUpdateUpload);
            if (!is_null($failedProductsSheetUrl)) {
                $this->f3->set("failedProductsSheetUrl", "/" . $failedProductsSheetUrl);
            }

            $dbStockUpdateUpload->update();

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "";
            $this->webResponse->data = View::instance()->render('app/products/stock/uploadResult.php');
            echo $this->webResponse->jsonResponse();;
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
        }
    }

    function getBonusUpload()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "Bonus Update"; //$this->f3->get('vModule_bonus_title');
            $this->webResponse->data = View::instance()->render('app/products/bonus/upload.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function getBonusDownload()
    {
        if ($this->f3->ajax()) {
            ini_set('max_execution_time', 600);

            // Get all related products
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $query = "entityId IN ($arrEntityId)";
            $dbProducts = new BaseModel($this->db, "vwEntityProductSellSummary");
            $allProducts = $dbProducts->findWhere($query);

            // Setup excel sheet
            $sheetnameUserInput = 'User Input';
            $sheetnameDatabaseInput = 'Database Input';
            $sheetnameVariables = 'Variables';

            // Prepare data for variables sheet
            $arrProducts = [
                ['Name', 'Value']
            ];

            $mapProductIdName = [];
            $productsNum = 2;
            $nameField = "productName_" . $this->objUser->language;
            foreach ($allProducts as $product) {
                $productsNum++;
                $arrProducts[] = array($product[$nameField], $product['id']);
                $mapProductIdName[$product['id']] = $product[$nameField];
            }

            $sampleFilePath = 'app/files/samples/products-bonus-sample.xlsx';
            $spreadsheet = Excel::loadFile($sampleFilePath);

            // Change active sheet to variables
            $sheet = $spreadsheet->setActiveSheetIndex(2);

            // Set products in excel
            $sheet->fromArray($arrProducts, NULL, 'A2', true);

            // Change active sheet to database input
            $sheet = $spreadsheet->setActiveSheetIndex(1);

            // Set validation and formula
            Excel::setCellFormulaVLookup($sheet, 'A3', count($allProducts), "'User Input'!A", 'Variables!$A$3:$B$' . $productsNum);

            // Hide database and variables sheet
            Excel::hideSheetByName($spreadsheet, $sheetnameDatabaseInput);
            Excel::hideSheetByName($spreadsheet, $sheetnameVariables);

            // Get all bonuses
            $allBonuses = [];

            $allProductsIds = implode(", ", array_keys($mapProductIdName));
            $dbBonus = new BaseModel($this->db, "entityProductSellBonusDetail");
            $allBonuses = $dbBonus->findWhere("entityProductId in (" . $allProductsIds . ") AND isActive = 1");

            $bonusesNum = count($allBonuses) + 2;

            // Change active sheet to user input
            $sheet = $spreadsheet->setActiveSheetIndex(0);

            // Set data validation for bonuses and stock availability
            Excel::setDataValidation($sheet, 'A3', 'A2505', 'TYPE_LIST', 'Variables!$A$3:$A$' . $bonusesNum);

            // Add all bonuses to multidimensional array
            $multiBonuses = [];
            $fields = [
                "entityProductId",
                "minOrder",
                "bonus"
            ];
            $i = 3;
            foreach ($allBonuses as $bonus) {
                $singleBonus = [];
                foreach ($fields as $field) {
                    if ($field == "entityProductId") {
                        $cellValue = $mapProductIdName[$bonus[$field]];
                    } else {
                        $cellValue = $bonus[$field];
                    }
                    array_push($singleBonus, $cellValue);
                }
                array_push($multiBonuses, $singleBonus);
                $i++;
            }

            // Fill rows with bonuses
            $sheet->fromArray($multiBonuses, NULL, 'A3', true);

            // Create excel sheet
            $productsSheetUrl = "files/downloads/reports/products-bonus/products-bonus-" . $this->objUser->id . "-" . time() . ".xlsx";
            Excel::saveSpreadsheetToPath($spreadsheet, $productsSheetUrl);

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "Bonus Download";
            $this->webResponse->data = "/" . $productsSheetUrl;
            echo $this->webResponse->jsonResponse();
        }
    }

    function postBonusUpload()
    {
        $fileName = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_FILENAME);
        $ext = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_EXTENSION);
        // basename($_FILES["file"]["name"])

        $targetFile = "files/uploads/reports/products-bonus/" . $this->objUser->id . "-" . $fileName . "-" . time() . ".$ext";

        if ($ext == "xlsx" || $ext == "xls" || $ext == "csv") {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                $dbStockUpdateUpload = new BaseModel($this->db, "stockUpdateUpload");
                $dbStockUpdateUpload->userId = $this->objUser->id;
                $dbStockUpdateUpload->filePath = $targetFile;
                $dbStockUpdateUpload->entityId = $this->objUser->entityId;
                $dbStockUpdateUpload->addReturnID();
                echo "OK";
            }
        }
    }

    function postBonusUploadProcess()
    {
        ini_set('max_execution_time', 1000);
        ini_set('mysql.connect_timeout', 1000);

        $dbBonusUpdateUpload = new BaseModel($this->db, "stockUpdateUpload");

        $dbBonusUpdateUpload->getByField("userId", $this->objUser->id, "insertDateTime desc");

        // $inputFileType = Excel::identifyFileType($dbBonusUpdateUpload->filePath);
        try {
            // $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
            // $spreadsheet = $reader->load($dbBonusUpdateUpload->filePath);
            $spreadsheet = Excel::loadFile($dbBonusUpdateUpload->filePath);

            // Change active sheet to database input
            $sheet = $spreadsheet->setActiveSheetIndex(1);

            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $dbEntityProductBonus = new BaseModel($this->db, "entityProductSellBonusDetail");

            // Get all related products
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $query = "entityId IN ($arrEntityId)";
            $dbProducts = new BaseModel($this->db, "vwEntityProductSell");
            $allProducts = $dbProducts->findWhere($query);

            $mapProductIdProduct = [];
            foreach ($allProducts as $product) {
                $mapProductIdProduct[$product['productId']] = $product;
                $mapProductIdMinQuant[$product['productId']] = [];
            }
            $productIdsWithBonus = [];

            $fields = [
                "A" => "productId",
                "B" => "minOrder",
                "C" => "bonus"
            ];

            $allBonuses = [];
            $allErrors = [];

            $dbBonusUpdateUpload->recordsCount = 0;

            $firstRow = true;
            $secondRow = false;
            $finished = false;
            foreach ($sheet->getRowIterator() as $row) {
                $singleBonus = [];

                if ($firstRow) {
                    $firstRow = false;
                    $secondRow = true;
                    continue;
                } else if ($secondRow) {
                    $secondRow = false;
                    continue;
                }

                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);

                $errors = [];

                foreach ($cellIterator as $cell) {
                    $cellLetter = $cell->getColumn();
                    $cellValue = $cell->getCalculatedValue();

                    array_push($singleBonus, $cellValue);

                    switch ($cellLetter) {
                        case "A":
                            if (!is_numeric($cellValue)) {
                                $finished = true;
                                break;
                            } else {
                                if (array_key_exists((string)$cellValue, $mapProductIdProduct)) {
                                    $dbProduct = $mapProductIdProduct[$cellValue];
                                    array_push($productIdsWithBonus, $cellValue);
                                } else {
                                    array_push($errors, "Product not found");
                                }
                            }
                            break;
                        case "B":
                            if (!filter_var($cellValue, FILTER_VALIDATE_INT) || (float)$cellValue < 0) {
                                array_push($errors, "Minimum Quantity must be a positive whole number");
                            } else {
                                $minOrder = (int)$cellValue;
                                $allQuant = $mapProductIdMinQuant[$dbProduct['productId']];

                                if (!is_null($dbProduct)) {
                                    if (in_array($minOrder, $allQuant)) {
                                        array_push($errors, "Minimum Quantity should be unique by product");
                                    } else {
                                        array_push($allQuant, $minOrder);
                                        $mapProductIdMinQuant[$dbProduct['productId']] = $allQuant;
                                    }
                                }
                            }
                            break;
                        case "C":
                            if (!filter_var($cellValue, FILTER_VALIDATE_INT) || (float)$cellValue < 0) {
                                array_push($errors, "Bonus Quantity must be a positive whole number");
                            } else {
                                $bonus = (int)$cellValue;
                            }
                            break;
                    }
                }

                if ($finished) {
                    break;
                }

                $dbBonusUpdateUpload->recordsCount++;

                array_push($allBonuses, $singleBonus);
                array_push($allErrors, $errors);

                if (count($errors) > 0) {
                    $failedSheet = true;
                }
            }

            if ($failedSheet) {
                // Setup excel sheet
                $sheetnameUserInput = 'User Input';
                $sheetnameDatabaseInput = 'Database Input';
                $sheetnameVariables = 'Variables';

                // Prepare data for variables sheet
                $arrProducts = [
                    ['Name', 'Value']
                ];

                $mapProductIdName = [];
                $productsNum = 2;
                $nameField = "productName_" . $this->objUser->language;
                foreach ($allProducts as $product) {
                    $productsNum++;
                    $arrProducts[] = array($product[$nameField], $product['productId']);
                    $mapProductIdName[$product['productId']] = $product[$nameField];
                }

                $sampleFilePath = 'app/files/samples/products-bonus-sample.xlsx';
                $spreadsheet = Excel::loadFile($sampleFilePath);

                // Change active sheet to variables
                $sheet = $spreadsheet->setActiveSheetIndex(2);

                // Set products in excel
                $sheet->fromArray($arrProducts, NULL, 'A2', true);

                // Change active sheet to database input
                $sheet = $spreadsheet->setActiveSheetIndex(1);

                // Set validation and formula
                Excel::setCellFormulaVLookup($sheet, 'A3', count($allProducts), "'User Input'!A", 'Variables!$A$3:$B$' . $productsNum);

                // Hide database and variables sheet
                Excel::hideSheetByName($spreadsheet, $sheetnameDatabaseInput);
                Excel::hideSheetByName($spreadsheet, $sheetnameVariables);

                // Change active sheet to user input
                $sheet = $spreadsheet->setActiveSheetIndex(0);

                // Set data validation for products
                Excel::setDataValidation($sheet, 'A3', 'A2505', 'TYPE_LIST', 'Variables!$A$3:$A$' . $productsNum);

                $sheet->setCellValue('D2', 'Error');
                $sheet->getStyle('D2')->applyFromArray(Excel::STYlE_CENTER_BOLD_BORDER_THICK);

                // Add all bonuses to multidimensional array
                $multiBonuses = [];
                $fields = [
                    "productId",
                    "minOrder",
                    "bonus"
                ];
                for ($i = 0; $i < count($allBonuses); $i++) {
                    $bonus = $allBonuses[$i];
                    $singleBonus = [];
                    $j = 0;
                    foreach ($fields as $field) {
                        if ($field == "productId") {
                            $cellValue = $mapProductIdName[$bonus[$j]];
                        } else {
                            $cellValue = $bonus[$j];
                        }
                        array_push($singleBonus, $cellValue);
                        $j++;
                    }
                    $errors = $allErrors[$i];
                    $error = join(", ", $errors);
                    array_push($singleBonus, $error);

                    array_push($multiBonuses, $singleBonus);
                }

                // Fill rows with products
                $sheet->fromArray($multiBonuses, NULL, 'A3', true);

                // Create excel sheet
                $failedProductsSheetUrl = "files/downloads/reports/products-bonus/products-bonus-" . $this->objUser->id . "-" . time() . ".xlsx";
                Excel::saveSpreadsheetToPath($spreadsheet, $failedProductsSheetUrl);
            } else {
                $productIdsWithoutBonus = [];
                foreach ($mapProductIdProduct as $productId => $product) {
                    if (!in_array($productId, $productIdsWithBonus)) {
                        array_push($productIdsWithoutBonus, $productId);
                    }
                }
                $productIdsWithoutBonusStr = implode(", ", array_keys($productIdsWithoutBonus));
                $productIdsWithBonusStr = implode(", ", array_keys($productIdsWithBonus));

                $allProductsIds = implode(", ", array_keys($mapProductIdProduct));

                $commands = [
                    "UPDATE entityProductSell SET bonusTypeId = '2' WHERE productId IN (" . $productIdsWithBonusStr . ");",
                    "UPDATE entityProductSell SET bonusTypeId = '1' WHERE productId IN (" . $productIdsWithoutBonusStr . ");",
                    "DELETE FROM entityProductSellBonusDetail WHERE entityProductId IN (" . $allProductsIds . ") AND isActive = 1",
                ];

                foreach ($allBonuses as $bonus) {
                    $query = "INSERT INTO entityProductSellBonusDetail (`entityProductId`, `minOrder`, `bonus`, `isActive`) VALUES ('" . $bonus[0] . "', '" . $bonus[1] . "', '" . $bonus[2] . "', '1')";
                    array_push($commands, $query);
                }

                $this->db->exec($commands);
            }

            // Update logs
            if ($failedSheet) {
                $dbBonusUpdateUpload->failedLog = json_encode($allBonuses);
            } else {
                $dbBonusUpdateUpload->successLog = json_encode($allBonuses);
            }

            $this->f3->set("objBonusUpdateUpload", $dbBonusUpdateUpload);
            if (!is_null($failedProductsSheetUrl)) {
                $this->f3->set("failedProductsSheetUrl", "/" . $failedProductsSheetUrl);
            }

            $dbBonusUpdateUpload->update();

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "";
            $this->webResponse->data = View::instance()->render('app/products/bonus/uploadResult.php');
            echo $this->webResponse->jsonResponse();;
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
        }
    }

    function getBulkAddUpload()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vModule_bulk_add_title');
            $this->webResponse->data = View::instance()->render('app/products/bulkAdd/upload.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function getBulkAddDownload()
    {
        if ($this->f3->ajax()) {
            // Setup excel sheet
            $sheetnameUserInput = 'User Input';
            $sheetnameDatabaseInput = 'Database Input';
            $sheetnameVariables = 'Variables';

            // Prepare data for variables sheet
            $arrScientificName = [
                ['Name', 'Value']
            ];
            $arrCountry = [
                ['Name', 'Value']
            ];
            $arrCategory = [
                ['Name', 'Value']
            ];
            $arrSubcategory = [
                ['Name', 'Value']
            ];
            $arrIngredient = [
                ['Name', 'Value']
            ];

            $dbScientificName = new BaseModel($this->db, "scientificName");
            $allScientificName = $dbScientificName->findAll("name asc");

            $scientificNum = 2;
            foreach ($allScientificName as $scientificName) {
                $scientificNum++;
                $arrScientificName[] = array($scientificName['name'], $scientificName['id']);
            }

            $dbCountry = new BaseModel($this->db, "country");
            $dbCountry->name = "name_" . $this->objUser->language;
            $allCountry = $dbCountry->findAll("name asc");

            $countryNum = 2;
            foreach ($allCountry as $country) {
                $countryNum++;
                $arrCountry[] = array($country['name'], $country['id']);
            }

            $dbCategory = new BaseModel($this->db, "category");
            $dbCategory->name = "name_" . $this->objUser->language;
            $allCategory = $dbCategory->findAll("name asc");

            $categoryNum = 2;
            foreach ($allCategory as $category) {
                $categoryNum++;
                $arrCategory[] = array($category['name'], $category['id']);
            }

            $dbSubcategory = new BaseModel($this->db, "subcategory");
            $dbSubcategory->name = "name_" . $this->objUser->language;
            $allSubcategory = $dbSubcategory->findAll("name asc");

            $subcategoryNum = 2;
            foreach ($allSubcategory as $subcategory) {
                $subcategoryNum++;
                $arrSubcategory[] = array($subcategory['name'], $subcategory['id']);
            }

            $dbIngredient = new BaseModel($this->db, "ingredient");
            $dbIngredient->name = "name_" . $this->objUser->language;
            $allIngredient = $dbIngredient->findAll("name asc");

            $ingredientNum = 2;
            foreach ($allIngredient as $ingredient) {
                $ingredientNum++;
                $arrIngredient[] = array($ingredient['name'], $ingredient['id']);
            }

            $sampleFilePath = 'app/files/samples/products-add-sample.xlsx';
            $spreadsheet = Excel::loadFile($sampleFilePath);

            // Change active sheet to variables
            $sheet = $spreadsheet->setActiveSheetIndex(2);

            // Set dropdown variables in excel
            $sheet->fromArray($arrScientificName, NULL, 'A2', true);
            $sheet->fromArray($arrCountry, NULL, 'D2', true);
            $sheet->fromArray($arrCategory, NULL, 'G2', true);
            $sheet->fromArray($arrSubcategory, NULL, 'J2', true);
            $sheet->fromArray($arrIngredient, NULL, 'M2', true);

            // Change active sheet to database input
            $sheet = $spreadsheet->setActiveSheetIndex(1);

            // Set validation and formula
            Excel::setCellFormulaVLookup($sheet, 'A3', 2505, "'User Input'!A", 'Variables!$A$3:$B$' . $scientificNum);
            Excel::setCellFormulaVLookup($sheet, 'B3', 2505, "'User Input'!B", 'Variables!$D$3:$E$' . $countryNum);
            Excel::setCellFormulaVLookup($sheet, 'R3', 2505, "'User Input'!R", 'Variables!$G$3:$H$' . $categoryNum);
            Excel::setCellFormulaVLookup($sheet, 'S3', 2505, "'User Input'!S", 'Variables!$J$3:$K$' . $subcategoryNum);
            Excel::setCellFormulaVLookup($sheet, 'T3', 2505, "'User Input'!T", 'Variables!$M$3:$N$' . $ingredientNum);

            // Hide database and variables sheet
            Excel::hideSheetByName($spreadsheet, $sheetnameDatabaseInput);
            Excel::hideSheetByName($spreadsheet, $sheetnameVariables);

            // Change active sheet to user input
            $sheet = $spreadsheet->setActiveSheetIndex(0);

            // Set data validation for dropdowns
            Excel::setDataValidation($sheet, 'A3', 'A2505', 'TYPE_LIST', 'Variables!$A$3:$A$' . $scientificNum);
            Excel::setDataValidation($sheet, 'B3', 'B2505', 'TYPE_LIST', 'Variables!$D$3:$D$' . $countryNum);
            Excel::setDataValidation($sheet, 'R3', 'R2505', 'TYPE_LIST', 'Variables!$G$3:$G$' . $categoryNum);
            Excel::setDataValidation($sheet, 'S3', 'S2505', 'TYPE_LIST', 'Variables!$J$3:$J$' . $subcategoryNum);
            Excel::setDataValidation($sheet, 'T3', 'T2505', 'TYPE_LIST', 'Variables!$M$3:$M$' . $ingredientNum);

            // Create excel sheet
            $productsSheetUrl = "files/downloads/reports/products-add/products-add-" . $this->objUser->id . "-" . time() . ".xlsx";
            Excel::saveSpreadsheetToPath($spreadsheet, $productsSheetUrl);

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "Bulk Download";
            $this->webResponse->data = "/" . $productsSheetUrl;
            echo $this->webResponse->jsonResponse();
        }
    }

    function postBulkAddUpload()
    {
        $fileName = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_FILENAME);
        $ext = pathinfo(basename($_FILES["file"]["name"]), PATHINFO_EXTENSION);
        // basename($_FILES["file"]["name"])

        $targetFile = "files/uploads/reports/products-add/" . $this->objUser->id . "-" . $fileName . "-" . time() . ".$ext";

        if ($ext == "xlsx" || $ext == "xls" || $ext == "csv") {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                $dbBulkAddUpload = new BaseModel($this->db, "bulkAddUpload");
                $dbBulkAddUpload->userId = $this->objUser->id;
                $dbBulkAddUpload->filePath = $targetFile;
                $dbBulkAddUpload->entityId = $this->objUser->entityId;
                $dbBulkAddUpload->addReturnID();
                echo "OK";
            }
        }
    }

    function postBulkAddUploadProcess()
    {
        ini_set('max_execution_time', 1000);
        ini_set('mysql.connect_timeout', 1000);

        $dbBulkAddUpload = new BaseModel($this->db, "bulkAddUpload");

        $dbBulkAddUpload->getByField("userId", $this->objUser->id, "insertDateTime desc");

        try {
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $entityId = $arrEntityId;

            $spreadsheet = Excel::loadFile($dbBulkAddUpload->filePath);

            // Change active sheet to database input
            $sheet = $spreadsheet->setActiveSheetIndex(1);

            // Get all scientific names
            $dbScientificName = new BaseModel($this->db, "scientificName");
            $allScientificName = $dbScientificName->findAll("name asc");

            $allScientificId = [];
            $mapScientificIdName = [];
            foreach ($allScientificName as $scientificName) {
                array_push($allScientificId, $scientificName['id']);
                $mapScientificIdName[$scientificName['id']] = $scientificName['name'];
            }

            // Get all countries
            $dbCountry = new BaseModel($this->db, "country");
            $dbCountry->name = "name_" . $this->objUser->language;
            $allCountry = $dbCountry->findAll("name asc");

            $allCountryId = [];
            $mapCountryIdName = [];
            foreach ($allCountry as $country) {
                array_push($allCountryId, $country['id']);
                $mapCountryIdName[$country['id']] = $country['name'];
            }

            // Get all categories
            $dbCategory = new BaseModel($this->db, "category");
            $dbCategory->name = "name_" . $this->objUser->language;
            $allCategory = $dbCategory->findAll("name asc");

            $allCategoryId = [];
            $mapCategoryIdName = [];
            foreach ($allCategory as $category) {
                array_push($allCategoryId, $category['id']);
                $mapCategoryIdName[$category['id']] = $category['name'];
            }

            // Get all subcategories
            $dbSubcategory = new BaseModel($this->db, "subcategory");
            $dbSubcategory->name = "name_" . $this->objUser->language;
            $allSubcategory = $dbSubcategory->findAll("name asc");

            $allSubcategoryId = [];
            $mapSubcategoryIdName = [];
            foreach ($allSubcategory as $subcategory) {
                array_push($allSubcategoryId, $subcategory['id']);
                $mapSubcategoryIdName[$subcategory['id']] = $subcategory['name'];
            }

            // Get all ingredients
            $dbIngredient = new BaseModel($this->db, "ingredient");
            $dbIngredient->name = "name_" . $this->objUser->language;
            $allIngredient = $dbIngredient->findAll("name asc");

            $allIngredientId = [];
            $mapIngredientIdName = [];
            foreach ($allIngredient as $ingredient) {
                array_push($allIngredientId, $ingredient['id']);
                $mapIngredientIdName[$ingredient['id']] = $ingredient['name'];
            }

            $fields = [
                "A" => "scientificNameId",
                "B" => "madeInCountryId",
                "C" => "name_en",
                "D" => "name_ar",
                "E" => "name_fr",
                "F" => "subtitle_ar",
                "G" => "subtitle_en",
                "H" => "subtitle_fr",
                "I" => "description_ar",
                "J" => "description_en",
                "K" => "description_fr",
                "L" => "unitPrice",
                "M" => "stock",
                "N" => "maximumOrderQuantity",
                "O" => "manufacturerName",
                "P" => "batchNumber",
                "Q" => "itemCode",
                "R" => "categoryId",
                "S" => "subcategoryId",
                "T" => "activeIngredientsId",
                "U" => "expiryDate",
                "V" => "strength"
            ];

            $successProducts = [];
            $failedProducts = [];

            $allErrors = [];

            $dbBulkAddUpload->recordsCount = 0;
            $successRecords = 0;
            $failedRecords = 0;
            $unchangedRecords = 0;

            $firstRow = true;
            $secondRow = false;
            $finished = false;
            foreach ($sheet->getRowIterator() as $row) {
                if ($firstRow) {
                    $firstRow = false;
                    $secondRow = true;
                    continue;
                } else if ($secondRow) {
                    $secondRow = false;
                    continue;
                }

                $dbProduct = new BaseModel($this->db, "product");
                $dbEntityProduct = new BaseModel($this->db, "entityProductSell");

                $product = [];

                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(FALSE);

                $errors = [];

                foreach ($cellIterator as $cell) {
                    $cellLetter = $cell->getColumn();
                    $cellValue = $cell->getCalculatedValue();

                    if ($cellValue === "#REF!") {
                        $cellValue = $cell->getOldCalculatedValue();
                    }

                    array_push($product, $cellValue);

                    switch ($cellLetter) {
                        case "A":
                            if (!in_array($cellValue, $allScientificId)) {
                                $finished = true;
                            } else {
                                $dbProduct->scientificNameId = $cellValue;
                            }
                            break;
                        case "B":
                            if (!in_array($cellValue, $allCountryId)) {
                                array_push($errors, "Made In invalid");
                            } else {
                                $dbProduct->madeInCountryId = $cellValue;
                            }
                            break;
                        case "C":
                            if (!$cellValue) {
                                array_push($errors, "Brand Name AR required");
                            } else {
                                $dbProduct->name_ar = $cellValue;
                            }
                            break;
                        case "D":
                            if (!$cellValue) {
                                array_push($errors, "Brand Name EN required");
                            } else {
                                $dbProduct->name_en = $cellValue;
                            }
                            break;
                        case "E":
                            if (!$cellValue) {
                                array_push($errors, "Brand Name FR required");
                            } else {
                                $dbProduct->name_fr = $cellValue;
                            }
                            break;
                        case "F":
                            $dbProduct->subtitle_ar = $cellValue;
                            break;
                        case "G":
                            $dbProduct->subtitle_en = $cellValue;
                            break;
                        case "H":
                            $dbProduct->subtitle_fr = $cellValue;
                            break;
                        case "I":
                            if (!$cellValue) {
                                array_push($errors, "Description AR required");
                            } else {
                                $dbProduct->description_ar = $cellValue;
                            }
                            break;
                        case "J":
                            if (!$cellValue) {
                                array_push($errors, "Description EN required");
                            } else {
                                $dbProduct->description_en = $cellValue;
                            }
                            break;
                        case "K":
                            if (!$cellValue) {
                                array_push($errors, "Description FR required");
                            } else {
                                $dbProduct->description_fr = $cellValue;
                            }
                            break;
                        case "L":
                            if (!is_numeric($cellValue) || (float)$cellValue < 0) {
                                array_push($errors, "Unit Price must be a positive number");
                            } else {
                                $dbEntityProduct->unitPrice = round((float)$cellValue, 2);
                            }
                            break;
                        case "M":
                            if (!filter_var($cellValue, FILTER_VALIDATE_INT) || (float)$cellValue < 0) {
                                array_push($errors, "Available Quantity must be a positive whole number");
                            } else {
                                $dbEntityProduct->stock = (int)$cellValue;
                            }
                            break;
                        case "N":
                            if (!filter_var($cellValue, FILTER_VALIDATE_INT) || (float)$cellValue < 0) {
                                array_push($errors, "Maximum Order Quantity must be a positive whole number");
                            } else {
                                $dbEntityProduct->maximumOrderQuantity = (int)$cellValue;
                            }
                            break;
                        case "O":
                            $dbProduct->manufacturerName = $cellValue;
                            break;
                        case "P":
                            $dbProduct->batchNumber = $cellValue;
                            break;
                        case "Q":
                            $dbProduct->itemCode = $cellValue;
                            break;
                        case "R":
                            if (!in_array($cellValue, $allCategoryId)) {
                                array_push($errors, "Category invalid");
                            } else {
                                $dbProduct->categoryId = $cellValue;
                            }
                            break;
                        case "S":
                            if (!in_array($cellValue, $allSubcategoryId)) {
                                array_push($errors, "Subcategory invalid");
                            } else {
                                $dbSubcategory->getWhere("id = $cellValue AND categoryId = $dbProduct->categoryId");
                                if($dbSubcategory->dry()) {
                                    array_push($errors, "Subcategory invalid");
                                } else {
                                    $dbProduct->subcategoryId = $cellValue;
                                }
                            }
                            break;
                        case "T":
                            if ($cellValue != "#N/A" && !in_array($cellValue, $allIngredientId)) {
                                array_push($errors, "Ingredient invalid");
                            } else {
                                $activeIngredientsId = $cellValue;
                            }
                            break;
                        case "U":
                            if (!is_null($cellValue)) {
                                if (!is_int($cellValue)) {
                                    array_push($errors, "Expiry Date must fit a date format (mm/dd/yyyy)");
                                } else {
                                    $expiryDate = Excel::excelDateToRegularDate($cellValue, "m/d/Y");
                                    $dbProduct->expiryDate = $expiryDate;
                                }
                            }
                            break;
                        case "V":
                            $dbProduct->strength = $cellValue;
                            break;
                    }
                }

                if ($finished) {
                    break;
                }

                $dbBulkAddUpload->recordsCount++;

                if (count($errors) === 0) {
                    $dbProduct->addReturnID();

                    $dbEntityProduct->productId = $dbProduct->id;
                    $dbEntityProduct->entityId = $entityId;
                    $dbEntityProduct->stockStatusId = 1;
                    $dbEntityProduct->bonusTypeId = 1;
                    $dbEntityProduct->stockUpdateDateTime = $dbEntityProduct->getCurrentDateTime();
                    $dbEntityProduct->add();

                    if($activeIngredientsId) {
                        $dbProductIngredient = new BaseModel($this->db, "productIngredient");
                        $dbProductIngredient->productId = $dbProduct->id;
                        $dbProductIngredient->ingredientId = $activeIngredientsId;
                        $dbProductIngredient->add();
                    }

                    array_push($successProducts, $product);
                    $successRecords++;
                } else {
                    array_push($failedProducts, $product);
                    array_push($allErrors, $errors);
                    $failedRecords++;
                }
            }

            if (count($failedProducts) > 0) {
                // Setup excel sheet
                $sheetnameUserInput = 'User Input';
                $sheetnameDatabaseInput = 'Database Input';
                $sheetnameVariables = 'Variables';

                // Prepare data for variables sheet
                $arrScientificName = [
                    ['Name', 'Value']
                ];
                $arrCountry = [
                    ['Name', 'Value']
                ];
                $arrCategory = [
                    ['Name', 'Value']
                ];
                $arrSubcategory = [
                    ['Name', 'Value']
                ];
                $arrIngredient = [
                    ['Name', 'Value']
                ];

                $scientificNum = 2;
                foreach ($allScientificName as $scientificName) {
                    $scientificNum++;
                    $arrScientificName[] = array($scientificName['name'], $scientificName['id']);
                }

                $countryNum = 2;
                foreach ($allCountry as $country) {
                    $countryNum++;
                    $arrCountry[] = array($country['name'], $country['id']);
                }

                $categoryNum = 2;
                foreach ($allCategory as $category) {
                    $categoryNum++;
                    $arrCategory[] = array($category['name'], $category['id']);
                }

                $subcategoryNum = 2;
                foreach ($allSubcategory as $subcategory) {
                    $subcategoryNum++;
                    $arrSubcategory[] = array($subcategory['name'], $subcategory['id']);
                }

                $ingredientNum = 2;
                foreach ($allIngredient as $ingredient) {
                    $ingredientNum++;
                    $arrIngredient[] = array($ingredient['name'], $ingredient['id']);
                }

                $sampleFilePath = 'app/files/samples/products-add-sample.xlsx';
                $spreadsheet = Excel::loadFile($sampleFilePath);

                // Change active sheet to variables
                $sheet = $spreadsheet->setActiveSheetIndex(2);

                // Set dropdown variables in excel
                $sheet->fromArray($arrScientificName, NULL, 'A2', true);
                $sheet->fromArray($arrCountry, NULL, 'D2', true);
                $sheet->fromArray($arrCategory, NULL, 'G2', true);
                $sheet->fromArray($arrSubcategory, NULL, 'J2', true);
                $sheet->fromArray($arrIngredient, NULL, 'M2', true);

                // Change active sheet to database input
                $sheet = $spreadsheet->setActiveSheetIndex(1);

                // Set validation and formula
                Excel::setCellFormulaVLookup($sheet, 'A3', 2505, "'User Input'!A", 'Variables!$A$3:$B$' . $scientificNum);
                Excel::setCellFormulaVLookup($sheet, 'B3', 2505, "'User Input'!B", 'Variables!$D$3:$E$' . $countryNum);
                Excel::setCellFormulaVLookup($sheet, 'R3', 2505, "'User Input'!R", 'Variables!$G$3:$H$' . $categoryNum);
                Excel::setCellFormulaVLookup($sheet, 'S3', 2505, "'User Input'!S", 'Variables!$J$3:$K$' . $subcategoryNum);
                Excel::setCellFormulaVLookup($sheet, 'T3', 2505, "'User Input'!T", 'Variables!$M$3:$N$' . $ingredientNum);

                // Hide database and variables sheet
                Excel::hideSheetByName($spreadsheet, $sheetnameDatabaseInput);
                Excel::hideSheetByName($spreadsheet, $sheetnameVariables);

                // Change active sheet to user input
                $sheet = $spreadsheet->setActiveSheetIndex(0);

                // Set data validation for dropdowns
                Excel::setDataValidation($sheet, 'A3', 'A2505', 'TYPE_LIST', 'Variables!$A$3:$A$' . $scientificNum);
                Excel::setDataValidation($sheet, 'B3', 'B2505', 'TYPE_LIST', 'Variables!$D$3:$D$' . $countryNum);
                Excel::setDataValidation($sheet, 'R3', 'R2505', 'TYPE_LIST', 'Variables!$G$3:$G$' . $categoryNum);
                Excel::setDataValidation($sheet, 'S3', 'S2505', 'TYPE_LIST', 'Variables!$J$3:$J$' . $subcategoryNum);
                Excel::setDataValidation($sheet, 'T3', 'T2505', 'TYPE_LIST', 'Variables!$M$3:$M$' . $ingredientNum);

                $sheet->setCellValue('W2', 'Error');
                $sheet->getStyle('W2')->applyFromArray(Excel::STYlE_CENTER_BOLD_BORDER_THICK);

                // Add all products to multidimensional array
                $multiProducts = [];
                $fields = [
                    "scientificNameId",
                    "madeInCountryId",
                    "name_en",
                    "name_ar",
                    "name_fr",
                    "subtitle_ar",
                    "subtitle_en",
                    "subtitle_fr",
                    "description_ar",
                    "description_en",
                    "description_fr",
                    "unitPrice",
                    "stock",
                    "maximumOrderQuantity",
                    "manufacturerName",
                    "batchNumber",
                    "itemCode",
                    "categoryId",
                    "subcategoryId",
                    "activeIngredientsId",
                    "expiryDate",
                    "strength"
                ];
                $i = 3;
                for ($i = 0; $i < count($failedProducts); $i++) {
                    $product = $failedProducts[$i];
                    $singleProduct = [];
                    $j = 0;
                    foreach ($fields as $field) {
                        $cellValue = "";
                        if ($field == "scientificNameId") {
                            $cellValue = $mapScientificIdName[$product[$j]];
                        } else if ($field == "madeInCountryId") {
                            $cellValue = $mapCountryIdName[$product[$j]];
                        } else if ($field == "categoryId") {
                            $cellValue = $mapCategoryIdName[$product[$j]];
                        } else if ($field == "subcategoryId") {
                            $cellValue = $mapSubcategoryIdName[$product[$j]];
                        } else if ($field == "activeIngredientsId") {
                            $cellValue = $mapIngredientIdName[$product[$j]];
                        } else if ($product[$j] !== 0) {
                            $cellValue = $product[$j];
                        }
                        array_push($singleProduct, $cellValue);
                        $j++;
                    }
                    $errors = $allErrors[$i];
                    $error = join(", ", $errors);
                    array_push($singleProduct, $error);

                    array_push($multiProducts, $singleProduct);
                }
                // Fill rows with products
                $sheet->fromArray($multiProducts, NULL, 'A3', true);

                // Create excel sheet
                $failedProductsSheetUrl = "files/downloads/reports/products-add/products-add-" . $this->objUser->id . "-" . time() . ".xlsx";
                Excel::saveSpreadsheetToPath($spreadsheet, $failedProductsSheetUrl);
            }

            // Update logs
            if (count($successProducts) > 0) {
                $dbBulkAddUpload->successLog = json_encode($successProducts);
            }

            if (count($failedProducts) > 0) {
                $dbBulkAddUpload->failedLog = json_encode($failedProducts);
            }

            // Update counts and rates
            $dbBulkAddUpload->completedCount = $successRecords;
            $dbBulkAddUpload->failedCount = $failedRecords;

            if ($successRecords + $failedRecords !== 0) {
                $dbBulkAddUpload->importSuccessRate = round($successRecords / ($successRecords + $failedRecords), 2) * 100;
                $dbBulkAddUpload->importFailureRate = round($failedRecords / ($successRecords + $failedRecords), 2) * 100;
            } else {
                $dbBulkAddUpload->importSuccessRate = 0;
                $dbBulkAddUpload->importFailureRate = 0;
            }

            $this->f3->set("objBulkAddUpload", $dbBulkAddUpload);
            if (!is_null($failedProductsSheetUrl)) {
                $this->f3->set("failedProductsSheetUrl", "/" . $failedProductsSheetUrl);
            }

            $dbBulkAddUpload->update();

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "";
            $this->webResponse->data = View::instance()->render('app/products/bulkAdd/uploadResult.php');
            echo $this->webResponse->jsonResponse();
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
        }
    }

    function getBulkAddImageUpload()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $this->webResponse->errorCode = 1;
            $this->webResponse->title = $this->f3->get('vModule_bulk_add_image_title');
            $this->webResponse->data = View::instance()->render('app/products/bulkAddImage/upload.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function postBulkAddImageUpload()
    {
        $mapNewOldFileName = [];
        $error = false;

        $fileCount = count($_FILES["file"]["name"]);
        for ($i = 0; $i < $fileCount; $i++) {
            $success = false;

            $fileName = pathinfo(basename($_FILES["file"]["name"][$i]), PATHINFO_FILENAME);
            $ext = pathinfo(basename($_FILES["file"]["name"][$i]), PATHINFO_EXTENSION);

            $newFileName = $fileName . "-" . time() . ".$ext";
            $targetFile = "assets/img/products/" . $newFileName;

            if ($ext == "png" || $ext == "jpg" || $ext == "jpeg") {
                if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], $targetFile)) {
                    $mapNewOldFileName[$newFileName] = $fileName;
                    $success = true;
                }
            }

            if (!$success) {
                $error = true;
                break;
            }
        }

        if (!$error) {
            echo json_encode($mapNewOldFileName);
        }
    }

    function postBulkAddImageUploadProcess()
    {
        $mapNewOldFileNameStr = $this->f3->get('POST.mapNewOldFileName');
        $mapNewOldFileName = json_decode($mapNewOldFileNameStr);

        // Get all product ids
        $allProductId = [];
        $dbProduct = new BaseModel($this->db, "product");
        $dbProduct->name = "name_" . $this->objUser->language;
        $allProduct = $dbProduct->findAll("id asc");

        // Check if file name starts with an existing product id
        $mapFileNameProduct = [];
        foreach ($mapNewOldFileName as $newFileName => $oldFileName) {
            $product = null;
            $allParts = explode("-", $oldFileName);
            if (count($allParts) > 1 && filter_var($allParts[0], FILTER_VALIDATE_INT)) {
                $productIdInitial = (int)$allParts[0];
                foreach ($allProduct as $prod) {
                    if ($productIdInitial == $prod['id']) {
                        $product = new stdClass();
                        $product->id = $prod['id'];
                        $product->name = $prod['name'];
                        break;
                    }
                }
            }

            $mapFileNameProduct[$newFileName] = $product;
        }

        $this->f3->set("mapFileNameProduct", $mapFileNameProduct);

        $this->webResponse->errorCode = 1;
        $this->webResponse->title = "";
        $this->webResponse->data = View::instance()->render('app/products/bulkAddImage/uploadResult.php');
        echo $this->webResponse->jsonResponse();
    }

    function getProductList()
    {
        $this->handleGetListFilters("product", ['name_en', 'name_fr', 'name_ar'], 'name_' . $this->objUser->language);
    }

    function postBulkAddImage()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/distributor/product/bulk/add/image/upload");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $mapProductIdImage = $this->f3->get('POST.mapProductIdImage');

            $dbProduct = new BaseModel($this->db, "product");
            foreach ($mapProductIdImage as $productId => $image) {
                $dbProduct->getWhere("id = $productId");
                if (!$dbProduct->dry()) {
                    $dbProduct->image = $image;
                    $dbProduct->update();
                }
            }

            echo $this->webResponse->jsonResponseV2(1, "Success", $this->f3->get('vResponse_imagesAdded'));
        }
    }
}