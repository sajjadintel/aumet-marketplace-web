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
                        <button type="button" class="btn btn-secondary font-weight-bold modalActionCancel" id="modalActionCancel"><?php echo $vButton_cancel ?></button>
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
                    <div class="col-md-12 form-group">
                        <div id="smarttab">

                            <ul class="nav">
                                <li>
                                    <a class="nav-link" href="#tab-1">
                                        <?php echo $orderDetails ?>
                                    </a>
                                </li>
                                <li>
                                    <a class="nav-link" href="#tab-2">
                                        <?php echo $orderLogs ?>
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">
                                <div id="tab-1" class="tab-pane" role="tabpanel">
                                    <table id="order_details_datatable" class="compact hover order-column row-border table datatable datatable-bordered datatable-head-custom">
                                    </table>
                                </div>
                                <div id="tab-2" class="tab-pane" role="tabpanel">
                                    <table id="order_details_datatable_logs" class="compact hover order-column row-border table datatable datatable-bordered datatable-head-custom">
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mt-10 text-center">
                        <a id="modalPrint" target="_blank" href="web/distributor/order/print" class="btn btn-sm btn-primary btn-hover-primary" title="Download PDF">
                            <i class="nav-icon la la-print p-0"></i>
                            Download PDF
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
                    <div class="col-md-6 display-none">
                        <div class="row form-group">
                            <div class="col-md-6 form-label" id="feedbackModalBranchLabel"></div>
                            <div class="col-md-6 form-text" id="feedbackModalBranchText"></div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="row form-group">
                            <div class="col-md-3 form-label" id="feedbackModalAddressLabel"></div>
                            <div class="col-md-9 form-text" id="feedbackModalAddressText"></div>
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
                        <a id="feedbackModalSave" class="btn btn-sm btn-primary btn-hover-primary" title="Save Feedback">
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

<!-- Missing Products Modal-->
<div class="modal fade" id="missingProductModal" tabindex="-1" role="dialog" aria-labelledby="missingProductModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" action="/web/pharmacy/order/missingProducts" class="modalForm" id="missingProductModalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="missingProductModalTitle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control" name="orderId" id="missingProductOrderId">
                    <input type="hidden" name="fnCallback" class="modalValueCallback" id="editQuantityProductCallback" value="WebApp.reloadDatatable" />


                    <div class="row">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row form-group">
                                    <div class="col-md-6 form-label" id="missingProductModalCustomerNameLabel"></div>
                                    <div class="col-md-6 form-text" id="missingProductModalCustomerNameText"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row form-group">
                                    <div class="col-md-6 form-label" id="missingProductModalStatusLabel"></div>
                                    <div class="col-md-6 form-text" id="missingProductModalStatusText"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row form-group">
                                    <div class="col-md-6 form-label" id="missingProductModalTotalLabel"></div>
                                    <div class="col-md-6 form-text" id="missingProductModalTotalText"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row form-group">
                                    <div class="col-md-6 form-label" id="missingProductModalDateLabel"></div>
                                    <div class="col-md-6 form-text" id="missingProductModalDateText"></div>
                                </div>
                            </div>
                            <div class="col-md-6 display-none">
                                <div class="row form-group">
                                    <div class="col-md-6 form-label" id="missingProductModalBranchLabel"></div>
                                    <div class="col-md-6 form-text" id="missingProductModalBranchText"></div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row form-group">
                                    <div class="col-md-3 form-label" id="missingProductModalAddressLabel"></div>
                                    <div class="col-md-9 form-text" id="missingProductModalAddressText"></div>
                                </div>
                            </div>

                            <hr>


                        </div>

                        <div class="col-md-12 form-group">
                            <div id="missingProductListRepeater">
                                <div class="row">
                                    <div data-repeater-list="missingProductsRepeater" class="col-lg-12">
                                        <div data-repeater-item="" class="form-group row align-items-end">
                                            <!--                                            <input type="hidden" id="missingProductId" name="productId" class="form-control">-->
                                            <div class="col-md-4">
                                                <label><?php echo $vModal_product ?>:</label>
                                                <select class="select2 form-control" name="productId" tabindex="-1">
                                                </select>
                                                <div class="d-md-none mb-2"></div>
                                            </div>
                                            <div class="col-md-4">
                                                <label><?php echo $vModal_quantity ?>:</label>
                                                <input type="number" min="1" max="100000" id="missingProductQuantity" name="quantity" class="form-control missingProductQuantity" placeholder="<?php echo $vModal_enterMissingProduct ?>">
                                                <div class="d-md-none mb-2"></div>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="javascript:;" id="missingProductDelete" data-repeater-delete="" class="btn btn-sm font-weight-bolder btn-light-danger">
                                                    <i class="la la-trash-o"></i><?php echo $vModal_delete ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <a href="javascript:;" id="missingProductAdd" data-repeater-create="" class="btn btn-sm font-weight-bolder btn-light-primary">
                                            <i class="la la-plus"></i><?php echo $vModal_add ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="modal-editQuantity-button">
                        <button type="button" class="btn btn-primary font-weight-bold modalAction" id="missingProductModalAction"><?php echo $vModal_SaveChanges ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Quantity Modal-->
