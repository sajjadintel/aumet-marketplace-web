<?php

class CartController extends Controller {

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
            $dbEntityProduct->getWhere("entityId=$entityId and productId=$productId");

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


                $newQuantity = $dbCartDetail->quantity + $quantity;
                $dbCartDetail->quantity = $newQuantity;

                if ($dbEntityProduct->bonusTypeId == 2) {
                    $dbBonus = new BaseModel($this->db, "entityProductSellBonusDetail");
                    $arrBonus = $dbBonus->findWhere("entityProductId = '$dbEntityProduct->id' AND isActive = 1", 'minOrder DESC');

                    $entityProductBonusType = new BaseModel($this->db, "entityProductBonusType");
                    $entityProductBonusType->getWhere("id = '$dbEntityProduct->bonusTypeId'");

                    $quantityFree = $this->calculateBonus($dbCartDetail->quantity, $arrBonus, $entityProductBonusType->formula);
                }

                $maxOrder = min($dbEntityProduct->stock, $dbEntityProduct->maximumOrderQuantity);
                $total = $quantityFree + $newQuantity;
                if ($total > $maxOrder) {
                    $this->webResponse->errorCode = Constants::STATUS_ERROR;
                    $this->webResponse->title = "";
                    $this->webResponse->message = "Not allowed (max: $maxOrder)";
                    echo $this->webResponse->jsonResponse();
                    return;
                }

                $dbCartDetail->quantityFree = $quantityFree;

                $dbCartDetail->unitPrice = $dbEntityProduct->unitPrice;
                $dbCartDetail->save();

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

