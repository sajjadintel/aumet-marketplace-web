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
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-7">
                        <h2 class="font-weight-bolder text-primary font-size-h3 mb-0"><?php echo $vModule_cart_offersTitle ?></h2>
                        <a href="#" class="btn btn-light-primary btn-sm font-weight-bolder"><?php echo $vModule_cart_offersMore ?></a>
                    </div>

                    <div class="row">

                        <div class="col-md-4 col-xxl-4 col-lg-12">
                            <div class="card card-custom card-shadowless">
                                <div class="card-body p-0">
                                    <div class="overlay">
                                        <div class="overlay-wrapper rounded bg-light text-center">
                                            <img src="/assets/img/sale1.png" alt="" class="mw-100 w-200px">
                                        </div>
                                        <div class="overlay-layer">
                                            <a href="#" class="btn font-weight-bolder btn-sm btn-primary mr-2"></a>
                                            <a href="#" class="btn font-weight-bolder btn-sm btn-light-primary"></a>
                                        </div>
                                    </div>
                                    <div class="text-center mt-5 mb-md-0 mb-lg-5 mb-md-0 mb-lg-5 mb-lg-0 mb-5 d-flex flex-column">
                                        <a href="#" class="font-size-h5 font-weight-bolder text-dark-75 text-hover-primary mb-1"></a>
                                        <span class="font-size-lg"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-xxl-4 col-lg-12">
                            <div class="card card-custom card-shadowless">
                                <div class="card-body p-0">
                                    <div class="overlay">
                                        <div class="overlay-wrapper rounded bg-light text-center">
                                            <img src="/assets/img/sale2.png" alt="" class="mw-100 w-200px">
                                        </div>
                                        <div class="overlay-layer">
                                            <a href="#" class="btn font-weight-bolder btn-sm btn-primary mr-2"></a>
                                            <a href="#" class="btn font-weight-bolder btn-sm btn-light-primary"></a>
                                        </div>
                                    </div>
                                    <div class="text-center mt-5 mb-md-0 mb-lg-5 mb-md-0 mb-lg-5 mb-lg-0 mb-5 d-flex flex-column">
                                        <a href="#" class="font-size-h5 font-weight-bolder text-dark-75 text-hover-primary mb-1"></a>
                                        <span class="font-size-lg"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-xxl-4 col-lg-12">
                            <div class="card card-custom card-shadowless">
                                <div class="card-body p-0">
                                    <div class="overlay">
                                        <div class="overlay-wrapper rounded bg-light text-center">
                                            <img src="/assets/img/sale3.png" alt="" class="mw-100 w-200px">
                                        </div>
                                        <div class="overlay-layer">
                                            <a href="#" class="btn font-weight-bolder btn-sm btn-primary mr-2"></a>
                                            <a href="#" class="btn font-weight-bolder btn-sm btn-light-primary"></a>
                                        </div>
                                    </div>
                                    <div class="text-center mt-5 mb-md-0 mb-lg-5 mb-md-0 mb-lg-5 mb-lg-0 mb-5 d-flex flex-column">
                                        <a href="#" class="font-size-h5 font-weight-bolder text-dark-75 text-hover-primary mb-1"></a>
                                        <span class="font-size-lg"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <div class="card card-custom ">

                <div class="card-header flex-wrap border-0 pt-6 pb-0">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder font-size-h3 text-primary"><?php echo $vModule_cart_title ?></span>
                    </h3>
                    <div class="card-toolbar">
                        <div class="dropdown dropdown-inline">
                            <a href="javascript:;" class="btn btn-primary font-weight-bolder font-size-sm" onclick="WebApp.loadPage('/web/pharmacy/product/search');"><?php echo $vModule_cart_continueShopping ?></a>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    <div class="table-responsive">
                        <table class="table">

                            <thead>
                                <tr>
                                    <th><?php echo $vModule_product_name ?></th>
                                    <th><?php echo $vModule_product_distributor ?></th>
                                    <th class="text-center"><?php echo $vModule_cart_quantity ?></th>
                                    <th class="text-right"><?php echo $vModule_cart_unitPrice ?></th>
                                    <th class="text-right"><?php echo $vModule_cart_productOrderPrice ?></th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $totalPrice = 0;
                                $arrEntityTotalPrice = [];

                                ?>
                                <?php foreach ($arrCartDetail as $objItem) : ?>

                                    <tr>
                                        <td class="d-flex align-items-center font-weight-bolder font-size-h5">

                                            <div class="symbol symbol-60 flex-shrink-0 mr-4 bg-light">
                                                <img class="productImage" src="<?php echo $objItem->image ?>">
                                            </div>
                                            <a href="javascript:;" onclick="WebApp.loadSubPage('/web/entity/<?php echo $objItem->entityId ?>/product/<?php echo $objItem->id ?>')" class="text-dark text-hover-primary"><?php echo $objItem->productName_en ?></a>
                                        </td>
                                        <td class="text-left align-middle "><?php echo $objItem->entityName_ar ?></td>
                                        <td class="text-center align-middle">
                                            <a href="javascript:;" class="btn btn-xs btn-light-success btn-icon mr-2">
                                                <i class="ki ki-minus icon-xs"></i>
                                            </a>
                                            <span class="mr-2 font-weight-bolder"><?php echo $objItem->quantity ?></span>
                                            <a href="javascript:;" class="btn btn-xs btn-light-success btn-icon">
                                                <i class="ki ki-plus icon-xs"></i>
                                            </a>
                                        </td>
                                        <td class="text-right align-middle font-weight-bolder font-size-h5"><?php echo $objItem->unitPrice ?></td>
                                        <td class="text-right align-middle font-weight-bolder font-size-h5"><?php echo $objItem->quantity  * $objItem->unitPrice ?></td>
                                        <td class="text-right align-middle">
                                            <a href="javascript:;" onclick="Cart.removeItem(<?php echo $objItem->id ?>, false)" class="btn btn-sm btn-light btn-text-danger btn-hover-primary btn-icon  mr-2" title="">
                                                <span class="svg-icon svg-icon-md">
                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24" />
                                                            <path d="M6,8 L6,20.5 C6,21.3284271 6.67157288,22 7.5,22 L16.5,22 C17.3284271,22 18,21.3284271 18,20.5 L18,8 L6,8 Z" fill="#000000" fill-rule="nonzero" />
                                                            <path d="M14,4.5 L14,4 C14,3.44771525 13.5522847,3 13,3 L11,3 C10.4477153,3 10,3.44771525 10,4 L10,4.5 L5.5,4.5 C5.22385763,4.5 5,4.72385763 5,5 L5,5.5 C5,5.77614237 5.22385763,6 5.5,6 L18.5,6 C18.7761424,6 19,5.77614237 19,5.5 L19,5 C19,4.72385763 18.7761424,4.5 18.5,4.5 L14,4.5 Z" fill="#000000" opacity="0.3" />
                                                        </g>
                                                    </svg>
                                                </span>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                    $totalPrice += ($objItem->quantity  * $objItem->unitPrice);
                                    $objEntityItem = null;
                                    if (!array_key_exists($objItem->entityId, $arrEntityTotalPrice)) {
                                        $objEntityItem = new stdClass();
                                        $objEntityItem->name = $objItem->entityName_ar;
                                        $objEntityItem->totalCost = 0;
                                    } else {
                                        $objEntityItem = $arrEntityTotalPrice[$objItem->entityId];
                                    }

                                    $objEntityItem->totalCost += ($objItem->quantity  * $objItem->unitPrice);
                                    $arrEntityTotalPrice[$objItem->entityId] = $objEntityItem;
                                    ?>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="3"></td>
                                    <td class="font-weight-bolder font-size-h4 text-right text-primary"><?php echo $vModule_cart_totalPrice ?></td>
                                    <td class="font-weight-bolder font-size-h4 text-right"><?php echo $totalPrice ?></td>
                                    <td colspan="1"></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="border-0 text-muted text-right pt-0"></td>
                                </tr>
                                <tr>
                                    <td colspan="6" class="border-0 text-muted text-right pt-0"><?php echo $vModule_cart_term ?></td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="border-0 pt-10">
                                        <!-- <form>
                                            <div class="form-group row">
                                                <div class="col-md-3 d-flex align-items-center">
                                                    <label class="font-weight-bolder">Apply Voucher</label>
                                                </div>
                                                <div class="col-md-7">
                                                    <div class="input-group w-100">
                                                        <input type="text" class="form-control" placeholder="Voucher Code">
                                                        <div class="input-group-append">
                                                            <button class="btn btn-secondary" type="button">Apply</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form> -->
                                    </td>
                                    <td colspan="3" class="border-0 text-right pt-10">
                                        <a href="#" class="btn btn-success font-weight-bolder px-8"><?php echo $vModule_cart_proceedToCheckOut ?></a>
                                    </td>
                                </tr>
                            </tbody>





                        </table>
                    </div>

                </div>
            </div>




        </div>

    </div>
</div>
<!--end::Container-->
<script>
    $('.productImage').on("error", function() {
        $(this).attr('src', '/assets/img/default-product-image.png');
    });

    SearchDataTable.init();
</script>
<?php ob_end_flush(); ?>