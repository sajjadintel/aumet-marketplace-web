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
            <h2 class="text-primary font-weight-bolder mt-10 mb-15 font-size-h4"><?php echo $vModule_feedback_header; ?></h2>
        </div>

        <div class="card card-custom gutter-b mt-5">
            <div class="card-body">
                <!--begin: Datatable-->
                <div class="datatable datatable-bordered datatable-head-custom" id="kt_datatable"></div>
                <!--end: Datatable-->
            </div>
        </div>
    </div>
    <!--end::Container-->
    <script>
        var query = {

        };
        $(document).ready(function() {
            CustomerFeedbackDataTable.init(query);
        });
    </script>
<?php ob_end_flush(); ?>