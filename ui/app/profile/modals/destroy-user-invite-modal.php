<!-- Modal-->
<div class="modal fade" id="destroyUserInviteModal" tabindex="-1" role="dialog" aria-labelledby="destroyUserInviteModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
            <form method="POST" action="/web/distributor/invite/{0}/destroy" class="modalForm" id="destroyUserInviteModalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="destroyUserInviteModalTitle">Destroy</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="fnCallback" class="modalValueCallback" id="destroyUserInviteCallback" value="DistributorUserInvitesDataTable.resetFormActionAndReloadDatatable" />
                    <div class="row">
                        <div class="col-md-6 form-group">
                            Are you sure you want to delete this invite?
                            <pre id="inviteEmail"></pre>
                            <input type="hidden" class="form-control" id="destroyUserInviteEmail" name="email">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="modal-edit-group-button">
                        <button type="button" class="btn btn-danger font-weight-bold modalAction" id="destroyUserInviteModalAction"><?php echo $vButton_delete; ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>