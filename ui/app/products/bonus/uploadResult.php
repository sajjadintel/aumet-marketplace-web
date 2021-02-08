<div class="row">
    <?php if (is_null($failedProductsSheetUrl)) : ?>
        <div class="col-4">
            <div class="card card-custom bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url(/theme/assets/media/svg/shapes/abstract-1.svg)">
                <div class="card-body my-4">
                    <p class="card-title font-weight-bolder text-primary font-size-h2 mb-4 text-hover-state-dark d-block">Import Successful</p>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="col-4">
            <div class="card card-custom bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url(/theme/assets/media/svg/shapes/abstract-2.svg)">
                <div class="card-body my-4">
                    <a href=<?php echo $failedProductsSheetUrl ?> target="_blank" class="card-title font-weight-bolder text-danger font-size-h2 mb-4 text-hover-state-dark d-block">Import Failed <span class="text-dark">(Download Failed Records)<span></a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<div class="row">
    <div class="col-4">
        <div class="d-flex flex-column-fluid">
            <a class="btn btn-lg btn-primary mr-2 btn-lg-radius" title="Go back" onclick="location.reload()">
                Go back
            </a>
        </div>
    </div>
</div>