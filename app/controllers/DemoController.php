<?php

class DemoController extends Controller
{

    public function beforeroute()
    {
        //parent::beforeroute(); // TODO: Change the autogenerated stub
    }

    public function testWelcomeEmail(){

        echo NotificationHelper::sendOnboardingPharmacyNotification($this->f3, $this->db, 190, 's.qarem+05@aumet.com', 'Sahar Pharmacy', 201, 192);
    }

    function getGenerateOrder()
    {
        $entityBuyerId = rand(7, 26);

        /*
            id int AI PK
            entityBuyerId int
            entitySellerId int
            userBuyerId int
            userSellerId int
            statusId int
            serial varchar(16)
            currencyId int
            subtotal decimal(10,3)
            total decimal(10,3)
            insertDateTime datetime
            updateDateTime datetime
            note varchar(500)
         */
        
        $dbOrder = new BaseModel($this->db, "order");
        $dbOrder->entityBuyerId = $entityBuyerId;
        $dbOrder->entitySellerId = 1;
        $dbOrder->userBuyerId = 3;
        //$dbOrder->userSellerId int
        $dbOrder->statusId = 1;
        $dbOrder->serial = rand(1000, 100000);
        $dbOrder->currencyId = 3;
        $dbOrder->note = "";
        $dbOrder->subtotal = 0;
        $dbOrder->total = 0;
        $dbOrder->addReturnID();

        $itemsCount = rand(5,22);

        $arrProd = [40,75,76,78,81,91,93,105,114,119,158,197,205,250,258,263,281,286,288,301,324,326,343,372,415];

        $arrProdSelected = [];

        for ($i=0; $i<$itemsCount; $i++){

            $pid = $arrProd[rand(0,24)];

            while(in_array($pid, $arrProdSelected)){
                $pid = $arrProd[rand(0,24)];
            }

            $arrProdSelected[] = $pid;

            $dbOrderDetailProduct = new BaseModel($this->db, "entityProductSell");
            $dbOrderDetailProduct->getById($pid);

            $dbOrderDetail = new BaseModel($this->db, "orderDetail");
            $dbOrderDetail->orderId = $dbOrder->id;
            $dbOrderDetail->entityProductId = $dbOrderDetailProduct->id;
            $dbOrderDetail->quantity  = rand(8,30);
            $dbOrderDetail->unitPrice  = $dbOrderDetailProduct->unitPrice;
            $dbOrderDetail->tax  = 0;
            $dbOrderDetail->addReturnID();

            $dbOrder->subtotal += $dbOrderDetail->quantity * $dbOrderDetail->unitPrice;
            $dbOrder->total += $dbOrderDetail->quantity * $dbOrderDetail->unitPrice;
        }

        $dbOrder->update();

        echo "Done";
    }

    function get()
    {
        $this->f3->set("LANGUAGE", "en");

        if (!$this->f3->ajax()) {
            $this->f3->set("pageURL", "/web/demo/editor/scientificnames");
            echo View::instance()->render('app/layout/layout.php');
        } else {
            $this->webResponse->errorCode = Constants::STATUS_SUCCESS;
            $this->webResponse->title = $this->f3->get('vTitle_dashboard');
            $this->webResponse->data = View::instance()->render('app/demo/scientificNames.php');
            echo $this->webResponse->jsonResponse();
        }
    }
}
