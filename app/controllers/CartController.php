<?php

class CartController extends Controller
{

    function get()
    {
        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/app/cart");
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
            $this->f3->set("pageURL", "/app/cart");
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
                $dbCartDetail->quantity += $quantity;
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
            $this->f3->set("pageURL", "/app/cart");
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
            $this->f3->set("pageURL", "/app/cart");
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
}