            $dbCartDetail = new BaseModel($this->db, "cartDetail");
            $dbCartDetail->getByField("id", $id);
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
            $dbEntityProduct->getWhere("entityId=$entityId and productId=$productId");

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
                    $seller->name = $cartDetail[$nameField];
                    array_push($allSellers, $seller);
                }

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

            // Set paymenet methods
            $dbPaymentMethod = new BaseModel($this->db, "orderPaymentMethod");
            $nameField = "name_" . $this->objUser->language;
            $dbPaymentMethod->name = $nameField;
            $allPaymentMethods = $dbPaymentMethod->all();
            $this->f3->set('allPaymentMethods', $allPaymentMethods);

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
            $productId = $this->f3->get('POST.productId');
            $sellerId = $this->f3->get('POST.sellerId');
            $cartDetailId = $this->f3->get('POST.cartDetailId');
            $quantity = $this->f3->get('POST.quantity');

            $dbBonus = new BaseModel($this->db, "entityProductSellBonusDetail");
            $arrBonus = $dbBonus->findWhere("entityProductId = '$productId' AND isActive = 1");

            $quantityFree = 0;
            $maxMinOrder = 0;
            foreach ($arrBonus as $bonus) {
                if ($bonus['minOrder'] <= $quantity && $maxMinOrder < $bonus['minOrder']) {
                    $maxMinOrder = $bonus['minOrder'];
                    $quantityFree = $bonus['bonus'];
                }
            }

            $dbCartDetail = new BaseModel($this->db, "cartDetail");
            $dbCartDetail->getById($cartDetailId);
            $dbCartDetail->quantity = $quantity;
            $dbCartDetail->quantityFree = $quantityFree;

            $dbEntityProduct = new BaseModel($this->db, "entityProductSell");
            $dbEntityProduct->getWhere("productId=$productId");

            $maxOrder = min($dbEntityProduct->stock, $dbEntityProduct->maximumOrderQuantity);
            $total = $quantityFree + $quantity;
            if ($total > $maxOrder) {
                $this->webResponse->errorCode = Constants::STATUS_ERROR;
                $this->webResponse->title = "";
                $this->webResponse->message = "Not allowed (max: $maxOrder)";
                echo $this->webResponse->jsonResponse();
                return;
            }

            $dbCartDetail->update();

            $dbCartDetailFull = new BaseModel($this->db, "vwCartDetail");
            $cartDetailFull = $dbCartDetailFull->getById($cartDetailId)[0];

            // Get cart count
            $arrCartDetail = $dbCartDetail->getByField("accountId", $this->objUser->accountId);
            $cartCount = 0;
            foreach ($arrCartDetail as $cartDetail) {
                $cartCount += $cartDetail->quantity;
                $cartCount += $cartDetail->quantityFree;
            }

            $cartDetail = new stdClass();
            $cartDetail->productId = $cartDetailFull['productId'];
            $cartDetail->quantity = $cartDetailFull['quantity'];
            $cartDetail->quantityFree = $cartDetailFull['quantityFree'];
            $cartDetail->entityId = $cartDetailFull['entityId'];
            $cartDetail->cartCount = $cartCount;

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
            $dbEntityProduct->getWhere("entityId=$sellerId and productId=$productId");

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

    function getCartCheckoutSubmitConfirmation()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/cart/checkout");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $modal = new stdClass();
            $modal->modalTitle = $this->f3->get('vModule_cartCheckout_orderConfirmationTitle');
            $modal->modalText = $this->f3->get('vModule_cartCheckout_orderConfirmation');
            $modal->modalRoute = '/web/cart/checkout/submit/' . $this->f3->get('PARAMS.paymentMethodId');
            $modal->modalButton = $this->f3->get('vButton_confirm');
            $modal->id = $this->objUser->accountId;
            $modal->fnCallback = 'CartCheckout.submitOrderSuccess';

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

            $paymentMethodId = $this->f3->get('PARAMS.paymentMethodId');

            // Get user account
            $dbAccount = new BaseModel($this->db, "account");
            $account = $dbAccount->getById($this->objUser->accountId)[0];

            // TODO: Adjust buyerBranchId logic
            $dbEntityBranch = new BaseModel($this->db, "entityBranch");
            $entityBranch = $dbEntityBranch->getByField("entityId", $account->entityId)[0];

            // Add to orderGrand
            $dbOrderGrand = new BaseModel($this->db, "orderGrand");
            $dbOrderGrand->buyerEntityId = $account->entityId;
            $dbOrderGrand->buyerBranchId = $entityBranch->id;
            $dbOrderGrand->buyerUserId = $this->objUser->id;

            $dbOrderGrand->paymentMethodId = $paymentMethodId;

            $dbOrderGrand->addReturnID();
            $grandOrderId = $dbOrderGrand->id;

            $dbCartDetail = new BaseModel($this->db, "vwCartDetail");
            $nameField = "productName_" . $this->objUser->language;
            $dbCartDetail->name = $nameField;
            $arrCartDetail = $dbCartDetail->getByField("accountId", $this->objUser->accountId);

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

                // Add to order
                $dbOrder = new BaseModel($this->db, "order");
                $dbOrder->orderGrandId = $grandOrderId;
                $dbOrder->entityBuyerId = $account->entityId;
                $dbOrder->entitySellerId = $sellerId;
                $dbOrder->branchBuyerId = $entityBranch->id;
                $dbOrder->branchSellerId = $sellerEntityBranch->id;
                $dbOrder->userBuyerId = $this->objUser->id;
                $dbOrder->userSellerId = null;
                $dbOrder->statusId = 1;
                $dbOrder->paymentMethodId = $paymentMethodId;

                // TODO: Adjust serial logic
                $dbOrder->serial = mt_rand(100000, 999999);

                $dbOrder->currencyId = $currencyId;
                $dbOrder->subtotal = $subTotal;
                $dbOrder->vat = $tax;
                $dbOrder->total = $total;
                $dbOrder->addReturnID();

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

                $mapSellerIdOrderId[$sellerId] = $dbOrder->id;
                $this->f3->set('products', $cartItemsBySeller);
                $this->f3->set('currencySymbol', $mapSellerIdCurrency[$sellerId]->symbol);
                $this->f3->set('subTotal', round($subTotal, 2));
                $this->f3->set('tax', round($tax, 2));
                $this->f3->set('total', round($total, 2));
                $this->f3->set('ordersUrl', "web/distributor/order/new");
                $this->f3->set('name', "Buyer name: " . $buyerName);

                $arrEntityUserProfile = $dbEntityUserProfile->getByField("entityId", $sellerId);
                foreach ($arrEntityUserProfile as $entityUserProfile) {
                    $emailHandler->appendToAddress($entityUserProfile->userEmail, $entityUserProfile->userFullName);
                }
                $htmlContent = View::instance()->render($emailFile);

                $subject = "Aumet - New Order Confirmation (" . $dbOrder->id . ")";
                if (getenv('ENV') != Constants::ENV_PROD) {
                    $subject .= " - (Test: " . getenv('ENV') . ")";

                    if (getenv('ENV') == Constants::ENV_LOC) {
                        $emailHandler->resetTos();
                        $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                        $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
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

            $arrEntityUserProfile = $dbEntityUserProfile->getByField("entityId", $account->entityId);
            foreach ($arrEntityUserProfile as $entityUserProfile) {
                $emailHandler->appendToAddress($entityUserProfile->userEmail, $entityUserProfile->userFullName);
            }
            $htmlContent = View::instance()->render($emailFile);

            $subject = "Aumet - you've got a new order! (" . implode(", ", $allOrderId) . ")";
            if (getenv('ENV') != Constants::ENV_PROD) {
                $subject .= " - (Test: " . getenv('ENV') . ")";
                if (getenv('ENV') == Constants::ENV_LOC) {
                    $emailHandler->resetTos();
                    $emailHandler->appendToAddress("carl8smith94@gmail.com", "Antoine Abou Cherfane");
                    $emailHandler->appendToAddress("patrick.younes.1.py@gmail.com", "Patrick");
                }
            }
            $emailHandler->sendEmail(Constants::EMAIL_NEW_ORDER, $subject, $htmlContent);

            $allProductId = [];
            foreach ($arrCartDetail as $cartDetail) {
                array_push($allProductId, $cartDetail->entityProductId);
            }
            $allProductId = implode(",", $allProductId);

            $dbProduct = new BaseModel($this->db, "entityProductSell");
            $arrProduct = $dbProduct->findWhere("productId IN ($allProductId)");

            $mapProductIdVat = [];
            foreach ($arrProduct as $product) {
                $mapProductIdVat[$product['id']] = $product['vat'];
            }

            $commands = [];
            foreach ($arrCartDetail as $cartDetail) {
                $orderId = $mapSellerIdOrderId[$cartDetail->entityId];
                $entityProductId = $cartDetail->entityProductId;
                $quantity = $cartDetail->quantity;
                $note = $cartDetail->note;
                $quantityFree = $cartDetail->quantityFree;
                $unitPrice = $cartDetail->unitPrice;
                $tax = $mapProductIdVat[$entityProductId];

                $query = "INSERT INTO orderDetail (`orderId`, `entityProductId`, `quantity`, `note`, `quantityFree`, `unitPrice`, `tax`) VALUES ('" . $orderId . "', '" . $entityProductId . "', '" . $quantity . "', '" . $note . "', '" . $quantityFree . "', '" . $unitPrice . "', '" . $tax . "');";
                array_push($commands, $query);
            }

            $this->db->exec($commands);

            $dbCartDetail = new BaseModel($this->db, "cartDetail");
            $dbCartDetail->erase("accountId=" . $this->objUser->accountId);

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
}
