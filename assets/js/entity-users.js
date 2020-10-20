'use strict';
// Class definition

var EntityUsersDataTable = (function () {
    // Private functions

    var datatable;
    var _readParams;

    var _init = function (objQuery) {
        _readParams = objQuery;
        datatable = $('#kt_datatable').KTDatatable({
            // datasource definition

            data: {
                type: 'remote',
                source: {
                    read: {
                        url: '/web/distributor/users',
                        params: _readParams,
                    },
                },
                serverPaging: true,
                serverFiltering: true,
                serverSorting: true,
            },

            // layout definition
            layout: {
                scroll: false, // enable/disable datatable scroll both horizontal and vertical when needed.
                footer: false, // display/hide footer
            },

            // column sorting
            sortable: true,

            pagination: true,

            // Order settings
            order: [[2, 'asc']],

            // columns definition
            columns: [
                {
                    field: 'id',
                    title: '#',
                    sortable: 'asc',
                    width: 40,
                    type: 'number',
                    selector: false,
                    textAlign: 'left',
                    autoHide: false,
                },
                {
                    field: 'productName_en', // + docLang,
                    title: WebAppLocals.getMessage('productName'),
                    autoHide: false,
                },
                {
                    field: 'image',
                    title: '',
                    autoHide: true,
                    sortable: false,
                    template: function (row) {
                        return (
                            '<div class="symbol symbol-60 flex-shrink-0 mr-4 bg-light"> <div class="symbol-label" style="background-image: url(\'' +
                            row.image +
                            '\')" ></div></div>'
                        );
                    },
                },
                {
                    field: 'scientificName',
                    title: WebAppLocals.getMessage('productScintificName'),
                    autoHide: true,
                },
                {
                    field: 'entityName_ar', // + docLang,
                    title: WebAppLocals.getMessage('sellingEntityName'),
                    autoHide: false,
                },
                {
                    field: 'expiryDate',
                    title: WebAppLocals.getMessage('expiryDate'),
                    autoHide: true,
                    template: function (row) {
                        if (row.expiryDate) {
                            return (
                                '<span class="label label-lg font-weight-bold label-inline" style="direction: ltr">' + moment(row.expiryDate).format('DD / MM / YYYY') + '</span>'
                            );
                        } else {
                            return '';
                        }
                    },
                },
                {
                    field: 'stockStatusId',
                    sortable: false,
                    title: WebAppLocals.getMessage('stockAvailability'),
                    autoHide: true,
                    // callback function support for column rendering
                    template: function (row) {
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
                        // output += '<div class="text-muted">' + (row.stockUpdateDateTime != null ? jQuery.timeago(row.stockUpdateDateTime) : 'NA') + '</div>';

                        return output;
                    },
                },
                {
                    field: 'stockUpdateDateTime',
                    title: WebAppLocals.getMessage('stockUpdateDateTime'),
                    autoHide: false,
                    template: function (row) {
                        if (row.stockUpdateDateTime) {
                            return '<span class="label label-lg font-weight-bold label-inline" style="direction: ltr">' + moment(row.stockUpdateDateTime).fromNow() + '</span>';
                        } else {
                            return '';
                        }
                    },
                },
                {
                    field: 'unitPrice', // + docLang,
                    title: WebAppLocals.getMessage('unitPrice'),
                    autoHide: false,
                    template: function (row) {
                        return row.unitPrice + ' ' + row.currency;
                    },
                },
                // {
                // 	field: 'bonusOptions', // + docLang,
                // 	title: WebAppLocals.getMessage('bonus'),
                // 	autoHide: false,
                // 	sortable: false,
                // 	template: function (row) {
                // 		if (row.stockStatusId == 1) {
                // 			var tdText = '';
                // 			row.bonusOptions.sort((a, b) => parseInt(a.minOrder) - parseInt(b.minOrder));
                // 			row.bonusOptions.forEach((element) => {
                // 				tdText +=
                // 					'<a href="javascript:;" onclick=\'SearchDataTable.onBonusOptionCallback(' +
                // 					JSON.stringify(row) +
                // 					', ' +
                // 					JSON.stringify(element) +
                // 					' )\'><span id="bonusOption-' +
                // 					row.id +
                // 					'-' +
                // 					element.id +
                // 					'" class="label label-xl label-light label-square label-inline mr-2 bonus-option-label-' +
                // 					row.id +
                // 					'">' +
                // 					element.name +
                // 					' </span></a>';
                // 			});
                // 			//var bonus = math.evaluate('floor(quantity / 6) * 2', row);
                // 			//return '<span id="bonus-' + row.id + '" class="label label-xl label-rounded label-primary" style="width: 50px">' + bonus + ' </span>';
                // 			return tdText;
                // 		} else {
                // 			return '';
                // 		}
                // 	},
                // },
                {
                    field: 'Actions',
                    title: '',
                    sortable: false,
                    width: 200,
                    overflow: 'visible',
                    autoHide: false,
                    template: function (row) {
                        var outActions = '';

                        var btnEdit =
                            '<a href="javascript:;" onclick=\'DistributorProductsDataTable.productEditModal(' +
                            row.id +
                            ')\' \
                        class="btn btn-sm btn-primary btn-hover-primary mr-2" title="Edit">\
                        <i class="nav-icon la la-edit p-0"></i> ' +
                            WebAppLocals.getMessage('edit') +
                            '</a>';

                        var btnEditQuantity =
                            '<a href="javascript:;" onclick=\'DistributorProductsDataTable.productEditQuantityModal(' +
                            row.id +
                            ')\' \
                        class="btn btn-sm btn-primary btn-hover-primary mr-2" title="Edit">\
                        <i class="nav-icon la la-edit p-0"></i> ' +
                            WebAppLocals.getMessage('editQuantity') +
                            '</a>';

                        // switch (row.stockStatusId) {
                        // 	case 1:
                        // 		outActions += btnViewProduct;
                        // 		if (row.cart > 0) {
                        // 			outActions += btnAddMoreToCart;
                        // 		} else {
                        // 			outActions += btnAddToCart;
                        // 		}
                        // 		SearchDataTable.changeProductQuantityCallback(row);
                        // 		break;
                        // 	case 2:
                        // 		outActions += btnViewProduct;
                        // 		outActions += btnNotifyMe;
                        // 		break;
                        // 	case 3:
                        // 		outActions += btnViewProduct;
                        // 		outActions += btnNotifyMe;
                        // 		break;
                        // }

                        outActions += btnEdit;
                        outActions += btnEditQuantity;

                        return outActions;
                    },
                },
            ],
        });
    };

    var _productEditModal = function (productId) {
        WebApp.get('/web/distributor/users/' + productId, _productEditModalOpen);
    };

    var _productEditQuantityModal = function (productId) {
        WebApp.get('/web/distributor/product/quantity/' + productId, _productEditQuantityModalOpen);
    };

    var _productAddModal = function () {
        _productAddModalOpen();
    };

    var _productEditModalOpen = function (webResponse) {
        $('#editModalForm').attr('action', '/web/distributor/product/edit');
        $('#editProductId').val(webResponse.data.product.id);

        $('#editModalTitle').html(WebAppLocals.getMessage('edit'));
        $("label[for='editProductScientificName']").text(WebAppLocals.getMessage('productScintificName'));
        $("label[for='editProductCountry']").text(WebAppLocals.getMessage('madeInCountry'));
        $("label[for='editProductNameAr']").text(WebAppLocals.getMessage('productName') + ' AR');
        $("label[for='editProductNameEn']").text(WebAppLocals.getMessage('productName') + ' EN');
        $("label[for='editProductNameFr']").text(WebAppLocals.getMessage('productName') + ' FR');
        $("label[for='editUnitPrice']").text(WebAppLocals.getMessage('unitPrice'));

        $('#editProductScientificName').append(new Option(webResponse.data.product.scientificName, webResponse.data.product.scientificNameId));
        $('#editProductScientificName').val(webResponse.data.product.scientificNameId);
        $('#editProductCountry').append(new Option(webResponse.data.product['madeInCountryName_' + docLang], webResponse.data.product.madeInCountryId));
        $('#editProductCountry').val(webResponse.data.product.madeInCountryId);
        $('#editProductNameAr').val(webResponse.data.product.productName_ar);
        $('#editProductNameEn').val(webResponse.data.product.productName_en);
        $('#editProductNameFr').val(webResponse.data.product.productName_fr);
        $('#editUnitPrice').val(webResponse.data.product.unitPrice);
        $('#editModalAction').html(WebAppLocals.getMessage('edit'));
        $('#editModal').appendTo('body').modal('show');
    };

    var _productEditQuantityModalOpen = function (webResponse) {
        $('#editQuantityModalForm').attr('action', '/web/distributor/product/editQuantity');
        $('#editQuantityProductId').val(webResponse.data.product.id);

        $('#editQuantityModalTitle').html(WebAppLocals.getMessage('editQuantity'));

        $("label[for='editQuantityStock']").text(WebAppLocals.getMessage('quantityAvailable'));
        $('#editQuantityStock').val(webResponse.data.product.stock);

        switch (webResponse.data.product.stockStatusId) {
            case 1:
                $('#editQuantityStockAvailability').bootstrapSwitch('state', true);
                $('#editQuantityStockAvailability').bootstrapSwitch('disabled', true);
                break;
            case 2:
                $('#editQuantityStockAvailability').bootstrapSwitch('state', false);
                break;
            case 3:
                $('#editQuantityStockAvailability').bootstrapSwitch('state', true);
                break;
            default:
                break;
        }
        $('#editQuantityStock').on('change paste keyup', function () {
            if ($(this).val() > 0) {
                $('#editQuantityStockAvailability').bootstrapSwitch('disabled', true);
            } else {
                $('#editQuantityStockAvailability').bootstrapSwitch('disabled', false);
            }
        });

        $('#editQuantityModalAction').html(WebAppLocals.getMessage('editQuantity'));
        $('#editQuantityModal').appendTo('body').modal('show');
    };

    var _productAddModalOpen = function () {
        $('#addModalForm').attr('action', '/web/distributor/product/add');

        $('#addModalTitle').html(WebAppLocals.getMessage('add'));
        $("label[for='addProductScientificName']").text(WebAppLocals.getMessage('productScintificName'));
        $("label[for='addProductCountry']").text(WebAppLocals.getMessage('madeInCountry'));
        $("label[for='addProductNameAr']").text(WebAppLocals.getMessage('productName') + ' AR');
        $("label[for='addProductNameEn']").text(WebAppLocals.getMessage('productName') + ' EN');
        $("label[for='addProductNameFr']").text(WebAppLocals.getMessage('productName') + ' FR');
        $("label[for='addUnitPrice']").text(WebAppLocals.getMessage('unitPrice'));
        $("label[for='addStock']").text(WebAppLocals.getMessage('quantityAvailable'));

        $('#addModalAction').html(WebAppLocals.getMessage('add'));
        $('#addModal').appendTo('body').modal('show');
    };

    return {
        // public functions
        init: function (objQuery) {
            _init(objQuery);
        },
        setReadParams: function (objQuery) {
            _readParams = objQuery;
            datatable.setDataSourceParam('query', _readParams);
            datatable.reload();
        },
        reloadDatatable: function () {
            datatable.reload();
        },
        productEditQuantityModal: function (productId) {
            _productEditQuantityModal(productId);
        },
        productEditModal: function (productId) {
            _productEditModal(productId);
        },
        productAddModal: function () {
            _productAddModal();
        },
        showColumn: function (columnName) {
            datatable.showColumn(columnName);
        },
        hideColumn: function (columnName) {
            datatable.hideColumn(columnName);
        },
    };
})();
