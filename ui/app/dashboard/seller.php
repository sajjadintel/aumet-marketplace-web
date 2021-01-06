<!--begin::Entry-->
<div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Dashboard-->
        <div class="row">

            <div class="col-md-3">
                <div class="card card-custom bg-NavyBlue card-stretch gutter-b">
                    <div class="card-body my-4">
                        <a href="/web/distributor/order/pending" class="card-title font-weight-bolder text-white font-size-h6 mb-4 text-hover-state-dark d-block">Orders</a>
                        <div class="font-weight-bold text-white font-size-sm">
                            <span class="font-size-h2 mr-2">
                                <?php if ($dashboard_order > $dashboard_orderYesterday) {
                                    echo '<i class="la la-rocket la-lg text-white"></i> ';
                                } elseif ($dashboard_order < $dashboard_orderYesterday) {
                                    echo '<i class="la la-arrow-down la-lg text-white"></i> ';
                                } else {
                                    echo '<i class="la la-equals la-lg text-white"></i> ';
                                }
                                echo $dashboard_order; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-3">
                <div class="card card-custom bg-Blue card-stretch gutter-b">
                    <div class="card-body my-4">
                        <a href="web/distributor/order/pending" class="card-title font-weight-bolder text-white font-size-h6 mb-4 text-hover-state-dark d-block">Revenue</a>
                        <div class="font-weight-bold text-white font-size-sm">
                            <span class="font-size-h2 mr-2">
                                <?php if ($dashboard_revenue > $dashboard_revenueYesterday) {
                                    echo '<i class="la la-rocket la-lg text-white"></i> ';
                                } elseif ($dashboard_revenue < $dashboard_revenueYesterday) {
                                    echo '<i class="la la-arrow-down la-lg text-white"></i> ';
                                } else {
                                    echo '<i class="la la-equals la-lg text-white"></i> ';
                                }
                                echo 'AED ' . $dashboard_revenue; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-custom bg-BlueGrotto card-stretch gutter-b">
                    <div class="card-body my-4">
                        <a href="web/distributor/customer" class="card-title font-weight-bolder text-white font-size-h6 mb-4 text-hover-state-dark d-block">Customers</a>
                        <div class="font-weight-bold text-white font-size-sm">
                            <span class="font-size-h2 mr-2">
                                <?php if ($dashboard_customer > $dashboard_customerYesterday) {
                                    echo '<i class="la la-rocket la-lg text-white"></i> ';
                                } elseif ($dashboard_customer < $dashboard_customerYesterday) {
                                    echo '<i class="la la-arrow-down la-lg text-white"></i> ';
                                } else {
                                    echo '<i class="la la-equals la-lg text-white"></i> ';
                                }
                                echo $dashboard_customer; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card card-custom bg-BabyBlue card-stretch gutter-b">
                    <div class="card-body my-4">
                        <a href="web/distributor/customer" class="card-title font-weight-bolder text-white font-size-h6 mb-4 text-hover-state-dark d-block">New Customers</a>
                        <div class="font-weight-bold text-white font-size-sm">
                            <span class="font-size-h2 mr-2">
                                <?php if ($dashboard_new_customer > $dashboard_new_customerYesterday) {
                                    echo '<i class="la la-rocket la-lg text-white"></i> ';
                                } elseif ($dashboard_new_customer < $dashboard_new_customerYesterday) {
                                    echo '<i class="la la-arrow-down la-lg text-white"></i> ';
                                } else {
                                    echo '<i class="la la-equals la-lg text-white"></i> ';
                                }
                                echo $dashboard_new_customer; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!--begin::Row-->
        <div class="row">

            <div class="col-6">
                <div class="card card-custom card-stretch gutter-b">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">Recent Orders</span>
                            <span class="text-muted mt-3 font-weight-bold font-size-sm">New Unprocessed Orders</span>
                        </h3>
                    </div>
                    <div class="card-body pt-2 pb-2 mt-n3">
                        <!--begin: Datatable-->
                        <table id="order_datatable" class="compact hover order-column row-border table datatable datatable-bordered datatable-head-custom">
                        </table>
                    </div>

                </div>
            </div>

            <div class="col-6">
                <div class="card card-custom card-stretch gutter-b">
                    <div class="card-header border-0 pt-5">
                        <h3 class="card-title align-items-start flex-column">
                            <span class="card-label font-weight-bolder text-dark">Best Selling Products</span>
                            <span class="text-muted mt-3 font-weight-bold font-size-sm">Top 5 most selling products</span>
                        </h3>
                    </div>
                    <div class="card-body pt-2 pb-2 mt-n3">
                        <!--begin: Datatable-->
                        <table id="products_datatable" class="compact hover order-column row-border table datatable datatable-bordered datatable-head-custom">
                        </table>
                        <!-- <div class="datatable datatable-bordered datatable-head-custom" id="kt_datatable_products"></div> -->
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<style>
    #order_datatable_wrapper td {
        font-size: 11px;
    }
    #products_datatable_wrapper td {
        font-size: 11px;
    }
