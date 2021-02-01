'use strict';

// Class Definition
var WebFeedbackModals = (function () {
    var rating = 0;

    var _orderFeedbackModal = function (orderId) {
        WebApp.get('/web/pharmacy/feedback/' + orderId, _orderViewModalOpen);
    };

    var _orderViewModalOpen = function (webResponse) {
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
        $('#feedbackModalBranchLabel').html(WebAppLocals.getMessage('branch'));
        $('#feedbackModalCustomerNameLabel').html(WebAppLocals.getMessage('entitySeller'));
        $('#feedbackModalCustomerNameText').html(webResponse.data.order.entitySeller + ' (' + webResponse.data.order.userSeller + ')');
        $('#feedbackModalBranchText').html(webResponse.data.order.branchSeller);
        $('#feedbackModalStatusLabel').html(WebAppLocals.getMessage('orderStatus'));
        $('#feedbackModalStatusText').html(status);
        $('#feedbackModalTotalLabel').html(WebAppLocals.getMessage('orderTotal'));
        $('#feedbackModalTotalText').html(webResponse.data.order.currency + WebApp.formatMoney(webResponse.data.order.total);
        $('#feedbackModalDateLabel').html(WebAppLocals.getMessage('insertDate'));
        $('#feedbackModalDateText').html(webResponse.data.order.insertDateTime);
        $('#feedbackModalAddressLabel').html(WebAppLocals.getMessage('address'));
        $('#feedbackModalAddressText').html(webResponse.data.order.addressBuyer);
        $('#feedbackModalRatingLabel').html(WebAppLocals.getMessage('orderRating'));
        $('#feedbackModalCommentLabel').html(WebAppLocals.getMessage('orderComment'));
        $('#feedbackModalComment').val('');

        $('#feedbackModalTitle').html(WebAppLocals.getMessage('orderFeedback'));


        $('#modalPrint').attr('href', '/web/pharmacy/order/print/' + webResponse.data.order.id);

        // This will remove older created rating bars
        $("#feedbackModalRating").empty();

        $.ratePicker("#feedbackModalRating", {
            rate: function (mRating) {
                rating = mRating;
            }
        });


        // $('#feedbackModalSave').click(function () {
        $('#feedbackModalSave').off('click').click(function () {
            WebApp.post('/web/pharmacy/feedback', {
                    id: webResponse.data.order.id,
                    rating: rating,
                    comment: $('#feedbackModalComment').val()
                }
                , _sendFeedbackSuccessCallback);
        });

        $('#feedbackModal').modal('show');
    };

    var _sendFeedbackSuccessCallback = function (webResponse) {
        $('#feedbackModal').modal('hide');
        WebApp.alertSuccess(webResponse.title);
        WebApp.reloadDatatable();
    };

    return {
        orderFeedbackModal: function (orderId) {
            _orderFeedbackModal(orderId);
        },
    };
})();
