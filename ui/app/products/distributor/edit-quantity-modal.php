<!-- Modal-->
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
                            <input class="form-control fit-width " type="number" name="stock" id="editQuantityStock">
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
                                                <input type="email" id="editQuantityBonusMinOrder" name="minOrder" class="form-control" placeholder="Enter Minimum Order Amount">
                                                <div class="d-md-none mb-2"></div>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Bonus:</label>
                                                <input type="email" id="editQuantityBonusQuantity" name="bonus" class="form-control" placeholder="Enter Bonus Amount">
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
                        <button type="button" class="btn btn-primary font-weight-bold modalAction" id="editQuantityModalAction">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>