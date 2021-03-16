<?php


abstract class FcmNotification
{
    protected $title;
    protected $body;
    protected $type;
    protected $options;
    /**
     * @var FcmHandler
     */
    protected $handler;

    const AVAILABLE_NOTIFICATIONS = [
        Constants::ORDER_STATUS_PENDING => NewOrderNotification::class,
    ];

    public abstract function send($users, $options = null, $additionalData = null);
}