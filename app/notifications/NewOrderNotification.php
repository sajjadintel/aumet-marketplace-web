<?php

class NewOrderNotification extends FcmNotification
{
    protected $title = 'New Order';
    protected $body = 'You have received a new order for Product X quantity Y. Click here to view all pending orders';
    protected $type = FcmHandler::NOTIFICATION_TYPE_NEW_ORDER;
    protected $options;

    public function __construct()
    {
        $this->options = \Kreait\Firebase\Messaging\WebPushConfig::fromArray([
            'fcm_options' => [
                'link' => getenv('DOMAIN_URL') . 'web/distributor/order/pending'
            ]
        ]);
    }

    protected function fillBody($additionalData)
    {
        $this->body = 'You have received a new order for ';
        foreach ($additionalData as $datum) {
            $this->body .= "Product {$datum->productName_en} quantity {$datum->quantity} ";
        }

        $this->body .= '. Click here to view all pending orders';
    }

    public function send($users, $additionalData = null, $options = null)
    {
        $this->fillBody($additionalData);
        $this->handler = new FcmHandler($this->title, $this->body, $this->type, $users, $options);
        return $this->handler->send();
    }
}