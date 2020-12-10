'use strict';

// Class Definition
var WebMissingProductModals = (function () {
    var rating = 0;

    var _orderMissingProductPharmacyModal = function (orderId) {
        WebApp.get('/web/distributor/order/' + orderId, _missingProductModalOpen);
    };

    var _missingProductModalOpen = function (webResponse) {
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
        $('#missingProductModalTitle').html(WebAppLocals.getMessage('orderReportMissing'));
        $('#missingProductModalCustomerNameLabel').html(WebAppLocals.getMessage('entitySeller'));
        $('#missingProductModalCustomerNameText').html(webResponse.data.order.entitySeller + ' (' + webResponse.data.order.userSeller + ')');
        $('#missingProductModalStatusLabel').html(WebAppLocals.getMessage('orderStatus'));
        $('#missingProductModalStatusText').html(status);
        $('#missingProductModalTotalLabel').html(WebAppLocals.getMessage('orderTotal'));
        $('#missingProductModalTotalText').html(webResponse.data.order.currency + Math.round((parseFloat(webResponse.data.order.total) + Number.EPSILON) * 100) / 100);
        $('#missingProductModalDateLabel').html(WebAppLocals.getMessage('insertDate'));
        $('#missingProductModalDateText').html(webResponse.data.order.insertDateTime);
        $('#missingProductModalBranchLabel').html(WebAppLocals.getMessage('branch'));
        $('#missingProductModalBranchText').html(webResponse.data.order.branchBuyer);
        $('#missingProductModalAddressLabel').html(WebAppLocals.getMessage('address'));
        $('#missingProductModalAddressText').html(webResponse.data.order.addressBuyer);

        $('#missingProductModalForm').attr('action', '/web/pharmacy/order/missingProducts');

        $('#missingProductOrderId').val(webResponse.data.order.id);

        $repeater.setList([]);

        $products = webResponse.data.orderDetail;

        $products = $.map($products, function (obj) {
            obj.text = obj.productNameEn
            return obj;
        });


        $('#missingProductModal').appendTo('body').modal('show');
    };


    return {
        orderMissingProductPharmacyModal: function (orderId) {
            _orderMissingProductPharmacyModal(orderId);
        },
    };
})();
