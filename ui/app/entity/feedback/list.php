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
        <h2 class="text-primary font-weight-bolder mt-10 mb-15 font-size-h4"><?php echo $vModule_feedback_header; ?></h2>
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

        var columnDefs = [{
            className: "export_datatable",
            targets: [0, 1, 2, 3]
        }, {
            targets: 0,
            title: '#',
            data: 'id',
            render: function (data, type, row, meta) {
                var output = '(' + row.id + ') - #' + row.serial;
                return output;
            }
        }, {
            targets: 1,
            title: WebAppLocals.getMessage('entityBuyer'),
            data: 'entityName_' + docLang,
            render: $.fn.dataTable.render.ellipsis( 100 )
        }, {
            targets: 2,
            title: WebAppLocals.getMessage('userFullname'),
            data: 'userFullname',
            render: $.fn.dataTable.render.ellipsis( 100 )
        }, {
            targets: 3,
            title: WebAppLocals.getMessage('insertDate'),
            data: 'insertDateTime',
            render: function (data, type, row, meta) {
                var output = '';
                if (row.insertDateTime) {
                    output = '<span class="label label-lg font-weight-bold label-inline" style="direction: ltr">' + moment(row.insertDateTime).format('DD / MM / YYYY') + '</span>';
                }
                return output
            }
        }, {
            targets: 4,
            title: WebAppLocals.getMessage('orderRating'),
            data: 'stars',
            render: function (data, type, row, meta) {
                var status = {
                    1: {
                        title: 'Poor',
                        class: ' label-danger',
                    },
                    2: {
                        title: 'Fair',
                        class: ' label-warning',
                    },
                    3: {
                        title: 'Good',
                        class: ' label-dark',
                    },
                    4: {
                        title: 'Very Good',
                        class: ' label-info',
                    },
                    5: {
                        title: 'Excellent',
                        class: ' label-primary',
                    },
                };

                var output = '';

                console.log('testtest', row.stars, status[row.stars].class, status[row.stars].title);
                output += '<div><span class="label label-lg font-weight-bold ' + status[row.stars].class + ' label-inline">' + status[row.stars].title + '</span></div>';

                return output;
            }
        }, {
            targets: 5,
            title: '',
            data: 'stars',
            render: function (data, type, row, meta) {
                var cssData = 'text-muted';
                switch (row.rateId) {
                    case 1:
                        cssData = 'text-danger';
                        break;
                    case 2:
                        cssData = 'text-warning';
                        break;
                    case 3:
                        cssData = 'text-dark';
                        break;
                    case 4:
                        cssData = 'text-info';
                        break;
                    case 5:
                        cssData = 'text-primary';
                        break;
                }

                var output = '<div>';
                for (var i = 0; i < row.stars; i++) {
                    output += '<i class="icon-xl fas fa-star mr-1 ' + cssData + '"></i>';
                }
                for (; i < 5; i++) {
                    output += '<i class="icon-xl far fa-star mr-1 text-muted"></i>';
                }
                output += '</div>';

                return output;
            }
        }, {
            targets: 6,
            title: WebAppLocals.getMessage('orderComment'),
            data: 'feedback',
            render: function (data, type, row, meta) {
                var output = '';
                if (row.insertDateTime) {
                    output = '<span class="label label-lg font-weight-bold label-inline" style="direction: ltr" title="'+
                        row.feedback +
                    '"> ' + +  WebApp.truncateText(row.feedback, 100) + '</span>';
                }
                return output
            }
        }, {
            targets: 6,
            title: '',
            data: 'id',
            orderable: false,
            render: function (data, type, row, meta) {

                var btnView =
                    '<a href="javascript:;" onclick=\'WebAppModals.orderViewModal(' +
                    row.id +
                    ')\' \
                        class="btn btn-sm navi-link btn-outline-primary btn-hover-primary mr-2" title="View">\
                        <i class="nav-icon la la-eye p-0"></i> &nbsp&nbsp' +
                    WebAppLocals.getMessage('view') +
                    '</a>';


                var outActions = '';

                outActions += btnView;

                return outActions;
            },
        }];

        var searchQuery = {};


        $('.select2-search__field').addClass(" h-auto py-1 px-1 font-size-h6");

        var initiate = function () {
            WebApp.CreateDatatableServerside("Feedbacks List", elementId, url, columnDefs, searchQuery);
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


