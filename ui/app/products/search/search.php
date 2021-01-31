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
    <div class="d-flex align-items-stretch text-center flex-column">
        <h2 class="text-primary font-weight-bolder mt-10 mb-15 font-size-h4"><?php echo $vModule_search_header ?></h2>
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
                    <select class="select2 form-control" id="searchProductsBrandNameInput" multiple="" name="brandName" data-select2-id="searchProductsBrandNameInput" tabindex="-1" aria-hidden="true">

                    </select>
                </div>
            </div>

            <div class="d-flex flex-column-fluid">
                <div class="input-group input-group-lg mr-5">
                    <div class="input-group-prepend pt-3 pl-1 pr-1">
                        <span class="svg-icon svg-icon-xl">
                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                    <polygon points="0 0 24 0 24 24 0 24" />
                                    <path d="M1.4152146,4.84010415 C11.1782334,10.3362599 14.7076452,16.4493804 12.0034499,23.1794656 C5.02500006,22.0396582 1.4955883,15.9265377 1.4152146,4.84010415 Z" fill="#000000" opacity="0.3" />
                                    <path d="M22.5950046,4.84010415 C12.8319858,10.3362599 9.30257403,16.4493804 12.0067693,23.1794656 C18.9852192,22.0396582 22.5146309,15.9265377 22.5950046,4.84010415 Z" fill="#000000" opacity="0.3" />
                                    <path d="M12.0002081,2 C6.29326368,11.6413199 6.29326368,18.7001435 12.0002081,23.1764706 C17.4738192,18.7001435 17.4738192,11.6413199 12.0002081,2 Z" fill="#000000" opacity="0.3" />
                                </g>
                            </svg>
                        </span>
                    </div>
                    <select class="select2 form-control" id="searchProductsScieceNameInput" multiple="" name="scientificName" data-select2-id="searchProductsScieceNameInput" tabindex="-1" aria-hidden="true">

                    </select>
                </div>
            </div>

            <?php
            $roleId = $_SESSION['objUser']->roleId;
            if (!Helper::isDistributor($roleId)) { ?>
                <div class="d-flex flex-column-fluid">
                    <div class="input-group input-group-lg mr-5">
                        <div class="input-group-prepend pt-3 pl-1 pr-1">
                            <span class="svg-icon svg-icon-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <path d="M20.4061385,6.73606154 C20.7672665,6.89656288 21,7.25468437 21,7.64987309 L21,16.4115967 C21,16.7747638 20.8031081,17.1093844 20.4856429,17.2857539 L12.4856429,21.7301984 C12.1836204,21.8979887 11.8163796,21.8979887 11.5143571,21.7301984 L3.51435707,17.2857539 C3.19689188,17.1093844 3,16.7747638 3,16.4115967 L3,7.64987309 C3,7.25468437 3.23273352,6.89656288 3.59386153,6.73606154 L11.5938615,3.18050598 C11.8524269,3.06558805 12.1475731,3.06558805 12.4061385,3.18050598 L20.4061385,6.73606154 Z" fill="#000000" opacity="0.3" />
                                        <polygon fill="#000000" points="14.9671522 4.22441676 7.5999999 8.31727912 7.5999999 12.9056825 9.5999999 13.9056825 9.5999999 9.49408582 17.25507 5.24126912" />
                                    </g>
                                </svg>
                            </span>
                        </div>
                        <select class="select2 form-control" id="searchProductsDistributorNameInput" multiple="" name="distributorName" data-select2-id="searchProductsDistributorNameInput" tabindex="-1" aria-hidden="true">
                        </select>
                    </div>
                </div>
            <?php } ?>

            <div class="d-flex flex-column-fluid">
                <div class="input-group input-group-lg">
                    <div class="input-group-prepend ">
                        <span class="input-group-text border-0 py-1 px-3">
                            <span class="svg-icon svg-icon-xl">

                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                        <rect x="0" y="0" width="24" height="24" />
                                        <rect fill="#000000" opacity="0.3" x="4" y="4" width="8" height="16" />
                                        <path d="M6,18 L9,18 C9.66666667,18.1143819 10,18.4477153 10,19 C10,19.5522847 9.66666667,19.8856181 9,20 L4,20 L4,15 C4,14.3333333 4.33333333,14 5,14 C5.66666667,14 6,14.3333333 6,15 L6,18 Z M18,18 L18,15 C18.1143819,14.3333333 18.4477153,14 19,14 C19.5522847,14 19.8856181,14.3333333 20,15 L20,20 L15,20 C14.3333333,20 14,19.6666667 14,19 C14,18.3333333 14.3333333,18 15,18 L18,18 Z M18,6 L15,6 C14.3333333,5.88561808 14,5.55228475 14,5 C14,4.44771525 14.3333333,4.11438192 15,4 L20,4 L20,9 C20,9.66666667 19.6666667,10 19,10 C18.3333333,10 18,9.66666667 18,9 L18,6 Z M6,6 L6,9 C5.88561808,9.66666667 5.55228475,10 5,10 C4.44771525,10 4.11438192,9.66666667 4,9 L4,4 L9,4 C9.66666667,4 10,4.33333333 10,5 C10,5.66666667 9.66666667,6 9,6 L6,6 Z" fill="#000000" fill-rule="nonzero" />
                                    </g>
                                </svg>

                            </span>
                        </span>
                    </div>
                    <input id="searchStockStatus" data-switch="true" type="checkbox" checked="checked" data-on-text="<?php echo $vModule_search_stockStatus_Available ?>" data-handle-width="50" data-off-text="<?php echo $vModule_search_stockStatus_others ?>" data-on-color="primary" />
                </div>
            </div>

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
                    <select class="select2 form-control" id="searchProductsCategoryInput" multiple="" name="category" data-select2-id="searchProductsCategoryInput" tabindex="-1" aria-hidden="true">
                    </select>
                </div>
            </div>


        </form>
        <!--a href="#" class="btn btn-text-danger btn-hover-text-primary font-weight-bold w-100 mt-5 m-auto"><?php echo $vModule_search_unavailableHeader ?></a-->
    </div>

    <div class="card card-custom gutter-b mt-20">
        <div class="card-body">
            <!--begin: Datatable-->
            <table id="datatable" class="compact hover order-column row-border table datatable datatable-bordered datatable-head-custom">
            </table>
            <!--end: Datatable-->
        </div>
    </div>
