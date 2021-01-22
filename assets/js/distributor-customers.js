'use strict';
// Class definition
var DistributorCustomersDataTable = (function () {

    var _customerEditGroupModal = function (fromOrders, firstId, secondId) {
        if(fromOrders) {
            var buyerId = firstId;
            var sellerId = secondId;
            WebApp.get('/web/distributor/customer/relation/' + buyerId + '/' + sellerId, _customerEditGroupModalOpen);
        } else {
            var customerId = firstId;
            WebApp.get('/web/distributor/customer/' + customerId, _customerEditGroupModalOpen);
        }
    };

    var _customerEditGroupModalOpen = function (webResponse) {
        $('#editModalForm').attr('action', '/web/distributor/customer/edit/group');
        $('#editGroupCustomerId').val(webResponse.data.customer.id);
        
        $('#editGroupCustomerGroup').empty();
        $('#editGroupCustomerGroup').select2({
            placeholder: WebAppLocals.getMessage('customerGroup'),
            tags: true,
            createSearchChoice: function(term, data) {
                if ($(data).filter(function() {
                    return this.text.localeCompare(term) === 0;
                }).length === 0) {
                    return {
                        id: term,
                        text: term
                    }
                };
            },
            allowClear: true
        });
        webResponse.data.arrCustomerGroup.forEach((customerGroup) => {
            $('#editGroupCustomerGroup').append(new Option(customerGroup.name, customerGroup.id)).trigger('change');
        })
        if(webResponse.data.customer.customerGroupId) {
            $('#editGroupCustomerGroup').val(webResponse.data.customer.customerGroupId);
        }

		$('#editGroupModal').appendTo('body').modal('show');
    };

    return {
        // public functions
        reloadDatatable: function () {
            WebApp.reloadDatatable();
        },
        customerEditGroupModal: function (fromOrders, firstId, secondId) {
            _customerEditGroupModal(fromOrders, firstId, secondId);
        },
    };
})();
