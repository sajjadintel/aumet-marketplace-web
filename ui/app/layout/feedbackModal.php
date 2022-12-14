<!-- Modal-->
<div class="modal fade " id="popupModal2" tabindex="-1" role="dialog" aria-labelledby="popupModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form method="POST" action="/" class="modalForm" id="modalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="popupModalTitle">Modal Title</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" id="popupModalValueId" value="" />
                    <input type="hidden" name="fnCallback" class="modalValueCallback" id="modalValueCallback" value="" />
                    <p class="modal-text" id="popupModalText"></p>
                </div>
                <div class="modal-footer">
                    <div class="modal-add-button">
                        <button type="button" class="btn btn-primary font-weight-bold modalAction" id="modalAction">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog" aria-labelledby="feedbackModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row form-group">
                            <div class="col-md-6 form-label" id="modalCustomerNameLabel"></div>
                            <div class="col-md-6 form-text" id="modalCustomerNameText"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row form-group">
                            <div class="col-md-6 form-label" id="modalStatusLabel"></div>
                            <div class="col-md-6 form-text" id="modalStatusText"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row form-group">
                            <div class="col-md-6 form-label" id="modalTotalLabel"></div>
                            <div class="col-md-6 form-text" id="modalTotalText"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row form-group">
                            <div class="col-md-6 form-label" id="modalDateLabel"></div>
                            <div class="col-md-6 form-text" id="modalDateText"></div>
                        </div>
                    </div>
                    <div class="col-md-6 display-none">
                        <div class="row form-group">
                            <div class="col-md-6 form-label" id="modalBranchLabel"></div>
                            <div class="col-md-6 form-text" id="modalBranchText"></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row form-group">
                            <div class="col-md-3 form-label" id="modalAddressLabel"></div>
                            <div class="col-md-9 form-text" id="modalAddressText"></div>
                        </div>
                    </div>
                </div>
                <div class="row">

                    <div class="col-md-12 mt-10 text-center">
                        <a id="modelFeedback" target="_blank" href="web/distributor/order/print" class="btn btn-sm btn-primary btn-hover-primary" title="Save Feedback">
                            <i class="nav-icon la la-save p-0"></i>
                            Save Feedback
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="genericModal" tabindex="-1" role="dialog" aria-labelledby="genericModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content" id="genericModalContent">
        </div>
    </div>
</div>