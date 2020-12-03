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
        <h2 class="text-primary font-weight-bolder mt-5 mb-10 font-size-h4"><?php echo $vModule_order_header; ?></h2>
        <form class="d-flex position-relative w-100 m-auto">

            <div class="d-flex flex-column-fluid">
                <a class="btn btn-lg btn-primary mr-2 btn-lg-radius" title="Export To Excel" onclick="">
                    <i class="la la-file-excel-o"></i> Export to Excel
                </a>
            </div>
            <div class="d-flex flex-column-fluid">
                <div class="input-group input-group-lg mr-5">
                    <div class="input-group-prepend pt-3 pl-1 pr-1">
                        <span class="svg-icon svg-icon-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path d="M13,5 L15,5 L15,20 L13,20 L13,5 Z M5,5 L5,20 L3,20 C2.44771525,20 2,19.5522847 2,19 L2,6 C2,5.44771525 2.44771525,5 3,5 L5,5 Z M16,5 L18,5 L18,20 L16,20 L16,5 Z M20,5 L21,5 C21.5522847,5 22,5.44771525 22,6 L22,19 C22,19.5522847 21.5522847,20 21,20 L20,20 L20,5 Z" fill="#000000" />
                                    <polygon fill="#000000" opacity="0.3" points="9 5 9 20 7 20 7 5" />
                                </g>
                            </svg>
                        </span>
                    </div>
                    <select class="select2 form-control" id="searchOrdersBuyerInput" multiple="" name="buyerName" data-select2-id="searchOrdersBuyerInput" tabindex="-1" aria-hidden="true">
                    </select>
                </div>
            </div>

            <div class="d-flex flex-column-fluid">
                <div class="input-group input-group-lg mr-5">
                    <div class="input-daterange input-group" id="searchOrdersDatePicker">
                        <span class="la-icon">
                            <i class="text-primary la la-calendar"></i>
                        </span>
                        <input class="form-control h-auto py-1 px-1 font-size-h6 standard-radius pl-4" type="text" id="searchOrdersDateInput" name="dateRange" />
                    </div>
                </div>
            </div>

        </form>
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
    var searchQuery = {
        entityBuyerId: [],
        startDate: null,
        endDate: null
    };

    var _selectBuyer = $('#searchOrdersBuyerInput').select2({
        placeholder: "<?php echo $vModule_search_sellerNamePlaceholder ?>",

        ajax: {
            url: '/web/order/customer/list',
            dataType: 'json',
            processResults: function(response) {
                return {
                    results: response.data.results,
                    pagination: {
                        more: response.data.pagination
                    }
                }
            }
        }
    });
    _selectBuyer.on("select2:select", function(e) {
        searchQuery.entityBuyerId = $("#searchOrdersBuyerInput").val();
	      PharmacyOrdersDataTable.setReadParams(searchQuery);

    });
    _selectBuyer.on("select2:unselect", function(e) {
        searchQuery.entityBuyerId = $("#searchOrdersBuyerInput").val();
	      PharmacyOrdersDataTable.setReadParams(searchQuery);
    });

    $('#searchOrdersDateInput').daterangepicker({
        opens: 'left',
        startDate: moment('2020-01-01'),
        endDate: moment(),
    }, function(start, end, label) {
        searchQuery.startDate = start.format('YYYY-MM-DD');
        searchQuery.endDate = end.format('YYYY-MM-DD');
	      PharmacyOrdersDataTable.setReadParams(searchQuery);
    });

    $('.select2-search__field').addClass(" h-auto py-1 px-1 font-size-h6");

    $(document).ready(function() {
	      PharmacyOrdersDataTable.init(searchQuery);
    });
</script>
<?php ob_end_flush(); ?>