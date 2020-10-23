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
    <div class="d-flex align-items-stretch flex-column">

        <div class="d-flex flex-column-fluid">
            <a class="btn btn-lg btn-primary mr-2 btn-lg-radius" title="Export To Excel" onclick="">
                <i class="la la-file-excel-o"></i> Download Sample Excel File
            </a>
        </div>
    </div>


    <div class="card card-custom gutter-b mt-5">
        <div class="card-body">
            <div class="d-flex align-items-center flex-column">

                    <div class="dropzone dropzone-default dropzone-success" id="dropZoneProductsStockUpload">
                        <div class="dropzone-msg dz-message needsclick">
                            <h3 class="dropzone-msg-title">Drop files here or click to upload.</h3>
                            <span class="dropzone-msg-desc">Only .xlsx,.xls,.csv files are allowed for upload</span>
                        </div>
                    </div>
            </div>
        </div>
    </div>
</div>
<!--end::Container-->
<script type="text/javascript" src="/assets/js/products-stock-upload.js<?php echo $platformVersion ?>"></script>
<?php ob_end_flush(); ?>



