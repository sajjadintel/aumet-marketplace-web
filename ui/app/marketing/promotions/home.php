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

    <form class="d-flex position-relative w-100 m-auto flex-wrap">

        <div class="d-flex flex-column-fluid">
            <div class="input-group input-group-lg justify-content-end">
                <div class="input-group-prepend ">

                    <label>
                        <button type="button" class="btn btn-lg btn-primary btn-hover-primary mr-2 btn-lg-radius" title="<?php echo $vModule_promotion_addPromotion; ?>" onclick="WebApp.loadPage('/web/distributor/marketing/promotion/add')">
                            <i class="nav-icon la la-plus p-0"></i> <?php echo $vButton_add; ?>
                        </button>
                    </label>

                </div>
            </div>


    </form>
    </div>

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
                targets: [0, 1, 2, 3, 4, 5]
            },
            {
                targets: 0,
                title: '#',
                data: 'id',
            },
            {
                targets: 1,
                title: WebAppLocals.getMessage('name'),
                data: 'name',
            },
            {
                targets: 2,
                title: WebAppLocals.getMessage('start'),
                data: 'startDate',
            },
            {
                targets: 3,
                title: WebAppLocals.getMessage('end'),
                data: 'endDate',
            },
            {
                targets: 4,
                title: WebAppLocals.getMessage('active'),
                data: 'active',
                render: function (data, type, row, meta) {
                    var output = '';
                    if(row.active) {
                        output = '<i class="la la-check text-primary"></i>';  
                    } else {
                        output = '<i class="la la-close"></i>';  
                    }

                    return output;
                },
            },
            {
                targets: 5,
                title: '',
                data: 'id',
                orderable: false,
                render: function (data, type, row, meta) {
                    var output = '<a href="javascript:;" onclick=\'WebApp.loadPage("/web/distributor/marketing/promotion/edit/' + row.id + '")\' \
                        class="btn btn-sm btn-primary mr-2" title="' + WebAppLocals.getMessage('edit') + '">\
                        <i class="nav-icon la la-edit p-0"></i> ' + WebAppLocals.getMessage('edit') + '</a>';

                    output += '<a href="javascript:;" onclick=\'DistributorPromotions.deletePromotion("' + row.id + '")\' \
                        class="btn btn-sm btn-danger mr-2" title="' + WebAppLocals.getMessage('delete') + '">\
                        <i class="nav-icon la la-trash p-0"></i> ' + WebAppLocals.getMessage('delete') + '</a>';
                    
                    return '<div class="d-flex justify-content-center">' + output + '</div>';
                },
            }
        ];

        var initiate = function () {
            WebApp.CreateDatatableServerside("Promotions List", elementId, url, columnDefs);
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