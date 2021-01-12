<!-- Modal-->
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