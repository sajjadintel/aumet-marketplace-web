<!-- Modal-->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" action="/web/distributor/product/edit" class="modalForm" id="editModalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalTitle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control" name="id" id="editProductId">
                    <input type="hidden" name="fnCallback" class="modalValueCallback" id="editProductCallback" value="DistributorProductsDataTable.reloadDatatable"/>
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <div class="image-input image-input-empty image-input-outline">
                                <div class="image-input-wrapper" id="editProductImageHolder"></div>
                                <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                       data-action="change" data-toggle="tooltip" title=""
                                       data-original-title="Change avatar">
                                    <i class="fa fa-pen icon-sm text-muted"></i>
                                    <input type="file" name="profile_avatar" accept=".png, .jpg, .jpeg" id="editProductImage"/>
                                    <input type="hidden" name="image" id="editProductImageInput"/>
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
                            <label for="editProductScientificName" class="form-control-label">Scientific Name</label>
                            <select class="select2 form-control" id="editProductScientificName" name="scientificNameId"
                                    data-select2-id="editProductScientificName" tabindex="-1" aria-hidden="true">
                                <option disabled>Scientific Name</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="editProductCountry" class="form-control-label">Made In Country</label>
                            <select class="select2 form-control" id="editProductCountry" name="madeInCountryId"
                                    data-select2-id="editProductCountry" tabindex="-1" aria-hidden="true">
                                <option disabled>Made In Country</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="editProductNameAr" class="form-control-label">Product Name (Arabic)</label>
                            <input type="text" class="form-control" name="name_ar" id="editProductNameAr">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="editProductNameEn" class="form-control-label">Product Name (English)</label>
                            <input type="text" class="form-control" name="name_en" id="editProductNameEn">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="editProductNameFr" class="form-control-label">Product Name (French)</label>
                            <input type="text" class="form-control" name="name_fr" id="editProductNameFr">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="editUnitPrice" class="form-control-label">Unit Price</label>
                            <input type="number" class="form-control" name="unitPrice" id="editUnitPrice" min="0" pattern="^\d*(\.\d{0,2})?$" step="0.01" onchange="this.value = this.value > 0? parseFloat(this.value).toFixed(2) : 0;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="modal-edit-button">
                        <button type="button" class="btn btn-primary font-weight-bold modalAction" id="editModalAction">
                            Save changes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>