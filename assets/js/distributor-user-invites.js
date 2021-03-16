var DistributorUserInvitesDataTable = (function () {

    var _createUserInviteModal = function () {
		$('#addUserInviteModal').appendTo('body').modal('show');
    };

    var _destroyUserInviteModal = function (id, email) {
        const form = $('#destroyUserInviteModalForm');
        form.attr('action', String.format(form.attr('action'), id));
        $('#inviteEmail').text(`Email ${email}`)
        $('#destroyUserInviteModal').appendTo('body').modal('show');
    }

    function _resetFormAction() {
        const form = $('#destroyUserInviteModalForm');
        form.attr('action', '/web/distributor/invite/{0}/destroy');
    }
    return {
        // public functions
        reloadDatatable: function () {
            WebApp.reloadDatatable();
        },
        resetFormActionAndReloadDatatable: function () {
              _resetFormAction();
              WebApp.reloadDatatable();
        },
        createUserInviteModal: function () {
            _createUserInviteModal();
        },
        destroyUserInviteModal: function (id, email) {
            _destroyUserInviteModal(id, email);
        }
    };
})();
