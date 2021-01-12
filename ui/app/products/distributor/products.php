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
                        <input id="searchStockStatus" data-switch="true" type="checkbox" data-on-text="<?php echo $vModule_search_stockStatus_Available ?>" data-handle-width="70" data-off-text="<?php echo $vModule_search_stockStatus_others ?>" data-on-color="primary" />
                    </div>
                </div>

                <div class="d-flex flex-column-fluid">
                    <div class="input-group input-group-lg">
                        <div class="input-group-prepend ">

                        <label class="myLabel">
                            <a class="btn btn-lg btn-primary btn-hover-primary mr-2 btn-lg-radius" title="Add Product" onclick="DistributorProductsDataTable.productAddModal()">
                                <i class="nav-icon la la-plus p-0"></i> <?php echo $vButton_add; ?>
                            </a>
                        </label>
                        
                        <label class="myLabel">
                            <a class="btn btn-lg btn-primary btn-hover-primary mr-2 btn-lg-radius" title="Bulk Add Products" onclick="WebApp.loadPage('/web/distributor/product/bulk/add/upload')">
                                <i class="nav-icon la la-boxes p-0"></i> <?php echo $vButton_bulk_add; ?>
                            </a>
                        </label>
                        
                        <label class="myLabel">
                            <a class="btn btn-lg btn-primary btn-hover-primary mr-2 btn-lg-radius" title="Bulk Add Images for Products" onclick="WebApp.loadPage('/web/distributor/product/bulk/add/image/upload')">
                                <i class="nav-icon la la-images p-0"></i> <?php echo $vButton_bulk_add_image; ?>
                            </a>
                        </label>
                    </div>
                </div>


            </form>
        </div>

        <div class="card card-custom gutter-b mt-10">
            <div class="card-body">
                <!--begin: Datatable-->
                <table
                    id="datatable"
                    class="compact hover order-column row-border table datatable datatable-bordered datatable-head-custom">
                </table>
                <!--end: Datatable-->
            </div>
        </div>
    </div>
    <!--end::Container-->
    <script>
        var PageClass = function () {
            var elementId = "#datatable";
            var url = '<?php echo $_SERVER['REQUEST_URI']; ?>';

            var columnDefs = [{
                className: "export_datatable",
                targets: [0, 1, 2, 3, 4]
            }, {
                targets: 0,
                title: WebAppLocals.getMessage('productName'),
                data: 'id',
                visible: false,
                render: function (data, type, row, meta) {
                    return row.id;
                },
            }, {
                targets: 1,
                title: WebAppLocals.getMessage('productName'),
                data: 'productName_en',
                render: function (data, type, row, meta) {
                    var output = '<div style="display:flex;flex-direction:row;align-items: center"><div><a href="javascript:;" onclick="WebApp.loadSubPage(\'/web/entity/' +
                        row.entityId +
                        '/product/' +
                        row.productId +
                        '\')"> ' +
                        '<div class="symbol symbol-60 flex-shrink-0 mr-4 bg-light"> <div class="symbol-label" style="background-image: url(\'' +
                        row.image +
                        '\')" ></div></div>'
                        + '</a></div>';
                    output += '<div><span href="javascript:;" onclick="WebApp.loadSubPage(\'/web/entity/' +
                        row.entityId +
                        '/product/' +
                        row.productId +
                        '\')"> ' +
                        row['productName_' + docLang]
                        + '</span></div></div>';
                    return output;
                },
            }, {
                targets: 2,
                title: WebAppLocals.getMessage('productScientificName'),
                data: 'scientificName',
                render: function (data, type, row, meta) {
                    var output = row.scientificName;
                    return output
                }
            }, {
                targets: 3,
                title: WebAppLocals.getMessage('stockAvailability'),
                data: 'stockStatusId',
                render: function (data, type, row, meta) {
                    var status = {
                        1: {
                            title: WebAppLocals.getMessage('stockAvailability_available'),
                            class: ' label-primary',
                        },
                        2: {
                            title: WebAppLocals.getMessage('stockAvailability_notAvailable'),
                            class: ' label-danger',
                        },
                        3: {
                            title: WebAppLocals.getMessage('stockAvailability_availableSoon'),
                            class: ' label-warning',
                        },
                    };

                    var output = '';

                    output +=
                        '<div><span class="label label-lg font-weight-bold ' +
                        status[row.stockStatusId].class +
                        ' label-inline">' +
                        status[row.stockStatusId].title +
                        '</span></div>';

                    return output;
                }
            }, {
                targets: 4,
                title: WebAppLocals.getMessage('stockUpdateDateTime'),
                data: 'stockUpdateDateTime',
                render: function (data, type, row, meta) {
                    if (row.stockUpdateDateTime) {
                        return '<span class="label label-lg font-weight-bold label-inline" style="direction: ltr">' + moment(row.stockUpdateDateTime).fromNow() + '</span>';
                    } else {
                        return '';
                    }
                }
            }, {
                targets: 5,
                title: WebAppLocals.getMessage('unitPrice'),
                data: 'unitPrice',
                render: function (data, type, row, meta) {
                    return '<span class="font-size-sm">' + row.currency + '</span>' + ' <b class="font-size-h4">' + row.unitPrice + '</b>';
                }
            }, {
                targets: 6,
                title: '',
                data: 'id',
                orderable: false,
                render: function (data, type, row, meta) {
                    var outActions = '';

                    var btnEdit =
                        '<a href="javascript:;" onclick=\'DistributorProductsDataTable.productEditModal(' +
                        row.productId +
                        ')\' \
                    class="btn btn-sm btn-primary btn-hover-primary mr-2" title="Edit">\
                    <i class="nav-icon la la-edit p-0"></i> ' +
                        WebAppLocals.getMessage('edit') +
                        '</a>';

                    var btnEditQuantity =
                        '<a href="javascript:;" onclick=\'DistributorProductsDataTable.productEditQuantityModal(' +
                        row.productId +
                        ')\' \
                    class="btn btn-sm btn-primary btn-hover-primary mr-2" title="Edit">\
                    <i class="nav-icon la la-box p-0"></i> ' +
                        WebAppLocals.getMessage('editQuantity') +
                        '</a>';

                    outActions += btnEdit;
                    outActions += btnEditQuantity;

                    return outActions;
                },
            }];


            var searchQuery = {
                productId: [],
                scientificNameId: [],
                stockOption: 1,

            };

            var dbAdditionalOptions = {
                datatableOptions: {
                    order: [
                        [0, 'desc']
                    ],
                }
            };

            var _selectBrand = $('#searchProductsBrandNameInput').select2({
                placeholder: "<?php echo $vModule_search_brandNameplaceholder ?>",

                ajax: {
                    url: '/web/product/brandname/list',
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
                WebApp.CreateDatatableServerside("Products List", elementId, url, columnDefs, searchQuery,dbAdditionalOptions);

            });
            _selectBrand.on("select2:unselect", function(e) {
                searchQuery.productId = $("#searchProductsBrandNameInput").val();
                WebApp.CreateDatatableServerside("Products List", elementId, url, columnDefs, searchQuery,dbAdditionalOptions);
            });


            var _selectScientific = $('#searchProductsScieceNameInput').select2({
                placeholder: "<?php echo $vModule_search_scientificNamePlaceholder ?>",

                ajax: {
                    url: '/web/product/scientificname/list',
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
                WebApp.CreateDatatableServerside("Products List", elementId, url, columnDefs, searchQuery,dbAdditionalOptions);
            });
            _selectScientific.on("select2:unselect", function(e) {
                searchQuery.scientificNameId = $("#searchProductsScieceNameInput").val();
                WebApp.CreateDatatableServerside("Products List", elementId, url, columnDefs, searchQuery,dbAdditionalOptions);
            });

            $('#searchStockStatus').bootstrapSwitch().on("switchChange.bootstrapSwitch", function(event, state) {
                searchQuery.stockOption = state ? 1 : 0;
                WebApp.CreateDatatableServerside("Products List", elementId, url, columnDefs, searchQuery,dbAdditionalOptions);
            });

            var _selectScientificEdit = $('#editProductScientificName').select2({
                placeholder: "<?php echo $vModule_search_scientificNamePlaceholder ?>",

                ajax: {
                    url: '/web/product/scientificname/list',
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

            var _selectScientificAdd = $('#addProductScientificName').select2({
                placeholder: "<?php echo $vModule_search_scientificNamePlaceholder ?>",

                ajax: {
                    url: '/web/product/scientificname/list',
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

            var _selectCountryEdit = $('#editProductCountry').select2({
                placeholder: "<?php echo $vModule_search_countryPlaceholder ?>",

                ajax: {
                    url: '/web/product/country/list',
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

            var _selectCountryAdd = $('#addProductCountry').select2({
                placeholder: "<?php echo $vModule_search_countryPlaceholder ?>",

                ajax: {
                    url: '/web/product/country/list',
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

            $('.select2-search__field').addClass(" h-auto py-1 px-1 font-size-h6");


            var initiate = function () {
                WebApp.CreateDatatableServerside("Products List", elementId, url, columnDefs, searchQuery,dbAdditionalOptions);
            };
            return {
                init: function () {
                    initiate();
                },
            };
        }();

        PageClass.init();
    </script>
<?php ob_end_flush(); ?>
<?php include_once 'edit-modal.php';
include_once 'edit-quantity-modal.php';
include_once 'add-modal.php'; ?>