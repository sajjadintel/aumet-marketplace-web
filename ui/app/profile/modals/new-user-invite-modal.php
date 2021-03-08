<!-- Modal-->
<div class="modal fade" id="addUserInviteModal" tabindex="-1" role="dialog" aria-labelledby="addUserInviteModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" action="/web/distributor/invite/create" class="modalForm" id="addUserInviteModalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserInviteModalTitle"><?php echo $vModule_profile_inviteUserTitle; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control" name="id" id="addUserInviteCustomerId">
                    <input type="hidden" name="fnCallback" class="modalValueCallback" id="addUserInviteCallback" value="DistributorUserInvitesDataTable.reloadDatatable" />
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="addUserInvite" class="form-control-label"><?php echo $vModule_profile_email; ?></label>
                            <input type="email" class="form-control" id="addUserInvite" name="userInviteEmail">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="modal-edit-group-button">
                        <button type="button" class="btn btn-primary font-weight-bold modalAction" id="addUserInviteModalAction"><?php echo $vButton_save; ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>