<?php

class MarketingController extends Controller
{
    function getMarketingPromotions()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vModule_promotion_title');
            $this->webResponse->data = View::instance()->render('app/marketing/promotions/home.php');
            echo $this->webResponse->jsonResponse();
        }
    }
    
    function postMarketingPromotions()
    {
        ## Read values from Datatables
        $datatable = new Datatable($_POST);

        $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
        $query = "isActive = 1 AND entityId IN ($arrEntityId)";

        $fullQuery = $query;

        $dbData = new BaseModel($this->db, "entityPromotion");
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
    
    function getMarketingAddPromotions() 
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));

            $dbData = new BaseModel($this->db, "entityPromotion");
            $totalRecords = $dbData->count("isActive = 1 AND entityId IN ($arrEntityId)");
            if($totalRecords == Constants::MAX_DISTRIBUTOR_PROMOTION_COUNT) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->message = $this->f3->get('vModule_promotion_addLimit', Constants::MAX_DISTRIBUTOR_PROMOTION_COUNT);
                echo $this->webResponse->jsonResponse();
                return;
            }

            // Get products
            $dbEntityProduct = new BaseModel($this->db, "vwEntityProductSellSummary");
            $dbEntityProduct->productName = "productName_" . $this->objUser->language;
            $arrProductsDb = $dbEntityProduct->findWhere("entityId IN ($arrEntityId)");
            $arrProducts = [];
            foreach($arrProductsDb as $productDb) {
                $product = new stdClass();
                $product->id = $productDb['productId'];
                $product->name = $productDb['productName'];
                $product->image = $productDb['productImage'];

                array_push($arrProducts, $product);
            }
            $this->f3->set('arrProducts', $arrProducts);

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vModule_promotion_addTitle');
            $this->webResponse->data = View::instance()->render('app/marketing/promotions/add.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function postMarketingAddPromotions() 
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/distributor/product");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));

            $dbData = new BaseModel($this->db, "entityPromotion");
            $totalRecords = $dbData->count("isActive = 1 AND entityId IN ($arrEntityId)");
            if($totalRecords == Constants::MAX_DISTRIBUTOR_PROMOTION_COUNT) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->message = $this->f3->get('vModule_promotion_addLimit', Constants::MAX_DISTRIBUTOR_PROMOTION_COUNT);
                echo $this->webResponse->jsonResponse();
                return;
            }
            
            $name = $this->f3->get('POST.name');
            $active = $this->f3->get('POST.active');
            $startDate = $this->f3->get('POST.startDate');
            $endDate = $this->f3->get('POST.endDate');
            $image = $this->f3->get('POST.image');
            $message = $this->f3->get('POST.message');
            $arrFeaturedProducts = $this->f3->get('POST.arrFeaturedProducts');

            if (strlen($name) == 0 || strlen($startDate) == 0 || strlen($endDate) == 0 || strlen($message) == 0) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->message = $this->f3->get('vModule_promotion_missingFields');
                echo $this->webResponse->jsonResponse();
                return;
            }

            $startDateTime = strtotime($startDate);
            $endDateTime = strtotime($endDate);
            if($endDateTime < $startDateTime) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->message = $this->f3->get('vModule_promotion_endDateInvalid');
                echo $this->webResponse->jsonResponse();
                return;
            }
            
            $entityId = $arrEntityId;

            $this->checkLength($name, 'name', 200);
            $this->checkLength($message, 'message', 5000);

            if (!$arrFeaturedProducts) {
                $arrFeaturedProducts = [];
            }
            
            $arrProductId = [];
            foreach($arrFeaturedProducts as $featuredProduct) {
                $productId = $featuredProduct['productId'];
                if(strlen($productId) > 0) {
                    if(in_array($productId, $arrProductId)) {
                        $this->webResponse->errorCode = Constants::STATUS_ERROR;
                        $this->webResponse->message = $this->f3->get('vModule_promotion_duplicateFeaturedProduct');
                        echo $this->webResponse->jsonResponse();
                        return;
                    } else {
                        array_push($arrProductId, $productId);
                    }
                } else {
                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->message = $this->f3->get('vModule_promotion_featuredProductInvalid');
                    echo $this->webResponse->jsonResponse();
                    return;
                }
            }

            $dbEntityPromotion = new BaseModel($this->db, "entityPromotion");
            $dbEntityPromotion->entityId = $entityId;
            $dbEntityPromotion->name = $name;
            $dbEntityPromotion->image = $image;
            $dbEntityPromotion->startDate = $startDate;
            $dbEntityPromotion->endDate = $endDate;
            $dbEntityPromotion->message = $message;
            $dbEntityPromotion->isActive = 1;

            if(strlen($active) > 0) {
                $dbEntityPromotion->active = 1;
            } else {
                $dbEntityPromotion->active = 0;
            }

            $dbEntityPromotion->addReturnID();

            $dbEntityPromotionProduct = new BaseModel($this->db, "entityPromotionProduct");
            foreach($arrFeaturedProducts as $featuredProduct) {
                $dbEntityPromotionProduct->entityPromotionId = $dbEntityPromotion->id;
                $dbEntityPromotionProduct->productId = $featuredProduct['productId'];
                $dbEntityPromotionProduct->add();
            }

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS_SHOW_DIALOG;
            $this->webResponse->message = $this->f3->get('vModule_promotion_added');
            echo $this->webResponse->jsonResponse();
        }
    }

    function getMarketingEditPromotions() 
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $promotionId = $this->f3->get('PARAMS.promotionId');

            // Check if product belongs to distributor
            $dbPromotion = new BaseModel($this->db, "entityPromotion");
            $dbPromotion->getWhere("id=$promotionId");

            if (!array_key_exists((string) $dbPromotion['entityId'], $this->f3->get('SESSION.arrEntities'))) {
                $this->webResponse->errorCode = Constants::STATUS_CODE_REDIRECT_TO_WEB;
                echo $this->webResponse->jsonResponse();
                return;
            }
            
            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));

            $this->f3->set('promotion', $dbPromotion);
            
            // Get related products
            $dbEntityProductPromotion = new BaseModel($this->db, "vwEntityPromotionProduct");
            $dbEntityProductPromotion->productName = "productName_" . $this->objUser->language;
            $arrPromotionProductsDb = $dbEntityProductPromotion->findWhere("entityPromotionId=$promotionId");
            $arrPromotionProducts = [];
            foreach($arrPromotionProductsDb as $promotionProductDb) {
                $promotionProduct = new stdClass();
                $promotionProduct->id = $promotionProductDb['productId'];
                $promotionProduct->name = $promotionProductDb['productName'];

                array_push($arrPromotionProducts, $promotionProduct);
            }
            $this->f3->set('arrFeaturedProducts', $arrPromotionProducts);

            // Get products
            $dbEntityProduct = new BaseModel($this->db, "vwEntityProductSellSummary");
            $dbEntityProduct->productName = "productName_" . $this->objUser->language;
            $arrProductsDb = $dbEntityProduct->findWhere("entityId IN ($arrEntityId)");
            $arrProducts = [];
            foreach($arrProductsDb as $productDb) {
                $product = new stdClass();
                $product->id = $productDb['productId'];
                $product->name = $productDb['productName'];
                $product->image = $productDb['productImage'];

                array_push($arrProducts, $product);
            }
            $this->f3->set('arrProducts', $arrProducts);

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vModule_promotion_editTitle');
            $this->webResponse->data = View::instance()->render('app/marketing/promotions/edit.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function postMarketingEditPromotions() 
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/distributor/product");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $promotionId = $this->f3->get('POST.promotionId');

            $dbEntityPromotion = new BaseModel($this->db, "entityPromotion");
            $dbEntityPromotion->getWhere("id=$promotionId");

            if ($dbEntityPromotion->dry()) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->message = $this->f3->get('vModule_promotion_notFound');
                echo $this->webResponse->jsonResponse();
            } else {
                $name = $this->f3->get('POST.name');
                $active = $this->f3->get('POST.active');
                $startDate = $this->f3->get('POST.startDate');
                $endDate = $this->f3->get('POST.endDate');
                $image = $this->f3->get('POST.image');
                $message = $this->f3->get('POST.message');
                $arrFeaturedProducts = $this->f3->get('POST.arrFeaturedProducts');

                if (strlen($name) == 0 || strlen($startDate) == 0 || strlen($endDate) == 0 || strlen($message) == 0) {
                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->message = $this->f3->get('vModule_promotion_missingFields');
                    echo $this->webResponse->jsonResponse();
                    return;
                }

                $startDateTime = strtotime($startDate);
                $endDateTime = strtotime($endDate);
                if($endDateTime < $startDateTime) {
                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->message = $this->f3->get('vModule_promotion_endDateInvalid');
                    echo $this->webResponse->jsonResponse();
                    return;
                }
                
                $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
                $entityId = $arrEntityId;

                $this->checkLength($name, 'name', 200);
                $this->checkLength($message, 'message', 5000);

                if (!$arrFeaturedProducts) {
                    $arrFeaturedProducts = [];
                }
                
                $arrProductId = [];
                foreach($arrFeaturedProducts as $featuredProduct) {
                    $productId = $featuredProduct['productId'];
                    if(strlen($productId) > 0) {
                        if(in_array($productId, $arrProductId)) {
                            $this->webResponse->errorCode = Constants::STATUS_ERROR;
                            $this->webResponse->message = $this->f3->get('vModule_promotion_duplicateFeaturedProduct');
                            echo $this->webResponse->jsonResponse();
                            return;
                        } else {
                            array_push($arrProductId, $productId);
                        }
                    } else {
                        $this->webResponse->errorCode = Constants::STATUS_ERROR;
                        $this->webResponse->message = $this->f3->get('vModule_promotion_featuredProductInvalid');
                        echo $this->webResponse->jsonResponse();
                        return;
                    }
                }

                $dbEntityPromotion->entityId = $entityId;
                $dbEntityPromotion->name = $name;
                $dbEntityPromotion->image = $image;
                $dbEntityPromotion->startDate = $startDate;
                $dbEntityPromotion->endDate = $endDate;
                $dbEntityPromotion->message = $message;

                if(strlen($active) > 0) {
                    $dbEntityPromotion->active = 1;
                } else {
                    $dbEntityPromotion->active = 0;
                }

                $dbEntityPromotion->update();

                $dbEntityPromotionProduct = new BaseModel($this->db, "entityPromotionProduct");
                $dbEntityPromotionProduct->getWhere("entityPromotionId=$promotionId");
                while (!$dbEntityPromotionProduct->dry()) {
                    $dbEntityPromotionProduct->delete();
                    $dbEntityPromotionProduct->next();
                }

                foreach($arrFeaturedProducts as $featuredProduct) {
                    $dbEntityPromotionProduct->entityPromotionId = $dbEntityPromotion->id;
                    $dbEntityPromotionProduct->productId = $featuredProduct['productId'];
                    $dbEntityPromotionProduct->add();
                }

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS_SHOW_DIALOG;
                $this->webResponse->message = $this->f3->get('vModule_promotion_edited');
                echo $this->webResponse->jsonResponse();
            }
        }
    }

    function postMarketingBannerUploadPromotion()
    {
        $allValidExtensions = [
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

    function getMarketingDeletePromotions()
    {
        if (!$this->f3->ajax()) {
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $promotionId = $this->f3->get('PARAMS.promotionId');

            // Check if product belongs to distributor
            $dbPromotion = new BaseModel($this->db, "entityPromotion");
            $dbPromotion->getWhere("id=$promotionId");

            if (!array_key_exists((string) $dbPromotion['entityId'], $this->f3->get('SESSION.arrEntities'))) {
                $this->webResponse->errorCode = Constants::STATUS_CODE_REDIRECT_TO_WEB;
                echo $this->webResponse->jsonResponse();
                return;
            }

            $dbPromotion->isActive = 0;
            $dbPromotion->update();

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->message = $this->f3->get('vModule_promotion_deleted');
            echo $this->webResponse->jsonResponse();
        }
    }
}
