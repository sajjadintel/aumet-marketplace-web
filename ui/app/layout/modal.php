<!-- Modal-->
<div class="modal fade " id="popupModal" tabindex="-1" role="dialog" aria-labelledby="popupModal" aria-hidden="true">
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


<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModal" aria-hidden="true">
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
                    <div class="col-md-6">
                        <div class="row form-group">
                            <div class="col-md-6 form-label" id="modalBranchLabel"></div>
                            <div class="col-md-6 form-text" id="modalBranchText"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row form-group">
                            <div class="col-md-6 form-label" id="modalAddressLabel"></div>
                            <div class="col-md-6 form-text" id="modalAddressText"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 form-group">
                        <div>
                            <input name="modalOrderDetailLog" id="modalBootstrapOrderDetailLog" data-switch="true" type="checkbox" checked="checked" data-on-text="True" data-handle-width="150" data-off-text="False" data-on-color="primary" data-off-color="secondary" />
                        </div>
                    </div>
                    <div class="col-md-12">
                        <!--begin: Datatable-->
                        <table id="order_details_datatable" class="compact hover order-column row-border table datatable datatable-bordered datatable-head-custom">
                        </table>
                        <!--end: Datatable-->
                    </div>
                    <div class="col-md-12 mt-10 text-center">
                        <a id="modalPrint" target="_blank" href="web/distributor/order/print" class="btn btn-sm btn-primary btn-hover-primary" title="Print Order">
                            <i class="nav-icon la la-print p-0"></i>
                            Print Order
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="feedbackModal" tabindex="-1" role="dialog" aria-labelledby="feedbackModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="feedbackModalTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row form-group">
                            <div class="col-md-6 form-label" id="feedbackModalCustomerNameLabel"></div>
                            <div class="col-md-6 form-text" id="feedbackModalCustomerNameText"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row form-group">
                            <div class="col-md-6 form-label" id="feedbackModalStatusLabel"></div>
                            <div class="col-md-6 form-text" id="feedbackModalStatusText"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row form-group">
                            <div class="col-md-6 form-label" id="feedbackModalTotalLabel"></div>
                            <div class="col-md-6 form-text" id="feedbackModalTotalText"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row form-group">
                            <div class="col-md-6 form-label" id="feedbackModalDateLabel"></div>
                            <div class="col-md-6 form-text" id="feedbackModalDateText"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row form-group">
                            <div class="col-md-6 form-label" id="feedbackModalBranchLabel"></div>
                            <div class="col-md-6 form-text" id="feedbackModalBranchText"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row form-group">
                            <div class="col-md-6 form-label" id="feedbackModalAddressLabel"></div>
                            <div class="col-md-6 form-text" id="feedbackModalAddressText"></div>
                        </div>
                    </div>

                    <hr>

                    <div class="col-md-12">
                        <div class="row form-group">
                            <div class="col-md-12 form-label" id="feedbackModalRatingLabel">Rating:</div>
                            <div class="col-md-12"><span id="feedbackModalRating" data-stars="0"></span></div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row form-group">
                            <div class="col-md-12 form-label" id="feedbackModalCommentLabel">Comment:</div>
                            <div class="col-md-12"><input id="feedbackModalComment" class="form-control" type="text" /></div>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12 mt-10 text-center">
                        <a id="feedbackModalSave" class="btn btn-sm btn-primary btn-hover-primary" title="Print Order">
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

<!-- Add Bonus Modal -->
<div class="modal fade" id="addBonusModal" tabindex="-1" role="dialog" aria-labelledby="addBonusModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBonusModalTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 form-group">
                        <input type="hidden" class="form-control" name="productId" id="addBonusProductId">
                        <input type="hidden" class="form-control" name="entityId" id="addBonusEntityId">
                        <div id="addBonusListRepeater">
                            <div class="row">
                                <div data-repeater-list="bonusRepeater" class="col-lg-12">
                                    <div data-repeater-item="" class="form-group row align-items-end">
                                        <input type="hidden" id="addBonusId" name="bonusId" class="form-control">
                                        <div class="col-md-4">
                                            <label>Min Order:</label>
                                            <input type="email" id="addBonusMinOrder" name="minOrder" class="form-control" placeholder="Enter Minimum Order Amount" disabled>
                                            <div class="d-md-none mb-2"></div>
                                        </div>
                                        <div class="col-md-4">
                                            <label>Bonus:</label>
                                            <input type="email" id="addBonusQuantity" name="bonus" class="form-control" placeholder="Enter Bonus Amount" disabled>
                                            <div class="d-md-none mb-2"></div>
                                        </div>
                                        <div class="col-md-4">
                                            <a onclick="SearchDataTable.productAddBonus(this.name)" name="addButton" id="addBonusAction" class="btn btn-sm font-weight-bolder btn-light-primary">
                                                <i class="la la-plus"></i>Add</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="modal-close-button">
                    <button type="button" class="btn btn-primary font-weight-bold" id="addBonusDone" data-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>
</div>