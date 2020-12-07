<?php

class CartController extends Controller
{

    function get()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/cart");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            global $dbConnection;

            $dbCartDetail = new BaseModel($dbConnection, "vwCartDetail");
            $arrCartDetail = $dbCartDetail->getByField("accountId", $this->objUser->accountId);
            $this->f3->set('arrCartDetail', $arrCartDetail);

            $dbCartOffers = new BaseModel($dbConnection, "vwEntityProductSell");
            $arrCartOffers = $dbCartOffers->getWhere("stockStatusId=1 ", "id asc", 3);
            $this->f3->set('arrCartOffers', $arrCartOffers);

            $this->webResponse->errorCode = 1;
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

            global $dbConnection;

            $dbEntityProduct = new BaseModel($dbConnection, "entityProductSell");
            $dbEntityProduct->getWhere("entityId=$entityId and productId=$productId");

            if ($dbEntityProduct->dry()) {
                $this->webResponse->errorCode = 2;
                $this->webResponse->title = "";
                $this->webResponse->message = "No Product";
                echo $this->webResponse->jsonResponse();
            } else {
                $dbEntity = new BaseModel($dbConnection, "entity");
                $dbEntity->name = "name_" . $this->objUser->language;
                $dbEntity->getById($entityId);

                $dbProduct = new BaseModel($dbConnection, "product");
                $dbProduct->name = "name_" . $this->objUser->language;
                $dbProduct->getById($productId);

                $dbCartDetail = new BaseModel($dbConnection, "cartDetail");
                $dbCartDetail->getWhere("entityProductId = $dbEntityProduct->id and accountId=" . $this->objUser->accountId);
                $dbCartDetail->accountId = $this->objUser->accountId;
                $dbCartDetail->entityProductId = $dbEntityProduct->id;
                $dbCartDetail->userId = $this->objUser->id;
                $dbCartDetail->quantity = $dbCartDetail->quantity + $quantity;

                $dbCartDetail->unitPrice = $dbEntityProduct->unitPrice;
                $dbCartDetail->save();

                $this->webResponse->errorCode = 1;
                $this->webResponse->title = "";
                $this->webResponse->data = $dbProduct->name;
                echo $this->webResponse->jsonResponse();
            }
        }
    }

    function postRemoveItem()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/cart");
            echo View::instance()->render('app/layout/layout.php');
        } else {

            $id = $this->f3->get('POST.id');

            global $dbConnection;

            $dbCartDetail = new BaseModel($dbConnection, "cartDetail");
            $dbCartDetail->getByField("id", $id);
            $dbCartDetail->erase();

            $this->webResponse->errorCode = 1;
            $this->webResponse->title = "";
            $this->webResponse->data = "Done";
            echo $this->webResponse->jsonResponse();
        }
    }

    function getStatus()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/cart");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            global $dbConnection;

            $dbCartDetail = new BaseModel($dbConnection, "cartDetail");
            $ItemsCount = $dbCartDetail->count("accountId=" . $this->objUser->accountId);

            $this->webResponse->errorCode = 1;
            $this->webResponse->title = "";
            $this->webResponse->data = new stdClass();
            $this->webResponse->data->itemsCount = $ItemsCount;
            echo $this->webResponse->jsonResponse();
        }
    }

    function getCartCheckout()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/cart/checkout");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            global $dbConnection;

            $dbCartDetail = new BaseModel($dbConnection, "vwCartDetail");
            
            $nameField = "productName_" . $this->objUser->language;
            $dbCartDetail->name = $nameField;
            
            $arrCartDetail = $dbCartDetail->getByField("accountId", $this->objUser->accountId);
            
            // Group cart items by seller id
            $allCartItems = [];
            $allSellers = [];
            foreach($arrCartDetail as $cartDetail) {
                $sellerId = $cartDetail->entityId;

                $cartItemsBySeller = [];
                if(array_key_exists($sellerId, $allCartItems)) {
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
            $this->f3->set('allCartItems', $allCartItems);
            $this->f3->set('allSellers', $allSellers);

            // Get all currencies
            $dbCurrencies = new BaseModel($dbConnection, "currency");
            $allCurrencies = $dbCurrencies->all();
            
            $mapCurrencyIdCurrency = [];
            foreach($allCurrencies as $currency) {
                $currencyObj = new stdClass();
                $currencyObj->id = $currency->id;
                $currencyObj->symbol = $currency->symbol;
                $currencyObj->conversionToUSD = $currency->conversionToUSD;

                $mapCurrencyIdCurrency[$currency->id] = $currencyObj;
            }
            $this->f3->set('mapCurrencyIdCurrency', $mapCurrencyIdCurrency);

            // Get currency by entity
            $dbEntities = new BaseModel($dbConnection, "entity");
            $allEntities = $dbEntities->all();
            
            $mapSellerIdCurrency = [];
            foreach($allEntities as $entity) {
                $mapSellerIdCurrency[$entity->id] = $mapCurrencyIdCurrency[$entity->currencyId];
            }
            $this->f3->set('mapSellerIdCurrency', $mapSellerIdCurrency);
            
            // Set buyer currency
            $dbAccount = new BaseModel($dbConnection, "account");
            $account = $dbAccount->getById($this->objUser->accountId)[0];
            $buyerCurrency = $mapSellerIdCurrency[$account->entityId];
            $this->f3->set('buyerCurrency', $buyerCurrency);


            $this->webResponse->errorCode = 1;
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

            global $dbConnection;
            
            $dbEntityProduct = new BaseModel($dbConnection, "entityProductSell");
            $dbEntityProduct->getWhere("entityId=$sellerId and productId=$productId");

            $dbCartDetail = new BaseModel($dbConnection, "cartDetail");
            $dbCartDetail->getById($cartDetailId);
            $dbCartDetail->quantity = $quantity;
            $dbCartDetail->update();

            $dbCartDetailFull = new BaseModel($dbConnection, "vwCartDetail");
            $cartDetailFull = $dbCartDetailFull->getById($cartDetailId)[0];

            $cartDetail = new stdClass();
            $cartDetail->productId = $cartDetailFull->productId;
            $cartDetail->quantity = $cartDetailFull->quantity;
            $cartDetail->entityId = $cartDetailFull->entityId;

            $this->webResponse->errorCode = 1;
            $this->webResponse->title = "";
            $this->webResponse->data = $cartDetail;
            echo $this->webResponse->jsonResponse();
        }
    }

    function getCartCheckoutSubmitConfirmation()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/cart/checkout");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $total = $this->f3->get('POST.total');
            $mapSellerIdTotal = $this->f3->get('POST.mapSellerIdTotal');
            
            $modal = new stdClass();
            $modal->modalTitle = $this->f3->get('vModule_cartCheckout_orderConfirmationTitle');
            $modal->modalText = $this->f3->get('vModule_cartCheckout_orderConfirmation');
            $modal->modalRoute = '/web/cart/checkout/submit';
            $modal->modalButton = $this->f3->get('vButton_confirm');
            $modal->id = $this->objUser->accountId;
            $modal->fnCallback = 'CartCheckout.submitOrderSuccess';

            $body = new stdClass();
            $body->total = $total;
            $body->mapSellerIdTotal = $mapSellerIdTotal;
            $modal->body = json_encode($body);

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
            global $dbConnection;
            
            // Get user account
            $dbAccount = new BaseModel($dbConnection, "account");
            $account = $dbAccount->getById($this->objUser->accountId)[0];

            // TODO: Adjust buyerBranchId logic
            $dbEntityBranch = new BaseModel($dbConnection, "entityBranch");
            $entityBranch = $dbEntityBranch->getByField("entityId", $account->entityId)[0];

            // Add to orderGrand
            $dbOrderGrand = new BaseModel($dbConnection, "orderGrand");
            $dbOrderGrand->buyerEntityId = $account->entityId;
            $dbOrderGrand->buyerBranchId = $entityBranch->id;
            $dbOrderGrand->buyerUserId = $this->objUser->id;
            $dbOrderGrand->addReturnID();

            $dbCartDetail = new BaseModel($dbConnection, "vwCartDetail");
            $nameField = "productName_" . $this->objUser->language;
            $dbCartDetail->name = $nameField;
            $arrCartDetail = $dbCartDetail->getByField("accountId", $this->objUser->accountId);

            // Get all currencies
            $dbCurrencies = new BaseModel($dbConnection, "currency");
            $allCurrencies = $dbCurrencies->all();
            
            $mapCurrencyIdCurrency = [];
            foreach($allCurrencies as $currency) {
                $mapCurrencyIdCurrency[$currency->id] = $currency;
            }

            // Get currency by entity
            $dbEntities = new BaseModel($dbConnection, "entity");
            $allEntities = $dbEntities->all();
            
            $mapSellerIdCurrency = [];
            foreach($allEntities as $entity) {
                $mapSellerIdCurrency[$entity->id] = $mapCurrencyIdCurrency[$entity->currencyId];
            }
            
            // Get buyer currency
            $dbAccount = new BaseModel($dbConnection, "account");
            $account = $dbAccount->getById($this->objUser->accountId)[0];
            $buyerCurrency = $mapSellerIdCurrency[$account->entityId];

            // Group cart items by seller id
            $allCartItems = [];
            $allSellers = [];
            foreach($arrCartDetail as $cartDetail) {
                $sellerId = $cartDetail->entityId;

                $cartItemsBySeller = [];
                if(array_key_exists($sellerId, $allCartItems)) {
                    $cartItemsBySeller = $allCartItems[$sellerId];
                } else {
                    $nameField = "entityName_" . $this->objUser->language;

                    $seller = new stdClass();
                    $seller->sellerId = $sellerId;
                    array_push($allSellers, $seller);
                }

                array_push($cartItemsBySeller, $cartDetail);
                $allCartItems[$sellerId] = $cartItemsBySeller;
            }

            $mapSellerIdOrderId = [];
            foreach($allSellers as $seller) {
                $sellerId = $seller->sellerId;
                $cartItemsBySeller = $allCartItems[$sellerId];

                $total = 0;
                foreach($cartItemsBySeller as $cartItem) {
                    $total += $cartItem->quantity * $cartItem->unitPrice;
                }
                
                // TODO: Adjust sellerBranchId logic
                $sellerEntityBranch = $dbEntityBranch->getByField("entityId", $sellerId)[0];

                // Add to order
                $dbOrder = new BaseModel($dbConnection, "order");
                $dbOrder->orderGrandId = $dbOrderGrand->id; 
                $dbOrder->entityBuyerId = $account->entityId;
                $dbOrder->entitySellerId = $sellerId;
                $dbOrder->branchBuyerId = $entityBranch->id;
                $dbOrder->branchSellerId = $sellerEntityBranch->id;
                $dbOrder->userBuyerId = $this->objUser->id;
                $dbOrder->userSellerId = null;
                $dbOrder->statusId = 1;
                
                // TODO: Adjust serial logic
                $dbOrder->serial = mt_rand(100000, 999999);
                
                $dbOrder->currencyId = $mapSellerIdCurrency[$sellerId]->id;
                $dbOrder->subtotal = $total;
                $dbOrder->total = $total;
                $dbOrder->addReturnID();

                $mapSellerIdOrderId[$sellerId] = $dbOrder->id;
            }
            
            $commands = [];
            foreach($arrCartDetail as $cartDetail) {
                $orderId = $mapSellerIdOrderId[$cartDetail->entityId];
                $entityProductId = $cartDetail->entityProductId;
                $quantity = $cartDetail->quantity;
                $unitPrice = $cartDetail->unitPrice;

                $query = "INSERT INTO orderDetail (`orderId`, `entityProductId`, `quantity`, `unitPrice`) VALUES ('".$orderId."', '".$entityProductId."', '".$quantity."', '".$unitPrice."');";
                array_push($commands, $query);
            }
            
            $this->db->exec($commands);

            $this->webResponse->errorCode = 1;
            $this->webResponse->title = "";
            $this->webResponse->data = "Done";
            echo $this->webResponse->jsonResponse();
        }
    }
}
