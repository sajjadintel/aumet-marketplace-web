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
                    targets: [0, 1, 2, 3, 4, 5, 6]
                },
                {
                    targets: 0,
                    title: '#',
                    data: 'id',
                },
                {
                    targets: 1,
                    title: WebAppLocals.getMessage('entityBuyer'),
                    data: 'buyerName',
                },
                {
                    targets: 2,
                    title: WebAppLocals.getMessage('orderCount'),
                    data: 'orderCount',
                    render: function (data, type, row, meta) {
                        var output = null;

                        output = row.orderCount;
                        return output;
                    }
                },
                {
                    targets: 3,
                    title: WebAppLocals.getMessage('orderTotal'),
                    data: 'orderTotal',
                    render: function (data, type, row, meta) {
                        var output = null;

                        output = row.currencySymbol + ' ' + row.orderTotal;
                        return output;
                    }
                },
                {
                    targets: 4,
                    title: WebAppLocals.getMessage('orderTotalPaid'),
                    data: 'orderTotalPaid',
                    render: function (data, type, row, meta) {
                        var output = null;

                        output = row.currencySymbol + ' ' + row.orderTotalPaid;
                        return output;
                    }
                },
                {
                    targets: 5,
                    title: WebAppLocals.getMessage('orderTotalUnPaid'),
                    data: 'orderTotalUnPaid',
                    orderable: false,
                    render: function (data, type, row, meta) {
                        var output = null;

                        output = row.currencySymbol + ' ' + row.orderTotalUnPaid;

                        return output;
                    }
                },
                {
                    targets: 6,
                    title: WebAppLocals.getMessage('customerStatus'),
                    data: 'statusId',
                    render: function (data, type, row, meta) {
                        var status = {
                            1: {
                                title: WebAppLocals.getMessage('relationAvailable'),
                                class: ' label-success',
                            },
                            2: {
                                title: WebAppLocals.getMessage('relationBlacklisted'),
                                class: ' label-danger',
                            },
                        };

                        var output = '';

                        output += '<div><span class="label label-lg font-weight-bold ' + status[row.statusId].class + ' label-inline">' + status[row.statusId].title + '</span></div>';

                        return output;
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