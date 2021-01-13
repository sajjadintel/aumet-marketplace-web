<?php

$f3->route('GET /web/demo/order/add', 'DemoController->getGenerateOrder');

$f3->route('GET /', 'LandingController->get');
$f3->route('GET /@lang', 'LandingController->get');

$f3->route('GET /web/auth/signin', 'AuthController->getSignIn');
$f3->route('POST /web/auth/signin_nofirebase', 'AuthController->postSignIn_NoFirebase');
$f3->route('POST /web/auth/signin', 'AuthController->postSignIn');

$f3->route('GET /web/auth/signup', 'AuthController->getSignUp');
$f3->route('GET /web/auth/forgot', 'AuthController->getForgottenPassword');

$f3->route('GET /web/auth/signout', 'AuthController->getSignOut');

$f3->route('GET /web', 'DashboardController->get');
$f3->route('GET /web/support', 'DashboardController->support');

$f3->route('GET /web/me/menu', 'UserController->getMenu');
$f3->route('GET /web/me/switchLanguage/@lang', 'UserController->switchLanguage');

$f3->route('GET /web/dashboard', 'DashboardController->get');

$f3->route('GET /web/product/search', 'SearchController->getSearchProducts');
$f3->route('POST /web/product/search', 'SearchController->postSearchProducts');
$f3->route('GET /web/entity/@entityId/product/@productId', 'ProductsController->getEntityProduct');

$f3->route('GET /web/product/brandname/list', 'SearchController->getProductBrandNameList');
$f3->route('GET /web/product/category/list', 'SearchController->getAllCategoryList');
$f3->route('GET /web/product/scientificname/list', 'SearchController->getProductScientificNameList');
$f3->route('GET /web/product/country/list', 'SearchController->getProductCountryList');
$f3->route('GET /web/order/customer/list', 'SearchController->getOrderBuyerList');
$f3->route('GET /web/order/Distributor/list', 'SearchController->getOrderSellerList');
$f3->route('GET /web/order/Distributor/listAll', 'SearchController->getAllSellerList');

$f3->route('GET /web/searchbar', 'SearchController->handleSearchBar');


$f3->route('GET /web/customercare', 'CustomerCareController->get');

$f3->route('GET /web/cart', 'CartController->get');
$f3->route('POST /web/cart/add', 'CartController->postAddItem');
$f3->route('GET /web/cart/status', 'CartController->getStatus');
$f3->route('POST /web/cart/remove', 'CartController->postRemoveItem');
$f3->route('GET /web/cart/remove/confirm/@itemId', 'CartController->getRemoveItemConfirmation');
$f3->route('POST /web/cart/checkout/update', 'CartController->postCartCheckoutUpdate');
$f3->route('POST /web/cart/checkout/note', 'CartController->postNoteCartCheckoutUpdate');
$f3->route('GET /web/cart/checkout', 'CartController->getCartCheckout');
$f3->route('POST /web/cart/checkout/submit/@paymentMethodId', 'CartController->postCartCheckoutSubmit');
$f3->route('GET /web/cart/checkout/submit/confirm/@paymentMethodId', 'CartController->getCartCheckoutSubmitConfirmation');
$f3->route('GET /web/thankyou/@grandOrderId', 'CartController->getThankyou');

$f3->route('GET /web/demo/editor/scientificnames', 'DemoController->get');


// START APM-10 APM-11 APM-35 
$f3->route('GET /web/distributor/order/pending', 'OrderController->getDistributorOrdersPending');
$f3->route('GET /web/distributor/order/unpaid', 'OrderController->getDistributorOrdersUnpaid');
$f3->route('GET /web/distributor/order/history', 'OrderController->getDistributorOrdersHistory');
$f3->route('GET /web/distributor/order/@orderId', 'OrderController->getOrderDetails');
$f3->route('GET /web/distributor/order/print/@orderId', 'OrderController->getPrintOrderInvoice');
$f3->route('GET /web/distributor/order/confirm/@orderId/@statusId', 'OrderController->getOrderConfirmation');
$f3->route('GET /web/distributor/order/confirm/@orderId/@statusId/dashboard', 'OrderController->getOrderConfirmationDashboard');

$f3->route('GET /web/distributor/orderMissingProducts/@orderId', 'OrderController->getOrderMissingProducts');

$f3->route('POST /web/distributor/order/pending', 'OrderController->postDistributorOrdersPending');
$f3->route('POST /web/distributor/order/unpaid', 'OrderController->postDistributorOrdersUnpaid');
$f3->route('POST /web/distributor/order/history', 'OrderController->postDistributorOrdersHistory');
$f3->route('POST /web/distributor/order/cancel', 'OrderController->postCancelOrder');
$f3->route('POST /web/distributor/order/complete', 'OrderController->postCompleteOrder');
$f3->route('POST /web/distributor/order/paid', 'OrderController->postPaidOrder');
$f3->route('POST /web/distributor/order/process', 'OrderController->postProcessOrder');
$f3->route('POST /web/distributor/order/onhold', 'OrderController->postOnHoldOrder');
// END  APM-10 APM-11 APM-35

