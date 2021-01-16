'use strict';

// Class Definition
var WebMissingProductModals = (function () {
    var repeater;
    var products;

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

        repeater = $('#missingProductListRepeater').repeater({
            isFirstItemUndeletable: true,
            show: function () {
                $(this).slideDown();
                _validateInput(this);
                _initSelect2(this);
            },
            hide: function (deleteElement) {
                if (confirm( WebAppLocals.getMessage('missingProducts_deleteConfirmation'))) {
                    $(this).slideUp(deleteElement);
                }
            },
        });


        repeater.setList([]);

        products = webResponse.data.orderDetail;

        products = $.map(products, function (obj) {
            obj.text = obj.productNameEn;
            obj.id = obj.productCode;
            return obj;
        });

        $('#missingProductModal').appendTo('body').modal('show');

    };


    var _initSelect2 = function initSelect2(input) {
        $(input).find('.select2').select2({
            placeholder: WebAppLocals.getMessage('missingProducts_filterByProduct'),
            data: products,
        });
    }

    var _validateInput = function validateInput(input) {
        $(input).find('.missingProductQuantity').keydown(function () {
            // Save old value.
            if (!$(this).val() || (parseInt($(this).val()) <= $(this).attr('max') && parseInt($(this).val()) >= $(this).attr('min')))
                $(this).data("old", $(this).val());
        });
        $(input).find('.missingProductQuantity').keyup(function () {
            // Check correct, else revert back to old value.
            if (!$(this).val() || (parseInt($(this).val()) <= $(this).attr('max') && parseInt($(this).val()) >= $(this).attr('min')))
                ;
            else
                $(this).val($(this).data("old"));
        });
    }

    return {
        orderMissingProductPharmacyModal: function (orderId) {
            _orderMissingProductPharmacyModal(orderId);
        },
    };
})();