</style>
<script>
    var PageClass = function() {
        var elementId = "#order_datatable";
        var url = '/web/distributor/order/recent';

        var columnDefsOrders = [{
            className: "export_datatable",
            targets: [0, 1, 2, 3]
        }, {
            targets: 0,
            title: '#',
            data: 'id',
            orderable: false,
            render: function(data, type, row, meta) {
                var output = '#' + row.id ;
                return output;
            }
        }, {
            targets: 1,
            title: WebAppLocals.getMessage('entityBuyer'),
            data: 'entityBuyer',
            orderable: false,
            render: function(data, type, row, meta) {
                var output = row.entityBuyer;
                return output;
            },
        }, {
            targets: 2,
            title: WebAppLocals.getMessage('date'),
            data: 'insertDateTime',
            orderable: false,
            render: function(data, type, row, meta) {
                var output = '';
                if (row.insertDateTime) {
                    output = '<span class="label label-lg font-weight-bold label-inline" style="direction: ltr">' + moment(row.insertDateTime).format('DD/MM/YYYY') + '</span>';
                };
                return output
            }
        }, {
            targets: 3,
            title: WebAppLocals.getMessage('orderTotal'),
            data: 'total',
            orderable: false,
            render: function(data, type, row, meta) {
                var output = row.currency + ' <strong>' + Math.round((parseFloat(row.total) + Number.EPSILON) * 100) / 100 + ' </strong>';
                return output;
            },
        }, {
            targets: 4,
            title: '',
            data: 'id',
            orderable: false,
            render: function(data, type, row, meta) {
                var dropdownStart =
                    '<div class="dropdown dropdown-inline">\
                            <a href="javascript:;" class="btn btn-sm navi-link btn-primary btn-hover-primary mr-2" data-toggle="dropdown">\
								<i class="nav-icon la la-ellipsis-h p-0"></i></a>\
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
						<i class="nav-icon la la-print p-0"></i></a>';
                var btnView =
                    '<a href="javascript:;" onclick=\'WebAppModals.orderViewModal(' +
                    row.id +
                    ')\' \
						class="btn btn-sm navi-link btn-outline-primary btn-hover-primary mr-2" title="View">\
						<i class="nav-icon la la-eye p-0"></i></a>';

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
                var outActions = '';

                outActions += btnView;
                outActions += btnPrint;

                switch (row.statusId) {
                    case 1:
                        outActions += dropdownStart;
                        outActions += dropdownItemStart + btnOrderProcess + dropdownItemEnd;
                        outActions += dropdownItemStart + btnOrderOnHold + dropdownItemEnd;
                        outActions += dropdownEnd;
                        break;
                    case 2:
                        outActions += dropdownStart;
                        outActions += dropdownItemStart + btnOrderProcess + dropdownItemEnd;
                        outActions += dropdownItemStart + btnOrderCancel + dropdownItemEnd;
                        outActions += dropdownEnd;
                        break;
                    case 3:
                        outActions += dropdownStart;
                        outActions += dropdownItemStart + btnOrderComplete + dropdownItemEnd;
                        outActions += dropdownItemStart + btnOrderOnHold + dropdownItemEnd;
                        outActions += dropdownEnd;
                        break;
                    case 6:
                        outActions += btnOrderPaid;
                }

                return outActions;
            }
        }];

        var columnDefsProducts = [{
                className: "export_datatable",
                targets: [0, 1, 2, 3, 4, 5, 6]
            },
            {
                "className": "none",
                "targets": [2, 3, 5]
            }, {
                targets: 0,
                title: WebAppLocals.getMessage('name'),
                data: 'productName_en',
                orderable: false,
                render: function(data, type, row, meta) {
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
                }
            }, {
                targets: 1,
                title: WebAppLocals.getMessage('quantity'),
                data: 'quantityOrdered',
                orderable: false,
            }, {
                targets: 2,
                title: WebAppLocals.getMessage('productScientificName'),
                data: 'scientificName',
                orderable: false,
            }, {
                targets: 3,
                title: WebAppLocals.getMessage('expiryDate'),
                data: 'expiryDate',
                orderable: false,
                render: function(data, type, row, meta) {
                    var output = '';
                    if (row.expiryDate) {
                        output = '<span class="label label-lg font-weight-bold label-inline" style="direction: ltr">' + moment(row.expiryDate).format('DD / MM / YYYY') + '</span>';
                    };
                    return output
                }
            }, {
                targets: 4,
                title: WebAppLocals.getMessage('availability'),
                orderable: false,
                data: 'stockStatusId',
                width: '120px',
                render: function(data, type, row, meta) {
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
                targets: 5,
                title: WebAppLocals.getMessage('stockUpdateDateTime'),
                data: 'stockUpdateDateTime',
                orderable: false,
                render: function(data, type, row, meta) {
                    var output = '';
                    if (row.stockUpdateDateTime) {
                        output = '<span class="label label-lg font-weight-bold label-inline" style="direction: ltr">' + moment(row.stockUpdateDateTime).fromNow() + '</span>';
                    }
                    return output;
                }
            }, {
                targets: 6,
                title: WebAppLocals.getMessage('price'),
                data: 'unitPrice',
                orderable: false,
                width: '50px',
                render: function(data, type, row, meta) {
                    var output = '';
                    if (row.stockUpdateDateTime) {
                        output = row.currency + ' <strong>' + row.unitPrice + '</strong>';
                    }
                    return output;
                }
            }
        ];

        var initiate = function() {
            WebApp.CreateDatatableServerside("Recent Orders", "#order_datatable", "/web/distributor/order/recent", columnDefsOrders, null, null, [[0, 'desc']]);
            WebApp.CreateDatatableServerside("Best Selling Products", "#products_datatable", "/web/distributor/product/bestselling", columnDefsProducts);
        };
        return {
            init: function() {
                initiate();
            },
        };
    }();

    PageClass.init();
</script>