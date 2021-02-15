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
    var PageClass = function() {
        var elementId = "#datatable";
        var url = '<?php echo $_SERVER['REQUEST_URI']; ?>';

        var columnDefs = [{
            className: "export_datatable",
            targets: [0, 1, 2, 3]
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
            title: WebAppLocals.getMessage('branchSeller'),
            data: 'branchSeller',
            render: $.fn.dataTable.render.ellipsis(100)
        }, {
            targets: 3,
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
            targets: 4,
            title: WebAppLocals.getMessage('orderTotal'),
            data: 'total',
            render: function(data, type, row, meta) {
                var output = row.currency + ' <strong>' + WebApp.formatMoney(row.total) + ' </strong>';
                return output;
            },
        }, {
            targets: 5,
            title: WebAppLocals.getMessage('tax'),
            data: 'tax',
            render: function(data, type, row, meta) {
                var output = WebApp.formatMoney(row.tax, 2) + '%';
                return output;
            },
        }, {
            targets: 6,
            title: WebAppLocals.getMessage('orderTotalWithVAT'),
            data: 'total',
            render: function(data, type, row, meta) {
                var output = row.currency + ' <strong>' + WebApp.formatMoney(row.total) + ' </strong>';
                return output;
            },
        }, {
            targets: 7,
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

                var btnFeedback =
                    '<a href="javascript:;" onclick=\'WebFeedbackModals.orderFeedbackModal(' +
                    row.id +
                    ')\' \
                          class="btn btn-sm navi-link btn-outline-primary btn-hover-primary mr-2" title="View">\
                          <i class="nav-icon la la-comment p-0"></i> &nbsp&nbsp' +
                    WebAppLocals.getMessage('feedback') +
                    '</a>';

                var btnView =
                    '<a href="javascript:;" onclick=\'WebAppModals.orderViewPharmacyModal(' +
                    row.id +
                    ')\' \
                          class="btn btn-sm navi-link btn-outline-primary btn-hover-primary mr-2" title="View">\
                          <i class="nav-icon la la-eye p-0"></i> &nbsp&nbsp' +
                    WebAppLocals.getMessage('view') +
                    '</a>';


                var outActions = '';

                outActions += btnFeedback;
                outActions += btnView;

                return outActions;
            },
        }];

        var searchQuery = {};


        $('.select2-search__field').addClass(" h-auto py-1 px-1 font-size-h6");

        var initiate = function() {
            WebApp.CreateDatatableServerside("Feedbacks List", elementId, url, columnDefs, searchQuery);
        };
        return {
            init: function() {
                initiate();
            },
        };
    }();

    PageClass.init();
</script>
<?php ob_end_flush(); ?>