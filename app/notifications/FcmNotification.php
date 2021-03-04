<?php


abstract class FcmNotification
{
    protected $title;
    protected $body;
    protected $type;
    protected $options;

    const AVAILABLE_NOTIFICATIONS = [
        Constants::ORDER_STATUS_PENDING => NewOrderNotification::class,
    ];

    public abstract function serialize($users, $bonusData = null);
}