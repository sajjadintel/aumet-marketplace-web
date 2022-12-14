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
    const ORDER_STATUS_MISSING_PRODUCTS = 8;
    const ORDER_STATUS_CANCELED_PHARMACY = 9;

    ## Email Types
    const EMAIL_NEW_ORDER = 'New Order';
    const EMAIL_LOW_STOCK = 'Low Stock';
    const EMAIL_RESET_PASSWORD = 'Reset Password';
    const EMAIL_MISSING_PRODUCTS = 'Missing Products';
    const EMAIL_MODIFY_SHIPPED_QUANTITY = 'Modify Shipped Quantity';
    const EMAIL_CUSTOMER_SUPPORT_REQUEST = 'Customer Support Request';
    const EMAIL_CUSTOMER_SUPPORT_CONFIRMATION = 'Customer Support Confirmation';
    const EMAIL_ORDER_STATUS_UPDATE = 'Order Status Update';
    const EMAIL_PHARMACY_ACCOUNT_VERIFICATION = 'Pharmacy Account Verification';
    const EMAIL_DISTRIBUTOR_ACCOUNT_VERIFICATION = 'Distributor Account Verification';
    const EMAIL_PHARMACY_ACCOUNT_VERIFIED = 'Pharmacy Account Verified';
    const EMAIL_DISTRIBUTOR_ACCOUNT_VERIFIED = 'Distributor Account Verified';
    const EMAIL_PHARMACY_ACCOUNT_APPROVAL = 'Pharmacy Account Approval';
    const EMAIL_DISTRIBUTOR_ACCOUNT_APPROVAL = 'Distributor Account Approval';
    const EMAIL_PHARMACY_ACCOUNT_APPROVED = 'Pharmacy Account Approved';
    const EMAIL_DISTRIBUTOR_ACCOUNT_APPROVED = 'Distributor Account Approved';
    const EMAIL_NEW_CUSTOMER_GROUP = 'New Customer Group';
    const EMAIL_CHANGE_PROFILE_APPROVAL = 'Change Profile Approval';
    const EMAIL_CHANGE_PROFILE_APPROVED = 'Change Profile Approved';
    const EMAIL_WELCOME_PHARMACY = 'Welcome at Aumet';

    ### Status Codes
    const STATUS_CODE_REDIRECT_TO_WEB = 0;
    const STATUS_SUCCESS = 1;
    const STATUS_ERROR = 2;
    const STATUS_SUCCESS_SHOW_DIALOG = 3;

    ### User status
    const USER_STATUS_WAITING_VERIFICATION = 1;
    const USER_STATUS_PENDING_APPROVAL = 2;
    const USER_STATUS_ACCOUNT_ACTIVE = 3;

    ### User role
    const USER_ROLE_DISTRIBUTOR_SYSTEM_ADMINISTRATOR = 10;
    const USER_ROLE_DISTRIBUTOR_ENTITY_MANAGER = 20;
    const USER_ROLE_DISTRIBUTOR_SYSTEM_MANAGER = 30;
    const USER_ROLE_PHARMACY_SYSTEM_ADMINISTRATOR = 40;
    const USER_ROLE_AUMET_ADMIN = 1000;

    ### Entity type
    const ENTITY_TYPE_DISTRIBUTOR = 10;
    const ENTITY_TYPE_SUB_DISTRIBUTOR = 11;
    const ENTITY_TYPE_PHARMACY = 20;
    const ENTITY_TYPE_PHARMACY_CHAIN = 21;
    const ENTITY_TYPE_AUMET_ADMIN = 1000;

    ### Account status
    const ACCOUNT_STATUS_ACTIVE = 1;
    const ACCOUNT_STATUS_INACTIVE = 2;
    const ACCOUNT_STATUS_BLOCKED = 3;

    ### Menu
    const MENU_DISTRIBUTOR = 1;
    const MENU_PHARMACY = 2;
    const MENU_ADMIN = 1000;

    ### Bonus type
    const BONUS_TYPE_FIXED = 1;
    const BONUS_TYPE_PERCENTAGE = 2;
    const BONUS_TYPE_DYNAMIC = 3;

    ### Database
    const MAX_INT_VALUE = 2147483647;

    ### Max Count
    const MAX_DISTRIBUTOR_PROMOTION_COUNT = 10;
}
