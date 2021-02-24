<?php

$f3->route('GET /web/demo/order/add', 'DemoController->getGenerateOrder');

$f3->route('GET /', 'LandingController->get');
$f3->route('GET /@lang', 'LandingController->get');

$f3->route('GET /web/auth/signin', 'AuthController->getSignIn');
$f3->route('POST /web/auth/signin_nofirebase', 'AuthController->postSignIn_NoFirebase');
$f3->route('POST /web/auth/signin', 'AuthController->postSignIn');

$f3->route('GET /web/auth/signup', 'AuthController->getSignUp');
$f3->route('POST /web/auth/signup', 'AuthController->postSignUp');
$f3->route('POST /web/auth/signup/validate/step1', 'AuthController->postSignUpValidateStep1');
$f3->route('GET /web/auth/city/list/@countryId', 'AuthController->getCityByCountryList');
$f3->route('POST /web/auth/signup/document/upload', 'AuthController->postSignUpDocumentUpload');
$f3->route('GET /web/auth/forgot', 'AuthController->getForgottenPassword');
$f3->route('POST /web/auth/forgot', 'AuthController->postForgottenPassword');
$f3->route('GET /web/auth/reset', 'AuthController->getResetPassword');
$f3->route('POST /web/auth/reset', 'AuthController->postResetPassword');
$f3->route('GET /web/auth/verify/account', 'AuthController->getVerifyAccount');
$f3->route('GET /web/auth/approve/account', 'AuthController->getApproveAccount');

$f3->route('GET /web/auth/signout', 'AuthController->getSignOut');

$f3->route('GET /web', 'DashboardController->get');
$f3->route('GET /web/dashboard', 'DashboardController->getDashboard');
$f3->route('POST /web/notification/support', 'NotificationController->support');

$f3->route('GET /web/me/menu', 'UserController->getMenu');
$f3->route('GET /web/me/switchLanguage/@lang', 'UserController->switchLanguage');
$f3->route('POST /web/me/update-token', 'UserController->updateToken');

$f3->route('GET /web/pharmacy/product/search', 'SearchController->getSearchProducts');
$f3->route('POST /web/pharmacy/product/search', 'SearchController->postSearchProducts');
$f3->route('POST /web/pharmacy/product/search/@sort', 'SearchController->postSearchProducts');
$f3->route('GET /web/entity/@entityId/product/@productId', 'ProductsController->getEntityProduct');
$f3->route('POST /web/entity/@entityId/product/@productId', 'ProductsController->postEntityProduct');

$f3->route('GET /web/product/brandname/list', 'SearchController->getProductBrandNameList');
$f3->route('GET /web/product/category', 'SearchController->getAllCategoryList');
$f3->route('GET /web/product/scientificname/list', 'SearchController->getProductScientificNameList');
$f3->route('GET /web/product/country/list', 'SearchController->getProductCountryList');
$f3->route('GET /web/product/category/list', 'SearchController->getProductCategoryList');
$f3->route('GET /web/product/subcategory/list/@categoryId', 'SearchController->getProductSubcategoryByCategoryList');
$f3->route('GET /web/product/ingredient/list', 'SearchController->getProductIngredientList');
$f3->route('GET /web/order/customer/list', 'SearchController->getOrderBuyerList');
$f3->route('GET /web/order/Distributor/list', 'SearchController->getOrderSellerList');
$f3->route('GET /web/order/Distributor/listAll', 'SearchController->getAllSellerList');
$f3->route('GET /web/order/Distributor/listAvailable', 'SearchController->getAvailableSellerList');
$f3->route('GET /web/customer/group/list/@entityId', 'SearchController->getRelationGroupByEnitityList');
$f3->route('GET /web/city/list/@countryId', 'SearchController->getCityByCountryList');
$f3->route('GET /web/country/list/@entityId', 'SearchController->getCountryByEntityList');
$f3->route('GET /web/customername/list/@entityId', 'SearchController->getCustomerNameByEntityList');

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
$f3->route('POST /web/cart/checkout/submit', 'CartController->postCartCheckoutSubmit');
$f3->route('POST /web/cart/checkout/submit/confirm', 'CartController->postCartCheckoutSubmitConfirmation');
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
$f3->route('GET /web/distributor/product/stock/@productId', 'ProductsController->getProductStockDetails');

$f3->route('POST /web/distributor/product', 'ProductsController->postDistributorProducts');
$f3->route('GET /web/distributor/product/list', 'ProductsController->getProductList');
$f3->route('POST /web/distributor/product/add', 'ProductsController->postAddDistributorProduct');
$f3->route('POST /web/distributor/product/edit', 'ProductsController->postEditDistributorProduct');
$f3->route('POST /web/distributor/product/image', 'ProductsController->postProductImage');
$f3->route('POST /web/distributor/product/subimage', 'ProductsController->postProductSubimage');
$f3->route('POST /web/distributor/product/editQuantity', 'ProductsController->postEditQuantityDistributorProduct');
$f3->route('POST /web/distributor/product/editStock', 'ProductsController->postEditStockDistributorProduct');
$f3->route('GET /web/distributor/product/canAdd', 'ProductsController->getDistributorCanAddProduct');

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
$f3->route('GET /web/distributor/customer/relation/@entityBuyerId/@entitySellerId', 'EntityController->getEntityCustomerRelationDetails');
$f3->route('POST /web/distributor/customer', 'EntityController->postEntityCustomers');
$f3->route('POST /web/distributor/customer/edit/group', 'EntityController->postEntityCustomersEditGroup');

