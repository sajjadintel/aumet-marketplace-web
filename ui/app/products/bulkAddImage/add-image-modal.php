<!-- Modal-->
<div class="modal fade" id="addImageModal" tabindex="-1" role="dialog" aria-labelledby="addImageModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addImageModalTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <?php $i = 0; ?>
                <?php foreach($mapFileNameProduct as $fileName => $product) : ?>
                    <?php $i++; ?>
                    <div id="row-<?php echo $i; ?>" class="row mb-10" style="justify-content: space-around; align-items: center;">
                        <div class="col-md-2" style="text-align: center;">
                            <div class="symbol symbol-100 flex-shrink-0 mr-4 bg-light">
                                <div class="symbol-label" style="background-image: url('/assets/img/products/<?php echo $fileName; ?>')" ></div>
                            </div>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="productName" class="form-control-label">Product</label>
                            <?php if(!is_null($product)) : ?>
                                <select class="select2 form-control productName" data-productId=<?php echo $product->id; ?> data-productName=<?php echo $product->name; ?> id="productName-<?php echo $i; ?>" name="productId" data-select2-id="productName-<?php echo $i; ?>" tabindex="-1" aria-hidden="true">
                                    <option selected disabled>Product</option>
                                </select>
                            <?php else : ?>
                                <select class="select2 form-control productName" tabindex="-1" aria-hidden="true">
                                    <option selected disabled>Product</option>
                                </select>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-2">
                            <a href="javascript:;" class="btn btn-sm font-weight-bolder btn-light-danger">
                                <i class="la la-trash-o"></i>Delete
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer">
                <div class="modal-close-button">
                    <button type="button" class="btn btn-primary font-weight-bold" id="addImageDone" data-dismiss="modal">Done</button>
                </div>
            </div>
        </div>
    </div>
</div>