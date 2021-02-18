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
                    targets: [0, 1, 2, 3, 4, 5, 6, 7]
                },
                {
                    targets: 0,
                    title: '#',
                    data: 'id',
                    render: function (data, type, row, meta) {
                        return meta.row + 1;
                    },
                },
                {
                    targets: 1,
                    title: WebAppLocals.getMessage('relationGroup'),
                    data: 'relationGroupName',
                    render: $.fn.dataTable.render.ellipsis( 100 )
                },
                {
                    targets: 2,
                    title: WebAppLocals.getMessage('groupMembers'),
                    data: 'groupMembers'
                },
                {
                    targets: 3,
                    title: WebAppLocals.getMessage('revenue'),
                    data: 'revenue',
                    render: function (data, type, row, meta) {
                        var output = null;

                        output = row.revenue + ' ' + row.currencySymbol;
                        return output;
                    }
                },
                {
                    targets: 4,
                    title: WebAppLocals.getMessage('totalOrders'),
                    data: 'totalOrders'
                },
                {
                    targets: 5,
                    title: WebAppLocals.getMessage('recentOrdersWeekly'),
                    data: 'recentOrdersWeekly'
                },
                {
                    targets: 6,
                    title: WebAppLocals.getMessage('recentOrdersMonthly'),
                    data: 'recentOrdersMonthly'
                },
                {
                    targets: 7,
                    title: '',
                    data: 'id',
                    orderable: false,
                    render: function (data, type, row, meta) {
                        var output = '';
                        if(row.arrEntity.length > 0) {                            
                            output += '<a href="javascript:;" onclick=\'DistributorCustomerGroup.collapseRow(this, "' + encodeURIComponent(JSON.stringify(row.arrEntity)) + '")\' class="btn btn-primary mr-2 mb-2 expand-button">+</a>';
                        }
                        
                        output +=
                            '<a href="javascript:;" onclick=\'DistributorCustomerGroup.customerGroupEditModal(' + row.id + ')\'\
                        class="btn btn-primary px-8 mr-2 mb-2" title="' + WebAppLocals.getMessage('edit') + '">' + WebAppLocals.getMessage('edit') + '</a>';

                        return '<div style="display: flex;">' + output + '</div>';
                    },
                }
            ];
            
            var dbAdditionalOptions = { 
                datatableOptions: {
                    createdRow: function (row, data, index) {
                        $(row).addClass('customer-group-datatable-header');
                    },
                }
            };

            var initiate = function () {
                WebApp.CreateDatatableServerside("Customer Group", elementId, url, columnDefs, null, dbAdditionalOptions);
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
<?php include_once 'edit-customer-group-modal.php'; ?>
<script>
    $(document).ready(function() {
        DistributorCustomerGroup.init();
    })
</script>