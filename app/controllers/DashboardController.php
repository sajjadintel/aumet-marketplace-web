<?php

class DashboardController extends Controller
{

    function get()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/dashboard");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            if ($this->objUser->menuId == 1) {
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
                $dbBanners = new BaseModel($this->db, "entityDashboardBanner");
                $dbBanners->image = $this->objUser->language == "ar"? "imageRtl" : "imageLtr";
                $dbBanners->title = "title" . ucfirst($this->objUser->language);
                $dbBanners->subtitle = "subtitle" . ucfirst($this->objUser->language);
                $dbBanners->buttonText = "buttonText" . ucfirst($this->objUser->language);
                $arrBanners = $dbBanners->getWhere("countryId = $buyerEntity->countryId", "id DESC", 5, 0);
                $this->f3->set('arrBanners', $arrBanners);
                

                $dbProducts = new BaseModel($this->db, "vwEntityProductSell");
                $dbProducts->name = "productName_" . $this->objUser->language;

                // Get newest products
                $arrNewestProductsDb = $dbProducts->findWhere("statusId = 1", "insertDateTime DESC", 4, 0);
                $arrNewestProducts = [];
                foreach($arrNewestProductsDb as $productDb) {
                    $product = new stdClass();
                    $product->name = $productDb['name'];
                    $product->image = $productDb['image'];

                    $productCurrency = $mapCurrencyIdCurrency[$productDb['currencyId']];
                    $priceUSD = $productDb['unitPrice'] * $productCurrency['conversionToUSD'];
                    $price = $priceUSD / $buyerCurrency['conversionToUSD'];
                    $product->price = round($price, 2) . " " . $buyerCurrency['symbol'];
                    
                    array_push($arrNewestProducts, $product);
                }
                $this->f3->set('arrNewestProducts', $arrNewestProducts);

                // Get top selling products
                $arrTopSellingProductsDb = $dbProducts->findWhere("statusId = 1", "quantityOrdered DESC", 4, 0);
                $arrTopSellingProducts = [];
                foreach($arrTopSellingProductsDb as $productDb) {
                    $product = new stdClass();
                    $product->name = $productDb['name'];
                    $product->image = $productDb['image'];

                    $productCurrency = $mapCurrencyIdCurrency[$productDb['currencyId']];
                    $priceUSD = $productDb['unitPrice'] * $productCurrency['conversionToUSD'];
                    $price = $priceUSD / $buyerCurrency['conversionToUSD'];
                    $product->price = round($price, 2) . " " . $buyerCurrency['symbol'];
                    
                    array_push($arrTopSellingProducts, $product);
                }
                $this->f3->set('arrTopSellingProducts', $arrTopSellingProducts);

                $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
                $this->webResponse->title = $this->f3->get('vTitle_dashboard');
                $this->webResponse->data = View::instance()->render('app/dashboard/buyer.php');
                echo $this->webResponse->jsonResponse();
            }
        }
    }
}
