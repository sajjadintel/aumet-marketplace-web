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

            var columnDefs = [
                {
                    className: "export_datatable",
                    targets: [0, 1, 2, 3, 4, 5, 6, 7, 8]
                },
                {
                    targets: 0,
                    title: '#',
                    data: 'id',
                },
                {
                    targets: 1,
                    title: WebAppLocals.getMessage('customerName'),
                    data: 'buyerName',
                    render: $.fn.dataTable.render.ellipsis( 100 )
                },
                {
                    targets: 2,
                    title: WebAppLocals.getMessage('relationGroup'),
                    data: 'relationGroupName',
                    render: $.fn.dataTable.render.ellipsis( 100 )
                },
                {
                    targets: 3,
                    title: WebAppLocals.getMessage('ordersCount'),
                    data: 'orderCount',
                    render: function (data, type, row, meta) {
                        var output = null;

                        output = row.orderCount;
                        return output;
                    }
                },
                {
                    targets: 4,
                    title: WebAppLocals.getMessage('orderTotal'),
                    data: 'orderTotalPaid',
                    render: function (data, type, row, meta) {
                        var output = null;

                        output = row.currencySymbol + ' ' + row.orderTotalPaid;
                        return output;
                    }
                },
                {
                    targets: 5,
                    title: WebAppLocals.getMessage('ordersCompleted'),
                    data: 'orderCountPaid',
                },
                {
                    targets: 6,
                    title: WebAppLocals.getMessage('country'),
                    data: 'buyerCountryName',
                },
                {
                    targets: 7,
                    title: WebAppLocals.getMessage('city'),
                    data: 'buyerCityName',
                },
                {
                    targets: 8,
                    title: '',
                    data: 'id',
                    orderable: false,
                    render: function (data, type, row, meta) {
                        var output =
                            '<a href="javascript:;" onclick=\'DistributorCustomersDataTable.customerEditGroupModal(false, ' + row.id + ')\'\
                        class="btn btn-sm navi-link btn-outline-primary btn-hover-primary mr-2" title="' + WebAppLocals.getMessage('addToGroup') + '">\
                        <i class="nav-icon la la-group p-0"></i></a>';

                        output +=
                            '<a href="javascript:;" onclick=\'window.location.href = "/web/distributor/order/history?customer=' + row.entityBuyerId + '"\'\
                        class="btn btn-sm navi-link btn-outline-primary btn-hover-primary mr-2" title="' + WebAppLocals.getMessage('viewOrders') + '">\
                        <i class="nav-icon la la-eye p-0"></i></a>';

                        return '<div style="display: flex;">' + output + '</div>';
                    },
                }
            ];


            var searchQuery = {
                statusId: []
            };


            var initiate = function () {
                WebApp.CreateDatatableServerside("Customers List", elementId, url, columnDefs, searchQuery);
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
<?php include_once 'edit-group-modal.php'; ?>