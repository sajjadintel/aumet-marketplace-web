<style>
	textarea {
		resize: none;
	}
</style>
<!-- Modal-->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" action="/web/distributor/product/edit" class="modalForm" id="editModalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalTitle"><?php echo $vModule_product_edit; ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control" name="id" id="editProductId">
                    <input type="hidden" name="fnCallback" class="modalValueCallback" id="editProductCallback" value="DistributorProductsDataTable.reloadDatatable" />
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
                            <label for="editProductScientificName" class="form-control-label"><?php echo $vModule_product_scientificName; ?></label>
                            <select class="select2 form-control" id="editProductScientificName" name="scientificNameId" data-select2-id="editProductScientificName" tabindex="-1" aria-hidden="true">
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="editProductCountry" class="form-control-label"><?php echo $vModule_product_madeIn; ?></label>
                            <select class="select2 form-control" id="editProductCountry" name="madeInCountryId" data-select2-id="editProductCountry" tabindex="-1" aria-hidden="true">
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="editProductNameAr" class="form-control-label"><?php echo $vModule_product_brandName; ?> AR</label>
                            <input type="text" class="form-control" name="name_ar" id="editProductNameAr">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="editProductNameEn" class="form-control-label"><?php echo $vModule_product_brandName; ?> EN</label>
                            <input type="text" class="form-control" name="name_en" id="editProductNameEn">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="editProductNameFr" class="form-control-label"><?php echo $vModule_product_brandName; ?> FR</label>
                            <input type="text" class="form-control" name="name_fr" id="editProductNameFr">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="editProductSubtitleAr" class="form-control-label"><?php echo $vModule_product_subtitle; ?> AR</label>
                            <input type="text" class="form-control" name="subtitle_ar" id="editProductSubtitleAr">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="editProductSubtitleEn" class="form-control-label"><?php echo $vModule_product_subtitle; ?> EN</label>
                            <input type="text" class="form-control" name="subtitle_en" id="editProductSubtitleEn">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="editProductSubtitleFr" class="form-control-label"><?php echo $vModule_product_subtitle; ?> FR</label>
                            <input type="text" class="form-control" name="subtitle_fr" id="editProductSubtitleFr">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="editProductDescriptionAr" class="form-control-label"><?php echo $vModule_product_description; ?> AR</label>    
                            <textarea class="form-control" id="editProductDescriptionAr" name="description_ar" rows="4"></textarea>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="editProductDescriptionEn" class="form-control-label"><?php echo $vModule_product_description; ?> EN</label>
                            <textarea class="form-control" id="editProductDescriptionEn" name="description_en" rows="4"></textarea>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="editProductDescriptionFr" class="form-control-label"><?php echo $vModule_product_description; ?> FR</label>
                            <textarea class="form-control" id="editProductDescriptionFr" name="description_fr" rows="4"></textarea>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="editUnitPrice" class="form-control-label"><?php echo $vModule_product_unitPrice . " (" . $buyerCurrency . ")"; ?></label>
                            <input type="number" class="form-control" name="unitPrice" id="editUnitPrice" min="0" pattern="^\d*(\.\d{0,2})?$" step="0.01" onchange="this.value = this.value > 0? parseFloat(this.value).toFixed(2) : 0;">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="editVat" class="form-control-label"><?php echo $vModule_product_vat; ?></label>
                            <input type="number" class="form-control" name="vat" id="editVat" min="0" pattern="^\d*(\.\d{0,2})?$" step="0.01" onchange="this.value = this.value > 0? parseFloat(this.value).toFixed(2) : 0;">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="editMaximumOrderQuantity" class="form-control-label"><?php echo $vModule_product_maximumOrderQuantity; ?></label>
                            <input type="number" class="form-control" name="maximumOrderQuantity" id="editMaximumOrderQuantity" min="0" step="1" onchange="this.value = this.value > 0? this.value : 0;" onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="editProductManufacturerName" class="form-control-label"><?php echo $vModule_product_manufacturerName; ?></label>
                            <input type="text" class="form-control" name="manufacturerName" id="editProductManufacturerName">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="editProductBatchNumber" class="form-control-label"><?php echo $vModule_product_batchNumber; ?></label>
                            <input type="text" class="form-control" name="batchNumber" id="editProductBatchNumber">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="editProductItemCode" class="form-control-label"><?php echo $vModule_product_itemCode; ?></label>
                            <input type="text" class="form-control" name="itemCode" id="editProductItemCode">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="editProductCategory" class="form-control-label"><?php echo $vModule_product_category; ?></label>
                            <select class="select2 form-control" id="editProductCategory" name="categoryId" data-select2-id="editProductCategory" tabindex="-1" aria-hidden="true">
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="editProductSubcategory" class="form-control-label"><?php echo $vModule_product_subcategory; ?></label>
                            <select class="select2 form-control" id="editProductSubcategory" name="subcategoryId" data-select2-id="editProductSubcategory" tabindex="-1" aria-hidden="true">
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="editActiveIngredients" class="form-control-label"><?php echo $vModule_product_activeIngredients; ?></label>
                            <select class="select2 form-control" id="editActiveIngredients" name="activeIngredients" data-select2-id="editActiveIngredients" tabindex="-1" aria-hidden="true" multiple>
                            </select>
                            <input type="hidden" name="activeIngredientsId" id="editActiveIngredientsVal"/>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="editProductExpiryDate" class="form-control-label"><?php echo $vModule_product_expiryDate; ?></label>
                            <input type="text" class="form-control" readonly name="expiryDate" id="editProductExpiryDate"/>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="editProductStrength" class="form-control-label"><?php echo $vModule_product_strength; ?></label>
                            <input type="text" class="form-control" name="strength" id="editProductStrength">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="modal-edit-button">
                        <button type="button" class="btn btn-primary font-weight-bold modalAction" id="editModalAction"><?php echo $vModule_product_edit; ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>