// START APM-37
$f3->route('GET /web/distributor/product', 'ProductsController->getDistributorProducts');
$f3->route('GET /web/distributor/product/@productId', 'ProductsController->getProductDetails');
$f3->route('GET /web/distributor/product/quantity/@productId', 'ProductsController->getProductQuantityDetails');

$f3->route('POST /web/distributor/product', 'ProductsController->postDistributorProducts');
$f3->route('GET /web/distributor/product/list', 'ProductsController->getProductList');
$f3->route('POST /web/distributor/product/add', 'ProductsController->postAddDistributorProduct');
$f3->route('POST /web/distributor/product/edit', 'ProductsController->postEditDistributorProduct');
$f3->route('POST /web/distributor/product/image', 'ProductsController->postProductImage');
$f3->route('POST /web/distributor/product/editQuantity', 'ProductsController->postEditQuantityDistributorProduct');

// Bulk add 
$f3->route('GET /web/distributor/product/bulk/add/download', 'ProductsController->getBulkAddDownload');
$f3->route('GET /web/distributor/product/bulk/add/upload', 'ProductsController->getBulkAddUpload');
$f3->route('POST /web/distributor/product/bulk/add/upload', 'ProductsController->postBulkAddUpload');
$f3->route('POST /web/distributor/product/bulk/add/upload/process', 'ProductsController->postBulkAddUploadProcess');

// Bulk add images 
$f3->route('GET /web/distributor/product/bulk/add/image/upload', 'ProductsController->getBulkAddImageUpload');
$f3->route('POST /web/distributor/product/bulk/add/image/upload', 'ProductsController->postBulkAddImageUpload');
$f3->route('POST /web/distributor/product/bulk/add/image/upload/process', 'ProductsController->postBulkAddImageUploadProcess');
$f3->route('POST /web/distributor/product/bulk/add/image', 'ProductsController->postBulkAddImage');

// Stock update
$f3->route('GET /web/distributor/product/stock/download', 'ProductsController->getStockDownload');
$f3->route('GET /web/distributor/product/stock/upload', 'ProductsController->getStockUpload');
$f3->route('POST /web/distributor/product/stock/upload', 'ProductsController->postStockUpload');
$f3->route('POST /web/distributor/product/stock/upload/process', 'ProductsController->postStockUploadProcess');

// Bonus update
$f3->route('GET /web/distributor/product/bonus/download', 'ProductsController->getBonusDownload');
$f3->route('GET /web/distributor/product/bonus/upload', 'ProductsController->getBonusUpload');
$f3->route('POST /web/distributor/product/bonus/upload', 'ProductsController->postBonusUpload');
$f3->route('POST /web/distributor/product/bonus/upload/process', 'ProductsController->postBonusUploadProcess');

$f3->route('GET /web/distributor/customer', 'EntityController->getEntityCustomers');
$f3->route('GET /web/distributor/customer/@customerId', 'EntityController->getEntityCustomerDetails');
$f3->route('POST /web/distributor/customer', 'EntityController->postEntityCustomers');

$f3->route('GET /web/distributor/customer/feedback', 'CustomersController->getOrderCustomersFeedback');
$f3->route('POST /web/distributor/customer/feedback', 'CustomersController->postOrderCustomersFeedback');

// START dashboard
$f3->route('POST /web/distributor/order/recent', 'OrderController->postDistributorOrdersRecent');
$f3->route('POST /web/distributor/product/bestselling', 'ProductsController->postDistributorProductsBestSelling');

$f3->route('POST /web/pharmacy/order/recent', 'OrderController->postPharmacyOrdersRecent');
// END dashboard

$f3->route('GET /web/notification/order/new', 'OrderController->getNotifcationsDistributorOrdersNew');


$f3->route('GET /web/pharmacy/order/pending', 'OrderController->getPharmacyOrdersPending');
$f3->route('GET /web/pharmacy/order/unpaid', 'OrderController->getPharmacyOrdersUnpaid');
$f3->route('GET /web/pharmacy/order/history', 'OrderController->getPharmacyOrdersHistory');

$f3->route('POST /web/pharmacy/order/pending', 'OrderController->postPharmacyOrdersPending');
$f3->route('POST /web/pharmacy/order/unpaid', 'OrderController->postPharmacyOrdersUnpaid');
$f3->route('POST /web/pharmacy/order/history', 'OrderController->postPharmacyOrdersHistory');

$f3->route('POST /web/pharmacy/order/missingProducts', 'OrderController->postPharmacyMissingProducts');

$f3->route('GET /web/pharmacy/order/print/@orderId', 'OrderController->getPrintOrderPharmacyInvoice');

$f3->route('GET /web/pharmacy/feedback/pending', 'FeedbackController->getPharmacyFeedbacksPending');
$f3->route('GET /web/pharmacy/feedback/history', 'FeedbackController->getPharmacyFeedbacksHistory');
$f3->route('GET /web/pharmacy/feedback/@orderId', 'FeedbackController->getPharmacyFeedback');
$f3->route('POST /web/pharmacy/feedback', 'FeedbackController->postPharmacyFeedback');

$f3->route('POST /web/pharmacy/feedback/pending', 'FeedbackController->postPharmacyFeedbacksPending');
$f3->route('POST /web/pharmacy/feedback/history', 'FeedbackController->postPharmacyFeedbacksHistory');
