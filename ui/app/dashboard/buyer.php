<style>
    .slick-dots {
        position: absolute;
        bottom: 20px;
        display: block;
        width: 100%;
        padding: 10;
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
</style>
<!--begin::Entry-->
<div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Dashboard-->
        <div class="row">

            <!--begin::Product-->
            <div class="col-9">
                <div class="card card-custom card-body card-stretch gutter-b">
                    <!--begin::Banner-->
                    <div class="mb-10">
                        <div class="autoplay" style="height: 280px;">
                            <?php foreach($arrBanners as $banner) : ?>
                                <div style="background-image: url('<?php echo $banner->image; ?>'); height: 280px;">
                                    <?php if ($objUser->language == "ar"): ?>
                                        <div style="height: 100%; display: flex; align-items: center; justify-content: flex-end;">
                                            <div class="col-5" style="margin-right: 50px; text-align: right;">
                                                <h1 style="color: #FFF; font-size: 30px; font-weight: 700;"><?php echo $banner->title; ?></h1>
                                                <h1 style="color: #FFF; font-size: 30px; font-weight: 700;"><?php echo $banner->subtitle; ?></h1>
                                                <?php if(!is_null($banner->buttonText)) : ?>
                                                    <button type="button" class="btn btn-primary btn-md mt-5" style="width: 140px; background-color: #1378BE; border-color: #1378BE;"  onclick="WebApp.loadSubPage('/web/entity/<?php echo $banner->entityId; ?>/product/<?php echo $banner->productId; ?>');"><?php echo $banner->buttonText; ?></button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php else : ?>
                                        <div style="height: 100%; display: flex; align-items: center;">
                                            <div class="col-5" style="margin-left: 50px">
                                                <h1 style="color: #FFF; font-size: 30px; font-weight: 700;"><?php echo $banner->title; ?></h1>
                                                <h1 style="color: #FFF; font-size: 30px; font-weight: 700;"><?php echo $banner->subtitle; ?></h1>
                                                <?php if(!is_null($banner->buttonText)) : ?>
                                                    <button type="button" class="btn btn-primary btn-md mt-5" style="width: 140px; background-color: #1378BE; border-color: #1378BE;" onclick="WebApp.loadSubPage('/web/entity/<?php echo $banner->entityId; ?>/product/<?php echo $banner->productId; ?>');"><?php echo $banner->buttonText; ?></button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!--begin::New Products-->
                    <div class="mb-10">
                        <div class="row pb-6" style="justify-content: space-between;">
                            <div class="col-3">
                                <span class="card-label font-weight-bolder font-size-h3"><?php echo $vModule_dashboardBuyer_newProducts ?></span>
                            </div>
                            <div class="col-3" style="display: flex; justify-content: flex-end;">
                                <a class="btn btn-light-primary font-weight-bold mr-2" onclick="WebApp.loadPage('/web/product/search?sort=newest')">
                                    <?php echo $vButton_view_all; ?>
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <?php foreach($arrNewestProducts as $product) : ?>
                                <div class="col-3">
                                    <div class="symbol flex-shrink-0 bg-light mb-4" style="width: 100%; height: 150px;">
                                        <div class="symbol-label" style="background-image: url('<?php echo $product->image; ?>'); width: 100%; height: 100%;"></div>
                                    </div>
                                    <p style="text-align: center; font-weight: bold;"><?php echo $product->name; ?></p>
                                    <p style="text-align: center;"><?php echo $product->price; ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <!--begin::Top Selling-->
                    <div>
                        <div class="row pb-6" style="justify-content: space-between;">
                            <div class="col-3">
                                <span class="card-label font-weight-bolder font-size-h3"><?php echo $vModule_dashboardBuyer_topSelling ?></span>
                            </div>
                            <div class="col-3" style="display: flex; justify-content: flex-end;">
                                <a class="btn btn-light-primary font-weight-bold mr-2" onclick="WebApp.loadPage('/web/product/search?sort=top-selling')">
                                    <?php echo $vButton_view_all; ?>
                                </a>
                            </div>
                        </div>
                        <div class="row">
                            <?php foreach($arrTopSellingProducts as $product) : ?>
                                <div class="col-3">
                                    <div class="symbol flex-shrink-0 bg-light mb-4" style="width: 100%; height: 150px;">
                                        <div class="symbol-label" style="background-image: url('<?php echo $product->image; ?>'); width: 100%; height: 100%;"></div>
                                    </div>
                                    <p style="text-align: center; font-weight: bold;"><?php echo $product->name; ?></p>
                                    <p style="text-align: center;"><?php echo $product->price; ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.autoplay').slick({
            arrows: false,
            dots: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            autoplay: true,
            fade: true,
            autoplaySpeed: 5000,
        });
    })
</script>