</div>
<!--end::Container-->
<script>
    var PageClass = function() {
        var elementId = "#datatable";
        var url = '/web/product/search';

        var columnDefs = [
            {
                className: "never",
                targets: [0]
            },
            {
                className: 'export_datatable',
                targets: [0, 1, 2, 3, 4, 5, 6, 7]
            },
            {
                targets: 0,
                title: 'id',
                data: 'id'
            },{
            targets: 1,
            title: WebAppLocals.getMessage('productName'),
            data: 'productName_en',
            render: function(data, type, row, meta) {
                var output = '<div style="display:flex;flex-direction:row;align-items: center"><div><a href="javascript:;" onclick="WebApp.loadSubPage(\'/web/entity/' +
                    row.entityId +
                    '/product/' +
                    row.id +
                    '\')"> ' +
                    '<div class="symbol symbol-60 flex-shrink-0 mr-4 bg-light"> <div class="symbol-label" style="background-image: url(\'' +
                    row.image +
                    '\')" ></div></div>' +
                    '</a></div>';
                output += '<div><span href="javascript:;" onclick="WebApp.loadSubPage(\'/web/entity/' +
                    row.entityId +
                    '/product/' +
                    row.id +
                    '\')" title="' +
                    row['productName_' + docLang] +
                    '"> ' +
                    WebApp.truncateText(row['productName_' + docLang], 100) +
                    '</span></div></div>';
                return output;
            },
        }, {
            targets: 2,
            title: WebAppLocals.getMessage('productScientificName'),
            data: 'scientificName'
        }, {
            targets: 3,
            title: WebAppLocals.getMessage('sellingEntityName'),
            data: 'entityName_' + docLang,
            render: $.fn.dataTable.render.ellipsis(100)
        }, {
            targets: 4,
            title: WebAppLocals.getMessage('stockAvailability'),
            data: 'stockStatusId',
            orderable: false,
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
            },
        }, {
            targets: 5,
            title: WebAppLocals.getMessage('unitPrice'),
            data: 'unitPrice',
            render: function(data, type, row, meta) {
                var output = row.unitPrice + ' ' + row.currency;

                return '<div style="width: max-content;">' + output + '</div>';
            },
        }, {
            targets: 6,
            title: WebAppLocals.getMessage('bonus'),
            data: 'activeBonus',
            render: function(data, type, row, meta) {
                let output = "";
                if (row.bonusTypeId === 2 && row.bonuses != null) {
                    /* let btnText = row.activeBonus ? row.activeBonus.minOrder + " / +" + row.activeBonus.bonus : "Select"; */
                    let btnText = "Select";
                    let allBonuses = row.bonuses.filter((bonus) => !row.activeBonus || row.activeBonus.id !== bonus.id);
                    let btnShowBonuses =
                        '<a style="width: max-content;" href="javascript:;" onclick=\'SearchDataTable.productAddBonusModal(' + row.id + ', ' + row.entityId + ', ' + JSON.stringify(allBonuses) + ')\'\
                        class="btn btn-sm btn-default btn-text-primary btn-hover-primary mr-2 mb-2" title="View Bonuses">\
                        <span>' + btnText + '</span></a>';
                    output += btnShowBonuses;
                }

                return output;
            },
        }, {
            targets: 7,
            title: WebAppLocals.getMessage('quantity'),
            data: 'id',
            orderable: false,
            render: function(data, type, row, meta) {
                var vQuantity = '';
                var output = '';
                var rowQuantity = 1;
                if (row.quantity) {
                    rowQuantity = row.quantity
                }

                if (row.stockStatusId == 1) {
                    let vQuantity =
                        '<input id="quantity-' +
                        row.id +
                        '" type="number" min="0" style="width: 70px; direction: ltr" value="' +
                        rowQuantity +
                        '" onkeypress="return event.charCode >= 48 && event.charCode <= 57"' +
                        '" oninput=\'SearchDataTable.changeProductQuantityCallback(' +
                        JSON.stringify(row) +
                        " )' >";
                    output += vQuantity;

                    let vQuantityFree =
                        '<input class="quantityFreeInput" id="quantityFreeInput-' +
                        row.id +
                        '" required style="display: none;">\
                            <span id="quantityFreeHolder-' +
                        row.id +
                        '" class="quantityFreeHolder label label-lg font-weight-bold label-primary label-inline" style="margin-left: 5px;"></span>';
                    output += vQuantityFree;
                }

                return '<div style="display: flex;">' + output + '</div>';
            },
        }, {
            targets: 8,
            title: '',
            data: 'id',
            orderable: false,
            render: function(data, type, row, meta) {

                var btnAddMoreToCart =
                    '<a style="display: flex;" href="javascript:;" onclick=\'SearchDataTable.onClickAddMoreToCart(' +
                    JSON.stringify(row) +
                    ' )\' class="btn btn-sm btn-primary btn-text-primary btn-hover-primary  mr-2 mb-2" title="Add to cart">\
                        <span class="svg-icon svg-icon-md">\
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">\
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\
                            <rect x="0" y="0" width="24" height="24"/>\
                            <path d="M18.1446364,11.84388 L17.4471627,16.0287218 C17.4463569,16.0335568 17.4455155,16.0383857 17.4446387,16.0432083 C17.345843,16.5865846 16.8252597,16.9469884 16.2818833,16.8481927 L4.91303792,14.7811299 C4.53842737,14.7130189 4.23500006,14.4380834 4.13039941,14.0719812 L2.30560137,7.68518803 C2.28007524,7.59584656 2.26712532,7.50338343 2.26712532,7.4104669 C2.26712532,6.85818215 2.71484057,6.4104669 3.26712532,6.4104669 L16.9929851,6.4104669 L17.606173,3.78251876 C17.7307772,3.24850086 18.2068633,2.87071314 18.7552257,2.87071314 L20.8200821,2.87071314 C21.4717328,2.87071314 22,3.39898039 22,4.05063106 C22,4.70228173 21.4717328,5.23054898 20.8200821,5.23054898 L19.6915238,5.23054898 L18.1446364,11.84388 Z" fill="#000000" opacity="0.3"/>\
                            <path d="M6.5,21 C5.67157288,21 5,20.3284271 5,19.5 C5,18.6715729 5.67157288,18 6.5,18 C7.32842712,18 8,18.6715729 8,19.5 C8,20.3284271 7.32842712,21 6.5,21 Z M15.5,21 C14.6715729,21 14,20.3284271 14,19.5 C14,18.6715729 14.6715729,18 15.5,18 C16.3284271,18 17,18.6715729 17,19.5 C17,20.3284271 16.3284271,21 15.5,21 Z" fill="#000000"/>\
                        </g></svg></span>\
                        <span class="label label-danger ml-2">' +
                    row.cart +
                    '</span></a>';

                var btnAddToCart =
                    '<a style="display: flex;" href="javascript:;" onclick=\'SearchDataTable.onClickAddToCart(' +
                    JSON.stringify(row) +
                    ' )\' class="btn btn-sm btn-default btn-text-primary btn-hover-primary  mr-2 mb-2" title="Add to cart">\
                        <span class="svg-icon svg-icon-md">\
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">\
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\
                            <rect x="0" y="0" width="24" height="24"/>\
                            <path d="M18.1446364,11.84388 L17.4471627,16.0287218 C17.4463569,16.0335568 17.4455155,16.0383857 17.4446387,16.0432083 C17.345843,16.5865846 16.8252597,16.9469884 16.2818833,16.8481927 L4.91303792,14.7811299 C4.53842737,14.7130189 4.23500006,14.4380834 4.13039941,14.0719812 L2.30560137,7.68518803 C2.28007524,7.59584656 2.26712532,7.50338343 2.26712532,7.4104669 C2.26712532,6.85818215 2.71484057,6.4104669 3.26712532,6.4104669 L16.9929851,6.4104669 L17.606173,3.78251876 C17.7307772,3.24850086 18.2068633,2.87071314 18.7552257,2.87071314 L20.8200821,2.87071314 C21.4717328,2.87071314 22,3.39898039 22,4.05063106 C22,4.70228173 21.4717328,5.23054898 20.8200821,5.23054898 L19.6915238,5.23054898 L18.1446364,11.84388 Z" fill="#000000" opacity="0.3"/>\
                            <path d="M6.5,21 C5.67157288,21 5,20.3284271 5,19.5 C5,18.6715729 5.67157288,18 6.5,18 C7.32842712,18 8,18.6715729 8,19.5 C8,20.3284271 7.32842712,21 6.5,21 Z M15.5,21 C14.6715729,21 14,20.3284271 14,19.5 C14,18.6715729 14.6715729,18 15.5,18 C16.3284271,18 17,18.6715729 17,19.5 C17,20.3284271 16.3284271,21 15.5,21 Z" fill="#000000"/>\
                        </g></svg></span></a>';

                var btnNotifyMe =
                    '<a href="javascript:;" class="btn btn-sm btn-default btn-text-primary btn-hover-primary btn-icon mr-2 mb-2" title="Add to cart">\
                        <span class="svg-icon svg-icon-md">\
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">\
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\
                            <rect x="0" y="0" width="24" height="24"/>\
                            <path d="M21,12.0829584 C20.6747915,12.0283988 20.3407122,12 20,12 C16.6862915,12 14,14.6862915 14,18 C14,18.3407122 14.0283988,18.6747915 14.0829584,19 L5,19 C3.8954305,19 3,18.1045695 3,17 L3,8 C3,6.8954305 3.8954305,6 5,6 L19,6 C20.1045695,6 21,6.8954305 21,8 L21,12.0829584 Z M18.1444251,7.83964668 L12,11.1481833 L5.85557487,7.83964668 C5.4908718,7.6432681 5.03602525,7.77972206 4.83964668,8.14442513 C4.6432681,8.5091282 4.77972206,8.96397475 5.14442513,9.16035332 L11.6444251,12.6603533 C11.8664074,12.7798822 12.1335926,12.7798822 12.3555749,12.6603533 L18.8555749,9.16035332 C19.2202779,8.96397475 19.3567319,8.5091282 19.1603533,8.14442513 C18.9639747,7.77972206 18.5091282,7.6432681 18.1444251,7.83964668 Z" fill="#000000"/>\
                            <circle fill="#000000" opacity="0.3" cx="19.5" cy="17.5" r="2.5"/>\
                            </g></svg></span></a>';

                var btnViewProduct =
                    '<a href="javascript:;" onclick="WebApp.loadSubPage(\'/web/entity/' +
                    row.entityId +
                    '/product/' +
                    row.id +
                    '\')" class="btn btn-sm btn-default btn-text-primary btn-hover-primary btn-icon mr-2 mb-2" title="View">\
                        <span class="svg-icon svg-icon-md">\
                        <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">\
                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">\
                            <rect x="0" y="0" width="24" height="24"/>\
                            <path d="M14.2928932,16.7071068 C13.9023689,16.3165825 13.9023689,15.6834175 14.2928932,15.2928932 C14.6834175,14.9023689 15.3165825,14.9023689 15.7071068,15.2928932 L19.7071068,19.2928932 C20.0976311,19.6834175 20.0976311,20.3165825 19.7071068,20.7071068 C19.3165825,21.0976311 18.6834175,21.0976311 18.2928932,20.7071068 L14.2928932,16.7071068 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"/>\
                            <path d="M11,16 C13.7614237,16 16,13.7614237 16,11 C16,8.23857625 13.7614237,6 11,6 C8.23857625,6 6,8.23857625 6,11 C6,13.7614237 8.23857625,16 11,16 Z M11,18 C7.13400675,18 4,14.8659932 4,11 C4,7.13400675 7.13400675,4 11,4 C14.8659932,4 18,7.13400675 18,11 C18,14.8659932 14.8659932,18 11,18 Z" fill="#000000" fill-rule="nonzero"/>\
                            <path d="M10.5,10.5 L10.5,9.5 C10.5,9.22385763 10.7238576,9 11,9 C11.2761424,9 11.5,9.22385763 11.5,9.5 L11.5,10.5 L12.5,10.5 C12.7761424,10.5 13,10.7238576 13,11 C13,11.2761424 12.7761424,11.5 12.5,11.5 L11.5,11.5 L11.5,12.5 C11.5,12.7761424 11.2761424,13 11,13 C10.7238576,13 10.5,12.7761424 10.5,12.5 L10.5,11.5 L9.5,11.5 C9.22385763,11.5 9,11.2761424 9,11 C9,10.7238576 9.22385763,10.5 9.5,10.5 L10.5,10.5 Z" fill="#000000" opacity="0.3"/>\
                            </g></svg></span></a>';

                var btnShowBonuses =
                    '<a href="javascript:;" onclick=\'SearchDataTable.productAddBonusModal(' + row.id + ')\'\
                    class="btn btn-default btn-text-primary btn-hover-primary mr-2 mb-2" title="View">\
                    <span>Show Bonuses</span></a>';

                var outActions = '';

                switch (row.stockStatusId) {
                    case 1:
                        outActions += btnViewProduct;
                        if (row.cart > 0) {
                            outActions += btnAddMoreToCart;
                        } else {
                            outActions += btnAddToCart;
                        }
                        SearchDataTable.changeProductQuantityCallback(row);
                        break;
                    case 2:
                        outActions += btnViewProduct;
                        outActions += btnNotifyMe;
                        break;
                    case 3:
                        outActions += btnViewProduct;
                        outActions += btnNotifyMe;
                        break;
                }

                return '<div style="display: flex;">' + outActions + '</div>';
            },
        }];

        var searchQuery = {
            productId: [],
            scientificNameId: [],
            entityId: [],
            stockOption: 1,
            categoryId: null,
            query: null,
        };

        var _selectBrand = $('#searchProductsBrandNameInput').select2({
            placeholder: "<?php echo $vModule_search_brandNameplaceholder ?>",
            tags: true,
            ajax: {
                url: '/web/product/brandname/list',
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

        _selectBrand.on("select2:select", function(e) {
            searchQuery.productId = $("#searchProductsBrandNameInput").val();
            updateDatatable();
        });

        _selectBrand.on("select2:unselect", function(e) {
            searchQuery.productId = $("#searchProductsBrandNameInput").val();
            updateDatatable();
        });


        function formatResult(node) {
            var $result = $('<span style="padding-left:' + (20 * (node.level - 1)) + 'px;">' + node.text + '</span>');
            return $result;
        }

        var lastParent = 0;
        var categories = [];

        var _category = $('#searchProductsCategoryInput').select2({
            placeholder: "<?php echo $vModule_search_categoryplaceholder ?>",
            tags: true,
            templateResult: formatResult,
            ajax: {
                url: '/web/product/category',
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
                        categories = data;
                    else
                        categories.push(...data);

                    return {
                        results: data,
                        pagination: {
                            more: response.data.pagination
                        }
                    }
                },
            }
        });


        /* Convert categories format:
         * 1- Change sub category name to: "parent - sub"
         * 2- Add parent category above sub categories  */
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

        /* remove sub categories if parent is selected */
        function filterSubCategories(selectedIds) {
            var filteredCategories = [];
            /* convert category ids to objects to have all data of category */
            var selectedItems = convertCategoryIdsToObjects(selectedIds);

            /* only select parent categories and sub categories that theirs parent is not selected */
            for (var i = 0; i < selectedItems.length; i++) {
                if (isSubCategory(selectedItems[i]) || isParentSubCategoryExists(selectedItems[i], selectedItems)) {
                    filteredCategories.push(selectedItems[i]);
                }
            }
            /* convert category objets to ids to be the select2 format */
            return convertCategoryObjectsToIds(filteredCategories);
        }

        /* get category object corresponding to id given */
        function getCategoryById(id) {
            for (var i = 0; i < categories.length; i++) {
                if (categories[i].id == id) {
                    return categories[i];
                }
            }
        }

        function convertCategoryIdsToObjects(ids) {
            var objects = [];
            for (var i = 0; i < ids.length; i++) {
                objects.push(getCategoryById(ids[i]))
            }
            return objects;
        }

        function convertCategoryObjectsToIds(categories) {
            var ids = [];
            for (var i = 0; i < categories.length; i++) {
                ids.push(categories[i].id);
            }
            return ids;
        }

        function getCategoryParentIds(selectedItems) {
            var parentIds = [];
            for (var i = 0; i < selectedItems.length; i++) {
                if (selectedItems[i].parent_id == 0) {
                    parentIds.push(selectedItems[i]);
                }
            }
            return parentIds;
        }


        function isParentSubCategoryExists(category, categories) {
            if (category.parent_id == 0) {
                for (var i = 0; i < categories.length; i++) {
                    if (categories[i].parent_id == category.id) {
                        return false;
                    }
                }
            }
            return true;
        }

        function isSubCategory(category) {
            return category.parent_id != 0
        }


        _category.on("select2:select", function(e) {
            var filteredData = filterSubCategories(_category.val());
            _category.val(filteredData).trigger('change');

            searchQuery.categoryId = $("#searchProductsCategoryInput").val();
            updateDatatable();
        });

        _category.on("select2:unselect", function(e) {
            searchQuery.categoryId = $("#searchProductsCategoryInput").val();
            updateDatatable();
        });


        var _selectScientific = $('#searchProductsScieceNameInput').select2({
            placeholder: "<?php echo $vModule_search_scientificNamePlaceholder ?>",
            tags: true,
            ajax: {
                url: '/web/product/scientificname/list',
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

        _selectScientific.on("select2:select", function(e) {
            searchQuery.scientificNameId = $("#searchProductsScieceNameInput").val();
            updateDatatable();
        });

        _selectScientific.on("select2:unselect", function(e) {
            searchQuery.scientificNameId = $("#searchProductsScieceNameInput").val();
            updateDatatable();
        });

        var _selectDistributor = $('#searchProductsDistributorNameInput').select2({
            placeholder: "<?php echo $vModule_search_distributorNamePlaceholder ?>",
            tags: true,
            ajax: {
                url: '/web/order/Distributor/listAll',
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

        _selectDistributor.on("select2:select", function(e) {
            searchQuery.entityId = $("#searchProductsDistributorNameInput").val();
            updateDatatable();
        });

        _selectDistributor.on("select2:unselect", function(e) {
            searchQuery.entityId = $("#searchProductsDistributorNameInput").val();
            updateDatatable();
        });

        $('#searchStockStatus').bootstrapSwitch().on("switchChange.bootstrapSwitch", function(event, state) {
            searchQuery.stockOption = state ? 1 : 0;
            updateDatatable();
            <?php /*
            if (state) {
                SearchDataTable.hideColumn('stockStatusId');
            } else {
                SearchDataTable.showColumn('stockStatusId');
            }
            */ ?>

        });

        $('.select2-search__field').addClass(" h-auto py-1 px-1 font-size-h6");

        var initiate = function() {
            var urlParams = new URLSearchParams(window.location.search);
            var sortParam = urlParams.get('sort');


            var allSortParams = [
                "newest",
                "top-selling"
            ];
            if (allSortParams.includes(sortParam)) {
                url += "/" + sortParam;
            }
            updateDatatable();
        };

        var query = <?php echo isset($_GET['query']) ? "'" . $_GET['query'] . "'" : 'null'; ?>;
        var distributorId = <?php echo isset($_GET['distributorId']) ? "'" . $_GET['distributorId'] . "'" : 'null'; ?>;
        var scientificNameId = <?php echo isset($_GET['scientificNameId']) ? "'" . $_GET['scientificNameId'] . "'" : 'null'; ?>;

        var dbAdditionalOptions = {
            processing: false,
            datatableOptions: {
                buttons: [],
            }
        };

        function updateDatatable() {
            if (query != null)
                searchQuery.query = query;
            if (distributorId != null && !searchQuery.entityId.includes(distributorId))
                searchQuery.entityId.push(distributorId);
            if (scientificNameId != null && !searchQuery.scientificNameId.includes(scientificNameId))
                searchQuery.scientificNameId.push(scientificNameId);
            WebApp.CreateDatatableServerside("Product List", elementId, url, columnDefs, searchQuery, dbAdditionalOptions);

        }

        return {
            init: function() {
                initiate();
            },
        };
    }();

    $(document).ready(function() {
        PageClass.init();
    })
</script>
<?php ob_end_flush(); ?>