// Customer Group
$f3->route('GET /web/distributor/customer/group', 'EntityController->getEntityCustomerGroup');
$f3->route('POST /web/distributor/customer/group', 'EntityController->postEntityCustomerGroup');
$f3->route('GET /web/distributor/customer/group/@customerGroupId', 'EntityController->getEntityCustomerGroupDetails');
$f3->route('POST /web/distributor/customer/group/edit', 'EntityController->postEntityCustomerGroupEdit');

$f3->route('GET /web/distributor/customer/feedback', 'CustomersController->getOrderCustomersFeedback');
$f3->route('POST /web/distributor/customer/feedback', 'CustomersController->postOrderCustomersFeedback');

// START dashboard
$f3->route('POST /web/distributor/order/recent', 'OrderController->postDistributorOrdersRecent');
$f3->route('POST /web/distributor/product/bestselling', 'ProductsController->postDistributorProductsBestSelling');

$f3->route('POST /web/pharmacy/order/recent', 'OrderController->postPharmacyOrdersRecent');
// END dashboard

$f3->route('GET /web/notification/order/new', 'OrderController->getNotificationsDistributorOrdersNew');


$f3->route('GET /web/pharmacy/order/pending', 'OrderController->getPharmacyOrdersPending');
$f3->route('GET /web/pharmacy/order/unpaid', 'OrderController->getPharmacyOrdersUnpaid');
$f3->route('GET /web/pharmacy/order/history', 'OrderController->getPharmacyOrdersHistory');
$f3->route('GET /web/pharmacy/order/@orderId', 'OrderController->getOrderDetails');

$f3->route('POST /web/pharmacy/order/pending', 'OrderController->postPharmacyOrdersPending');
$f3->route('POST /web/pharmacy/order/unpaid', 'OrderController->postPharmacyOrdersUnpaid');
$f3->route('POST /web/pharmacy/order/history', 'OrderController->postPharmacyOrdersHistory');

$f3->route('POST /web/pharmacy/order/missingProducts', 'OrderController->postPharmacyMissingProducts');
$f3->route('POST /web/distributor/order/editQuantityOrder', 'OrderController->postEditQuantityOrder');

$f3->route('GET /web/pharmacy/order/print/@orderId', 'OrderController->getPrintOrderPharmacyInvoice');

$f3->route('GET /web/pharmacy/feedback/pending', 'FeedbackController->getPharmacyFeedbacksPending');
$f3->route('GET /web/pharmacy/feedback/history', 'FeedbackController->getPharmacyFeedbacksHistory');
$f3->route('GET /web/pharmacy/feedback/@orderId', 'FeedbackController->getPharmacyFeedback');
$f3->route('POST /web/pharmacy/feedback', 'FeedbackController->postPharmacyFeedback');

$f3->route('POST /web/pharmacy/feedback/pending', 'FeedbackController->postPharmacyFeedbacksPending');
$f3->route('POST /web/pharmacy/feedback/history', 'FeedbackController->postPharmacyFeedbacksHistory');

$f3->route('GET /web/profile', 'ProfileController->getProfile');
$f3->route('POST /web/profile/document/upload', 'ProfileController->postProfileDocumentUpload');
$f3->route('POST /web/profile/image', 'ProfileController->postProfileImageUpload');
$f3->route('POST /web/pharmacy/profile/myProfile', 'ProfileController->postPharmacyProfileMyProfile');
$f3->route('POST /web/pharmacy/profile/accountSetting', 'ProfileController->postPharmacyProfileAccountSetting');
$f3->route('POST /web/distributor/profile/myProfile', 'ProfileController->postDistributorProfileMyProfile');
$f3->route('POST /web/distributor/profile/accountSetting', 'ProfileController->postDistributorProfileAccountSetting');
$f3->route('POST /web/distributor/profile/paymentSetting', 'ProfileController->postDistributorProfilePaymentSetting');

$f3->route('GET /web/review/pharmacy/profile/approve', 'ReviewController->getReviewPharmacyProfileApprove');
$f3->route('GET /web/review/distributor/profile/approve', 'ReviewController->getReviewDistributorProfileApprove');


$f3->route('GET /web/test/welcome/email/atrash', 'AuthController->getProcessPharmacies');
$f3->route('GET /web/auth/onboarding/activate/pharmacy', 'AuthController->getProcessEmailOnboarding');


include_once('routes-permission.php');