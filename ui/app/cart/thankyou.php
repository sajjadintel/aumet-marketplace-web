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

                <div class="card card-custom" style="margin-bottom: 20px;">

                    <div class="text-center p-10">
                        <p class="font-weight-bolder font-size-h3">
                            Thank you for ordering with Aumet. Your orders have been sent to the distributors and you will be notified of any update
                        </p>
                    </div>

                    <?php foreach ($allOrders as $order) : ?>

                        <div class="text-center p-10">
                            <div class="card-header flex-wrap border-0 pt-6 pb-0">
                                <h3 class="card-title align-items-start flex-column">
                                    <span class="card-label font-weight-bolder font-size-h3 text-primary">Order ID <?php echo $order->id ?></span>
                                </h3>
                            </div>
                        </div>

                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
    <!--end::Container-->
<?php ob_end_flush(); ?>