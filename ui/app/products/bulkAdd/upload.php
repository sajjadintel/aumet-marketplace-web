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
		WebApp.get('/web/distributor/product/bulk/add/download', downloadExcelSheet);
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
            <a id="export-to-excel" class="btn btn-lg btn-primary mr-2 btn-lg-radius" title="Export To Excel">
                <i class="la la-file-excel-o"></i> Download Sample Product Excel File
            </a>
        </div>
    </div>


    <div class="card card-custom gutter-b mt-5">
        <div class="card-body">

            <div class="d-flex align-items-center flex-column" id="dropZoneProductsBulkAddProgressContainer">
                <div class="progress w-100" style="height: 40px;">
                    <div class="progress-bar progress-bar-striped bg-primary font-size-h3" id="dropZoneProductsBulkAddProgress" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">Uploading: 0 %</div>
                </div>
            </div>

            <div class="d-flex align-items-center flex-column mt-10">
                <div class="dropzone dropzone-default dropzone-success" id="dropZoneProductsBulkAdd">
                    <div class="dropzone-msg dz-message needsclick">
                        <h3 class="dropzone-msg-title">Drop files here or click to upload.</h3>
                        <span class="dropzone-msg-desc">Only .xlsx,.xls,.csv files are allowed for upload</span>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <div class="d-flex align-items-stretch flex-column mt-20" id="productsBulkAddProcessResultContainer">


    </div>
</div>
<!--end::Container-->
<script type="text/javascript" src="/assets/js/products-bulk-add.js<?php echo $platformVersion ?>"></script>
<?php ob_end_flush(); ?>