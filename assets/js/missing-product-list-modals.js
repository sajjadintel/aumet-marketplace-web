'use strict';

// Class Definition
var OrderMissingProductListModals = (function () {
    var isPharmacy = false;

    var columnDefsOrderDetails = [
        {
            className: 'export_datatable',
            targets: '_all',
        },
        {
            targets: 0,
            title: WebAppLocals.getMessage('productCode'),
            data: 'productCode',
        },
        {
            targets: 1,
            title: WebAppLocals.getMessage('productName'),
            data: 'productNameEn',
        },
        {
            targets: 2,
            title: WebAppLocals.getMessage('productScientificName'),
            data: 'scientificName',
        },
        {
            targets: 3,
            title: WebAppLocals.getMessage('quantity'),
            data: 'quantity',
        },
        {
            targets: 4,
            title: WebAppLocals.getMessage('unitPrice'),
            data: 'unitPrice',
            render: function (data, type, row, meta) {
                var output = row.currency + ' <strong>' + Math.round((parseFloat(row.unitPrice) + Number.EPSILON) * 100) / 100 + '</strong>';
                return output;
            },
        },
        {
            targets: 5,
            title: WebAppLocals.getMessage('tax'),
            data: 'tax',
            render: function (data, type, row, meta) {
                var output = Math.round((parseFloat(row.tax) + Number.EPSILON) * 100) / 100 + '%';
                return output;
            },
        },
        {
            targets: 6,
            title: WebAppLocals.getMessage('orderTotal'),
            data: 'unitPrice',
            render: function (data, type, row, meta) {
                var output = parseFloat(row.unitPrice) * parseFloat(row.quantity) * (1 + parseFloat(row.tax) / 100);
                output = row.currency + ' <strong>' + Math.round((output + Number.EPSILON) * 100) / 100 + '</strong>';
                return output;
            },
        },
    ];

    var _orderMissingProductListPharmacyModal = function (orderId, mIsPharmacy) {
        isPharmacy = mIsPharmacy;
        WebApp.get('/web/distributor/orderMissingProducts/' + orderId, _missingProductListModalOpen);
    };

    var _missingProductListModalOpen = function (webResponse) {
        var status = '';
        switch (webResponse.data.order.statusId) {
            case 1:
                status = WebAppLocals.getMessage('orderStatus_Pending');
                break;
            case 2:
                status = WebAppLocals.getMessage('orderStatus_OnHold');
                break;
            case 3:
                status = WebAppLocals.getMessage('orderStatus_Processing');
                break;
            case 4:
                status = WebAppLocals.getMessage('orderStatus_Completed');
                break;
            case 5:
                status = WebAppLocals.getMessage('orderStatus_Canceled');
                break;
            case 6:
                status = WebAppLocals.getMessage('orderStatus_Received');
                break;
            case 7:
                status = WebAppLocals.getMessage('orderStatus_Paid');
                break;
            case 8:
                status = WebAppLocals.getMessage('orderStatus_MissingProducts');
                break;
        }

        $('#missingProductListModalTitle').html(WebAppLocals.getMessage('orderDetails'));
        $('#missingProductListModalBranchLabel').html(WebAppLocals.getMessage('branch'));
        if (isPharmacy) {
            $('#missingProductListModalNameLabel').html(WebAppLocals.getMessage('entitySeller'));
            $('#missingProductListModalNameText').html(webResponse.data.order.entitySeller + ' (' + webResponse.data.order.userSeller + ')');
            $('#missingProductListModalBranchText').html(webResponse.data.order.branchSeller);
        } else {
            $('#missingProductListModalNameLabel').html(WebAppLocals.getMessage('entityBuyer'));
            $('#missingProductListModalNameText').html(webResponse.data.order.entityBuyer + ' (' + webResponse.data.order.userBuyer + ')');
            $('#missingProductListModalBranchText').html(webResponse.data.order.branchBuyer);
        }
        $('#missingProductListModalStatusLabel').html(WebAppLocals.getMessage('orderStatus'));
        $('#missingProductListModalStatusText').html(status);
        $('#missingProductListModalTotalLabel').html(WebAppLocals.getMessage('orderTotal'));
        $('#missingProductListModalTotalText').html(webResponse.data.order.currency + Math.round((parseFloat(webResponse.data.order.total) + Number.EPSILON) * 100) / 100);
        $('#missingProductListModalDateLabel').html(WebAppLocals.getMessage('insertDate'));
        $('#missingProductListModalDateText').html(webResponse.data.order.insertDateTime);
        $('#missingProductListModalAddressLabel').html(WebAppLocals.getMessage('address'));
        $('#missingProductListModalAddressText').html(webResponse.data.order.addressBuyer);

        $('#modalBootstrapOrderDetailLog').attr('data-on-text', WebAppLocals.getMessage('orderDetails'));
        $('#modalBootstrapOrderDetailLog').attr('data-off-text', WebAppLocals.getMessage('orderLogs'));


        WebApp.CreateDatatableLocal('Order Missing Products', '#missingProductListModalDatatable', webResponse.data.orderDetail, columnDefsOrderDetails);

        $('#missingProductListModal').modal('show');
    };


    return {
        orderMissingProductListPharmacyModal: function (orderId) {
            _orderMissingProductListPharmacyModal(orderId, true);
        },
        orderMissingProductListDistributorModal: function (orderId) {
            _orderMissingProductListPharmacyModal(orderId, false);
        },
    };
})();