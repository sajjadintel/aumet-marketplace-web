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
                        <span class="card-label font-weight-bolder text-primary"><?php echo $objEntityProduct->productName_en ?></span>
                        <span class="text-muted mt-3 font-weight-bold font-size-sm"></span>
                    </h3>
                    <div class="card-toolbar">
                        <a href="javascript:;" class="btn btn-primary font-weight-bolder font-size-sm " onclick="WebApp.closeSubPage();"><?php echo $vBack; ?></a>
                    </div>
                </div>

                <div class="card-body d-flex rounded  p-12 flex-column flex-md-row flex-lg-column flex-xxl-row">

                    <div class="bgi-no-repeat bgi-position-center bgi-size-contain h-300px h-md-auto h-lg-300px h-xxl-auto mw-100 w-550px bg-white" style="background-image: url('<?php echo $objEntityProduct->image ?>')"></div>


                    <div class="card card-custom w-auto w-md-300px w-lg-auto w-xxl-400px ml-auto">

                        <div class="card-body px-12 py-10 bg-primary">
                            <h3 class="font-weight-bolder font-size-h2 mb-1">
                                <a href="#" class="text-dark"><?php echo $objEntityProduct->productName_en ?></a>
                            </h3>
                            <div class="text-light font-size-h4 mb-9"><?php echo $objEntityProduct->unitPrice ?></div>
                            <div class="font-size-sm mb-8"></div>

                            <div class="d-flex mb-3">
                                <span class="text-light flex-root font-weight-bold"><?php echo $vModule_product_madeIn ?></span>
                                <span class="text-dark flex-root font-weight-bold"><?php echo $objEntityProduct->madeInCountryName_en ?></span>
                            </div>
                            <div class="d-flex mb-3">
                                <span class="text-light flex-root font-weight-bold"><?php echo $vModule_product_distributor ?></span>
                                <span class="text-dark flex-root font-weight-bold"><?php echo $objEntityProduct->entityName_ar ?></span>
                            </div>
                            <div class="d-flex mb-3">
                                <span class="text-light flex-root font-weight-bold"><?php echo $vModule_product_scientificName ?></span>
                                <span class="text-dark flex-root font-weight-bold"><?php echo $objEntityProduct->scientificName ?></span>
                            </div>
                            <div class="d-flex mb-3">
                                <span class="text-light flex-root font-weight-bold"><?php echo $vModule_product_stockStatus ?></span>
                                <span class="text-dark flex-root font-weight-bold"><?php echo $objEntityProduct->stockStatusName_ar ?></span>
                            </div>
                            <div class="d-flex mb-3">
                                <span class="text-light flex-root font-weight-bold"><?php echo $vModule_product_stockStatusDate ?></span>
                                <span class="text-dark flex-root font-weight-bold"><?php echo $objEntityProduct->stockUpdateDateTime ?></span>
                            </div>

                        </div>

                    </div>

                </div>
            </div>

            <div class="card card-custom">

                <div class="card-header border-0 py-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label font-weight-bolder text-primary"><?php echo $vModule_product_related ?></span>
                    </h3>
                    <!--<div class="card-toolbar">
                        <a href="#" class="btn btn-primary font-weight-bolder font-size-sm">New Report</a>
                    </div>-->
                </div>


                <div class="card-body py-0">

                    <div class="table-responsive">
                        <table class="table table-head-custom table-vertical-center" id="kt_advance_table_widget_4">
                            <thead>
                                <tr>

                                    <th class="text-center"><?php echo $vModule_product_name ?></th>
                                    <th></th>
                                    <th class="text-center"><?php echo $vModule_product_distributor ?></th>
                                    <th class="text-center"><?php echo $vModule_product_madeIn ?></th>
                                    <th class="text-center"><?php echo $vModule_product_stockStatusDate ?></th>
                                    <th class="text-center"><?php echo $vModule_product_unitPrice ?></th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>

                                <?php foreach ($arrRelatedEntityProduct as $objItem) : ?>

                                    <tr>
                                        <td class=" align-middle font-weight-bolder font-size-h5"><a href="javascript:;" onclick="WebApp.loadSubPage('/app/entity/<?php echo $objItem->entityId ?>/product/<?php echo $objItem->productId ?>')" class="text-dark text-hover-primary"><?php echo $objItem->productName_en ?></a></td>

                                        <td class="d-flex align-items-center font-weight-bolder">

                                            <div class="symbol symbol-60 flex-shrink-0 mr-4 bg-light">
                                                <div class="symbol-label" style="background-image: url('<?php echo $objItem->image ?>')"></div>
                                            </div>
                                        </td>

                                        <td class="align-middle font-weight-bolder font-size-h5"><?php echo $objItem->madeInCountryName_en ?></td>
                                        <td class="align-middle font-weight-bolder font-size-h5"><?php echo $objItem->entityName_ar ?></td>
                                        <td class="align-middle font-weight-bolder font-size-h5"><?php echo date("Y / m / d", strtotime($objItem->stockUpdateDateTime)) ?></td>
                                        <td class="align-middle font-weight-bolder font-size-h5"><?php echo $objItem->unitPrice ?></td>

                                        <td class="align-middle">
                                            <a href="javascript:;" onclick="Cart.addItem(<?php echo $objItem->entityId ?>,<?php echo $objItem->productId ?>)" class="btn btn-sm btn-default btn-text-primary btn-hover-primary btn-icon  mr-2" title="Add to cart">
                                                <span class="svg-icon svg-icon-md">
                                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <rect x="0" y="0" width="24" height="24" />
                                                            <path d="M18.1446364,11.84388 L17.4471627,16.0287218 C17.4463569,16.0335568 17.4455155,16.0383857 17.4446387,16.0432083 C17.345843,16.5865846 16.8252597,16.9469884 16.2818833,16.8481927 L4.91303792,14.7811299 C4.53842737,14.7130189 4.23500006,14.4380834 4.13039941,14.0719812 L2.30560137,7.68518803 C2.28007524,7.59584656 2.26712532,7.50338343 2.26712532,7.4104669 C2.26712532,6.85818215 2.71484057,6.4104669 3.26712532,6.4104669 L16.9929851,6.4104669 L17.606173,3.78251876 C17.7307772,3.24850086 18.2068633,2.87071314 18.7552257,2.87071314 L20.8200821,2.87071314 C21.4717328,2.87071314 22,3.39898039 22,4.05063106 C22,4.70228173 21.4717328,5.23054898 20.8200821,5.23054898 L19.6915238,5.23054898 L18.1446364,11.84388 Z" fill="#000000" opacity="0.3" />
                                                            <path d="M6.5,21 C5.67157288,21 5,20.3284271 5,19.5 C5,18.6715729 5.67157288,18 6.5,18 C7.32842712,18 8,18.6715729 8,19.5 C8,20.3284271 7.32842712,21 6.5,21 Z M15.5,21 C14.6715729,21 14,20.3284271 14,19.5 C14,18.6715729 14.6715729,18 15.5,18 C16.3284271,18 17,18.6715729 17,19.5 C17,20.3284271 16.3284271,21 15.5,21 Z" fill="#000000" />
                                                        </g>
                                                    </svg>
                                                </span>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                </div>

            </div>


        </div>

    </div>
</div>
<!--end::Container-->
<?php ob_end_flush(); ?>