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
        
        $('#editGroupRelationGroup').empty();
        $('#editGroupRelationGroup').select2({
            placeholder: WebAppLocals.getMessage('relationGroup'),
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
            maximumInputLength: 150,
            allowClear: true
        });
        webResponse.data.arrRelationGroup.forEach((relationGroup) => {
            $('#editGroupRelationGroup').append(new Option(relationGroup.name, relationGroup.id)).trigger('change');
        })
        if(webResponse.data.customer.relationGroupId) {
            $('#editGroupRelationGroup').val(webResponse.data.customer.relationGroupId).trigger('change');
        } else {
            $('#editGroupRelationGroup').val('').trigger('change');
        }

		$('#editGroupModal').appendTo('body').modal('show');
    };

    var _customerEditIdentifierModal = (function (customerId, customerIdentifier) {
        $('#customerIdentifier').val(customerIdentifier == 'null' ? '' : customerIdentifier);
        $('#customerId').val(customerId);
        $('#editCustomerIdentifierModal').appendTo('body').modal('show');
    });

    return {
        // public functions
        reloadDatatable: function () {
            WebApp.reloadDatatable();
        },
        customerEditGroupModal: function (fromOrders, firstId, secondId) {
            _customerEditGroupModal(fromOrders, firstId, secondId);
        },
        customerEditIdentifierModal: function (customerId, customerIdentifier) {
            _customerEditIdentifierModal(customerId, customerIdentifier);
        }
    };
})();
