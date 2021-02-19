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
                    <div class="input-group-prepend pl-1 pr-1">
                        <span class="la-icon">
                            <i class="text-primary la la-search"></i>
                        </span>
                    </div>
                    <select class="select2 form-control" id="buyerNameSelect" multiple="" name="distributorName" data-select2-id="buyerNameSelect" tabindex="-1" aria-hidden="true">
                    </select>
                </div>
            </div>

            <div class="d-flex flex-column-fluid">
                <div class="input-group input-group-lg mr-5">
                    <div class="input-group-prepend pl-1 pr-1">
                        <span class="la-icon">
                            <i class="text-primary la la-city"></i>
                        </span>
                    </div>
                    <select class="select2 form-control" id="countryCitySelect" multiple="" name="countryCity" data-select2-id="countryCitySelect" tabindex="-1" aria-hidden="true">
                    </select>
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


        function formatResult(node) {
            var $result = $('<span style="padding-left:' + (20 * (node.level - 1)) + 'px;">' + node.text + '</span>');
            return $result;
        }

        var lastParent = 0;
        var countries = [];

        var _country = $('#countryCitySelect').select2({
            placeholder: "<?php echo $vModule_search_countryPlaceholder ?>",
            templateResult: formatResult,
            ajax: {
                url: '/web/country/list/<?php echo $arrEntityId; ?>',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    var query = {
                        term: params.term,
                        page: params.page || 1
                    };
                    if (query.page == 1) {
                        lastParent = 0;
                    }
                    return query;
                },
                processResults: function(response) {
                    var data = convertDataFormat(response.data.results);
                    if (lastParent == 0)
                        countries = data;
                    else
                        countries.push(...data);

                    return {
                        results: data,
                        pagination: {
                            more: response.data.pagination
                        }
                    }
                },
            }
        });


        /* Convert countries format:
         * 1- Change city name to: "country - city"
         * 2- Add parent country above cities  */
        function convertDataFormat(data) {
            var output = [];
            for (var i = 0; i < data.length; i++) {
                var element = data[i];

                if (lastParent != element.parent_id) {
                    lastParent = element.parent_id;
                    output.push({
                        id: element.parent_id,
                        text: element.parent_name,
                        level: 1,
                        parent_id: 0
                    });
                }
                output.push({
                    id: element.id,
                    text: element.parent_name + " - " + element.name,
                    level: 2,
                    parent_id: element.parent_id
                });
            }
            return output;
        }

        /* remove cities if parent is selected */
        function filterCities(selectedIds) {
            var filteredCountries = [];
            /* convert country ids to objects to have all data of country */
            var selectedItems = convertCountryIdsToObjects(selectedIds);

            /* only select parent countries and cities that theirs parent is not selected */
            for (var i = 0; i < selectedItems.length; i++) {
                if (isCity(selectedItems[i]) || isParentCityExists(selectedItems[i], selectedItems)) {
                    filteredCountries.push(selectedItems[i]);
                }
            }
            /* convert country objets to ids to be the select2 format */
            return convertCountryObjectsToIds(filteredCountries);
        }

        /* get country object corresponding to id given */
        function getCountryById(id) {
            for (var i = 0; i < countries.length; i++) {
                if (countries[i].id == id) {
                    return countries[i];
                }
            }
        }

        function convertCountryIdsToObjects(ids) {
            var objects = [];
            for (var i = 0; i < ids.length; i++) {
                objects.push(getCountryById(ids[i]))
            }
            return objects;
        }

        function convertCountryObjectsToIds(countries) {
            var ids = [];
            for (var i = 0; i < countries.length; i++) {
                ids.push(countries[i].id);
            }
            return ids;
        }

        function getCountryParentIds(selectedItems) {
            var parentIds = [];
            for (var i = 0; i < selectedItems.length; i++) {
                if (selectedItems[i].parent_id == 0) {
                    parentIds.push(selectedItems[i]);
                }
            }
            return parentIds;
        }


        function isParentCityExists(country, countries) {
            if (country.parent_id == 0) {
                for (var i = 0; i < countries.length; i++) {
                    if (countries[i].parent_id == country.id) {
                        return false;
                    }
                }
            }
            return true;
        }

        function isCity(country) {
            return country.parent_id != 0
        }


        _country.on("select2:select", function(e) {
            var filteredData = filterCities(_country.val());
            _country.val(filteredData).trigger('change');

            searchQuery.countryId = $("#countryCitySelect").val();
            updateDatatable();
        });

        _country.on("select2:unselect", function(e) {
            searchQuery.countryId = $("#countryCitySelect").val();
            updateDatatable();
        });

        var _customerName = $('#buyerNameSelect').select2({
            placeholder: "<?php echo $vModule_search_customerNamePlaceholder ?>",
            ajax: {
                url: '/web/customername/list/<?php echo $arrEntityId; ?>',
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

        _customerName.on("select2:select", function(e) {
            searchQuery.buyerName = $("#buyerNameSelect").val();
            updateDatatable();
        });

        _customerName.on("select2:unselect", function(e) {
            searchQuery.buyerName = $("#buyerNameSelect").val();
            updateDatatable();
        });

        var searchQuery = {
            buyerName: [],
            countryId: null,
        };

        function updateDatatable() {
            WebApp.CreateDatatableServerside("Customers List", elementId, url, columnDefs, searchQuery);
        }

        var initiate = function () {
            updateDatatable();
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