<?php

class Constants
{
    ## HTTP Responses
    const HTTP_OK = 200;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;

    ## Environments
    const ENV_LOC = 'loc';
    const ENV_PROD = 'prod';

    const ORDER_STATUS_PENDING = 1;
    const ORDER_STATUS_ONHOLD = 2;
    const ORDER_STATUS_PROCESSING = 3;
    const ORDER_STATUS_COMPLETED = 4;
    const ORDER_STATUS_CANCELED = 5;
    const ORDER_STATUS_RECEIVED = 6;
    const ORDER_STATUS_PAID = 7;
}
