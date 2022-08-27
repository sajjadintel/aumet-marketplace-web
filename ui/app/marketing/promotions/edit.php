<div class="px-10 d-flex justify-content-center">
    <form method="POST" action="/web/distributor/marketing/promotion/edit" id="promotionForm" class="single-promotion-form">
        <input type="hidden" name="promotionId" value="<?php echo $promotion->id; ?>">
        <div class="row">
            <div class="col-md-12 form-group">
                <label for="name" class="form-control-label h3 font-weight-bolder"><?php echo $vModule_promotion_name; ?></label>
                <input type="text" class="form-control" name="name" id="name" placeholder="<?php echo $vModule_promotion_name; ?>" value="<?php echo $promotion->name; ?>">
            </div>
        </div>
        <div class="row form-group">
            <span class="col-2 single-promotion switch switch-success">
                <label class="d-flex align-items-center">
                    <?php if($promotion->active): ?>
                        <input id="active" type="checkbox" name="active" checked/>
                    <?php else: ?>
                        <input id="active" type="checkbox" name="active"/>
                    <?php endif; ?>
                    <span></span>
                    <p class="ml-5 mb-0"><?php echo $vModule_promotion_active; ?></p>
                </label>
            </span>
        </div>
        <div class="row">
            <div class="col-md-12 form-group">
                <label class="form-control-label h3 font-weight-bolder"><?php echo $vModule_promotion_duration; ?></label>
                <div class="d-flex align-items-center">
                    <div class="input-group date">
                        <input type="text" class="form-control" name="startDate" id="startDate" placeholder="<?php echo $vModule_promotion_start; ?>" readonly value="<?php echo $promotion->startDate; ?>">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="la la-calendar"></i>
                            </span>
                        </div>
                    </div>
                    <p class="px-5 mb-0 text-muted"><?php echo $vModule_promotion_to; ?></p>
                    <div class="input-group date">
                        <input type="text" class="form-control" name="endDate" id="endDate" placeholder="<?php echo $vModule_promotion_end; ?>" readonly value="<?php echo $promotion->endDate; ?>">
                        <div class="input-group-append">
                            <span class="input-group-text">
                                <i class="la la-calendar"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 form-group promotion-banner-container">
                <label class="form-control-label h3 font-weight-bolder"><?php echo $vModule_promotion_homepageBanner; ?></label>
                <input type="hidden" name="image" id="image" value="<?php echo $promotion->image; ?>">
                <div class="dropzone dropzone-default mb-2 promotion-banner-dropzone" id="dropzone">
                    <div class="dropzone-msg dz-message needsclick">
                        <h3 class="dropzone-msg-title">Drop files here or click to upload</h3>
                        <span class="dropzone-msg-desc">.jpeg, .jpg, .png</span>
                    </div>
                </div>
                <div id="errorMessage" class="font-size-h6 text-danger"></div>
            </div>
        </div>
        <div class="form-group" id="featuredProductsRepeater">
            <div class="row">
                <label class="col-md-12 form-control-label h3 font-weight-bolder mb-2"><?php echo $vModule_promotion_featuredProducts; ?></label>
                <div id="featuredProductsList" data-repeater-list="featuredProductsList" class="col-md-12" data-arrProducts="<?php echo htmlspecialchars(json_encode($arrProducts), ENT_QUOTES, 'UTF-8'); ?>" data-arrFeaturedProducts="<?php echo htmlspecialchars(json_encode($arrFeaturedProducts), ENT_QUOTES, 'UTF-8'); ?>">
                    <div data-repeater-item="" class="form-group row mb-0">
                        <div class="col-md-6 form-group">
                            <select id="featuredProducts" name="featuredProducts" class="form-control selectpicker featuredProductsSelect" data-live-search="true">
                            </select>
                            <div class="d-md-none mb-2"></div>
                        </div>
                        <div class="col-md-3">
                            <a href="javascript:;" class="btn btn-sm font-weight-bolder btn-light-primary" onclick="DistributorPromotions.useProductImage(this);">
                                <i class="la la-image"></i><?php echo $vModule_promotion_useImage; ?></a>
                        </div>
                        <div class="col-md-3">
                            <a href="javascript:;" id="featuredProductsDelete" data-repeater-delete="" class="btn btn-sm font-weight-bolder btn-light-danger">
                                <i class="la la-trash-o"></i><?php echo $vButton_delete; ?></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <a href="javascript:;" id="featuredProductsAdd" data-repeater-create="" class="btn btn-sm font-weight-bolder btn-light-primary">
                        <i class="la la-plus"></i><?php echo $vButton_add; ?></a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 form-group">
                <label for="message" class="form-control-label h3 font-weight-bolder"><?php echo $vModule_promotion_message; ?></label>
                <textarea type="text" class="single-promotion-textarea form-control" name="message" id="message" rows="4" placeholder="<?php echo $vModule_promotion_message; ?>"><?php echo $promotion->message; ?></textarea>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <button id="promotionSubmitButton" type="submit" class="btn btn-primary px-10 font-weight-bold"><?php echo $vModule_promotion_edit; ?></button>
        </div>
    </form>
</div>
<script>
    $(document).ready(function() {
        DistributorPromotions.initSingle();
    })
</script>