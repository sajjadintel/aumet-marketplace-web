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

    public function serialize($users, $bonusData = null)
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
            'type' => $this->type,
            'users' => $users,
            'options' => $this->options,
        ];
    }
}