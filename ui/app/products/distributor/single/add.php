<div class="px-5">
    <div class="d-flex">
        <div class="single-product-tab bg-white" data-tab="general" onclick="DistributorSingleProduct.changeTab(this);">
            <?php echo $vModule_product_general; ?>
        </div>
        <div class="single-product-tab" data-tab="images" onclick="DistributorSingleProduct.changeTab(this);">
            <?php echo $vModule_product_images; ?>
        </div>
        <div class="single-product-tab" data-tab="prices" onclick="DistributorSingleProduct.changeTab(this);">
            <?php echo $vModule_product_prices; ?>
        </div>
        <div class="single-product-tab" data-tab="stockSettings" onclick="DistributorSingleProduct.changeTab(this);">
            <?php echo $vModule_product_stockSettings; ?>
        </div>
    </div>
    <div class="card card-custom gutter-b">
        <div class="card-body">
            <form method="POST" action="/web/distributor/product/add" id="addForm">
                <div id="general-body" class="tab-body">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="nameAr" class="form-control-label"><?php echo $vModule_product_brandName; ?> AR</label>
                            <input type="text" class="form-control" name="nameAr" id="nameAr" autocomplete="off">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="nameEn" class="form-control-label"><?php echo $vModule_product_brandName; ?> EN</label>
                            <input type="text" class="form-control" name="nameEn" id="nameEn" autocomplete="off">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="subtitleAr" class="form-control-label"><?php echo $vModule_product_subtitle; ?> AR</label>
                            <input type="text" class="form-control" name="subtitleAr" id="subtitleAr" autocomplete="off">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="subtitleEn" class="form-control-label"><?php echo $vModule_product_subtitle; ?> EN</label>
                            <input type="text" class="form-control" name="subtitleEn" id="subtitleEn" autocomplete="off">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="descriptionAr" class="form-control-label"><?php echo $vModule_product_description; ?> AR</label>
                            <textarea class="form-control single-product-textarea" name="descriptionAr" id="descriptionAr" rows="4" autocomplete="off"></textarea>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="descriptionEn" class="form-control-label"><?php echo $vModule_product_description; ?> EN</label>
                            <textarea class="form-control single-product-textarea" name="descriptionEn" id="descriptionEn" rows="4" autocomplete="off"></textarea>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="scientificName" class="form-control-label"><?php echo $vModule_product_scientificName; ?></label>
                            <select class="select2 form-control" name="scientificNameId" id="scientificName" data-select2-id="scientificName" tabindex="-1" aria-hidden="true" autocomplete="off">
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="country" class="form-control-label"><?php echo $vModule_product_madeIn; ?></label>
                            <select class="select2 form-control" name="countryId" id="country" data-select2-id="country" tabindex="-1" aria-hidden="true" autocomplete="off">
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="itemCode" class="form-control-label"><?php echo $vModule_product_itemCode; ?></label>
                            <input type="text" class="form-control" name="itemCode" id="itemCode" autocomplete="off">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="manufacturerName" class="form-control-label"><?php echo $vModule_product_manufacturerName; ?></label>
                            <input type="text" class="form-control" name="manufacturerName" id="manufacturerName" autocomplete="off">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="strength" class="form-control-label"><?php echo $vModule_product_strength; ?></label>
                            <input type="text" class="form-control" name="strength" id="strength" autocomplete="off">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="activeIngredients" class="form-control-label"><?php echo $vModule_product_activeIngredients; ?></label>
                            <select class="select2 form-control" name="activeIngredients" id="activeIngredients" data-select2-id="activeIngredients" tabindex="-1" aria-hidden="true" multiple autocomplete="off">
                            </select>
                            <input type="hidden" name="activeIngredientsId" id="activeIngredientsId" autocomplete="off"/>
                        </div>
                    </div>
                </div>
                <div id="images-body" class="tab-body d-none">
                    <h4 class="pb-5"><?php echo $vModule_product_mainImage; ?></h4>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <div class="image-input image-input-empty image-input-outline">
                                <div class="image-input-wrapper single-product-image" id="imageHolder"></div>
                                <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow"
                                        data-action="change" data-toggle="tooltip" title=""
                                        data-original-title="Change avatar">
                                    <i class="fa fa-pen icon-sm text-muted"></i>
                                    <input type="file" name="profile_avatar" accept=".png, .jpg, .jpeg" id="image" autocomplete="off"/>
                                    <input type="hidden" name="image" id="imageInput"/>
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
                            <p id="imageError" class="pt-2 text-danger"></p>
                        </div>
                        <div class="col-md-3 form-group">
                            <div class="form-group">
                                <label for="imageAlt" class="form-control-label"><?php echo $vModule_product_mainImageAlt; ?></label>
                                <input type="text" class="form-control" name="imageAlt" id="imageAlt" autocomplete="off" placeholder="<?php echo $vModule_product_imageAltPlaceholder; ?>">
                            </div>
                            <div class="pt-5">
                                <a role="button" class="btn btn-light px-10 font-weight-bold d-flex single-product-removePhotoBtn" onclick="DistributorSingleProduct.removeMainPhoto();">
                                    <i class='la la-trash icon-lg text-danger mr-2'></i><?php echo $vModule_product_removePhoto; ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="border"></div>
                    <h4 class="py-5"><?php echo $vModule_product_subImages; ?></h4>
                    <div class="row">
                        <div class="col-md-10 form-group">
                            <div class="dropzone dropzone-multi" id="subimagesDropzone" style="background-color: unset;">
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
                                    <span id="subimagesErrorLabel" class="text-danger" style="display: none;"></span>
                                </div>
                                <div class="wrap-modal-slider">
                                    <div class="dropzone-items gallery" id="dropzoneItems" style="display: flex;">
                                        <div class="col-md-2 image-input image-input-empty image-input-outline dropzone-item" style="display: none; background-color: unset;">
                                            <div class="dropzone-error" data-dz-errormessage=""></div>
                                            <a id="dropzoneImageContainer">
                                                <div class="mb-2 image-input-wrapper" id="dropzoneImage" style="width: 100%; background-size: 100% 100%; box-shadow: 0 0.25rem 0.75rem 0.25rem rgb(0 0 0 / 8%); background-image: url('/assets/img/default-product-image.png');">
                                                    <div class="px-2 dropzone-progress" style="width: 100%; position: relative; top: 50%;">
                                                        <div class="progress">
                                                            <div class="progress-bar bg-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress=""></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                            <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow dropzone-delete" data-dz-remove="" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar" style="top: -3px; right: 0px; box-shadow: 0px 2px 4px 0px rgba(24, 28, 50, 0.3) !important;">
                                                <i class="flaticon2-cross icon-sm text-muted"></i>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="subimages" id="subimages"/>
                        </div>
                    </div>
                </div>
                <div id="prices-body" class="tab-body d-none">
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="vat" class="form-control-label"><?php echo $vModule_product_vat; ?></label>
                            <input type="text" class="form-control" name="vat" id="vat" min="0" pattern="^\d*(\.\d{0,2})?$" step="0.01" onkeypress="return ![43, 45, 101].includes(event.charCode)" autocomplete="off">
                        </div>
                    </div>
                    <div class="mb-10">
                        <div class="row form-group">
                            <span class="col-1 single-product-unitPrices switch switch-success">
                                <label>
                                    <input id="unitPricesCheckbox" type="checkbox" name="select"/>
                                    <span></span>
                                </label>
                            </span>
                            <label class="col-3 col-form-label"><?php echo $vModule_product_addUnitPrices; ?></label>
                        </div>
                        <div id="unitPricesRepeater">
                            <div class="row">
                                <div id="unitPricesList" data-repeater-list="unitPricesList" class="col-lg-12 single-product-unitPricesList" data-arrPaymentMethod="<?php echo htmlspecialchars(json_encode($arrPaymentMethod), ENT_QUOTES, 'UTF-8'); ?>">
                                    <div data-repeater-item="" class="form-group row mb-0 align-items-center">
                                        <input type="hidden" id="unitPricesId" name="id" class="form-control">
                                        <div class="col-md-2 form-group">
                                            <label for="paymentMethodId" class="form-control-label"><?php echo $vModule_product_paymentMethod; ?></label>
                                            <select id="paymentMethodId" name="paymentMethodId" class="form-control selectpicker paymentMethodSelect" data-live-search="true">
                                            </select>
                                            <div class="d-md-none mb-2"></div>
                                        </div>
                                        <div class="col-md-2 form-group">
                                            <label for="unitPrice" class="form-control-label"><?php echo $vModule_product_unitPrice ?></label>
                                            <input type="number" class="form-control unitPriceInput" name="unitPrice" id="unitPrice" min="0" pattern="^\d*(\.\d{0,2})?$" step="0.01" onchange="this.value = this.value > 0? parseFloat(this.value).toFixed(2) : !this.value? this.value : 0;" onkeypress="return ![43, 45, 101].includes(event.charCode)" autocomplete="off">
                                            <div class="d-md-none mb-2"></div>
                                        </div>
                                        <div class="col-md-2">
                                            <a href="javascript:;" id="unitPricesDelete" data-repeater-delete="" class="btn btn-sm font-weight-bolder btn-light-danger">
                                                <i class="la la-trash-o"></i><?php echo $vButton_delete; ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-4">
                                    <a href="javascript:;" id="unitPricesAdd" data-repeater-create="" class="btn btn-sm font-weight-bolder btn-light-primary">
                                        <i class="la la-plus"></i><?php echo $vButton_add; ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="stockSettings-body" class="tab-body d-none">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="batchNumber" class="form-control-label"><?php echo $vModule_product_batchNumber; ?></label>
                            <input type="text" class="form-control" name="batchNumber" id="batchNumber">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="expiryDate" class="form-control-label"><?php echo $vModule_product_expiryDate; ?></label>
                            <input type="text" class="form-control" readonly name="expiryDate" id="expiryDate">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="minimumOrderQuantity" class="form-control-label"><?php echo $vModule_product_minimumOrderQuantity; ?></label>
                            <input type="number" class="form-control" name="minimumOrderQuantity" id="minimumOrderQuantity" min="0" step="1" onchange="this.value = this.value > 0? this.value : !this.value? this.value : 0;" onkeypress="return event.charCode >= 48 && event.charCode <= 57" autocomplete="off">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="maximumOrderQuantity" class="form-control-label"><?php echo $vModule_product_maximumOrderQuantity; ?></label>
                            <input type="number" class="form-control" name="maximumOrderQuantity" id="maximumOrderQuantity" min="0" step="1" onchange="this.value = this.value > 0? this.value : !this.value? this.value : 0;" onkeypress="return event.charCode >= 48 && event.charCode <= 57" autocomplete="off">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="stock" class="form-control-label"><?php echo $vModule_product_availableQuantity; ?></label>
                            <input type="number" class="form-control" name="stock" id="stock" min="0" step="1" onchange="this.value = this.value > 0? this.value : !this.value? this.value : 0;" onkeypress="return event.charCode >= 48 && event.charCode <= 57" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <button id="addSubmitButton" type="submit" class="btn btn-primary px-10 font-weight-bold"><?php echo $vModule_product_addProduct; ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        DistributorSingleProduct.init('add');
    })
</script>