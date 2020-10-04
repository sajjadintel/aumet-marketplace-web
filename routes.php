<?php

$f3->route('GET /', 'LandingController->get');

$f3->route('GET /app/auth/signin', 'AuthController->getSignIn');
$f3->route('POST /app/auth/signin', 'AuthController->postSignIn');

$f3->route('GET /app/auth/signup', 'AuthController->getSignUp');
$f3->route('GET /app/auth/forgot', 'AuthController->getForgottenPassword');

$f3->route('GET /app/auth/signout', 'AuthController->getSignOut');

$f3->route('GET /app', 'DashboardController->get');

$f3->route('GET /app/me/menu', 'UserController->getMenu');

$f3->route('GET /app/dashboard', 'DashboardController->get');

$f3->route('GET /app/product/search', 'SearchController->getSearchProducts');
$f3->route('POST /app/product/search', 'SearchController->postSearchProducts');
$f3->route('GET /app/entity/@entityId/product/@productId', 'ProductsController->getEntityProduct');

$f3->route('GET /app/product/brandname/list', 'SearchController->getProductBrandNameList');
$f3->route('GET /app/product/scientificname/list', 'SearchController->getProductScientificNameList');


$f3->route('GET /app/customercare', 'CustomerCareController->get');

$f3->route('GET /app/cart', 'CartController->get');
$f3->route('POST /app/cart/add', 'CartController->postAddItem');
$f3->route('GET /app/cart/status', 'CartController->getStatus');
$f3->route('POST /app/cart/remove', 'CartController->postRemoveItem');

$f3->route('GET /app/demo/editor/scientificnames', 'DemoController->get');
