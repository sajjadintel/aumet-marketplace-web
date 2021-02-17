<!-- Modal-->
<div class="modal fade" id="editCustomerGroupModal" tabindex="-1" role="dialog" aria-labelledby="editCustomerGroupModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" action="/web/distributor/customer/group/edit" class="modalForm" id="editCustomerGroupModalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCustomerGroupModalTitle"><?php echo $vModule_customerGroup_editModalTitle; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control" name="id" id="editCustomerGroupId">
                    <input type="hidden" name="fnCallback" class="modalValueCallback" id="editCustomerGroupCallback" value="DistributorCustomerGroup.reloadDatatable" />
                    <div class="d-flex p-5 customer-group-modal-bg">
                        <div class="col-8 d-flex">
                            <div class="input-group w-50">
                                <div class="input-group-prepend"><span class="input-group-text"><i class="fa fa-search text-primary"></i></span></div>
                                <input id="editCustomerGroupSearch" type="text" class="form-control customer-group-modal-search" placeholder="Search..." onkeyup="DistributorCustomerGroup.filterMembers()"/>
                            </div>
                            <div class="w-25">
                                <select class="select2 form-control" id="editCustomerGroupNewId" name="newCustomerGroupId" data-select2-id="editCustomerGroupNewId" tabindex="-1" aria-hidden="true" disabled>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div id="groupMembersContainer">
                    </div>
                    <div id="nonGroupMembersContainer">
                        <div id="nonGroupMembersLabelContainer" class="d-flex p-3 customer-group-modal-bg">
                            <h6 class="mb-0"><?php echo $vModule_customerGroup_editModalNonGrouped; ?></h6>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="modal-edit-group-button">
                        <button type="button" class="btn btn-primary font-weight-bold" id="editCustomerGroupModalAction" onclick="DistributorCustomerGroup.saveCustomerGroup()"><?php echo $vButton_save; ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>