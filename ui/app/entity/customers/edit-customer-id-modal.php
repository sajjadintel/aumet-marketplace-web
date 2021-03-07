<!-- Modal-->
<div class="modal fade" id="editGroupModal" tabindex="-1" role="dialog" aria-labelledby="editGroupModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" action="/web/distributor/customer/edit/id" class="modalForm" id="editGroupModalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editGroupModalTitle"><?php echo $vModule_customer_editIdTitle; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control" name="id" id="editGroupCustomerId">
                    <input type="hidden" name="fnCallback" class="modalValueCallback" id="editGroupCallback" value="DistributorCustomersDataTable.reloadDatatable" />
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="editGroupRelationGroup" class="form-control-label"><?php echo $vModule_customer_relationGroup; ?></label>
                            <input type="text" name="customer_id" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="modal-edit-group-button">
                        <button type="button" class="btn btn-primary font-weight-bold modalAction" id="editGroupModalAction"><?php echo $vButton_save; ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>