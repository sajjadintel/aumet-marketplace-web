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
<script>
    $('#export-to-excel').on('click', function (e) {
        e.preventDefault();
		WebApp.get('/web/distributor/product/bonus/download', downloadExcelSheet);
    });
    
    function downloadExcelSheet(webResponse) {
        const link = webResponse.data;
        window.open(link, "_blank");
    }
</script>
<!--begin::Container-->
<div class="container-fluid">
    <div class="d-flex align-items-stretch flex-column">

        <div class="d-flex flex-column-fluid">
            <a id="export-to-excel" class="d-flex btn btn-lg btn-primary mr-2 btn-lg-radius" title="Export To Excel">
                <i class="la la-file-excel-o"></i> <?php echo $vModule_product_bonus_download_file_button; ?>
            </a>
            <span class="align-self-center text-primary font-size-h5"><?php echo $vModule_product_bonus_download_file_info_message; ?></span>
        </div>
    </div>


    <div class="card card-custom gutter-t gutter-b">
        <div class="card-body">

            <div class="d-none align-items-center flex-column" id="dropZoneProductsBonusUploadProgressContainer">
                <div class="progress w-100" style="height: 40px;">
                    <div class="progress-bar progress-bar-striped bg-primary font-size-h3" id="dropZoneProductsBonusUploadProgress" role="progressbar" style="width: 0;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">Uploading: 0 %</div>
                </div>
            </div>

            <div class="d-flex align-items-center flex-column" id="dropZoneProductsBonusUpload">
                <div class="dropzone dropzone-default dropzone-success p-0">
                    <div class="dropzone-msg dz-message needsclick m-0 p-12">
                        <h3 class="dropzone-msg-title"><?php echo $vModule_product_bonus_upload_file_info_message; ?></h3>
                        <span class="dropzone-msg-desc"><?php echo $vModule_product_bonus_upload_file_constraint_message; ?></span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="d-flex align-items-stretch flex-column gutter-t gutter-b" id="productsBonusUploadProcessResultContainer">

    </div>

    <div id="goBackContainer" class="row" style="display: none;">
        <div class="col">
            <div class="d-flex flex-column-fluid">
                <a class="btn btn-lg btn-primary mr-2 btn-lg-radius" title="Go back" onclick="history.back()">
                    <?php echo $vBack; ?>
                </a>
            </div>
        </div>
    </div>
</div>
<!--end::Container-->
<script type="text/javascript" src="/assets/js/products-bonus-upload.js<?php echo $platformVersion ?>"></script>
<?php ob_end_flush(); ?>