<?php

class DashboardController extends Controller {

    function get()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            if ($this->objUser->menuId == Constants::MENU_DISTRIBUTOR) {
                $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
                $query = "entitySellerId IN ($arrEntityId)";

                $dbData = new BaseModel($this->db, "vwDashboardSellerToday");
                $dbData->getWhere($query);

                $dbDataNewCustomer = new BaseModel($this->db, "vwNewCustomerToday");
                $dbDataNewCustomer->getWhere($query);

                $dbDataYesterday = new BaseModel($this->db, "vwDashboardSellerYesterday");
                $dbDataYesterday->getWhere($query);

                $dbDataNewCustomerYesterday = new BaseModel($this->db, "vwNewCustomerYesterday");
                $dbDataNewCustomerYesterday->getWhere($query);
                // $data = $data[0];

                $this->f3->set('dashboard_revenue', is_null($dbData['revenue']) ? 0 : $dbData['revenue']);
                $this->f3->set('dashboard_order', is_null($dbData['orderCount']) ? 0 : $dbData['orderCount']);
                $this->f3->set('dashboard_customer', is_null($dbData['customerCount']) ? 0 : $dbData['customerCount']);
                $this->f3->set('dashboard_new_customer', is_null($dbDataNewCustomer['newCustomerCount']) ? 0 : $dbDataNewCustomer['newCustomerCount']);

                $this->f3->set('dashboard_revenueYesterday', is_null($dbDataYesterday['revenue']) ? 0 : $dbDataYesterday['revenue']);
                $this->f3->set('dashboard_orderYesterday', is_null($dbDataYesterday['orderCount']) ? 0 : $dbDataYesterday['orderCount']);
                $this->f3->set('dashboard_customerYesterday', is_null($dbDataYesterday['customerCount']) ? 0 : $dbDataYesterday['customerCount']);
                $this->f3->set('dashboard_new_customerYesterday', is_null($dbDataNewCustomerYesterday['newCustomerCount']) ? 0 : $dbDataNewCustomerYesterday['newCustomerCount']);

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
                $this->webResponse->title = $this->f3->get('vTitle_homepage');
                $this->webResponse->data = View::instance()->render('app/dashboard/seller.php');
                echo $this->webResponse->jsonResponse();
            } else {
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

                // Get related banners
                $dbBanner = new BaseModel($this->db, "vwEntityDashboardBanner");
                $dbBanner->image = $this->objUser->language == "ar"? "imageRtl" : "imageLtr";
                $dbBanner->title = "title" . ucfirst($this->objUser->language);
                $dbBanner->subtitle = "subtitle" . ucfirst($this->objUser->language);
                $dbBanner->buttonText = "buttonText" . ucfirst($this->objUser->language);
                $arrBanner = $dbBanner->getWhere("countryId = $buyerEntity->countryId", "id DESC", 5, 0);
                $this->f3->set('arrBanner', $arrBanner);
                

                $dbProducts = new BaseModel($this->db, "vwEntityProductSell");
                $dbProducts->name = "productName_" . $this->objUser->language;

                // Get newest products
                $arrNewestProductsDb = $dbProducts->findWhere("statusId = 1", "insertDateTime DESC", 4, 0);
                $arrNewestProducts = [];
                foreach($arrNewestProductsDb as $productDb) {
                    $product = new stdClass();
                    $product->name = $productDb['name'];
                    $product->image = $productDb['image'];
                    $product->id = $productDb['id'];
                    $product->entityId = $productDb['entityId'];

                    $productCurrency = $mapCurrencyIdCurrency[$productDb['currencyId']];
                    $priceUSD = $productDb['unitPrice'] * $productCurrency['conversionToUSD'];
                    $price = $priceUSD / $buyerCurrency['conversionToUSD'];
                    $product->price = round($price, 2) . " " . $buyerCurrency['symbol'];
                    
                    array_push($arrNewestProducts, $product);
                }
                $this->f3->set('arrNewestProducts', $arrNewestProducts);

                // Get top selling products
                $arrTopSellingProductsDb = $dbProducts->findWhere("statusId = 1", "totalOrderQuantity DESC", 4, 0);
                $arrTopSellingProducts = [];
                foreach($arrTopSellingProductsDb as $productDb) {
                    $product = new stdClass();
                    $product->name = $productDb['name'];
                    $product->image = $productDb['image'];
                    $product->id = $productDb['id'];
                    $product->entityId = $productDb['entityId'];

                    $productCurrency = $mapCurrencyIdCurrency[$productDb['currencyId']];
                    $priceUSD = $productDb['unitPrice'] * $productCurrency['conversionToUSD'];
                    $price = $priceUSD / $buyerCurrency['conversionToUSD'];
                    $product->price = round($price, 2) . " " . $buyerCurrency['symbol'];
                    
                    array_push($arrTopSellingProducts, $product);
                }
                $this->f3->set('arrTopSellingProducts', $arrTopSellingProducts);

                // Get pending orders with details
                $dbOrder = new BaseModel($this->db, "vwOrderEntityUser");
                $arrPendingOrders = $dbOrder->findWhere("entityBuyerId IN ($arrEntityId) AND statusId IN (1,2,3)", "insertDateTime DESC", 3, 0);
                $this->f3->set('arrPendingOrders', $arrPendingOrders);
                
                if(count($arrPendingOrders) > 0) {
                    $mapOrderIdOrderDetails = [];
                    foreach($arrPendingOrders as $order) {
                        $mapOrderIdOrderDetails[$order["id"]] = [];
                    }
                    $allOrderId = implode(",", array_keys($mapOrderIdOrderDetails));
    
                    $dbOrderDetail = new BaseModel($this->db, "vwOrderDetail");
                    $dbOrderDetail->productName = "productName" . ucfirst($this->objUser->language);
                    $arrOrderDetails = $dbOrderDetail->findWhere("id IN ($allOrderId)");
                    foreach($arrOrderDetails as $orderDetail) {
                        $allOrderDetails = $mapOrderIdOrderDetails[$orderDetail["id"]];
                        array_push($allOrderDetails, $orderDetail);
                        $mapOrderIdOrderDetails[$orderDetail["id"]] = $allOrderDetails;
                    }
                    $this->f3->set('mapOrderIdOrderDetails', $mapOrderIdOrderDetails);
                }

                // Get top distributors
                $dbEntityRelation = new BaseModel($this->db, "vwEntityRelation");
                $dbEntityRelation->sellerName = "sellerName_" . $this->objUser->language;
                $arrEntityRelation = $dbEntityRelation->findWhere("sellerCountryId = $buyerEntity->countryId");
                
                $mapEntityIdName = [];
                $mapEntityIdTotal = [];
                foreach($arrEntityRelation as $entityRelation) {
                    $entitySellerId = $entityRelation["entitySellerId"];
                    $mapEntityIdName[$entitySellerId] = $entityRelation["sellerName"];
                    
                    if(array_key_exists($entitySellerId, $mapEntityIdTotal)) {
                        $orderTotalPaid = $mapEntityIdTotal[$entitySellerId];
                        $orderTotalPaid += $entityRelation["orderTotalPaid"];
                        $mapEntityIdTotal[$entitySellerId] = $orderTotalPaid; 
                    } else {
                        $mapEntityIdTotal[$entitySellerId] = $entityRelation["orderTotalPaid"];
                    }
                }
                arsort($mapEntityIdTotal);

                $topDistributorsCount = 5;
                $arrTopDistributors = [];
                foreach($mapEntityIdTotal as $entityId => $total) {
                    $entity = new stdClass();
                    $entity->id = $entityId;
                    $entity->name = $mapEntityIdName[$entityId];

                    if(count($arrTopDistributors) < $topDistributorsCount) {
                        array_push($arrTopDistributors, $entity);
                    } else {
                        break;
                    }
                }
                $this->f3->set('arrTopDistributors', $arrTopDistributors);

                

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
                $this->webResponse->title = $this->f3->get('vTitle_homepage');
                $this->webResponse->data = View::instance()->render('app/dashboard/buyerHomepage.php');
                echo $this->webResponse->jsonResponse();
            }
        }
    }

    function getDashboard()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/dashboard");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            if ($this->objUser->menuId == Constants::MENU_DISTRIBUTOR) {
                $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
                $query = "entitySellerId IN ($arrEntityId)";

                $dbData = new BaseModel($this->db, "vwDashboardSellerToday");
                $dbData->getWhere($query);

                $dbDataNewCustomer = new BaseModel($this->db, "vwNewCustomerToday");
                $dbDataNewCustomer->getWhere($query);

                $dbDataYesterday = new BaseModel($this->db, "vwDashboardSellerYesterday");
                $dbDataYesterday->getWhere($query);

                $dbDataNewCustomerYesterday = new BaseModel($this->db, "vwNewCustomerYesterday");
                $dbDataNewCustomerYesterday->getWhere($query);
                // $data = $data[0];

                $this->f3->set('dashboard_revenue', is_null($dbData['revenue']) ? 0 : $dbData['revenue']);
                $this->f3->set('dashboard_order', is_null($dbData['orderCount']) ? 0 : $dbData['orderCount']);
                $this->f3->set('dashboard_customer', is_null($dbData['customerCount']) ? 0 : $dbData['customerCount']);
                $this->f3->set('dashboard_new_customer', is_null($dbDataNewCustomer['newCustomerCount']) ? 0 : $dbDataNewCustomer['newCustomerCount']);

                $this->f3->set('dashboard_revenueYesterday', is_null($dbDataYesterday['revenue']) ? 0 : $dbDataYesterday['revenue']);
                $this->f3->set('dashboard_orderYesterday', is_null($dbDataYesterday['orderCount']) ? 0 : $dbDataYesterday['orderCount']);
                $this->f3->set('dashboard_customerYesterday', is_null($dbDataYesterday['customerCount']) ? 0 : $dbDataYesterday['customerCount']);
                $this->f3->set('dashboard_new_customerYesterday', is_null($dbDataNewCustomerYesterday['newCustomerCount']) ? 0 : $dbDataNewCustomerYesterday['newCustomerCount']);

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
                $this->webResponse->title = $this->f3->get('vTitle_dashboard');
                $this->webResponse->data = View::instance()->render('app/dashboard/seller.php');
                echo $this->webResponse->jsonResponse();
            } else {
                $arrEntityId = Helper::idListFromArray($this->f3->get('SESSION.arrEntities'));
                $query = "entityBuyerId IN ($arrEntityId)";

                $dbData = new BaseModel($this->db, "vwDashboardBuyerToday");
                $dbData->getWhere($query);

                $dbDataYesterday = new BaseModel($this->db, "vwDashboardBuyerYesterday");
                $dbDataYesterday->getWhere($query);

                $this->f3->set('dashboard_order', is_null($dbData['orderCount']) ? 0 : $dbData['orderCount']);
                $this->f3->set('dashboard_invoice', is_null($dbData['invoice']) ? 0 : $dbData['invoice']);

                $this->f3->set('dashboard_orderYesterday', is_null($dbDataYesterday['orderCount']) ? 0 : $dbDataYesterday['orderCount']);
                $this->f3->set('dashboard_invoiceYesterday', is_null($dbDataYesterday['invoice']) ? 0 : $dbDataYesterday['invoice']);

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
                $this->webResponse->title = $this->f3->get('vTitle_dashboard');
                $this->webResponse->data = View::instance()->render('app/dashboard/buyerDashboard.php');
                echo $this->webResponse->jsonResponse();
            }
        }
    }

}
