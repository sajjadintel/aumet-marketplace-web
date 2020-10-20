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
                    <input type="hidden" name="fnCallback" class="modalValueCallback" id="editQuantityProductCallback" value="DistributorProductsDataTable.reloadDatatable" />
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label for="editQuantityStock" class="form-control-label">Stock</label>
                            <div>
                                <input class="form-control fit-width" type="number" name="stock" id="editQuantityStock">
                                <input name="stockStatus" id="editQuantityStockAvailability" data-switch="true" type="checkbox" checked="checked" data-on-text="<?php echo $vModule_product_stockStatus_ComingSoon ?>" data-handle-width="150" data-off-text="<?php echo $vModule_product_stockStatus_NotAvailable ?>" data-on-color="primary" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label for="editQuantityBonusType" class="form-control-label">Has Bonus</label>
                            <div>
                                <input name="stockStatus" id="editQuantityBonusType" data-switch="true" type="checkbox" checked="checked" data-on-text="<?php echo $vModule_product_hasBonus ?>" data-handle-width="150" data-off-text="<?php echo $vModule_product_noBonus ?>" data-on-color="primary" />
                            </div>
                        </div>
                        <div class="col-md-12 form-group">
                            <button class="btn btn-primary btn-add btn-add-datatables" id="editQuantityBonusDatatableAddRow">+</button>
                            <table id="editQuantityBonusDatatable" class="datatables-net compact hover order-column row-border table report-section"></table>
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