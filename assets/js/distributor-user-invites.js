var DistributorUserInvitesDataTable = (function () {

    var _createUserInviteModal = function () {
		$('#addUserInviteModal').appendTo('body').modal('show');
    };

    return {
        // public functions
        reloadDatatable: function () {
            WebApp.reloadDatatable();
        },
        createUserInviteModal: function () {
            _createUserInviteModal();
        },
    };
})();
