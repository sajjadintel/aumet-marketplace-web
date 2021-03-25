<?php

class CartController extends Controller
{

    function get()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/cart");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $dbCartDetail = new BaseModel($this->db, "vwCartDetail");
            $arrCartDetail = $dbCartDetail->getByField("accountId", $this->objUser->accountId);
            $this->f3->set('arrCartDetail', $arrCartDetail);

            $dbCartOffers = new BaseModel($this->db, "vwEntityProductSell");
            $arrCartOffers = $dbCartOffers->getWhere("stockStatusId=1 ", "id asc", 3);
            $this->f3->set('arrCartOffers', $arrCartOffers);

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vTitle_cart');
            $this->webResponse->data = View::instance()->render('app/cart/cart.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function postAddItem()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/cart");
            echo View::instance()->render('app/layout/layout.php');
        } else {

            $entityId = $this->f3->get('POST.entityId');
            $productId = $this->f3->get('POST.productId');
            $quantity = $this->f3->get('POST.quantity');

            if (!is_numeric($quantity) || $quantity == 0) {
                $quantity = 1;
            }

            $dbEntityProduct = new BaseModel($this->db, "entityProductSell");
            $dbEntityProduct->getWhere("entityId=$entityId and id=$productId");

            if ($dbEntityProduct->dry()) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->title = "";
                $this->webResponse->message = "No Product";
                echo $this->webResponse->jsonResponse();
            } else {
                $dbEntity = new BaseModel($this->db, "entity");
                $dbEntity->name = "name_" . $this->objUser->language;
                $dbEntity->getById($entityId);

                $dbProduct = new BaseModel($this->db, "product");
                $dbProduct->name = "name_" . $this->objUser->language;
                $dbProduct->getById($productId);

                $dbCartDetail = new BaseModel($this->db, "cartDetail");
                $dbCartDetail->getWhere("entityProductId = $dbEntityProduct->id and accountId=" . $this->objUser->accountId);
                $dbCartDetail->accountId = $this->objUser->accountId;
                $dbCartDetail->entityProductId = $dbEntityProduct->id;
                $dbCartDetail->userId = $this->objUser->id;
                $dbCartDetail->quantity += $quantity;

                $bonusDetail = BonusHelper::calculateBonusQuantity($this->f3, $this->db, $this->objUser->language, $productId, $quantity);
                $quantityFree = $bonusDetail->quantityFree;
                $maxOrder = $bonusDetail->maxOrder;
                $total = $bonusDetail->total;

                if ($total > $maxOrder) {
                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->title = "";
                    $quantity = BonusHelper::calculateBonusQuantity($this->f3, $this->db, $this->objUser->language, $productId, $total, true)->maxOrder;
                    $this->webResponse->message = "Not allowed (max: $quantity)";
                    echo $this->webResponse->jsonResponse();
                    return;
                }

                $dbCartDetail->quantityFree = $quantityFree;

                $dbCartDetail->unitPrice = $dbEntityProduct->unitPrice;
                $dbCartDetail->vat = $dbEntityProduct->vat;
                $dbCartDetail->save();

                // Get cart count
                $arrCartDetail = $dbCartDetail->getByField("accountId", $this->objUser->accountId);
                $cartCount = 0;
                foreach ($arrCartDetail as $cartDetail) {
                    $cartCount += $cartDetail->quantity;
                    $cartCount += $cartDetail->quantityFree;
                }
                $this->objUser->cartCount = $cartCount;

                $data = new stdClass();
                $data->cartCount = $cartCount;
                $data->activeBonus = $bonusDetail->activeBonus;

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
                $this->webResponse->title = "";
                $this->webResponse->data = $data;
                echo $this->webResponse->jsonResponse();
            }
        }
    }

    function calculateBonus($quantity, $bonuses, $formula)
    {
        foreach ($bonuses as $bonus) {
            if ($quantity >= $bonus['minOrder']) {
                $response = 0;
                try {
                    $formula = str_replace('quantity', $quantity, $formula);
                    $formula = str_replace('minOrder', $bonus['minOrder'], $formula);
                    $formula = str_replace('bonus', $bonus['bonus'], $formula);
                    if (strpos($formula, ';') === false) {
                        $formula .= ';';
                    }
                    $formula = '$response = ' . $formula;
                    eval($formula);
                    return $response;
                } catch (Exception $e) {
                    return 0;
                }
            }
        }
        return 0;
    }

    function postRemoveItem()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/cart");
            echo View::instance()->render('app/layout/layout.php');
        } else {

            $id = $this->f3->get('POST.id');
            $userId = $this->objUser->id;
            $entityProductId = $this->f3->get('POST.entityProductId');

            $dbCartDetail = new BaseModel($this->db, "cartDetail");
            if (!empty($userId) && !empty($entityProductId)) {
                $dbCartDetail->getWhere("entityProductId=$entityProductId and userID=$userId");
            } else {
                $dbCartDetail->getByField("id", $id);
            }

            $dbCartDetail->erase();

            // Get cart count
            $arrCartDetail = $dbCartDetail->getByField("accountId", $this->objUser->accountId);
            $cartCount = 0;
            foreach ($arrCartDetail as $cartDetail) {
                $cartCount += $cartDetail->quantity;
                $cartCount += $cartDetail->quantityFree;
            }
            $this->objUser->cartCount = $cartCount;

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "";
            $this->webResponse->data = $cartCount;
            echo $this->webResponse->jsonResponse();
        }
    }

    function getStatus()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/cart");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $dbCartDetail = new BaseModel($this->db, "cartDetail");
            $ItemsCount = $dbCartDetail->count("accountId=" . $this->objUser->accountId);

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "";
            $this->webResponse->data = new stdClass();
            $this->webResponse->data->itemsCount = $ItemsCount;
            echo $this->webResponse->jsonResponse();
        }
    }

    function postAddBonusItem()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/cart");
            echo View::instance()->render('app/layout/layout.php');
        } else {

            $entityId = $this->f3->get('POST.entityId');
            $productId = $this->f3->get('POST.productId');
            $bonusId = $this->f3->get('POST.bonusId');

            $dbEntityProduct = new BaseModel($this->db, "entityProductSell");
            $dbEntityProduct->getWhere("entityId=$entityId and id=$productId");

            if ($dbEntityProduct->dry()) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->title = "";
                $this->webResponse->message = "No Product";
                echo $this->webResponse->jsonResponse();
            } else {
                $dbEntity = new BaseModel($this->db, "entity");
                $dbEntity->name = "name_" . $this->objUser->language;
                $dbEntity->getById($entityId);

                $dbProduct = new BaseModel($this->db, "product");
                $dbProduct->name = "name_" . $this->objUser->language;
                $dbProduct->getById($productId);

                $dbBonus = new BaseModel($this->db, "entityProductSellBonusDetail");
                $dbBonus->getById($bonusId);

                $dbCartDetail = new BaseModel($this->db, "cartDetail");
                $dbCartDetail->accountId = $this->objUser->accountId;
                $dbCartDetail->entityProductId = $dbEntityProduct->id;
                $dbCartDetail->userId = $this->objUser->id;
                $dbCartDetail->quantity = $dbBonus->minOrder;
                $dbCartDetail->quantityFree = $dbBonus->bonus;
                $dbCartDetail->unitPrice = $dbEntityProduct->unitPrice;
                $dbCartDetail->vat = $dbEntityProduct->vat;

                $dbCartDetail->addReturnID();

                // Get cart count
                $arrCartDetail = $dbCartDetail->getByField("accountId", $this->objUser->accountId);
                $cartCount = 0;
                foreach ($arrCartDetail as $cartDetail) {
                    $cartCount += $cartDetail->quantity;
                    $cartCount += $cartDetail->quantityFree;
                }
                $this->objUser->cartCount = $cartCount;

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
                $this->webResponse->title = "";
                $this->webResponse->data = $cartCount;
                echo $this->webResponse->jsonResponse();
            }
        }
    }

    function getCartCheckout()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/cart/checkout");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $dbCartDetail = new BaseModel($this->db, "vwCartDetail");

            $nameField = "productName_" . $this->objUser->language;
            $dbCartDetail->name = $nameField;

            $arrCartDetail = $dbCartDetail->getByField("accountId", $this->objUser->accountId);

            // Get all product ids
            $arrProductId = [];
            $mapProductIdDetail = [];
            foreach ($arrCartDetail as $cartDetail) {
                array_push($arrProductId, $cartDetail['entityProductId']);

                $productDetail = new stdClass();
                $productDetail->quantity = $cartDetail['quantity'];
                $productDetail->stock = $cartDetail['stock'];
                $productDetail->maximumOrderQuantity = $cartDetail['maximumOrderQuantity'];
                $mapProductIdDetail[$cartDetail['entityProductId']] = $productDetail;
            }

            // Get all related bonuses
            $mapProductIdBonus = [];
            $mapBonusIdRelationGroup = [];
            $mapSellerIdRelationGroupId = [];
            if (count($arrProductId) > 0) {
                $dbBonus = new BaseModel($this->db, "vwEntityProductSellBonusDetail");
                $dbBonus->bonusTypeName = "bonusTypeName_" . $this->objUser->language;
                $arrBonus = $dbBonus->getWhere("entityProductId IN (" . implode(",", $arrProductId) . ") AND isActive = 1");
                $arrBonusId = [];
                foreach ($arrBonus as $bonus) {
                    array_push($arrBonusId, $bonus['id']);
                }

                // Get special bonuses
                if (count($arrBonusId) > 0) {
                    $dbBonusRelationGroup = new BaseModel($this->db, "entityProductSellBonusDetailRelationGroup");
                    $arrBonusRelationGroup = $dbBonusRelationGroup->getWhere("bonusId IN (" . implode(",", $arrBonusId) . ")");

                    foreach ($arrBonusRelationGroup as $bonusRelationGroup) {
                        $bonusId = $bonusRelationGroup['bonusId'];
                        $arrRelationGroup = [];
                        if (array_key_exists($bonusId, $mapBonusIdRelationGroup)) {
                            $arrRelationGroup = $mapBonusIdRelationGroup[$bonusId];
                        }

                        array_push($arrRelationGroup, $bonusRelationGroup['relationGroupId']);
                        $mapBonusIdRelationGroup[$bonusId] = $arrRelationGroup;
                    }
                }

                $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
                $dbEntityRelation = new BaseModel($this->db, "entityRelation");
                $arrEntityRelation = $dbEntityRelation->getWhere("entityBuyerId IN ($arrEntityId)");
                foreach ($arrEntityRelation as $entityRelation) {
                    $mapSellerIdRelationGroupId[$entityRelation['entitySellerId']] = $entityRelation['relationGroupId'];
                }

                foreach ($arrBonus as $bonus) {
                    $productId = $bonus['entityProductId'];
                    $arrProductBonus = [];
                    if (array_key_exists($productId, $mapProductIdBonus)) {
                        $arrProductBonus = $mapProductIdBonus[$productId];
                    }
                    array_push($arrProductBonus, $bonus);
                    $mapProductIdBonus[$productId] = $arrProductBonus;
                }
            }
            // Group cart items by seller id with their bonuses
            $allCartItems = [];
            $allSellers = [];
            foreach ($arrCartDetail as $cartDetail) {
                $sellerId = $cartDetail['entityId'];
                $productId = $cartDetail['entityProductId'];

                $cartItemsBySeller = [];
                if (array_key_exists($sellerId, $allCartItems)) {
                    $cartItemsBySeller = $allCartItems[$sellerId];
                } else {
                    $nameField = "entityName_" . $this->objUser->language;

                    $seller = new stdClass();
                    $seller->sellerId = $sellerId;
                    $seller->name = $cartDetail[$nameField];
                    array_push($allSellers, $seller);
                }

                $arrProductBonus = [];
                $activeBonus = new stdClass();
                $activeBonus->totalBonus = 0;
                if (array_key_exists($productId, $mapProductIdBonus)) {
                    $arrBonus = $mapProductIdBonus[$productId];
                    foreach ($arrBonus as $bonus) {
                        $bonusId = $bonus['id'];

                        // Check if bonus available for buyer
                        $valid = false;
                        if (array_key_exists($bonusId, $mapBonusIdRelationGroup)) {
                            $arrRelationGroup = $mapBonusIdRelationGroup[$bonusId];
                            if (array_key_exists($sellerId, $mapSellerIdRelationGroupId)) {
                                $relationGroupId = $mapSellerIdRelationGroupId[$sellerId];
                                if (in_array($relationGroupId, $arrRelationGroup)) {
                                    $valid = true;
                                }
                            }
                        } else {
                            $valid = true;
                        }

                        if (!$valid) {
                            continue;
                        }

                        $bonusType = $bonus['bonusTypeName'];
                        $bonusTypeId = $bonus['bonusTypeId'];
                        $bonusMinOrder = $bonus['minOrder'];
                        $bonusBonus = $bonus['bonus'];

                        // Check if bonus is possible
                        $productDetail = $mapProductIdDetail[$productId];
                        $availableQuantity = min($productDetail->stock, $productDetail->maximumOrderQuantity);

                        if (!$productDetail->maximumOrderQuantity)
                            $availableQuantity = $productDetail->stock;
                        if (!$productDetail->stock)
                            $availableQuantity = 0;

                        $totalOrder = 0;
                        if ($bonusTypeId == Constants::BONUS_TYPE_FIXED || $bonusTypeId == Constants::BONUS_TYPE_DYNAMIC) {
                            $totalOrder = $bonusMinOrder + $bonusBonus;
                        } else if ($bonusTypeId == Constants::BONUS_TYPE_PERCENTAGE) {
                            $totalOrder = $bonusMinOrder + floor($bonusBonus * $bonusMinOrder / 100);
                        }
                        if ($totalOrder > $availableQuantity) {
                            continue;
                        }

                        $totalBonus = 0;
                        if ($productDetail->quantity >= $bonusMinOrder) {
                            if ($bonusTypeId == Constants::BONUS_TYPE_FIXED) {
                                $totalBonus = $bonusBonus;
                            } else if ($bonusTypeId == Constants::BONUS_TYPE_DYNAMIC) {
                                $totalBonus = floor($productDetail->quantity / $bonusMinOrder) * $bonusBonus;
                            } else if ($bonusTypeId == Constants::BONUS_TYPE_PERCENTAGE) {
                                $totalBonus = floor($productDetail->quantity * $bonusBonus / 100);
                            }
                        }

                        if ($bonusTypeId == Constants::BONUS_TYPE_PERCENTAGE) {
                            $bonusBonus .= "%";
                        }

                        if ($totalBonus > $activeBonus->totalBonus) {
                            $activeBonus->bonusType = $bonusType;
                            $activeBonus->minQty = $bonusMinOrder;
                            $activeBonus->bonuses = $bonusBonus;
                            $activeBonus->totalBonus = $totalBonus;
                        }

                        $found = false;
                        for ($i = 0; $i < count($arrProductBonus); $i++) {
                            $productBonus = $arrProductBonus[$i];
                            if ($productBonus->bonusType == $bonusType) {
                                $arrMinQty = $productBonus->arrMinQty;
                                array_push($arrMinQty, $bonusMinOrder);
                                $productBonus->arrMinQty = $arrMinQty;

                                $arrBonuses = $productBonus->arrBonuses;
                                array_push($arrBonuses, $bonusBonus);
                                $productBonus->arrBonuses = $arrBonuses;

                                $arrProductBonus[$i] = $productBonus;
                                $found = true;
                                break;
                            }
                        }

                        if (!$found) {
                            $productBonus = new stdClass();
                            $productBonus->bonusType = $bonusType;
                            $productBonus->arrMinQty = [$bonusMinOrder];
                            $productBonus->arrBonuses = [$bonusBonus];
                            array_push($arrProductBonus, $productBonus);
                        }
                    }
                }
                $cartDetail->arrBonus = $arrProductBonus;
                $cartDetail->activeBonus = $activeBonus;

                array_push($cartItemsBySeller, $cartDetail);
                $allCartItems[$sellerId] = $cartItemsBySeller;
            }

            foreach ($allCartItems as $sellerId => $cartItemsBySeller) {
                // Sort cart items to get product followed by its bonuses
                usort($cartItemsBySeller, function ($c1, $c2) {
                    $productIdDiff = $c1->entityProductId - $c2->entityProductId;
                    if ($productIdDiff === 0) {
                        return $c1->quantityFree - $c2->quantityFree;
                    } else {
                        return $productIdDiff;
                    }
                });
                $allCartItems[$sellerId] = $cartItemsBySeller;
            }

            $this->f3->set('allCartItems', $allCartItems);
            $this->f3->set('allSellers', $allSellers);

            // Get all currencies
            $dbCurrencies = new BaseModel($this->db, "currency");
            $allCurrencies = $dbCurrencies->all();

            $mapCurrencyIdCurrency = [];
            foreach ($allCurrencies as $currency) {
                $currencyObj = new stdClass();
                $currencyObj->id = $currency->id;
                $currencyObj->symbol = $currency->symbol;
                $currencyObj->conversionToUSD = $currency->conversionToUSD;

                $mapCurrencyIdCurrency[$currency->id] = $currencyObj;
            }
            $this->f3->set('mapCurrencyIdCurrency', $mapCurrencyIdCurrency);

            // Get currency by entity
            $dbEntities = new BaseModel($this->db, "entity");
            $allEntities = $dbEntities->all();

            $mapSellerIdCurrency = [];
            foreach ($allEntities as $entity) {
                $mapSellerIdCurrency[$entity->id] = $mapCurrencyIdCurrency[$entity->currencyId];
            }
            $this->f3->set('mapSellerIdCurrency', $mapSellerIdCurrency);

            // Set buyer currency
            $dbAccount = new BaseModel($this->db, "account");
            $account = $dbAccount->getById($this->objUser->accountId)[0];
            $buyerCurrency = $mapSellerIdCurrency[$account->entityId];
            $this->f3->set('buyerCurrency', $buyerCurrency);

            // Set payment methods
            $dbPaymentMethod = new BaseModel($this->db, "paymentMethod");
            $nameField = "name_" . $this->objUser->language;
            $dbPaymentMethod->name = $nameField;
            $arrPaymentMethod = $dbPaymentMethod->findAll();
            $mapPaymentMethodIdName = [];
            foreach ($arrPaymentMethod as $paymentMethod) {
                $mapPaymentMethodIdName[$paymentMethod['id']] = $paymentMethod['name'];
            }

            $dbEntityPaymentMethod = new BaseModel($this->db, "entityPaymentMethod");
            $mapSellerIdArrPaymentMethod = [];
            foreach ($allSellers as $seller) {
                $dbEntityPaymentMethod->getWhere("entityId=" . $seller->sellerId);
                $arrEntityPaymentMethod = [];
                while (!$dbEntityPaymentMethod->dry()) {
                    $paymentMethod = new stdClass();
                    $paymentMethod->id = $dbEntityPaymentMethod['paymentMethodId'];
                    $paymentMethod->name = $mapPaymentMethodIdName[$dbEntityPaymentMethod['paymentMethodId']];

                    array_push($arrEntityPaymentMethod, $paymentMethod);
                    $dbEntityPaymentMethod->next();
                }

                $mapSellerIdArrPaymentMethod[$seller->sellerId] = $arrEntityPaymentMethod;
            }

            $this->f3->set('mapSellerIdArrPaymentMethod', $mapSellerIdArrPaymentMethod);

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vTitle_cart');
            $this->webResponse->data = View::instance()->render('app/cart/cartCheckout.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    function getRemoveItemConfirmation()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/cart/checkout");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $modal = new stdClass();
            $modal->modalTitle = $this->f3->get('vModule_cartCheckout_removalConfirmationTitle');
            $modal->modalText = $this->f3->get('vModule_cartCheckout_removalConfirmation');
            $modal->modalRoute = '/web/cart/remove';
            $modal->modalButton = $this->f3->get('vButton_confirm');
            $modal->id = $this->f3->get('PARAMS.itemId');
            $modal->fnCallback = 'CartCheckout.removeItemSuccess';

            $this->f3->set('modalArr', $modal);
            echo $this->webResponse->jsonResponseV2(1, "", "", $modal);
            return;
        }
    }

    function postCartCheckoutUpdate()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/cart/checkout");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $entityProductId = $this->f3->get('POST.productId');
            $cartDetailId = $this->f3->get('POST.cartDetailId');
            $quantity = $this->f3->get('POST.quantity');

            $dbCartDetail = new BaseModel($this->db, "cartDetail");
            $dbCartDetail->getById($cartDetailId);


            $bonusDetail = BonusHelper::calculateBonusQuantity($this->f3, $this->db, $this->objUser->language, $entityProductId, $quantity);
            $quantityFree = $bonusDetail->quantityFree;
            $maxOrder = $bonusDetail->maxOrder;
            $total = $bonusDetail->total;

            if ($total > $maxOrder) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->title = "";
                $quantity = BonusHelper::calculateBonusQuantity($this->f3, $this->db, $this->objUser->language, $entityProductId, $total, true)->maxOrder;
                $this->webResponse->message = "Not allowed (max: $quantity)";
                echo $this->webResponse->jsonResponse();
                return;
            }

            $dbCartDetail->quantity = $quantity;
            $dbCartDetail->quantityFree = $quantityFree;
            $dbCartDetail->update();

            $dbCartDetailFull = new BaseModel($this->db, "vwCartDetail");
            $cartDetailFull = $dbCartDetailFull->getById($cartDetailId)[0];

            // Get cart count
            $dbCartDetail = $this->db->exec("CALL spGetCartCount({$this->objUser->accountId})");
            if (count($dbCartDetail) > 0) {
                $this->objUser->cartCount = intval($dbCartDetail[0]['cartCount']);
            }

            $cartDetail = new stdClass();
            $cartDetail->productId = $cartDetailFull['entityProductId'];
            $cartDetail->quantity = $cartDetailFull['quantity'];
            $cartDetail->quantityFree = $cartDetailFull['quantityFree'];
            $cartDetail->entityId = $cartDetailFull['entityId'];
            $cartDetail->cartCount = $this->objUser->cartCount;
            $cartDetail->activeBonus = $bonusDetail->activeBonus;

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "";
            $this->webResponse->data = $cartDetail;
            echo $this->webResponse->jsonResponse();
        }
    }

    function postNoteCartCheckoutUpdate()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/cart/checkout");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $productId = $this->f3->get('POST.productId');
            $sellerId = $this->f3->get('POST.sellerId');
            $cartDetailId = $this->f3->get('POST.cartDetailId');
            $note = $this->f3->get('POST.note');

            $dbEntityProduct = new BaseModel($this->db, "entityProductSell");
            $dbEntityProduct->getWhere("entityId=$sellerId and id=$productId");

            $dbCartDetail = new BaseModel($this->db, "cartDetail");
            $dbCartDetail->getById($cartDetailId);
            $dbCartDetail->note = $note;
            $dbCartDetail->update();

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "";
            $this->webResponse->data = null;
            echo $this->webResponse->jsonResponse();
        }
    }

    function postCartCheckoutSubmitConfirmation()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/cart/checkout");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $modal = new stdClass();
            $modal->modalTitle = $this->f3->get('vModule_cartCheckout_orderConfirmationTitle');
            $modal->modalText = $this->f3->get('vModule_cartCheckout_orderConfirmation');
            $modal->modalRoute = '/web/cart/checkout/submit';
            $modal->modalButton = $this->f3->get('vButton_confirm');
            $modal->id = $this->objUser->accountId;
            $modal->fnCallback = 'CartCheckout.submitOrderSuccess';

            $modalBody = new stdClass();
            $modalBody->mapSellerIdPaymentMethodId = $this->f3->get('POST.mapSellerIdPaymentMethodId');
            $modal->body = $modalBody;

            $this->f3->set('modalArr', $modal);
            echo $this->webResponse->jsonResponseV2(1, "", "", $modal);
            return;
        }
    }

    function postCartCheckoutSubmit()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/cart/checkout");
            echo View::instance()->render('app/layout/layout.php');
        } else {

            $modalBody = $this->f3->get('POST.body');
            $mapSellerIdPaymentMethodId = $modalBody['mapSellerIdPaymentMethodId'];

            // Get user account
            $dbAccount = new BaseModel($this->db, "account");
            $account = $dbAccount->getById($this->objUser->accountId)[0];
            $dbUserAccount = new BaseModel($this->db, "userAccount");

            // TODO: Adjust buyerBranchId logic
            $dbEntityBranch = new BaseModel($this->db, "entityBranch");
            $entityBranch = $dbEntityBranch->getByField("entityId", $account->entityId)[0];

            $dbCity = new BaseModel($this->db, "city");
            $city = $dbCity->getByField("id", $dbEntityBranch['cityId'])[0];

            $dbCountry = new BaseModel($this->db, "country");
            $country = $dbCountry->getByField("id", $city['countryId'])[0];
            $buyerAddress = $country['name_en'].' - '.$city['nameEn'].' - '.$entityBranch['address_en'];

            // TODO: Adjust buyerBranchId logic
            $dbSellerUser = new BaseModel($this->db, "user");

            // Add to orderGrand
            $dbOrderGrand = new BaseModel($this->db, "orderGrand");
            $dbOrderGrand->buyerEntityId = $account->entityId;
            $dbOrderGrand->buyerBranchId = $entityBranch->id;
            $dbOrderGrand->buyerUserId = $this->objUser->id;

            $dbOrderGrand->addReturnID();
            $grandOrderId = $dbOrderGrand->id;

            $dbCartDetail = new BaseModel($this->db, "vwCartDetail");
            $nameField = "productName_" . $this->objUser->language;
            $dbCartDetail->name = $nameField;
            $arrCartDetail = $dbCartDetail->getByField("accountId", $this->objUser->accountId);

            // Get all payment methods
            $dbPaymentMethod = new BaseModel($this->db, "paymentMethod");
            $dbPaymentMethod->name = "name_" . $this->objUser->language;
            $arrPaymentMethod = $dbPaymentMethod->all();

            $mapPaymentMethodIdName = [];
            foreach ($arrPaymentMethod as $paymentMethod) {
                $mapPaymentMethodIdName[$paymentMethod['id']] = $paymentMethod['name'];
            }

            // Get all currencies
            $dbCurrencies = new BaseModel($this->db, "currency");
            $allCurrencies = $dbCurrencies->all();

            $mapCurrencyIdCurrency = [];
            foreach ($allCurrencies as $currency) {
                $mapCurrencyIdCurrency[$currency->id] = $currency;
            }

            // Get currency by entity
            $dbEntities = new BaseModel($this->db, "entity");
            $nameField = "name_" . $this->objUser->language;
            $dbEntities->name = $nameField;
            $allEntities = $dbEntities->all();

            $buyerName = "";
            $mapSellerIdCurrency = [];
            foreach ($allEntities as $entity) {
                $mapSellerIdCurrency[$entity->id] = $mapCurrencyIdCurrency[$entity->currencyId];
                if ($entity->id === $account->entityId) {
                    $buyerName = $entity->name;
                }
            }

            // Get buyer currency
            $dbAccount = new BaseModel($this->db, "account");
            $account = $dbAccount->getById($this->objUser->accountId)[0];
            $buyerCurrency = $mapSellerIdCurrency[$account->entityId];

            // Group cart items by seller id
            $allCartItems = [];
            $allSellers = [];
            foreach ($arrCartDetail as $cartDetail) {
                $sellerId = $cartDetail->entityId;

                $cartItemsBySeller = [];
                if (array_key_exists($sellerId, $allCartItems)) {
                    $cartItemsBySeller = $allCartItems[$sellerId];
                } else {
                    $nameField = "entityName_" . $this->objUser->language;

                    $seller = new stdClass();
                    $seller->sellerId = $sellerId;
                    $seller->name = $cartDetail->$nameField;
                    array_push($allSellers, $seller);
                }

                array_push($cartItemsBySeller, $cartDetail);
                $allCartItems[$sellerId] = $cartItemsBySeller;
            }

            $emailHandler = new EmailHandler($this->db);
            $emailFile = "email/layout.php";
            $this->f3->set('domainUrl', getenv('DOMAIN_URL'));
            $this->f3->set('title', 'New Order');
            $this->f3->set('emailType', 'newOrder');
            $this->f3->set('orderSubmittedAt', date("Y-m-d H:i:s"));

            $dbEntityUserProfile = new BaseModel($this->db, "vwEntityUserProfile");

            $allProducts = [];
            $allSellerNames = [];
            $allOrderId = [];
            $mapCurrencyIdSubTotal = [];
            $mapCurrencyIdTax = [];
            $mapCurrencyIdTotal = [];

            $mapSellerIdOrderId = [];
            foreach ($allSellers as $seller) {
                $sellerId = $seller->sellerId;
                $cartItemsBySeller = $allCartItems[$sellerId];

                $currencyId = $mapSellerIdCurrency[$sellerId]->id;

                $subTotal = 0;
                $tax = 0;
                foreach ($cartItemsBySeller as $cartItem) {
                    $productPrice = $cartItem->quantity * $cartItem->unitPrice;
                    $subTotal += $productPrice;
                    $tax += $productPrice * $cartItem->vat / 100;
                    array_push($allProducts, $cartItem);
                }

                $total = $subTotal + $tax;

                if (array_key_exists($currencyId, $mapCurrencyIdSubTotal)) {
                    $mapCurrencyIdSubTotal[$currencyId] += $subTotal;
                } else {
                    $mapCurrencyIdSubTotal[$currencyId] = $subTotal;
                }

                if (array_key_exists($currencyId, $mapCurrencyIdTax)) {
                    $mapCurrencyIdTax[$currencyId] += $tax;
                } else {
                    $mapCurrencyIdTax[$currencyId] = $tax;
                }

                if (array_key_exists($currencyId, $mapCurrencyIdTotal)) {
                    $mapCurrencyIdTotal[$currencyId] += $total;
                } else {
                    $mapCurrencyIdTotal[$currencyId] = $total;
                }

                // TODO: Adjust sellerBranchId logic
                $sellerEntityBranch = $dbEntityBranch->getByField("entityId", $sellerId)[0];

                // TODO: Remove this when multiple user per account is managed
                $sellerUser = $dbEntityBranch->getByField("entityId", $sellerId)[0];

                // Add to order
                $dbOrder = new BaseModel($this->db, "order");
                $dbOrder->orderGrandId = $grandOrderId;
                $dbOrder->entityBuyerId = $account->entityId;
                $dbOrder->entitySellerId = $sellerId;
                $dbOrder->branchBuyerId = $entityBranch->id;
                $dbOrder->branchSellerId = $sellerEntityBranch->id;
                $dbOrder->userBuyerId = $this->objUser->id;
                // TODO: Remove this when multiple user per account is managed

                $sellerAccount = $dbAccount->getByField("entityId", $sellerId)[0];
                $sellerUserAccount = $dbUserAccount->getByField("accountId", $sellerAccount->id)[0];

                $dbOrder->userSellerId = $sellerUserAccount->userId;
                $dbOrder->statusId = 1;

                $paymentMethodId = $mapSellerIdPaymentMethodId[$sellerId];
                $dbOrder->paymentMethodId = $paymentMethodId;

                // TODO: Adjust serial logic
                $dbOrder->serial = mt_rand(100000, 999999);

                $dbOrder->currencyId = $currencyId;
                $dbOrder->subtotal = $subTotal;
                $dbOrder->vat = $tax;
                $dbOrder->total = $total;

                // Add the relation
                $dbRelation = new BaseModel($this->db, "entityRelation");
                $dbRelation->getWhere("entityBuyerId = $dbOrder->entityBuyerId AND entitySellerId = $dbOrder->entitySellerId");

                if ($dbRelation->dry()) {
                    $dbRelation->entityBuyerId = $dbOrder->entityBuyerId;
                    $dbRelation->entitySellerId = $dbOrder->entitySellerId;
                    $dbRelation->currencyId = $dbOrder->currencyId;
                    $dbRelation->orderCount = 1;
                    $dbRelation->orderTotal = $dbOrder->total;
                    $dbRelation->add();
                } else {
                    $dbRelation->orderCount++;
                    $dbRelation->orderTotal += $dbOrder->total;
                    $dbRelation->updatedAt = date('Y-m-d H:i:s');
                    $dbRelation->update();
                }

                $dbOrder->relationGroupId = $dbRelation->relationGroupId;
                $dbOrder->addReturnID();

                $mapSellerIdOrderId[$sellerId] = $dbOrder->id;
                $this->f3->set('products', $cartItemsBySeller);
                $this->f3->set('currencySymbol', $mapSellerIdCurrency[$sellerId]->symbol);
                $this->f3->set('subTotal', round($subTotal, 2));
                $this->f3->set('tax', round($tax, 2));
                $this->f3->set('total', round($total, 2));
                $this->f3->set('ordersUrl', "web/distributor/order/pending");
                $this->f3->set('name', "Buyer name: " . $buyerName);
                $this->f3->set('buyerFullUserName', "User Name: " . $this->objUser->fullname);
                $this->f3->set('buyerEmail', "Email: " . $this->objUser->email);
                $this->f3->set('buyerAddress', "Address: " . $buyerAddress);
                $this->f3->set('paymentMethod', $mapPaymentMethodIdName[$paymentMethodId]);

                $arrEntityUserProfile = $dbEntityUserProfile->getByField("entityId", $sellerId);
                foreach ($arrEntityUserProfile as $entityUserProfile) {
                    $emailHandler->appendToAddress($entityUserProfile->userEmail, $entityUserProfile->userFullName);
                }
                $htmlContent = View::instance()->render($emailFile);

                $subject = "Aumet - you've got a new order! (" . $dbOrder->id . ")";
                if (getenv('ENV') != Constants::ENV_PROD) {
                    $subject .= " - (Test: " . getenv('ENV') . ")";

                    if (getenv('ENV') == Constants::ENV_LOC) {
                        $emailHandler->resetTos();
                        $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                        $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
                        $emailHandler->appendToAddress("sajjadintel@gmail.com", "Sajad");
                        $emailHandler->appendToAddress("n.javaid@aumet.com", "Naveed Javaid");
                    }
                }

                $emailHandler->sendEmail(Constants::EMAIL_NEW_ORDER, $subject, $htmlContent);
                $emailHandler->resetTos();

                array_push($allSellerNames, $seller->name);
                array_push($allOrderId, $dbOrder->id);
            }

            $subTotalUSD = 0;
            $taxUSD = 0;
            $totalUSD = 0;
            foreach ($mapCurrencyIdCurrency as $currencyId => $currency) {
                if (array_key_exists($currencyId, $mapCurrencyIdSubTotal)) {
                    $subTotal = $mapCurrencyIdSubTotal[$currencyId];
                    $subTotalUSD += $subTotal * $currency->conversionToUSD;
                }

                if (array_key_exists($currencyId, $mapCurrencyIdTax)) {
                    $tax = $mapCurrencyIdTax[$currencyId];
                    $taxUSD += $tax * $currency->conversionToUSD;
                }

                if (array_key_exists($currencyId, $mapCurrencyIdTotal)) {
                    $total = $mapCurrencyIdTotal[$currencyId];
                    $totalUSD += $total * $currency->conversionToUSD;
                }
            }

            $subTotal = $subTotalUSD / $buyerCurrency->conversionToUSD;
            $tax = $taxUSD / $buyerCurrency->conversionToUSD;
            $total = $totalUSD / $buyerCurrency->conversionToUSD;

            $this->f3->set('products', $allProducts);
            $this->f3->set('currencySymbol', $buyerCurrency->symbol);
            $this->f3->set('subTotal', round($subTotal, 2));
            $this->f3->set('tax', round($tax, 2));
            $this->f3->set('total', round($total, 2));
            $this->f3->set('ordersUrl', "web/pharmacy/order/history");

            $name = count($allSellerNames) > 1 ? "Seller names: " : "Seller name: ";
            $name .= implode(", ", $allSellerNames);
            $this->f3->set('name', $name);

            $this->f3->set('paymentMethod', null);

            $arrEntityUserProfile = $dbEntityUserProfile->getByField("entityId", $account->entityId);
            foreach ($arrEntityUserProfile as $entityUserProfile) {
                $emailHandler->appendToAddress($entityUserProfile->userEmail, $entityUserProfile->userFullName);
            }
            $htmlContent = View::instance()->render($emailFile);

            if (count($allOrderId) > 1) {
                $subject = "Aumet - New Orders Confirmation (" . implode(", ", $allOrderId) . ")";
            } else {
                $subject = "Aumet - New Order Confirmation (" . implode(", ", $allOrderId) . ")";
            }
            if (getenv('ENV') != Constants::ENV_PROD) {
                $subject .= " - (Test: " . getenv('ENV') . ")";
                if (getenv('ENV') == Constants::ENV_LOC) {
                    $emailHandler->resetTos();
                    $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                    $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
                    $emailHandler->appendToAddress("sajjadintel@gmail.com", "Sajad");
                }
            }
            $emailHandler->sendEmail(Constants::EMAIL_NEW_ORDER, $subject, $htmlContent);

            $allProductId = [];
            foreach ($arrCartDetail as $cartDetail) {
                array_push($allProductId, $cartDetail->entityProductId);
            }
            $allProductId = implode(",", $allProductId);

            $dbProduct = new BaseModel($this->db, "entityProductSell");
            $arrProduct = $dbProduct->findWhere("id IN ($allProductId)");

            $commands = [];
            foreach ($arrCartDetail as $cartDetail) {
                $orderId = $mapSellerIdOrderId[$cartDetail->entityId];
                $entityProductId = $cartDetail->entityProductId;
                $quantity = $cartDetail->quantity;
                $note = $cartDetail->note;
                $quantityFree = $cartDetail->quantityFree;
                $unitPrice = $cartDetail->unitPrice;
                $vat = $cartDetail->vat;
                $totalQuantity = $quantity + $quantityFree;
                $freeRatio = $quantityFree / ($quantity + $quantityFree);

                $query = "INSERT INTO orderDetail (`orderId`, `entityProductId`, `quantity`, `quantityFree`, `freeRatio`, `requestedQuantity`, `shippedQuantity`, `note`, `unitPrice`, `tax`) VALUES "
                    . "('" . $orderId . "', '" . $entityProductId . "', '" . $quantity . "', '" . $quantityFree . "', '" . $freeRatio . "', '" . $totalQuantity . "', '" . $totalQuantity . "', '" . $note . "', '" . $unitPrice . "', '" . $vat . "');";

                array_push($commands, $query);
            }

            $cartDetails = (new BaseModel($this->db, 'vwCartDetail'))->find(['accountId = ?', $this->objUser->accountId]);
            $this->sendPushNotification($dbOrder, $cartDetails);

            $this->db->exec($commands);

            $dbCartDetail = new BaseModel($this->db, "cartDetail");
            $dbCartDetail->erase("accountId=" . $this->objUser->accountId);
            $this->objUser->cartCount = 0;

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = "";
            $this->webResponse->data = $grandOrderId;
            echo $this->webResponse->jsonResponse();
        }
    }

    public function getThankyou()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/thankyou/" . $this->f3->get("PARAMS.grandOrderId"));
            echo View::instance()->render('app/layout/layout.php');
        } else {

            if (!$this->f3->get("PARAMS.grandOrderId") || !is_numeric($this->f3->get("PARAMS.grandOrderId"))) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->title = "";
                $this->webResponse->message = "Invalid Grand Order Id";
                echo $this->webResponse->jsonResponse();
                return;
            }

            $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
            $query = "buyerEntityId IN ($arrEntityId)";
            $query .= " AND id= " . $this->f3->get("PARAMS.grandOrderId");

            $dbOrderGrand = new BaseModel($this->db, "orderGrand");
            $grandOrder = $dbOrderGrand->getWhere($query);

            if (sizeof($grandOrder) === 0) {
                $this->webResponse->errorCode = Constants::STATUS_CODE_REDIRECT_TO_WEB;
                echo $this->webResponse->jsonResponse();
                return;
            }
            $grandOrder = $grandOrder[0];

            $dbOrders = new BaseModel($this->db, "order");
            $dbOrders = $dbOrders->getWhere(" orderGrandId = " . $grandOrder->id);

            $this->f3->set('allOrders', $dbOrders);

            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vTitle_cart');
            $this->webResponse->data = View::instance()->render('app/cart/thankyou.php');
            echo $this->webResponse->jsonResponse();
        }
    }

    private function sendPushNotification($order, $cartDetails)
    {
        if (!array_key_exists($order->statusId, FcmNotification::AVAILABLE_NOTIFICATIONS)) {
            return;
        }

        $notificationClassName = FcmNotification::AVAILABLE_NOTIFICATIONS[$order->statusId];
        $notificationInstance = new $notificationClassName();
        $user = (new BaseModel($this->db, 'user'))->find(['id = ?', $order->userSellerId]);
        $notificationInstance->send($user, $cartDetails);
    }
}
