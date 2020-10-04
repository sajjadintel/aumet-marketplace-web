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
    <div class="d-flex align-items-stretch text-center flex-column">
        <h2 class="text-primary font-weight-bolder mt-10 mb-15 font-size-h4"><?php echo $vModule_search_header ?></h2>
        <form class="d-flex position-relative w-100 m-auto">

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
                    <select class="select2 form-control" id="searchProductsBrandNameInput" multiple="" name="brandName" data-select2-id="searchProductsBrandNameInput" tabindex="-1" aria-hidden="true">

                    </select>
                </div>
            </div>

            <div class="d-flex flex-column-fluid">
                <div class="input-group input-group-lg mr-5">
                    <div class="input-group-prepend pt-3 pl-1 pr-1">
                        <span class="svg-icon svg-icon-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24" />
                                    <path d="M1.4152146,4.84010415 C11.1782334,10.3362599 14.7076452,16.4493804 12.0034499,23.1794656 C5.02500006,22.0396582 1.4955883,15.9265377 1.4152146,4.84010415 Z" fill="#000000" opacity="0.3" />
                                    <path d="M22.5950046,4.84010415 C12.8319858,10.3362599 9.30257403,16.4493804 12.0067693,23.1794656 C18.9852192,22.0396582 22.5146309,15.9265377 22.5950046,4.84010415 Z" fill="#000000" opacity="0.3" />
                                    <path d="M12.0002081,2 C6.29326368,11.6413199 6.29326368,18.7001435 12.0002081,23.1764706 C17.4738192,18.7001435 17.4738192,11.6413199 12.0002081,2 Z" fill="#000000" opacity="0.3" />
                                </g>
                            </svg>
                        </span>
                    </div>
                    <select class="select2 form-control" id="searchProductsScieceNameInput" multiple="" name="scientificName" data-select2-id="searchProductsScieceNameInput" tabindex="-1" aria-hidden="true">

                    </select>
                </div>
            </div>

            <div class="d-flex flex-column-fluid">
                <div class="input-group input-group-lg mr-5">
                    <div class="input-group-prepend pt-3 pl-1 pr-1">
                        <span class="svg-icon svg-icon-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <rect x="0" y="0" width="24" height="24" />
                                    <path d="M20.4061385,6.73606154 C20.7672665,6.89656288 21,7.25468437 21,7.64987309 L21,16.4115967 C21,16.7747638 20.8031081,17.1093844 20.4856429,17.2857539 L12.4856429,21.7301984 C12.1836204,21.8979887 11.8163796,21.8979887 11.5143571,21.7301984 L3.51435707,17.2857539 C3.19689188,17.1093844 3,16.7747638 3,16.4115967 L3,7.64987309 C3,7.25468437 3.23273352,6.89656288 3.59386153,6.73606154 L11.5938615,3.18050598 C11.8524269,3.06558805 12.1475731,3.06558805 12.4061385,3.18050598 L20.4061385,6.73606154 Z" fill="#000000" opacity="0.3" />
                                    <polygon fill="#000000" points="14.9671522 4.22441676 7.5999999 8.31727912 7.5999999 12.9056825 9.5999999 13.9056825 9.5999999 9.49408582 17.25507 5.24126912" />
                                </g>
                            </svg>
                        </span>
                    </div>
                    <select class="select2 form-control" id="searchProductsDistributorNameInput" multiple="" name="distributorName" data-select2-id="searchProductsDistributorNameInput" tabindex="-1" aria-hidden="true">
                        <?php foreach ($arrEntities as $objItem) : ?>
                            <option value="<?php echo $objItem->id ?>"><?php echo $objItem->name ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="d-flex flex-column-fluid">
                <div class="input-group input-group-lg">
                    <div class="input-group-prepend ">
                        <span class="input-group-text border-0 py-1 px-3">
                            <span class="svg-icon svg-icon-xl">

                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <rect fill="#000000" opacity="0.3" x="4" y="4" width="8" height="16" />
                                        <path d="M6,18 L9,18 C9.66666667,18.1143819 10,18.4477153 10,19 C10,19.5522847 9.66666667,19.8856181 9,20 L4,20 L4,15 C4,14.3333333 4.33333333,14 5,14 C5.66666667,14 6,14.3333333 6,15 L6,18 Z M18,18 L18,15 C18.1143819,14.3333333 18.4477153,14 19,14 C19.5522847,14 19.8856181,14.3333333 20,15 L20,20 L15,20 C14.3333333,20 14,19.6666667 14,19 C14,18.3333333 14.3333333,18 15,18 L18,18 Z M18,6 L15,6 C14.3333333,5.88561808 14,5.55228475 14,5 C14,4.44771525 14.3333333,4.11438192 15,4 L20,4 L20,9 C20,9.66666667 19.6666667,10 19,10 C18.3333333,10 18,9.66666667 18,9 L18,6 Z M6,6 L6,9 C5.88561808,9.66666667 5.55228475,10 5,10 C4.44771525,10 4.11438192,9.66666667 4,9 L4,4 L9,4 C9.66666667,4 10,4.33333333 10,5 C10,5.66666667 9.66666667,6 9,6 L6,6 Z" fill="#000000" fill-rule="nonzero" />
                                    </g>
                                </svg>

                            </span>
                        </span>
                    </div>
                    <input id="searchStockStatus" data-switch="true" type="checkbox" checked="checked" data-on-text="<?php echo $vModule_search_stockStatus_Available ?>" data-handle-width="70" data-off-text="<?php echo $vModule_search_stockStatus_others ?>" data-on-color="primary" />
                </div>
            </div>


        </form>
        <a href="#" class="btn btn-text-danger btn-hover-text-primary font-weight-bold w-100 mt-5 m-auto"><?php echo $vModule_search_unavailableHeader ?></a>
    </div>

    <div class="card card-custom gutter-b mt-20">
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
        productId: [],
        scientificNameId: [],
        entityId: [],
        stockOption: 1
    };

    var _selectBrand = $('#searchProductsBrandNameInput').select2({
        placeholder: "<?php echo $vModule_search_brandNameplaceholder ?>",
        tags: true,
        ajax: {
            url: '/app/product/brandname/list',
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
    _selectBrand.on("select2:select", function(e) {
        searchQuery.productId = $("#searchProductsBrandNameInput").val();
        SearchDataTable.setReadParams(searchQuery);

    });
    _selectBrand.on("select2:unselect", function(e) {
        searchQuery.productId = $("#searchProductsBrandNameInput").val();
        SearchDataTable.setReadParams(searchQuery);
    });


    var _selectScientific = $('#searchProductsScieceNameInput').select2({
        placeholder: "<?php echo $vModule_search_scientificNamePlaceholder ?>",
        tags: true,
        ajax: {
            url: '/app/product/scientificname/list',
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
    _selectScientific.on("select2:select", function(e) {
        searchQuery.scientificNameId = $("#searchProductsScieceNameInput").val();
        SearchDataTable.setReadParams(searchQuery);
    });
    _selectScientific.on("select2:unselect", function(e) {
        searchQuery.scientificNameId = $("#searchProductsScieceNameInput").val();
        SearchDataTable.setReadParams(searchQuery);
    });
    var _selectDistributor = $('#searchProductsDistributorNameInput').select2({
        placeholder: "<?php echo $vModule_search_distributorNamePlaceholder ?>",
        tags: true
    });
    _selectDistributor.on("select2:select", function(e) {
        searchQuery.entityId = $("#searchProductsDistributorNameInput").val();
        SearchDataTable.setReadParams(searchQuery);
    });
    _selectDistributor.on("select2:unselect", function(e) {
        searchQuery.entityId = $("#searchProductsDistributorNameInput").val();
        SearchDataTable.setReadParams(searchQuery);
    });

    $('#searchStockStatus').bootstrapSwitch().on("switchChange.bootstrapSwitch", function(event, state) {
        searchQuery.stockOption = state ? 1 : 0;
        SearchDataTable.setReadParams(searchQuery);
        if (state) {
            SearchDataTable.hideColumn('stockStatusId');
        } else {
            SearchDataTable.showColumn('stockStatusId');
        }

    });

    $('.select2-search__field').addClass(" h-auto py-1 px-1 font-size-h6");
    SearchDataTable.init(searchQuery);
</script>
<?php ob_end_flush(); ?>