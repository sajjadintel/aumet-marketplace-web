<style>
	textarea {
		resize: none;
	}
</style>
<div class="px-5">
    <div class="card card-custom gutter-b">
        <div class="card-body">
            <h1 class="pb-2"><?php echo $vModule_product_editing . " '$product->name'"; ?></h2>
            <h4><?php echo $vModule_product_editingInfo; ?></h3>
        </div>
    </div>
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
            <div id="general-body" class="tab-body">
                <form method="POST" action="/web/distributor/product/general" id="generalForm">
                    <input type="hidden" name="productId" value="<?php echo $product->productId; ?>">
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="nameAr" class="form-control-label"><?php echo $vModule_product_brandName; ?> AR</label>
                            <input type="text" class="form-control" name="nameAr" id="nameAr" autocomplete="off" value="<?php echo $product->productName_ar; ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="nameEn" class="form-control-label"><?php echo $vModule_product_brandName; ?> EN</label>
                            <input type="text" class="form-control" name="nameEn" id="nameEn" autocomplete="off" value="<?php echo $product->productName_en; ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="subtitleAr" class="form-control-label"><?php echo $vModule_product_subtitle; ?> AR</label>
                            <input type="text" class="form-control" name="subtitleAr" id="subtitleAr" autocomplete="off" value="<?php echo $product->subtitle_ar; ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="subtitleEn" class="form-control-label"><?php echo $vModule_product_subtitle; ?> EN</label>
                            <input type="text" class="form-control" name="subtitleEn" id="subtitleEn" autocomplete="off" value="<?php echo $product->subtitle_en; ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="descriptionAr" class="form-control-label"><?php echo $vModule_product_description; ?> AR</label>
                            <textarea class="form-control" name="descriptionAr" id="descriptionAr" rows="4" autocomplete="off"><?php echo $product->description_ar; ?></textarea>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="descriptionEn" class="form-control-label"><?php echo $vModule_product_description; ?> EN</label>
                            <textarea class="form-control" name="descriptionEn" id="descriptionEn" rows="4" autocomplete="off"><?php echo $product->description_en; ?></textarea>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="scientificName" class="form-control-label"><?php echo $vModule_product_scientificName; ?></label>
                            <select class="select2 form-control" name="scientificNameId" id="scientificName" data-select2-id="scientificName" tabindex="-1" aria-hidden="true" autocomplete="off" value="<?php echo $product->scientificNameId; ?>">
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="country" class="form-control-label"><?php echo $vModule_product_madeIn; ?></label>
                            <select class="select2 form-control" name="countryId" id="country" data-select2-id="country" tabindex="-1" aria-hidden="true" autocomplete="off" value="<?php echo $product->madeInCountryId; ?>">
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="itemCode" class="form-control-label"><?php echo $vModule_product_itemCode; ?></label>
                            <input type="text" class="form-control" name="itemCode" id="itemCode" autocomplete="off" value="<?php echo $product->itemCode; ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="batchNumber" class="form-control-label"><?php echo $vModule_product_batchNumber; ?></label>
                            <input type="text" class="form-control" name="batchNumber" id="batchNumber" autocomplete="off" value="<?php echo $product->batchNumber; ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="manufacturerName" class="form-control-label"><?php echo $vModule_product_manufacturerName; ?></label>
                            <input type="text" class="form-control" name="manufacturerName" id="manufacturerName" autocomplete="off" value="<?php echo $product->manufacturerName; ?>">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="strength" class="form-control-label"><?php echo $vModule_product_strength; ?></label>
                            <input type="text" class="form-control" name="strength" id="strength" autocomplete="off" value="<?php echo $product->strength; ?>">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="activeIngredients" class="form-control-label"><?php echo $vModule_product_activeIngredients; ?></label>
                            <select class="select2 form-control" name="activeIngredients" id="activeIngredients" data-select2-id="activeIngredients" tabindex="-1" aria-hidden="true" multiple autocomplete="off">
                            </select>
                            <input type="hidden" name="activeIngredientsId" id="addActiveIngredientsVal" autocomplete="off"/>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button id="generalSubmitButton" type="submit" class="btn btn-primary px-10 font-weight-bold"><?php echo $vModule_product_saveChanges; ?></button>
                    </div>
                </form>
            </div>
            <div id="images-body" class="tab-body d-none">
                Images
            </div>
            <div id="prices-body" class="tab-body d-none">
                Prices
            </div>
            <div id="stockSettings-body" class="tab-body d-none">
                Stock Settings
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        DistributorSingleProduct.init();
    })
</script>