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
<div class="container-fluid">
    <div class="card card-custom gutter-b mt-5">
        <div class="card-body">

            <div class="d-flex align-items-center flex-column" id="dropZoneProductsBulkAddImageProgressContainer">
                <div class="progress w-100" style="height: 40px;">
                    <div class="progress-bar progress-bar-striped bg-primary font-size-h3" id="dropZoneProductsBulkAddImageProgress" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">Uploading: 0 %</div>
                </div>
            </div>

            <div class="d-flex align-items-center flex-column mt-10">
                <div class="dropzone dropzone-default dropzone-success" id="dropZoneProductsBulkAddImage">
                    <div class="dropzone-msg dz-message needsclick">
                        <h3 class="dropzone-msg-title">Drop files here or click to upload.</h3>
                        <span class="dropzone-msg-desc">Only .png,.jpg,.jpeg files are allowed for upload</span>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <div class="d-flex align-items-stretch flex-column mt-20" id="productsBulkAddImageProcessResultContainer">


    </div>
</div>
<!--end::Container-->
<script type="text/javascript" src="/assets/js/products-bulk-add-image.js<?php echo $platformVersion ?>"></script>
<?php ob_end_flush(); ?>