<div class="modal fade" id="editQuantityModal" tabindex="-1" role="dialog" aria-labelledby="editQuantityModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" action="/web/distributor/product/editQuantity" class="modalForm" id="editQuantityModalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editQuantityModalTitle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control" name="id" id="editQuantityProductId">
                    <input type="hidden" name="fnCallback" class="modalValueCallback" id="editQuantityProductCallback" value="WebApp.reloadDatatable" />

                    <div class="form-group row">
                        <label class="col-3 col-form-label">Stock Quantity:</label>
                        <div class="col-9">
                            <input class="form-control fit-width " type="number" name="stock" id="editQuantityStock" min="0" step="1" onchange="this.value = this.value > 0? this.value : 0;" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-12 form-group">
                            <label for="editQuantityStock " class="form-control-label col-md-4"></label>
                            <div class="col-md-8">

                            </div>
                        </div>
                        <div class="col-md-12 form-group">
                            <div>
                                <input name="stockStatus" id="editQuantityStockAvailability" data-switch="true" type="checkbox" checked="checked" data-on-text="<?php echo $vModule_product_stockStatus_ComingSoon ?>" data-handle-width="150" data-off-text="<?php echo $vModule_product_stockStatus_NotAvailable ?>" data-on-color="primary" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label for="editQuantityBonusType" class="form-control-label">Has Bonus</label>
                            <div>
                                <input name="bonusType" id="editQuantityBonusType" data-switch="true" type="checkbox" checked="checked" data-on-text="<?php echo $vModule_product_stockStatus_hasBonus ?>" data-handle-width="150" data-off-text="<?php echo $vModule_product_stockStatus_noBonus ?>" data-on-color="primary" />
                            </div>
                        </div>

                        <div class="col-md-12 form-group">
                            <div id="editQuantityBonusListRepeater">
                                <div class="row">
                                    <div data-repeater-list="bonusRepeater" class="col-lg-12">
                                        <div data-repeater-item="" class="form-group row align-items-end">
                                            <input type="hidden" id="editQuantityBonusId" name="bonusId" class="form-control">
                                            <div class="col-md-4">
                                                <label>Min Order:</label>
                                                <input type="email" id="editQuantityBonusMinOrder" name="minOrder" class="form-control" placeholder="Enter Minimum Order Amount" min="0" step="1" onchange="this.value = this.value > 0? this.value : 0;" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                                <div class="d-md-none mb-2"></div>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Bonus:</label>
                                                <input type="email" id="editQuantityBonusQuantity" name="bonus" class="form-control" placeholder="Enter Bonus Amount" min="0" step="1" onchange="this.value = this.value > 0? this.value : 0;" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                                <div class="d-md-none mb-2"></div>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="javascript:;" id="editQuantityBonusDelete" data-repeater-delete="" class="btn btn-sm font-weight-bolder btn-light-danger">
                                                    <i class="la la-trash-o"></i>Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <a href="javascript:;" id="editQuantityBonusAdd" data-repeater-create="" class="btn btn-sm font-weight-bolder btn-light-primary">
                                            <i class="la la-plus"></i>Add</a>
                                    </div>
                                </div>
                            </div>
                            <!--
                            <button class="btn btn-primary btn-add btn-add-datatables" id="editQuantityBonusDatatableAddRow">+</button>
                            <table id="editQuantityBonusDatatable" class="datatables-net compact hover order-column row-border table report-section"></table> -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="modal-editQuantity-button">
                        <button type="button" class="btn btn-primary font-weight-bold modalAction" id="editQuantityModalAction"><?php echo $vModal_SaveChanges ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modify Quantity Order Modal-->
