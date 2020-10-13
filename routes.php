<?php

$f3->route('GET /', 'LandingController->get');

$f3->route('GET /web/auth/signin', 'AuthController->getSignIn');
$f3->route('POST /web/auth/signin', 'AuthController->postSignIn');

$f3->route('GET /web/auth/signup', 'AuthController->getSignUp');
$f3->route('GET /web/auth/forgot', 'AuthController->getForgottenPassword');

$f3->route('GET /web/auth/signout', 'AuthController->getSignOut');

$f3->route('GET /web', 'DashboardController->get');

$f3->route('GET /web/me/menu', 'UserController->getMenu');

$f3->route('GET /web/dashboard', 'DashboardController->get');

$f3->route('GET /web/product/search', 'SearchController->getSearchProducts');
$f3->route('POST /web/product/search', 'SearchController->postSearchProducts');
$f3->route('GET /web/entity/@entityId/product/@productId', 'ProductsController->getEntityProduct');

$f3->route('GET /web/product/brandname/list', 'SearchController->getProductBrandNameList');
$f3->route('GET /web/product/scientificname/list', 'SearchController->getProductScientificNameList');


$f3->route('GET /web/customercare', 'CustomerCareController->get');

$f3->route('GET /web/cart', 'CartController->get');
$f3->route('POST /web/cart/add', 'CartController->postAddItem');
$f3->route('GET /web/cart/status', 'CartController->getStatus');
$f3->route('POST /web/cart/remove', 'CartController->postRemoveItem');

$f3->route('GET /web/demo/editor/scientificnames', 'DemoController->get');


// START APM-10 APM-11 APM-35 
$f3->route('GET /web/distributor/order', 'OrderController->getDistributorOrders');
$f3->route('GET /web/distributor/order/@orderId', 'OrderController->getOrderDetails');
$f3->route('GET /web/distributor/order/print/@orderId', 'OrderController->getPrintOrderInvoice');
$f3->route('GET /web/distributor/order/confirm/@orderId/@statusId', 'OrderController->getOrderConfirmation');
$f3->route('POST /web/distributor/order', 'OrderController->postDistributorOrders');
$f3->route('POST /web/distributor/order/cancel', 'OrderController->postCancelOrder');
$f3->route('POST /web/distributor/order/complete', 'OrderController->postCompleteOrder');
$f3->route('POST /web/distributor/order/process', 'OrderController->postProcessOrder');
$f3->route('POST /web/distributor/order/onhold', 'OrderController->postOnHoldOrder');
// END  APM-10 APM-11 APM-35

// START APM-37
$f3->route('GET /web/distributor/product', 'ProductsController->getDistributorProducts');
$f3->route('GET /web/distributor/product/@productId', 'ProductsController->getProductDetails');
$f3->route('POST /web/distributor/product', 'ProductsController->postDistributorProducts');
$f3->route('POST /web/distributor/product/add', 'ProductsController->postAddDistributorProduct');
$f3->route('POST /web/distributor/product/edit', 'ProductsController->postEditDistributorProduct');
// END APM-37