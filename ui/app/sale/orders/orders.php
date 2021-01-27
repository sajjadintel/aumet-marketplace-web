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
                <table id="datatable" class="compact hover order-column row-border table datatable datatable-bordered datatable-head-custom">
                </table>
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
                targets: [1, 2, 3, 4, 9, 10, 11, 12, 13, 14]
            },
                {
                    targets: 0,
                    title: '#',
                    data: 'id',
                    render: function (data, type, row, meta) {
                        var output = row.id;
                        return output;
                    }
                },
                {
                    targets: 1,
                    title: WebAppLocals.getMessage('orderId'),
                    data: 'id',
                    visible: false,
                    render: function (data, type, row, meta) {
                        return row.id;
                    }
                },
                {
                    targets: 2,
                    title: WebAppLocals.getMessage('entityBuyer'),
                    data: 'entityBuyer',
                    render: $.fn.dataTable.render.ellipsis( 100 )
                },
                {
                    targets: 3,
                    title: WebAppLocals.getMessage('insertDate'),
                    data: 'insertDateTime',
                    render: function (data, type, row, meta) {
                        var output = '';
                        if (row.insertDateTime) {
                            output = '<span class="label label-lg font-weight-bold label-inline" style="direction: ltr">' + moment(row.insertDateTime).format('DD/MM/YYYY') + '</span>';
                        }
                        return output
                    }
                },
                {
                    targets: 4,
                    title: WebAppLocals.getMessage('orderStatus'),
                    data: 'statusId',
                    render: function (data, type, row, meta) {
                        var status = {
                            1: {
                                title: WebAppLocals.getMessage('orderStatus_Pending'),
                                class: ' label-primary2',
                            },
                            2: {
                                title: WebAppLocals.getMessage('orderStatus_OnHold'),
                                class: ' label-warning',
                            },
                            3: {
                                title: WebAppLocals.getMessage('orderStatus_Processing'),
                                class: ' label-primary',
                            },
                            4: {
                                title: WebAppLocals.getMessage('orderStatus_Completed'),
                                class: ' label-primary',
                            },
                            5: {
                                title: WebAppLocals.getMessage('orderStatus_Canceled'),
                                class: ' label-danger',
                            },
                            6: {
                                title: WebAppLocals.getMessage('orderStatus_Received'),
                                class: ' label-primary',
                            },
                            7: {
                                title: WebAppLocals.getMessage('orderStatus_Paid'),
                                class: ' label-success',
                            },
                            8: {
                                title: WebAppLocals.getMessage('orderStatus_MissingProducts'),
                                class: ' label-danger',
                            },
                            9: {
                                title: WebAppLocals.getMessage('orderStatus_Canceled_Pharmacy'),
                                class: ' label-danger',
                            },
                        };

                        var output = '<div><span class="label label-lg font-weight-bold ' + status[row.statusId].class + ' label-inline">' + status[row.statusId].title + '</span></div>';
                        return output;
                    },
                },
                {
                    targets: 5,
                    title: WebAppLocals.getMessage('orderTotal'),
                    data: 'total',
                    render: function (data, type, row, meta) {
                        var output = row.currency + ' <strong>' + Math.round((parseFloat(row.total) + Number.EPSILON) * 100) / 100 + ' </strong>';
                        return output;
                    },
                },
                {
                    targets: 6,
                    title: WebAppLocals.getMessage('tax'),
                    data: 'tax',
                    render: function (data, type, row, meta) {
                        var output = Math.round((parseFloat(row.tax) + Number.EPSILON) * 100) / 100 + '%';
                        return output;
                    },
                },
                {
                    targets: 7,
                    title: WebAppLocals.getMessage('orderTotalWithVAT'),
                    data: 'total',
                    render: function (data, type, row, meta) {
                        var output = row.currency + ' <strong>' + Math.round((parseFloat(row.total) + Number.EPSILON) * 100) / 100 + ' </strong>';
                        return output;
                    },
                },
                {
                    targets: 8,
                    title: '',
                    data: 'id',
                    orderable: false,
                    render: function (data, type, row, meta) {
                        var dropdownStart =
                            '<div class="dropdown dropdown-inline">\
                                    <a href="javascript:;" class="btn btn-sm navi-link btn-primary btn-hover-primary mr-2" data-toggle="dropdown">\
                                        <i class="nav-icon la la-ellipsis-h p-0"></i> &nbsp&nbsp' +
                            WebAppLocals.getMessage('actions') +
                            '</a>\
                                    <div class="dropdown-menu dropdown-menu-md">\
                                        <ul class="navi flex-column navi-hover py-2">';
                        var dropdownEnd = '</ul>\
                            </div>\
						</div>';
                        var dropdownItemStart = '<li class="navi-item">';
                        var dropdownItemEnd = '</li>';

                        var btnPrint =
                            '<a href="/web/distributor/order/print/' +
                            row.id +
                            '" target="_blank" class="btn btn-sm navi-link btn-outline-primary btn-hover-primary mr-2" title="Print Order">\
                                <i class="nav-icon la la-print p-0"></i> &nbsp&nbsp' +
                            WebAppLocals.getMessage('print') +
                            '</a>';
                        var btnView =
                            '<a href="javascript:;" onclick=\'WebAppModals.orderViewModal(' +
                            row.id +
                            ')\' \
                                class="btn btn-sm navi-link btn-outline-primary btn-hover-primary mr-2" title="View">\
                                <i class="nav-icon la la-eye p-0"></i> &nbsp&nbsp' +
                            WebAppLocals.getMessage('view') +
                            '</a>';

                        var btnOrderProcess =
                            '<a class="navi-link" href="javascript:;" onclick=\'WebAppModals.orderStatusModal(' +
                            row.id +
                            ',3)\' \
                                class="btn btn-sm btn-primary btn-hover-primary  mr-2 navi-link" title="Order Process">\
                                <span class="navi-icon"><i class="la la-check"></i></span><span class="navi-text"> &nbsp&nbsp' +
                            WebAppLocals.getMessage('orderStatusMove') +
                            WebAppLocals.getMessage('orderStatus_Processing') +
                            '</span></a>';
                        var btnOrderComplete =
                            '<a class="navi-link" href="javascript:;" onclick=\'WebAppModals.orderStatusModal(' +
                            row.id +
                            ',4)\' \
                                class="btn btn-sm btn-primary btn-hover-primary  mr-2" navi-link title="Order Complete">\
                                <span class="navi-icon"><i class="la la-check"></i></span><span class="navi-text"> &nbsp&nbsp' +
                            WebAppLocals.getMessage('orderStatusMove') +
                            WebAppLocals.getMessage('orderStatus_Completed') +
                            '</span></a>';
                        var btnModifyQuantityOrder =
                            '<a class="navi-link" href="javascript:;" onclick=\'ModifyQuantityOrderModals.openModal(' +
                            row.id +
                            ',4)\' \
                                class="btn btn-sm btn-primary btn-hover-primary  mr-2" navi-link title="Order Complete">\
                                <span class="navi-icon"><i class="la la-edit"></i></span><span class="navi-text"> &nbsp&nbsp' +
                            WebAppLocals.getMessage('order_modifyQuantity') +
                            '</span></a>';
                        var btnOrderOnHold =
                            '<a class="navi-link bg-danger-hover" href="javascript:;" onclick=\'WebAppModals.orderStatusModal(' +
                            row.id +
                            ',2)\' \
                                class="btn btn-sm btn-primary btn-hover-primary mr-2 navi-link" title="Order On Hold">\
                                <span class="navi-icon"><i class="la la-times"></i></span><span class="navi-text"> &nbsp&nbsp' +
                            WebAppLocals.getMessage('orderStatusMove') +
                            WebAppLocals.getMessage('orderStatus_OnHold') +
                            '</span></a>';
                        var btnOrderCancel =
                            '<a class="navi-link bg-danger-hover" href="javascript:;" onclick=\'WebAppModals.orderStatusModal(' +
                            row.id +
                            ',5)\' \
                                class="btn btn-sm btn-primary btn-hover-primary  mr-2 navi-link" title="Order Cancel">\
                                <span class="navi-icon"><i class="la la-times"></i></span><span class="navi-text"> &nbsp&nbsp' +
                            WebAppLocals.getMessage('orderStatusMove') +
                            WebAppLocals.getMessage('orderStatus_Canceled') +
                            '</span></a>';

                        var btnOrderPaid =
                            '<a href="javascript:;" onclick=\'WebAppModals.orderStatusModal(' +
                            row.id +
                            ',7)\' \
                                class="btn btn-sm btn-primary btn-hover-primary  mr-2 navi-link" title="Order Paid">\
                                <i class="nav-icon la la-dollar p-0"></i> &nbsp&nbsp' +
                            WebAppLocals.getMessage('orderStatusMove') +
                            WebAppLocals.getMessage('orderStatus_Paid') +
                            '</a>';

                        var missingProduct =
                            '<a href="javascript:;" onclick=\'OrderMissingProductListModals.orderMissingProductListDistributorModal(' +
                            row.id +
                            ')\' \
                                class="btn btn-sm navi-link btn-outline-primary btn-hover-primary mr-2" title="View">\
                                <i class="nav-icon la la-box p-0"></i> &nbsp&nbsp' +
                            WebAppLocals.getMessage('orderMissingProduct') +
                            '</a>';

                            
                        var btnChangeCustomerGroup =
                            '<a class="navi-link" href="javascript:;" onclick=\'DistributorCustomersDataTable.customerEditGroupModal(true, ' + row.entityBuyerId + ',' + row.entitySellerId + ')\' \
                                class="btn btn-sm btn-primary btn-hover-primary  mr-2" navi-link title="Change Customer Group">\
                                <span class="navi-icon"><i class="la la-group"></i></span><span class="navi-text"> &nbsp&nbsp' +
                            WebAppLocals.getMessage('changeCustomerGroup') +
                            '</span></a>';


                        var outActions = '';

                        outActions += btnView;
                        outActions += btnPrint;

                        switch (row.statusId) {
                            case 6:
                                outActions += btnOrderPaid;
                                break;
                            case 8:
                                outActions += missingProduct;
                                break;
                        }

                        outActions += dropdownStart;
                        switch (row.statusId) {
                            case 1:
                                outActions += dropdownItemStart + btnOrderProcess + dropdownItemEnd;
                                outActions += dropdownItemStart + btnOrderOnHold + dropdownItemEnd;
                                outActions += dropdownItemStart + btnModifyQuantityOrder + dropdownItemEnd;
                                break;
                            case 2:
                                outActions += dropdownItemStart + btnOrderProcess + dropdownItemEnd;
                                outActions += dropdownItemStart + btnOrderCancel + dropdownItemEnd;
                                outActions += dropdownItemStart + btnModifyQuantityOrder + dropdownItemEnd;
                                break;
                            case 3:
                                outActions += dropdownItemStart + btnOrderComplete + dropdownItemEnd;
                                outActions += dropdownItemStart + btnOrderOnHold + dropdownItemEnd;
                                outActions += dropdownItemStart + btnModifyQuantityOrder + dropdownItemEnd;
                                break;
                        }
                        outActions += dropdownItemStart + btnChangeCustomerGroup + dropdownItemEnd;
                        outActions += dropdownEnd;

                        return outActions;
                    },
                },
                {
                    targets: 9,
                    title: WebAppLocals.getMessage('productId'),
                    data: 'productCode',
                    visible: false,
                    render: function (data, type, row, meta) {
                        return row.productCode;
                    },
                },
                {
                    targets: 10,
                    title: WebAppLocals.getMessage('productName'),
                    data: 'productName',
                    visible: false,
                },
                {
                    targets: 11,
                    title: WebAppLocals.getMessage('unitPrice'),
                    data: 'unitPrice',
                    visible: false,
                    render: function (data, type, row, meta) {
                        return row.unitPrice + ' ' + row.currency;
                    },
                },
                {
                    targets: 12,
                    title: WebAppLocals.getMessage('orderShippedQuantity'),
                    data: 'shippedQuantity',
                    visible: false,
                    render: function (data, type, row, meta) {
                        return row.shippedQuantity;
                    },
                },
                {
                    targets: 13,
                    title: WebAppLocals.getMessage('orderOrderedQuantity'),
                    data: 'requestedQuantity',
                    visible: false,
                },
                {
                    targets: 14,
                    title: WebAppLocals.getMessage('tax'),
                    data: 'tax',
                    visible: false,
                    render: function (data, type, row, meta) {
                        return row.tax + '%';
                    },
                },
                {
                    targets: 15,
                    title: WebAppLocals.getMessage('orderTotalWithVAT'),
                    data: 'quantity',
                    visible: false,
                    render: function (data, type, row, meta) {
                        var total = row.quantity * row.unitPrice;
                        total = Math.round((parseFloat(total) + Number.EPSILON) * 100) / 100;
                        return total + ' ' + row.currency;
                    },
                },
            ];

            var searchQuery = {
                entityBuyerId: [],
                startDate: null,
                endDate: null
            };

            var dbAdditionalOptions = {
                datatableOptions: {
                    order: [
                        [0, 'desc']
                    ],
                    rowCallback: function (row, data, index) {
                        if (!data['isVisible']) {
                            $(row).hide();
                        }
                    },
                }
            };

            var _selectBuyer = $('#searchOrdersBuyerInput').select2({
                placeholder: "<?php echo $vModule_search_buyerNamePlaceholder ?>",

                ajax: {
                    url: '/web/order/customer/list',
                    dataType: 'json',
                    processResults: function (response) {
                        return {
                            results: response.data.results,
                            pagination: {
                                more: response.data.pagination
                            }
                        }
                    }
                }
            });
            _selectBuyer.on("select2:select", function (e) {
                searchQuery.entityBuyerId = $("#searchOrdersBuyerInput").val();
                WebApp.CreateDatatableServerside("Orders List", elementId, url, columnDefs, searchQuery, dbAdditionalOptions);

            });
            _selectBuyer.on("select2:unselect", function (e) {
                searchQuery.entityBuyerId = $("#searchOrdersBuyerInput").val();
                WebApp.CreateDatatableServerside("Orders List", elementId, url, columnDefs, searchQuery, dbAdditionalOptions);
            });

            $('#searchOrdersDateInput').daterangepicker({
                opens: 'left',
                startDate: moment('2020-01-01'),
                endDate: moment(),
                locale: {
                    format: 'DD/MM/YYYY',
                }
            }, function (start, end, label) {
                searchQuery.startDate = start.format('YYYY-MM-DD');
                searchQuery.endDate = end.format('YYYY-MM-DD');
                WebApp.CreateDatatableServerside("Orders List", elementId, url, columnDefs, searchQuery, dbAdditionalOptions);
            });

            $('.select2-search__field').addClass(" h-auto py-1 px-1 font-size-h6");

            var initiate = function () {
                WebApp.CreateDatatableServerside("Orders List", elementId, url, columnDefs, searchQuery, dbAdditionalOptions);
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
<?php include_once 'ui/app/entity/customers/edit-group-modal.php'; ?>