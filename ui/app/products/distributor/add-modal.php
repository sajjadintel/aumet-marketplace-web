<style>
	textarea {
		resize: none;
	}
</style>
<!-- Modal-->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="addModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" action="/web/distributor/product/add" class="modalForm" id="addModalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalTitle"><?php echo $vModule_product_add; ?></h5>
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
                                    <input type="file" name="profile_avatar" accept=".png, .jpg, .jpeg" id="addProductImage" autocomplete="off"/>
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
                            <label for="addProductScientificName" class="form-control-label"><?php echo $vModule_product_scientificName; ?></label>
                            <select class="select2 form-control" id="addProductScientificName" name="scientificNameId" data-select2-id="addProductScientificName" tabindex="-1" aria-hidden="true" autocomplete="off">
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="addProductCountry" class="form-control-label"><?php echo $vModule_product_madeIn; ?></label>
                            <select class="select2 form-control" id="addProductCountry" name="madeInCountryId" data-select2-id="addProductCountry" tabindex="-1" aria-hidden="true" autocomplete="off">
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="addProductNameAr" class="form-control-label"><?php echo $vModule_product_brandName; ?> AR</label>
                            <input type="text" class="form-control" name="name_ar" id="addProductNameAr" autocomplete="off">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="addProductNameEn" class="form-control-label"><?php echo $vModule_product_brandName; ?> EN</label>
                            <input type="text" class="form-control" name="name_en" id="addProductNameEn" autocomplete="off">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="addProductNameFr" class="form-control-label"><?php echo $vModule_product_brandName; ?> FR</label>
                            <input type="text" class="form-control" name="name_fr" id="addProductNameFr" autocomplete="off">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="addProductSubtitleAr" class="form-control-label"><?php echo $vModule_product_subtitle; ?> AR</label>
                            <input type="text" class="form-control" name="subtitle_ar" id="addProductSubtitleAr" autocomplete="off">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="addProductSubtitleEn" class="form-control-label"><?php echo $vModule_product_subtitle; ?> EN</label>
                            <input type="text" class="form-control" name="subtitle_en" id="addProductSubtitleEn" autocomplete="off">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="addProductSubtitleFr" class="form-control-label"><?php echo $vModule_product_subtitle; ?> FR</label>
                            <input type="text" class="form-control" name="subtitle_fr" id="addProductSubtitleFr" autocomplete="off">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="addProductDescriptionAr" class="form-control-label"><?php echo $vModule_product_description; ?> AR</label>    
                            <textarea class="form-control" id="addProductDescriptionAr" name="description_ar" rows="4" autocomplete="off"></textarea>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="addProductDescriptionEn" class="form-control-label"><?php echo $vModule_product_description; ?> EN</label>
                            <textarea class="form-control" id="addProductDescriptionEn" name="description_en" rows="4" autocomplete="off"></textarea>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="addProductDescriptionFr" class="form-control-label"><?php echo $vModule_product_description; ?> FR</label>
                            <textarea class="form-control" id="addProductDescriptionFr" name="description_fr" rows="4" autocomplete="off"></textarea>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="addUnitPrice" class="form-control-label"><?php echo $vModule_product_unitPrice . " (" . $buyerCurrency . ")"; ?></label>
                            <input type="number" class="form-control" name="unitPrice" id="addUnitPrice" min="0" pattern="^\d*(\.\d{0,2})?$" step="0.01" onchange="this.value = this.value > 0? parseFloat(this.value).toFixed(2) : 0;" autocomplete="off">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="addStock" class="form-control-label"><?php echo $vModule_product_availableQuantity; ?></label>
                            <input type="number" class="form-control" name="stock" id="addStock" min="0" step="1" onchange="this.value = this.value > 0? this.value : 0;" onkeypress="return event.charCode >= 48 && event.charCode <= 57" autocomplete="off">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="addMaximumOrderQuantity" class="form-control-label"><?php echo $vModule_product_maximumOrderQuantity; ?></label>
                            <input type="number" class="form-control" name="maximumOrderQuantity" id="addMaximumOrderQuantity" min="0" step="1" onchange="this.value = this.value > 0? this.value : 0;" onkeypress="return event.charCode >= 48 && event.charCode <= 57" autocomplete="off">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="addProductManufacturerName" class="form-control-label"><?php echo $vModule_product_manufacturerName; ?></label>
                            <input type="text" class="form-control" name="manufacturerName" id="addProductManufacturerName" autocomplete="off">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="addProductBatchNumber" class="form-control-label"><?php echo $vModule_product_batchNumber; ?></label>
                            <input type="text" class="form-control" name="batchNumber" id="addProductBatchNumber" autocomplete="off">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="addProductItemCode" class="form-control-label"><?php echo $vModule_product_itemCode; ?></label>
                            <input type="text" class="form-control" name="itemCode" id="addProductItemCode" autocomplete="off">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="addProductCategory" class="form-control-label"><?php echo $vModule_product_category; ?></label>
                            <select class="select2 form-control" id="addProductCategory" name="categoryId" data-select2-id="addProductCategory" tabindex="-1" aria-hidden="true" autocomplete="off">
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="addProductSubcategory" class="form-control-label"><?php echo $vModule_product_subcategory; ?></label>
                            <select disabled class="select2 form-control" id="addProductSubcategory" name="subcategoryId" data-select2-id="addProductSubcategory" tabindex="-1" aria-hidden="true" autocomplete="off">
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="addActiveIngredients" class="form-control-label"><?php echo $vModule_product_activeIngredients; ?></label>
                            <select class="select2 form-control" id="addActiveIngredients" name="activeIngredients" data-select2-id="addActiveIngredients" tabindex="-1" aria-hidden="true" multiple autocomplete="off">
                            </select>
                            <input type="hidden" name="activeIngredientsId" id="addActiveIngredientsVal" autocomplete="off"/>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="addProductExpiryDate" class="form-control-label"><?php echo $vModule_product_expiryDate; ?></label>
                            <input type="text" class="form-control" readonly name="expiryDate" id="addProductExpiryDate" autocomplete="off"/>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="addProductStrength" class="form-control-label"><?php echo $vModule_product_strength; ?></label>
                            <input type="text" class="form-control" name="strength" id="addProductStrength" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="modal-add-button">
                        <button type="button" class="btn btn-primary font-weight-bold modalAction" id="addModalAction"><?php echo $vModule_product_add; ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>