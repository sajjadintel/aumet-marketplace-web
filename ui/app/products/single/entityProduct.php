<?php
ob_start("compress_htmlcode");
function compress_htmlcode($codedata)
{
    $searchdata = array(
        '/\>[^\S ]+/s', // remove whitespaces after tags
        '/[^\S ]+\</s', // remove whitespaces before tags
        '/(\s)+/s' // remove multiple whitespace sequences
    );
    $replacedata = array('>', '<', '\\1');
    $codedata = preg_replace($searchdata, $replacedata, $codedata);
    return $codedata;
}

?>
    <!--begin::Container-->
    <div class="container">
        <div class="d-flex flex-row">

            <div class="flex-row-fluid">


                <div class="card card-custom gutter-b">
                    <div class="card-header border-0 py-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-primary"></span>
                            <span class="text-muted mt-3 font-weight-bold font-size-sm"></span>
                        </h3>
                    </div>

                    <div class="card-body d-flex rounded flex-column flex-md-row flex-lg-column flex-xxl-row">
                        <div class="row w-100 product-detail">

                            <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12">
                                <img src="<?php echo $objEntityProduct->image ?>" alt="<?php echo $objEntityProduct->{"productName_" . $_SESSION['userLang']} ?>">
                            </div>

                            <div class="col-xxl-6 col-xl-6 col-lg-6 col-md-6 col-sm-12 col-xs-12 col-12">
                                <div>
                                    <h3 class=" font-size-h2 mb-1">
                                        <span class="font-weight-boldest"><?php echo $objEntityProduct->scientificName ?></span>
                                        <span class="font-weight-normal"><?php echo $objEntityProduct->{"productName_" . $_SESSION['userLang']} ?></span>
                                    </h3>
                                </div>


                                <div class="product-price-wrapper">

                                    <div>
                                        <span class="distributor-name"><?php echo $objEntityProduct->{"entityName_" . $_SESSION['userLang']} ?></spancl>
                                    </div>

                                    <hr>

                                    <div>
                                        <div>

                                        </div>
                                        <div>

                                        </div>
                                        <div id="product-actions" class="cart-buttons-product-detail">

                                            <div class="price">
                                                <div class="title"><?php echo $vModule_product_pricePerUnit ?></div>
                                                <div class="value">
                                                    <?php echo $objEntityProduct->unitPrice . ' ' . $objEntityProduct->currency ?>
                                                </div>
                                            </div>

                                            <?php if ($objEntityProduct->bonusTypeId == 2 && $objEntityProduct->bonusOptions != null) { ?>
                                                <div class="bonus">
                                                    <div class="title"><?php echo $vModule_cart_bonus ?></div>
                                                    <div class="value"><?php echo $objEntityProduct->bonusOptions[0]->name ?></div>
                                                </div>
                                            <?php } ?>


                                            <?php if (Helper::isPharmacy($_SESSION['objUser']->roleId) && $objEntityProduct->stockStatusId == 1) { ?>
                                                <div class="add-to-cart-wrapper">
                                                    <input id="quantity-<?php echo $objEntityProduct->id ?>" type="number" min="1" value="1" />
                                                    <div id="addToCart" onclick="addToCart()"><?php echo $vModule_product_addToCart ?></div>
                                                </div>
                                            <?php } ?>

                                        </div>
                                    </div>

                                </div>

                                <div class="product-item-details">

                                    <h3>Details</h3>

                                    <div class="d-flex mb-3">
                                        <span class="title"><?php echo $vModule_product_madeIn ?></span>
                                        <div class="country">
                                            <img src="/assets/img/countries/<?php echo strtolower(Helper::getCountryIso($objEntityProduct->madeInCountryName_en)) ?>.svg" style="width:34px;height:18px;">
                                            <span class="value"><?php echo $objEntityProduct->{"madeInCountryName_" . $_SESSION['userLang']} ?></span>
                                        </div>
                                    </div>
                                    <div class="d-flex mb-3">
                                        <span class="title"><?php echo $vModule_product_scientificName ?></span>
                                        <span class="value"><?php echo $objEntityProduct->scientificName ?></span>
                                    </div>
                                    <div class="d-flex mb-3">
                                        <span class="title"><?php echo $vModule_product_stockStatus ?></span>
                                        <span class="value"><?php echo $objEntityProduct->{"stockStatusName_" . $_SESSION['userLang']} ?></span>
                                    </div>
                                    <div class="d-flex mb-3">
                                        <span class="title"><?php echo $vModule_product_stockStatusDate ?></span>
                                        <span class="value"><?php echo (new DateTime($objEntityProduct->stockUpdateDateTime))->format('d/m/Y'); ?></span>
                                    </div>

                                </div>

                            </div>


                        </div>

                    </div>
                </div>


                <div>
                    <div class="product-item-similar-header">
                        <h3><?php echo $vModule_product_otherFromSameDistributor ?></h3>

                        <a href="javascript:;" onclick="viewAllSameDistributor()">View All</a>
                    </div>

                    <div class="row">
                        <?php foreach ($arrProductFromThisDistributor as $objItem) : ?>
                            <div class="col-xxl-2 col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 col-6" style="overflow: hidden">
                                <a href="javascript:;" onclick="WebApp.loadSubPage('/web/entity/<?php echo $objItem->entityId ?>/product/<?php echo $objItem->productId ?>')" class="text-dark text-hover-primary">
                                    <div class="card card-custom product-item">

                                        <div class="image-wrapper">
                                            <div class="image">
                                                <img src="<?php echo $objItem->image ?>" style="">
                                            </div>
                                            <div class="image-hover-wrapper">
                                                <div class="view-button">View</div>
                                            </div>
                                        </div>

                                        <div class="product-item-content">
                                            <span class="title"><?php echo $objItem->{"productName_" . $_SESSION['userLang']} ?></span>
                                            <span class="price"><?php echo $objItem->unitPrice . ' ' . $objEntityProduct->currency ?></span>
                                        </div>

                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div>
                    <div class="product-item-similar-header">
                        <h3 class="products-item-title">
                            <?php echo $vModule_product_other ?>
                            <?php echo $objEntityProduct->scientificName ?>
                        </h3>

                        <a href="javascript:;" onclick="viewAllSameScientificName()">View All</a>
                    </div>


                    <div class="row">
                        <?php foreach ($arrRelatedEntityProduct as $objItem) : ?>
                            <div class="col-xxl-2 col-xl-2 col-lg-3 col-md-4 col-sm-6 col-xs-6 col-6" style="overflow: hidden">
                                <a href="javascript:;" onclick="WebApp.loadSubPage('/web/entity/<?php echo $objItem->entityId ?>/product/<?php echo $objItem->productId ?>')" class="text-dark text-hover-primary">
                                    <div class="card card-custom product-item">

                                        <div class="image-wrapper">
                                            <div class="image">
                                                <img src="<?php echo $objItem->image ?>" style="">
                                            </div>
                                            <div class="image-hover-wrapper">
                                                <div class="view-button">View</div>
                                            </div>
                                        </div>

                                        <div class="product-item-content">
                                            <span class="title"><?php echo $objItem->{"productName_" . $_SESSION['userLang']} ?></span>
                                            <span class="price"><?php echo $objItem->unitPrice . ' ' . $objEntityProduct->currency ?></span>
                                        </div>

                                    </div>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>


            </div>

        </div>
    </div>

    <script>
        var row = <?= json_encode(['id' => $objEntityProduct->id, 'entityId' => $objEntityProduct->entityId, 'productId' => $objEntityProduct->productId, 'quantity' => 1]); ?>;

        function addToCart() {
            SearchDataTable.onClickAddMoreToCart(row);
        }

        function viewAllSameDistributor() {
            WebApp.loadPage('/web/product/search?distributorId=<?php echo $objEntityProduct->entityId ?>');
        }

        function viewAllSameScientificName() {
            WebApp.loadPage('/web/product/search?scientificNameId=<?php echo $objEntityProduct->scientificNameId?>');
        }

    </script>
    <!--end::Container-->
<?php ob_end_flush(); ?>