<style>
	textarea {
		resize: none;
	}

    .wrap-modal-slider {
        padding: 0 30px;
        opacity: 0;
        transition: all 0.3s;
    }

    .wrap-modal-slider.open {
        opacity: 1;
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
                        <div class="col-md-2 form-group">
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
                        <div class="col-md-10 form-group">
                            <div class="dropzone dropzone-multi" id="addProductSubimagesDropzone" style="background-color: unset;">
                                <div class="dropzone-panel mb-lg-0 mb-2">
                                    <a class="dropzone-select btn btn-light-primary font-weight-bolder font-size-h6 pl-6 pr-8 py-4 my-3 mr-3">
                                        <span class="svg-icon menu-icon">
                                            <!--begin::Svg Icon | path:assets/media/svg/icons/Files/Upload.svg-->
                                            <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                    <rect x="0" y="0" width="24" height="24"></rect>
                                                    <path d="M2,13 C2,12.5 2.5,12 3,12 C3.5,12 4,12.5 4,13 C4,13.3333333 4,15 4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 C2,15 2,13.3333333 2,13 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                                    <rect fill="#000000" opacity="0.3" x="11" y="2" width="2" height="14" rx="1"></rect>
                                                    <path d="M12.0362375,3.37797611 L7.70710678,7.70710678 C7.31658249,8.09763107 6.68341751,8.09763107 6.29289322,7.70710678 C5.90236893,7.31658249 5.90236893,6.68341751 6.29289322,6.29289322 L11.2928932,1.29289322 C11.6689749,0.916811528 12.2736364,0.900910387 12.6689647,1.25670585 L17.6689647,5.75670585 C18.0794748,6.12616487 18.1127532,6.75845471 17.7432941,7.16896473 C17.3738351,7.57947475 16.7415453,7.61275317 16.3310353,7.24329415 L12.0362375,3.37797611 Z" fill="#000000" fill-rule="nonzero"></path>
                                                </g>
                                            </svg>
                                            <!--end::Svg Icon-->
                                        </span><?php echo $vModule_product_uploadSubimages; ?>
                                    </a>
                                    <span id="addSubimagesErrorLabel" class="text-danger" style="display: none;"></span>
                                </div>
                                <div class="wrap-modal-slider">
                                    <div class="dropzone-items" id="addDropzoneItems" style="display: flex;">
                                        <div class="col-md-2 image-input image-input-empty image-input-outline dropzone-item" style="display: none; background-color: unset;">
                                            <div class="dropzone-error" data-dz-errormessage=""></div>
                                            <div class="mb-2 image-input-wrapper" id="dropzoneImage" style="width: 100%; height: 100px; background-size: 100% 100%; background-image: url('/assets/img/default-product-image.png'); box-shadow: 0 0.25rem 0.75rem 0.25rem rgb(0 0 0 / 8%);">
                                                <div class="px-2 dropzone-progress" style="width: 100%; position: relative; top: 50%;">
                                                    <div class="progress">
                                                        <div class="progress-bar bg-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress=""></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow dropzone-delete" data-dz-remove="" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar" style="top: -3px; right: 0px; box-shadow: 0px 2px 4px 0px rgba(24, 28, 50, 0.3) !important;">
                                                <i class="flaticon2-cross icon-sm text-muted"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="font-size-h6 font-weight-bolder text-muted mb-3 productImageErr" style="color:#e53935 !important;"></div>
                        </div>
                    </div>
                    <div class="row">
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
                        <div class="col-md-6 form-group">
                            <label for="addProductNameAr" class="form-control-label"><?php echo $vModule_product_brandName; ?> AR</label>
                            <input type="text" class="form-control" name="name_ar" id="addProductNameAr" autocomplete="off">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="addProductNameEn" class="form-control-label"><?php echo $vModule_product_brandName; ?> EN</label>
                            <input type="text" class="form-control" name="name_en" id="addProductNameEn" autocomplete="off">
                        </div>
                        <!-- <div class="col-md-4 form-group">
                            <label for="addProductNameFr" class="form-control-label"><?php echo $vModule_product_brandName; ?> FR</label>
                            <input type="text" class="form-control" name="name_fr" id="addProductNameFr" autocomplete="off">
                        </div> -->
                        <div class="col-md-6 form-group">
                            <label for="addProductSubtitleAr" class="form-control-label"><?php echo $vModule_product_subtitle; ?> AR</label>
                            <input type="text" class="form-control" name="subtitle_ar" id="addProductSubtitleAr" autocomplete="off">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="addProductSubtitleEn" class="form-control-label"><?php echo $vModule_product_subtitle; ?> EN</label>
                            <input type="text" class="form-control" name="subtitle_en" id="addProductSubtitleEn" autocomplete="off">
                        </div>
                        <!-- <div class="col-md-4 form-group">
                            <label for="addProductSubtitleFr" class="form-control-label"><?php echo $vModule_product_subtitle; ?> FR</label>
                            <input type="text" class="form-control" name="subtitle_fr" id="addProductSubtitleFr" autocomplete="off">
                        </div> -->
                        <div class="col-md-6 form-group">
                            <label for="addProductDescriptionAr" class="form-control-label"><?php echo $vModule_product_description; ?> AR</label>
                            <textarea class="form-control" id="addProductDescriptionAr" name="description_ar" rows="4" autocomplete="off"></textarea>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="addProductDescriptionEn" class="form-control-label"><?php echo $vModule_product_description; ?> EN</label>
                            <textarea class="form-control" id="addProductDescriptionEn" name="description_en" rows="4" autocomplete="off"></textarea>
                        </div>
                        <!-- <div class="col-md-4 form-group">
                            <label for="addProductDescriptionFr" class="form-control-label"><?php echo $vModule_product_description; ?> FR</label>
                            <textarea class="form-control" id="addProductDescriptionFr" name="description_fr" rows="4" autocomplete="off"></textarea>
                        </div> -->
                        <div class="col-md-3 form-group">
                            <label for="addUnitPrice" class="form-control-label"><?php echo $vModule_product_unitPrice . " (" . $buyerCurrency . ")"; ?></label>
                            <input type="number" class="form-control" name="unitPrice" id="addUnitPrice" min="0" pattern="^\d*(\.\d{0,2})?$" step="0.01" onchange="this.value = this.value > 0? parseFloat(this.value).toFixed(2) : !this.value? this.value : 0;" onkeypress="return ![43, 45, 101].includes(event.charCode)" autocomplete="off">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="addVat" class="form-control-label"><?php echo $vModule_product_vat; ?></label>
                            <input type="text" class="form-control" name="vat" id="addVat" min="0" pattern="^\d*(\.\d{0,2})?$" step="0.01" onkeypress="return ![43, 45, 101].includes(event.charCode)" autocomplete="off">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="addStock" class="form-control-label"><?php echo $vModule_product_availableQuantity; ?></label>
                            <input type="number" class="form-control" name="stock" id="addStock" min="0" step="1" onchange="this.value = this.value > 0? this.value : !this.value? this.value : 0;" onkeypress="return event.charCode >= 48 && event.charCode <= 57" autocomplete="off">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="addMaximumOrderQuantity" class="form-control-label"><?php echo $vModule_product_maximumOrderQuantity; ?></label>
                            <input type="number" class="form-control" name="maximumOrderQuantity" id="addMaximumOrderQuantity" min="0" step="1" onchange="this.value = this.value > 0? this.value : !this.value? this.value : 0;" onkeypress="return event.charCode >= 48 && event.charCode <= 57" autocomplete="off">
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
                        <button type="button" class="btn btn-primary font-weight-bold" id="addModalAction" onclick="DistributorProductsDataTable.productAdd();"><?php echo $vModule_product_add; ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
