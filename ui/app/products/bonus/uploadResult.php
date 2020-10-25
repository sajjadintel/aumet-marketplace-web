<div class="row">
    <div class="col-4">
        <div class="card card-custom bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url(/theme/assets/media/svg/shapes/abstract-1.svg)">
            <div class="card-body my-4">
                <a href="javascript:;" class="card-title font-weight-bolder text-primary font-size-h2 mb-4 text-hover-state-dark d-block">Import Success Rate</a>
                <div class="font-weight-bold text-dark font-size-h3">
                    <span class="text-dark font-weight-bolder font-size-h2 mr-2"><?php echo $objBonusUpdateUpload->importSuccessRate ?> %</span> Successful</div>
                <div class="progress progress-xs mt-7 bg-primary-o-40">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $objBonusUpdateUpload->importSuccessRate ?>%;" aria-valuenow="<?php echo $objBonusUpdateUpload->importSuccessRate ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-4">
        <div class="card card-custom bgi-no-repeat card-stretch gutter-b" style="background-position: right top; background-size: 30% auto; background-image: url(/theme/assets/media/svg/shapes/abstract-2.svg)">
            <div class="card-body my-4">
                <a href="javascript:;" class="card-title font-weight-bolder text-danger font-size-h2 mb-4 text-hover-state-dark d-block">Import Failure Rate (<span class="text-dark">Download Failed Records<span>)</a>
                <div class="font-weight-bold text-dark font-size-h3">
                    <span class="text-dark font-weight-bolder font-size-h2 mr-2"><?php echo $objBonusUpdateUpload->importFailureRate ?> %</span> Failure</div>
                <div class="progress progress-xs mt-7 bg-danger-o-40">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: <?php echo $objBonusUpdateUpload->importFailureRate ?>%;" aria-valuenow="<?php echo $objBonusUpdateUpload->importFailureRate ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>
    </div>

</div>