<div class="modal fade" id="modifyQuantityOrderModal" tabindex="-1" role="dialog" aria-labelledby="modifyQuantityOrderModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" action="" class="modalForm" id="modifyQuantityOrderModalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="modifyQuantityOrderModalTitle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control" name="orderId" id="modifyQuantityOrderOrderId">
                    <input type="hidden" name="fnCallback" class="modalValueCallback" id="editQuantityCallback" value="WebApp.reloadDatatable" />


                    <div class="row">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row form-group">
                                    <div class="col-md-6 form-label" id="modifyQuantityOrderModalCustomerNameLabel"></div>
                                    <div class="col-md-6 form-text" id="modifyQuantityOrderModalCustomerNameText"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row form-group">
                                    <div class="col-md-6 form-label" id="modifyQuantityOrderModalStatusLabel"></div>
                                    <div class="col-md-6 form-text" id="modifyQuantityOrderModalStatusText"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row form-group">
                                    <div class="col-md-6 form-label" id="modifyQuantityOrderModalTotalLabel"></div>
                                    <div class="col-md-6 form-text" id="modifyQuantityOrderModalTotalText"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row form-group">
                                    <div class="col-md-6 form-label" id="modifyQuantityOrderModalDateLabel"></div>
                                    <div class="col-md-6 form-text" id="modifyQuantityOrderModalDateText"></div>
                                </div>
                            </div>
                            <div class="col-md-6 display-none">
                                <div class="row form-group">
                                    <div class="col-md-6 form-label" id="modifyQuantityOrderModalBranchLabel"></div>
                                    <div class="col-md-6 form-text" id="modifyQuantityOrderModalBranchText"></div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="row form-group">
                                    <div class="col-md-3 form-label" id="modifyQuantityOrderModalAddressLabel"></div>
                                    <div class="col-md-9 form-text" id="modifyQuantityOrderModalAddressText"></div>
                                </div>
                            </div>

                            <hr>


                        </div>

                        <div class="col-md-12 form-group">
                            <div id="modifyQuantityOrderListRepeater">
                                <div class="row">
                                    <div data-repeater-list="modifyQuantityOrderRepeater" class="col-lg-12">
                                        <div data-repeater-item="" class="form-group row align-items-end">
                                            <input type="hidden" id="modifyQuantityOrderId" name="productCode" class="form-control">
                                            <div class="col-md-4">
                                                <label>product Name:</label>
                                                <input type="text" id="modifyQuantityOrderProductName" name="productName" class="form-control" disabled>
                                                <div class="d-md-none mb-2"></div>
                                            </div>
                                            <div class="col-md-4">
                                                <label id="modifyQuantityOrderQuantityTitle">Quantity:</label>
                                                <input type="text" id="modifyQuantityOrderQuantity" name="shippedQuantity" class="form-control" placeholder="Enter Quantity" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                                <div class="d-md-none mb-2"></div>
                                            </div>
                                            <input type="hidden" id="modifyQuantityOrderRequestedQuantity" name="requestedQuantity">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="modal-editQuantity-button">
                        <button type="button" class="btn btn-primary font-weight-bold modalAction" id="modifyQuantityOrderModalAction"><?php echo $vModal_SaveChanges ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="welcomeModal" data-backdrop="static" data-role-name="<?php echo strtolower(trim(explode('-', $objUser->roleName)[0])) ?>" data-login-counter="<?php echo $objUser->loginCounter ?>" tabindex="-1" role="dialog" aria-labelledby="welcomeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="welcomeModalLabel"><?php echo $vModal_welcome_title; ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="d-flex align-items-center justify-content-between p-4 flex-lg-wrap flex-xl-nowrap">
                    <div class="d-flex flex-column mr-5">
                        <a href="#" class="h4 text-dark text-hover-primary mb-5">
                            <?php echo $vModal_welcome_message; ?>
                        </a>

                        <div class="timeline timeline-1">
                            <div class="timeline-sep bg-primary-opacity-20"></div>
                            <div class="timeline-item">
                                <div class="timeline-label"><?php echo $vModal_welcome_step1_title; ?></div>
                                <div class="timeline-badge">
                                    <i class="fa fa-user text-primary"></i>
                                </div>
                                <div class="timeline-content text-muted font-weight-normal">
                                    <?php echo $vModal_welcome_step1_message; ?>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-label"><?php echo $vModal_welcome_step2_title; ?></div>
                                <div class="timeline-badge">
                                    <i class="fa fa-credit-card text-primary"></i>
                                </div>
                                <div class="timeline-content text-muted font-weight-normal">
                                    <?php echo $vModal_welcome_step2_message; ?>
                                </div>
                            </div>
                            <div class="timeline-item">
                                <div class="timeline-label"><?php echo $vModal_welcome_step3_title; ?></div>
                                <div class="timeline-badge">
                                    <i class="fa fa-map-marker-alt text-primary"></i>
                                </div>
                                <div class="timeline-content text-muted font-weight-normal">
                                    <?php echo $vModal_welcome_step3_message; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ml-6 ml-lg-0 ml-xxl-6 flex-shrink-0">
                        <a href="/web/profile" class="btn btn-success font-weight-bolder text-uppercase py-4 px-6">
                            <?php echo $vModal_welcome_callToAction; ?>
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light-primary font-weight-bold" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>