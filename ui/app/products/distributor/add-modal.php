<!-- Modal-->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" action="/web/distributor/product/add" class="modalForm" id="addModalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalTitle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control" name="id" id="addProductId">
                    <input type="hidden" name="fnCallback" class="modalValueCallback" id="addProductCallback" value="DistributorProductsDataTable.reloadDatatable" />
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <div class="image-input image-input-empty image-input-outline">
                                <div class="image-input-wrapper" id="addProductImageHolder"></div>
                                <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                       data-action="change" data-toggle="tooltip" title=""
                                       data-original-title="Change avatar">
                                    <i class="fa fa-pen icon-sm text-muted"></i>
                                    <input type="file" name="profile_avatar" accept=".png, .jpg, .jpeg" id="addProductImage"/>
                                    <input type="hidden" name="image" id="addProductImageInput"/>
                                </label>
                                <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                      data-action="cancel" data-toggle="tooltip" title="Cancel avatar">
                                    <i class="ki ki-bold-close icon-xs text-muted"></i>
                                </span>
                                <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                      data-action="remove" data-toggle="tooltip" title="Remove avatar">
                                    <i class="ki ki-bold-close icon-xs text-muted"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="addProductScientificName" class="form-control-label">Scientific Name</label>
                            <select class="select2 form-control" id="addProductScientificName" name="scientificNameId" data-select2-id="addProductScientificName" tabindex="-1" aria-hidden="true">
                                <option selected disabled>Scientific Name</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="addProductCountry" class="form-control-label">Country</label>
                            <select class="select2 form-control" id="addProductCountry" name="madeInCountryId" data-select2-id="addProductCountry" tabindex="-1" aria-hidden="true">
                                <option selected disabled>Made In Country</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="addProductNameAr" class="form-control-label">Product Name (Arabic)</label>
                            <input type="text" class="form-control" name="name_ar" id="addProductNameAr">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="addProductNameEn" class="form-control-label">Product Name (English)</label>
                            <input type="text" class="form-control" name="name_en" id="addProductNameEn">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="addProductNameFr" class="form-control-label">Product Name (French)</label>
                            <input type="text" class="form-control" name="name_fr" id="addProductNameFr">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="addUnitPrice" class="form-control-label">Unit Price</label>
                            <input type="number" class="form-control" name="unitPrice" id="addUnitPrice" min="0" pattern="^\d*(\.\d{0,2})?$" step="0.01" onchange="this.value = this.value > 0? parseFloat(this.value).toFixed(2) : 0;">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="addStock" class="form-control-label">Stock</label>
                            <input type="number" class="form-control" name="stock" id="addStock" min="0" step="1" onchange="this.value = this.value > 0? this.value : 0;" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="addMaximumOrderQuantity" class="form-control-label">Maximum Order Quantity</label>
                            <input type="number" class="form-control" name="maximumOrderQuantity" id="addMaximumOrderQuantity" min="0" step="1" onchange="this.value = this.value > 0? this.value : 0;" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="modal-add-button">
                        <button type="button" class="btn btn-primary font-weight-bold modalAction" id="addModalAction">Add Product</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>