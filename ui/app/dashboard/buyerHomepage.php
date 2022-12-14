<style>
    .slick-dots {
        position: absolute;
        bottom: 20px;
        display: block;
        width: 100%;
        padding: 10px;
        margin: 0;
        list-style: none;
        text-align: center;
    }

    .slick-dots li {
        position: relative;
        display: inline-block;
        width: 10px;
        height: 10px;
        margin: 0 1px;
        padding: 0;
        cursor: pointer;
    }

    .slick-dots li button:before {
        color: #FFF;
    }

    .slick-dots li.slick-active button:before {
        color: #FFF;
    }

    @media only screen and (max-width: 992px) {
        .dynamic-image {
	        width: 100%;
            height: 100px;
	        background-color: #f3f3f3 !important;
            /*margin: auto;*/
        }
    }

    @media only screen and (min-width: 992px) {
        .dynamic-image {
	        width: 100%;
            height: 150px;
	        background-color: #f3f3f3 !important;
	        /*margin: auto;*/
        }
    }

    @media only screen and (min-width: 1440px) {
        .dynamic-image {
            width: 100%;
            height: 200px;
	        background-color: #f3f3f3 !important;
          padding: 5px;
	        /*margin: auto;*/
        }
    }
</style>
<!--begin::Entry-->
<div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Dashboard-->
        <div class="row">

            <!--begin::Main-->
            <div class="col-9">
                <?php if (count($arrBanner) > 0) : ?>
                    <div class="row">
                        <div class="col-12">
                            <div class="card card-custom card-body card-stretch gutter-b">
                                <!--begin::Banner-->
                                <div class="slick-carousel m-0">
                                    <?php foreach ($arrBanner as $banner) : ?>
                                        <div class="item">
                                            <div class="img-fill">
                                                <img src="<?php echo $banner->image; ?>" alt="" style="max-height: 400px; width: auto; max-width: 100%">
                                                <div class="info">
                                                    <?php if ((($objUser->language == "ar") && ($banner->styleEn == 'ltr') || ($objUser->language !== "ar") && ($banner->styleEn == 'rtl'))) : ?>
                                                        <div class="col-md-5 offset-md-2 text-right h-100">
                                                            <h1 class="slick-hero-title"><?php echo $banner->title; ?></h1>
                                                            <h1 class="slick-hero-subtitle"><?php echo $banner->subtitle; ?></h1>
                                                            <?php if (!is_null($banner->buttonText)) : ?>
                                                                <button type="button" class="btn btn-primary btn-md mt-5 slick-hero-button" onclick="WebApp.loadSubPage('<?php echo $banner->buttonUrl; ?>');"><?php echo $banner->buttonText; ?></button>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php else : ?>
                                                        <div class="col-md-5 offset-md-2 text-left h-100">
                                                            <h1 class="slick-hero-title"><?php echo $banner->title; ?></h1>
                                                            <h1 class="slick-hero-subtitle"><?php echo $banner->subtitle; ?></h1>
                                                            <?php if (!is_null($banner->buttonText)) : ?>
                                                                <button type="button" class="btn btn-primary btn-md mt-5 slick-hero-button" onclick="WebApp.loadSubPage('<?php echo $banner->buttonUrl; ?>');"><?php echo $banner->buttonText; ?></button>
                                                            <?php endif; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <!--begin::New Products-->
                <div class="mb-10">
                    <div class="card card-custom card-body card-stretch gutter-b">
                        <div class="row pb-6" style="justify-content: space-between; align-items: center;">
                            <div class="col-3">
                                <span class="card-label font-weight-bolder font-size-h3 home-page-title"><?php echo $vModule_homepageBuyer_newProducts ?></span>
                            </div>
                            <div class="col-3" style="display: flex; justify-content: flex-end;">
                                <a class="btn btn-light-primary font-weight-bold" onclick="WebApp.loadPage('/web/pharmacy/product/search?sort=newest')">
                                    <?php echo $vButton_view_all; ?>
                                </a>
                            </div>
                        </div>
                        <div class="row mb-10">
                            <script>
                                var productItemListGTM =[];
                            </script>
                            <?php foreach ($arrNewestProducts as $product) : ?>
                                <div class="col-3 product-container">
                                    <div class="img-fill flex-shrink-0 bg-light mb-4 dynamic-image">
                                        <img class="productImage image-contain datalayer-image-click" src="<?php echo $product->image; ?>" style="cursor: pointer; width: 100%; height: 100%;" onclick="WebApp.loadSubPage('/web/entity/<?php echo $product->entityId; ?>/product/<?php echo $product->id; ?>');">
                                    </div>
                                    <p class="text-hover-primary datalayer-text-click" style="cursor: pointer; text-align: center; font-weight: bold;" onclick="WebApp.loadSubPage('/web/entity/<?php echo $product->entityId; ?>/product/<?php echo $product->id; ?>');"><?php echo $product->name; ?></p>
                                    <p style="text-align: center;"><?php echo $product->price; ?></p>

                                <?php $productListName="New products";$productListId="LNNEWPRODUCTS";//This is added for Google Datalayer segregation ?>
                                <?php include "productData.php"; ?>
                                </div>
                            <?php endforeach; ?>
                            <script>
                                $( document ).ready(function() {
                                    dataLayer.push({
                                        'event': 'view_item_list',
                                        'ecommerce': {
                                            'currency':'AED',
                                            'items': [
                                                productItemListGTM
                                            ]
                                        }
                                    });

                                });
                            </script>

                        </div>
                    </div>
                </div>
                <!--begin::Top Selling-->
                <div>
                    <div class="card card-custom card-body card-stretch gutter-b">
                        <div class="row pb-6" style="justify-content: space-between; align-items: center;">
                            <div class="col-3">
                                <span class="card-label font-weight-bolder font-size-h3 home-page-title"><?php echo $vModule_homepageBuyer_topSelling ?></span>
                            </div>
                            <div class="col-3" style="display: flex; justify-content: flex-end;">
                                <a class="btn btn-light-primary font-weight-bold" onclick="WebApp.loadPage('/web/pharmacy/product/search?sort=top-selling')">
                                    <?php echo $vButton_view_all; ?>
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <script>
                                var productItemListGTMTop =[];
                            </script>
                            <?php foreach ($arrTopSellingProducts as $product) : ?>
                                <div class="col-3 product-container">
                                    <div class="img-fill flex-shrink-0 bg-light mb-4 dynamic-image">
                                        <img class="productImage image-contain datalayer-image-click" src="<?php echo $product->image; ?>" style="cursor: pointer; width: 100%; height: 100%;" onclick="WebApp.loadSubPage('/web/entity/<?php echo $product->entityId; ?>/product/<?php echo $product->id; ?>');">
                                    </div>
                                    <p class="text-hover-primary datalayer-text-click" style="cursor: pointer; text-align: center; font-weight: bold;" onclick="WebApp.loadSubPage('/web/entity/<?php echo $product->entityId; ?>/product/<?php echo $product->id; ?>');"><?php echo $product->name; ?></p>
                                    <p style="text-align: center;"><?php echo $product->price; ?></p>

                                <?php $productListName="Top selling";$productListId="LNTOPSELLING";//This is added for Google Datalayer segregation ?>
                                <?php include "productData.php"; ?>
                                </div>
                            <?php endforeach; ?>
                            <script>
                                $( document ).ready(function() {
                                    dataLayer.push({
                                        'event': 'view_item_list',
                                        'ecommerce': {
                                            'currency':'AED',
                                            'items': [
                                                productItemListGTMTop
                                            ]
                                        }
                                    });

                                });
                            </script>
                        </div>
                    </div>
                </div>


            </div>
            <!--begin::Right Side-->
            <div class="col-3">
                <!--begin::Pending Orders-->
                <?php if (count($arrPendingOrders) > 0) : ?>
                    <div class="card card-custom card-body card-stretch gutter-b" style="height: auto; padding-right: 1rem; padding-left: 1rem;">
                        <div class="row" style="justify-content: space-between; align-items: center;">
                            <div class="col-7">
                                <span class="card-label font-weight-bolder font-size-h3"><?php echo $vModule_homepageBuyer_pendingOrders ?></span>
                            </div>
                            <div class="col-5" style="display: flex; justify-content: flex-end;">
                                <a class="btn btn-light-primary font-weight-bold" onclick="WebApp.loadPage('/web/pharmacy/order/pending')">
                                    <?php echo $vButton_view_all; ?>
                                </a>
                            </div>
                        </div>
                        <?php foreach ($arrPendingOrders as $order) : ?>
                            <div class="mt-10">
                                <div class="row mb-5">
                                    <div class="col-7">
                                        <span class="card-label font-weight-bolder font-size-h4"><?php echo $order['entitySeller']; ?></span>
                                    </div>
                                    <div class="col-5" style="display: flex; justify-content: flex-end;">
                                        <span class="statusLabel label label-lg font-weight-bold label-inline" style="width: max-content;" data-statusId="<?php echo $order['statusId']; ?>"></span>
                                    </div>
                                </div>
                                <div style="background-color: #F0F0F0; padding-left: 1rem; padding-right: 1rem;">
                                    <?php foreach ($mapOrderIdOrderDetails[$order['id']] as $orderDetail) : ?>
                                        <div class="row" style="align-items: center;justify-content: center;padding: 1rem;">
                                            <div class="col-9">
                                                <?php echo $orderDetail['productName']; ?>
                                            </div>
                                            <div class="col-3">
                                                x<?php echo $orderDetail['quantity'] + $orderDetail['quantityFree']; ?>
                                            </div>
                                        </div>
                                        <div style="border-bottom: 1px solid #CFCFCF;"></div>
                                    <?php endforeach; ?>
                                    <div class="row" style="align-items: center;justify-content: center;padding: 1rem;">
                                        <div class="col-12 font-weight-bolder" style="text-align: right;">
                                            Total: <?php echo $order['total'] . " " . $order['currency']; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <!--begin::Top Distributors-->
                <?php if (count($arrTopDistributors) > 0) : ?>
                    <div class="card card-custom card-body card-stretch gutter-b" style="height: auto; padding-right: 1rem; padding-left: 1rem;">
                        <div class="row" style="justify-content: space-between; align-items: center;">
                            <div class="col-12">
                                <span class="card-label font-weight-bolder font-size-h3"><?php echo $vModule_homepageBuyer_topDistributors ?></span>
                            </div>
                        </div>
                        <div>
                            <?php foreach ($arrTopDistributors as $entity) : ?>
                                <div class="font-size-h5" style="padding-top: 20px;display: flex;justify-content: space-between;align-items: center; cursor: pointer;" onclick="WebApp.loadPage('/web/pharmacy/product/search?distributorId=<?php echo $entity->id; ?>');">
                                    <div class="col-9">
                                        <?php echo $entity->name; ?>
                                    </div>
                                    <div class="col-3">
                                        <a class="btn btn-icon btn-light-success">
                                            <i class="flaticon2-next"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        initAutoplay();
        fillStatusLabel();
    });

    function initAutoplay() {
        $('.slick-carousel').slick({
            arrows: false,
            dots: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: true,
            fade: true,
            autoplaySpeed: 5000,
        });
    }

    function fillStatusLabel() {
        var status = {
            1: {
                title: WebAppLocals.getMessage('orderStatus_Pending'),
                class: ' label-primary2',
            },
            2: {
                title: WebAppLocals.getMessage('orderStatus_OnHold'),
                class: ' label-warning',
            },
            3: {
                title: WebAppLocals.getMessage('orderStatus_Processing'),
                class: ' label-primary',
            },
            4: {
                title: WebAppLocals.getMessage('orderStatus_Completed'),
                class: ' label-primary',
            },
            5: {
                title: WebAppLocals.getMessage('orderStatus_Canceled'),
                class: ' label-danger',
            },
            6: {
                title: WebAppLocals.getMessage('orderStatus_Received'),
                class: ' label-primary',
            },
            7: {
                title: WebAppLocals.getMessage('orderStatus_Paid'),
                class: ' label-success',
            },
            8: {
                title: WebAppLocals.getMessage('orderStatus_MissingProducts'),
                class: ' label-danger',
            },
            9: {
                title: WebAppLocals.getMessage('orderStatus_Canceled_Pharmacy'),
                class: ' label-danger',
            }
        };

        $('.statusLabel').each(function(index, element) {
            var statusId = $(element).attr("data-statusId");
            $(element).addClass(status[statusId].class);
            $(element).html(status[statusId].title);
        });
    }

    $('.productImage').on("error", function() {
        $(this).attr('src', '/assets/img/default-product-image.png');
    });
</script>