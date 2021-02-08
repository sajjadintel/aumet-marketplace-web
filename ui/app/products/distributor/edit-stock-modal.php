<style>
    .switch.switch-success:not(.switch-outline) input:empty:not(:checked) ~ span:before {
        background-color: #EBEDF3;
    }

    #editStockBonusList > div:not(:nth-child(1)) > div > label {
        display: none;
    }

    #editStockBonusList > div:not(:nth-child(1)) {
        align-items: start !important;
    }

    #editStockSpecialBonusList > div:not(:nth-child(1)) > div > label {
        display: none;
    }

    #editStockSpecialBonusList > div:not(:nth-child(1)) {
        align-items: start !important;
    }
</style>
<!-- Modal-->
<div class="modal fade editStockModal" id="editStockModal" tabindex="-1" role="dialog" aria-labelledby="editStockModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" action="/web/distributor/product/editStock" class="modalForm" id="editStockModalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStockModalTitle"><?php echo $vModule_product_editStock; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control" name="id" id="editStockProductId">
                    <input type="hidden" name="fnCallback" class="modalValueCallback" id="editStockProductCallback"  value="DistributorProductsDataTable.reloadDatatable" />
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="editStockStock" class="form-control-label"><?php echo $vModule_product_availableQuantity; ?></label>
                            <input type="number" class="form-control" name="stock" id="editStockStock" min="0" step="1" onchange="this.value = this.value > 0? this.value : !this.value? this.value : 0;" onkeypress="return event.charCode >= 48 && event.charCode <= 57" autocomplete="off">

                        </div>
                    </div>
                    <div class="mb-10">
                        <div class="row form-group">
                            <span class="col-1 switch switch-success">
                                <label>
                                    <input id="editStockBonusCheckbox" type="checkbox" name="select"/>
                                    <span></span>
                                </label>
                            </span>
                            <label class="col-3 col-form-label"><?php echo $vModule_product_addDefaultBonus; ?></label>
                        </div>
                        <div id="editStockBonusRepeater">
                            <div class="row">
                                <div id="editStockBonusList" data-repeater-list="editStockBonusList" class="col-lg-12">
                                    <div data-repeater-item="" class="form-group row mb-0" style="align-items: center;">
                                        <input type="hidden" id="editStockBonusId" name="id" class="form-control">
                                        <div class="col-md-2 form-group">
                                            <label for="editStockBonusTypeId" class="form-control-label"><?php echo $vModule_product_bonusType; ?></label>
                                            <select id="editStockBonusTypeId" name="bonusType" class="form-control selectpicker bonusTypeSelect" data-live-search="true">
                                            </select>
                                            <div class="d-md-none mb-2"></div>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label for="editStockBonusQuantity" class="form-control-label"><?php echo $vModule_product_quantity ?></label>
                                            <input type="number" id="editStockBonusQuantity" name="minOrder" class="form-control editStockBonusQuantityInput" min="0" step="1" onchange="this.value = this.value > 0? this.value : !this.value? this.value : 0;" onkeypress="return event.charCode >= 48 && event.charCode <= 57" autocomplete="off">
                                            <div class="d-md-none mb-2"></div>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label for="editStockBonus" class="form-control-label"><?php echo $vModule_product_bonus ?></label>
                                            <input type="number" id="editStockBonus" name="bonus" class="form-control editStockBonusInput" min="0" step="1" onkeypress="return event.charCode >= 48 && event.charCode <= 57" autocomplete="off">
                                            <div class="d-md-none mb-2"></div>
                                        </div>
                                        <div class="col-md-2">
                                            <a href="javascript:;" id="editStockBonusDelete" data-repeater-delete="" class="btn btn-sm font-weight-bolder btn-light-danger">
                                                <i class="la la-trash-o"></i><?php echo $vButton_delete; ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4">
                                    <a href="javascript:;" id="editStockBonusAdd" data-repeater-create="" class="btn btn-sm font-weight-bolder btn-light-primary">
                                        <i class="la la-plus"></i><?php echo $vButton_add; ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-10">
                        <div class="row form-group">
                            <span class="col-1 switch switch-success">
                                <label>
                                    <input id="editStockSpecialBonusCheckbox" type="checkbox" name="select"/>
                                    <span></span>
                                </label>
                            </span>
                            <label class="col-3 col-form-label"><?php echo $vModule_product_addSpecialBonus; ?></label>
                        </div>
                        <div id="editStockSpecialBonusRepeater">
                            <div class="row">
                                <div id="editStockSpecialBonusList" data-repeater-list="editStockSpecialBonusList" class="col-lg-12">
                                    <div data-repeater-item="" class="form-group row mb-0" style="align-items: center;">
                                        <input type="hidden" id="editStockSpecialBonusId" name="id" class="form-control">
                                        <div class="col-md-2 form-group">
                                            <label for="editStockSpecialBonusTypeId" class="form-control-label"><?php echo $vModule_product_bonusType; ?></label>
                                            <select id="editStockSpecialBonusTypeId" name="bonusType" class="form-control selectpicker specialBonusTypeSelect" data-live-search="true">
                                            </select>
                                            <div class="d-md-none mb-2"></div>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label for="editStockBonusQuantity" class="form-control-label"><?php echo $vModule_product_quantity ?></label>
                                            <input type="number" id="editStockSpecialBonusQuantity" name="minOrder" class="form-control editStockSpecialBonusQuantityInput" min="0" step="1" onchange="this.value = this.value > 0? this.value : !this.value? this.value : 0;" onkeypress="return event.charCode >= 48 && event.charCode <= 57" autocomplete="off">
                                            <div class="d-md-none mb-2"></div>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label for="editStockBonus" class="form-control-label"><?php echo $vModule_product_bonus ?></label>
                                            <input type="number" id="editStockSpecialBonus" name="bonus" class="form-control editStockSpecialBonusInput" min="0" step="1" onkeypress="return event.charCode >= 48 && event.charCode <= 57" autocomplete="off">
                                            <div class="d-md-none mb-2"></div>
                                        </div>
                                        <div class="col-md-4 form-group">
                                            <label for="editStockSpecialRelationGroupId" class="form-control-label"><?php echo $vModule_product_relationGroup; ?></label>
                                            <select id="editStockSpecialRelationGroupId" name="relationGroup" class="form-control selectpicker specialRelationGroupSelect" data-live-search="true" multiple>
                                            </select>
                                            <div class="d-md-none mb-2"></div>
                                        </div>
                                        <div class="col-md-2">
                                            <a href="javascript:;" id="editStockSpecialBonusDelete" data-repeater-delete="" class="btn btn-sm font-weight-bolder btn-light-danger">
                                                <i class="la la-trash-o"></i><?php echo $vButton_delete; ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4">
                                    <a href="javascript:;" id="editStockSpecialBonusAdd" data-repeater-create="" class="btn btn-sm font-weight-bolder btn-light-primary">
                                        <i class="la la-plus"></i><?php echo $vButton_add; ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="modal-editStock-button">
                        <button type="button" class="btn btn-primary font-weight-bold" id="editStockModalAction" onclick="DistributorProductsDataTable.productEditStock();"><?php echo $vModule_product_editStock; ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>