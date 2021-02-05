<!--begin::Entry-->
<div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Dashboard-->
        <div class="row">

            <div class="col-md-6">
                <div class="card card-custom bg-NavyBlue card-stretch gutter-b">
                    <div class="card-body my-4">
                        <a href="/web/distributor/order/new" class="card-title font-weight-bolder text-white font-size-h6 mb-4 text-hover-state-dark d-block">Orders</a>
                        <div class="font-weight-bold text-white font-size-sm">
                            <span class="font-size-h2 mr-2">
                                <?php /* if ($dashboard_order > $dashboard_orderYesterday) {
                                    echo '<i class="la la-rocket la-lg text-white"></i> ';
                                } elseif ($dashboard_order < $dashboard_orderYesterday) {
                                    echo '<i class="la la-arrow-down la-lg text-white"></i> ';
                                } else {
                                    echo '<i class="la la-equals la-lg text-white"></i> ';
                                } */
                                echo $dashboard_order; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-md-6">
                <div class="card card-custom bg-Blue card-stretch gutter-b">
                    <div class="card-body my-4">
                        <a href="web/distributor/order/new" class="card-title font-weight-bolder text-white font-size-h6 mb-4 text-hover-state-dark d-block">Invoices</a>
                        <div class="font-weight-bold text-white font-size-sm">
                            <span class="font-size-h2 mr-2">
                                <?php /* if ($dashboard_invoice > $dashboard_invoiceYesterday) {
                                    echo '<i class="la la-rocket la-lg text-white"></i> ';
                                } elseif ($dashboard_invoice < $dashboard_invoiceYesterday) {
                                    echo '<i class="la la-arrow-down la-lg text-white"></i> ';
                                } else {
                                    echo '<i class="la la-equals la-lg text-white"></i> ';
                                } */
                                echo $dashboard_invoice; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!--begin::Row-->
        <div class="row">

            <div class="col-12">
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
        </div>
    </div>
</div>

<script>
    var PageClass = function() {
        var elementId = "#order_datatable";
        var url = '/web/distributor/order/recent';

        var columnDefsOrders = [{
            className: "export_datatable",
            targets: [0, 1, 2, 3, 4]
        }, {
            targets: 0,
            title: '#',
            data: 'id',
            render: function(data, type, row, meta) {
                var output = '(' + row.id + ') - #' + row.serial;
                return output;
            }
        }, {
            targets: 1,
            title: WebAppLocals.getMessage('entitySeller'),
            data: 'entitySeller',
            render: $.fn.dataTable.render.ellipsis(100)
        }, {
            targets: 2,
            title: WebAppLocals.getMessage('insertDate'),
            data: 'insertDateTime',
            render: function(data, type, row, meta) {
                var output = '';
                if (row.insertDateTime) {
                    output = '<span class="label label-lg font-weight-bold label-inline" style="direction: ltr">' + moment(row.insertDateTime).format('DD / MM / YYYY') + '</span>';
                };
                return output
            }
        }, {
            targets: 3,
            title: WebAppLocals.getMessage('orderTotalWithVAT'),
            data: 'total',
            render: function(data, type, row, meta) {
                var output = row.currency + ' <strong>' + WebApp.formatMoney(row.total) + ' </strong>';
                return output;
            },
        }, {
            targets: 4,
            title: WebAppLocals.getMessage('orderStatus'),
            data: 'status',
            render: function(data, type, row, meta) {
                var status = {
                    1: {
                        title: WebAppLocals.getMessage('orderStatus_New'),
                        class: ' label-primary',
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
        }, {
            targets: 5,
            title: '',
            data: 'id',
            orderable: false,
            render: function(data, type, row, meta) {
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
                    '<a href="javascript:;" onclick=\'WebAppModals.orderViewPharmacyModal(' +
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
                /*
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
                 */

                return outActions;
            }
        }];

        var dbAdditionalOptions = {
            datatableOptions: {
                order: [
                    [0, 'desc']
                ],
                rowCallback: function(row, data, index) {
                    if (!data['isVisible']) {
                        $(row).hide();
                    }
                },
            }
        };

        var initiate = function() {
            WebApp.CreateDatatableServerside("Recent Orders", "#order_datatable", "/web/pharmacy/order/recent", columnDefsOrders, null, dbAdditionalOptions);
        };

        return {
            init: function() {
                initiate();
            },
        };
    }();

    PageClass.init();
</script>