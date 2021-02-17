'use strict';
// Class definition
var DistributorCustomerGroup = (function () {

    var _customerGroupEditModal = function (customerGroupId) {
        WebApp.get('/web/distributor/customer/group/' + customerGroupId, _customerGroupEditModalOpen);
    };

    var _customerGroupEditModalOpen = function (webResponse) {
        var customerGroupId = webResponse.data.customerGroup.id;
        $('#editModalForm').attr('action', '/web/distributor/customer/group/edit');
        $('#editCustomerGroupId').val(customerGroupId);
        $("#editCustomerGroupSearch").val('');

        $("#editCustomerGroupNewId").empty();
        
        webResponse.data.arrCustomerGroup.forEach((customerGroup) => {
            var selected = customerGroupId == customerGroup.id;
            $("#editCustomerGroupNewId").append(new Option(customerGroup.name, customerGroup.id, false, selected));
        });

        _changeGroupMembers(webResponse.data.mapCustomerGroupIdArrEntity[customerGroupId]);
        _changeNonGroupMembers(webResponse.data.arrEntityFree);
        
		$('#editCustomerGroupModal').appendTo('body').modal('show');
    };

    var _changeGroupMembers = function (members) {
        var groupMembersContainer = document.getElementById("groupMembersContainer");
        $(groupMembersContainer).empty();
        members.forEach((member) => {
            var memberContainer = _generateMember(member, true);
            groupMembersContainer.append(memberContainer);
        })
    }

    var _changeNonGroupMembers = function (members) {
        var nonGroupMembersContainer = document.getElementById("nonGroupMembersContainer");
        $(nonGroupMembersContainer).children().not(':first-child').remove();
        members.forEach((member) => {
            var memberContainer = _generateMember(member, false);
            nonGroupMembersContainer.append(memberContainer);
        })
        if(members.length > 0) {
            $("#nonGroupMembersLabelContainer").attr("style", "");
        } else {
            $("#nonGroupMembersLabelContainer").attr("style", "display: none !important");
        }
    }

    var _generateMember = function(member, isGroupMember) {
        var memberContainer = document.createElement("div");
        var className = "d-flex p-5 justify-content-between align-items-center border-bottom member";
        if(!isGroupMember) className += " nonGroup"
        memberContainer.className = className;
        memberContainer.id = "member-" + member.id; 

        var memberNameContainer = document.createElement("div");
        memberNameContainer.class = "col-10";
        
        var memberNameElement = document.createElement("h4");
        memberNameElement.className = "member-text";
        memberNameElement.textContent = member.name;
        memberNameContainer.append(memberNameElement);
        memberContainer.append(memberNameContainer);

        var memberButtonContainer = document.createElement("div");
        memberButtonContainer.className = "col-2 text-right";
        
        var memberButtonElement = document.createElement("a");
        memberButtonElement.className = "btn btn-default btn-hover-primary mr-2 mb-2 border customer-group-modal-button"
        var onclickAttr, innerHTML;
        if(isGroupMember) {
            onclickAttr = "DistributorCustomerGroup.removeGroupMemberModal('" + member.id + "', '" + member.name + "')";
            innerHTML = "<i class='la la-close p-0'></i>";
        } else {
            onclickAttr = "DistributorCustomerGroup.addGroupMemberModal('" + member.id + "', '" + member.name + "')";
            innerHTML = "<div class='d-flex align-items-center'><i class='la la-plus p-0 mr-1'></i>" + WebAppLocals.getMessage("addToGroup") + "</div>";
        }
        memberButtonElement.setAttribute("onclick", onclickAttr)
        memberButtonElement.innerHTML = innerHTML;
        memberButtonContainer.append(memberButtonElement);
        memberContainer.append(memberButtonContainer);

        return memberContainer;
    }

    var _removeGroupMemberModal = function (memberId, memberName) {
        var msg = WebAppLocals.getMessage("removeMemberConfirmationFirstPart") + " <b>" + memberName + "</b> " + WebAppLocals.getMessage("removeMemberConfirmationSecondPart");
		Swal.fire({
			html: msg,
			buttonsStyling: false,
			confirmButtonText: WebAppLocals.getMessage('remove'),
            showCloseButton: true,
            showCancelButton: true,
            cancelButtonText: WebAppLocals.getMessage('cancel'),
			customClass: {
				confirmButton: 'btn font-weight-bold btn-light-primary mx-10 px-10',
				cancelButton: 'btn font-weight-bold btn-secondary mx-10 px-10',
			},
		}).then(function (result) {
			if (result.isConfirmed) {
                _removeGroupMember(memberId, memberName);
            }
		});
    }

    var _removeGroupMember = function (memberId, memberName) {
        $("#member-" + memberId).remove();

        var nonGroupMembersContainer = document.getElementById("nonGroupMembersContainer");
        var member = {id: memberId, name: memberName};
        var memberContainer = _generateMember(member, false);
        nonGroupMembersContainer.append(memberContainer);
        $("#nonGroupMembersLabelContainer").attr("style", "");
    }

    var _addGroupMemberModal = function (memberId, memberName) {
		var msg = WebAppLocals.getMessage("assignMemberConfirmationFirstPart") + " <b>" + memberName + "</b> " + WebAppLocals.getMessage("assignMemberConfirmationSecondPart");
        Swal.fire({
			html: msg,
			buttonsStyling: false,
			confirmButtonText: WebAppLocals.getMessage('assign'),
            showCloseButton: true,
            showCancelButton: true,
            cancelButtonText: WebAppLocals.getMessage('cancel'),
			customClass: {
				confirmButton: 'btn font-weight-bold btn-light-primary mx-10 px-10',
				cancelButton: 'btn font-weight-bold btn-secondary mx-10 px-10',
			},
		}).then(function (result) {
			if (result.isConfirmed) {
                _addGroupMember(memberId, memberName);
            }
		});
    }

    var _addGroupMember = function (memberId, memberName) {
        $("#member-" + memberId).remove();
        if($("#nonGroupMembersContainer").children().length == 1) {
            $("#nonGroupMembersLabelContainer").attr("style", "display: none !important");
        }
        
        var groupMembersContainer = document.getElementById("groupMembersContainer");
        var member = {id: memberId, name: memberName};
        var memberContainer = _generateMember(member, true);
        groupMembersContainer.append(memberContainer);
    }
    
    var _filterMembers = function () {
        var searchText = $("#editCustomerGroupSearch").val().toLowerCase();
        var nonGroupMemberCount = 0;
        $(".member").each(function(index, elem) {
            var memberTextElement = $(elem).find(".member-text");
            var memberText = memberTextElement.text().toLowerCase();
            if(memberText.includes(searchText)) {
                $(elem).attr("style", "");
                if($(elem).hasClass("nonGroup")) {
                    nonGroupMemberCount += 1;
                }
            } else {
                $(elem).attr("style", "display: none !important");
            }
        })
        if(nonGroupMemberCount == 0) {
            $("#nonGroupMembersLabelContainer").attr("style", "display: none !important");
        } else {
            $("#nonGroupMembersLabelContainer").attr("style", "");
        }
    }

    var _saveCustomerGroup = function() {
        var id = $("#editCustomerGroupId").val();
        var arrEntityId = [];
        $("#groupMembersContainer").find(".member").each(function(index, elem) {
            var elementId = $(elem).attr("id");
            var allParts = elementId.split("-");
            arrEntityId.push(allParts[1]);
        });
        WebApp.post('/web/distributor/customer/group/edit', { id, arrEntityId }, _saveCustomerGroupSuccessCallback);
    }

    var _saveCustomerGroupSuccessCallback = function (webResponse) {
        WebApp.alertSuccess(webResponse.data);
        WebApp.reloadDatatable();
        
		$('#editCustomerGroupModal').modal('hide');
    }

    var _init = function () {
        $("#editCustomerGroupNewId").select2();
    }

    return {
        // public functions
        init: function () {
            _init();
        },
        reloadDatatable: function () {
            WebApp.reloadDatatable();
        },
        customerGroupEditModal: function (fromOrders, firstId, secondId) {
            _customerGroupEditModal(fromOrders, firstId, secondId);
        },
        removeGroupMemberModal: function(memberId, memberName) {
            _removeGroupMemberModal(memberId, memberName);
        },
        addGroupMemberModal: function(memberId, memberName) {
            _addGroupMemberModal(memberId, memberName);
        },
        filterMembers: function() {
            _filterMembers();
        },
        saveCustomerGroup: function() {
            _saveCustomerGroup();
        }
    };
})();
