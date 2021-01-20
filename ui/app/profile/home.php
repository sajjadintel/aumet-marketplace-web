<!--begin::Entry-->
<div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Profile-->
        <div class="row">
            <!--begin::Left Side-->
            <div class="col-3">
                <div class="card card-custom card-body card-stretch gutter-b" style="height: auto; padding-right: 1rem; padding-left: 1rem;">
                    <div class="row" style="justify-content: space-between; align-items: center;">
                        <div class="col-7">
                            <span class="card-label font-weight-bolder font-size-h3"><?php echo $vModule_homepageBuyer_pendingOrders ?></span>
                        </div>
                        <div class="col-5" style="display: flex; justify-content: flex-end;">
                            <a class="btn btn-hover-bg-primary btn-text-primary btn-hover-text-white font-weight-bold" onclick="WebApp.loadPage('/web/pharmacy/order/history')">
                                <?php echo $vButton_view_all; ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!--begin::Main-->
            <div class="col-9 px-10 py-5">
                <div class="card-label font-weight-bolder font-size-h1"><?php echo $vModule_profile_myProfileTitle ?></div>
                <div class="card-label font-size-h5 text-muted"><?php echo $vModule_profile_myProfileSubtitle ?></div>
                <form class="form pt-5" novalidate="novalidate" id="myProfileForm">
                    <div class="row pb-5">
                        <!--begin::Form Group-->
                        <div class="col-6 form-group">
                            <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vSignup_name ?></label>
                            <input type="text" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="name" placeholder="<?php echo $vSignup_name ?>" value="" />
                        </div>
                        <!--end::Form Group-->
                        <!--begin::Form Group-->
                        <div class="col-6 form-group">
                            <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vSignup_mobile ?></label>
                            <input type="tel" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="mobile" placeholder="<?php echo $vSignup_mobile ?>" value="+" style="direction: ltr;" />
                        </div>
                        <!--end::Form Group-->
                        <!--begin::Form Group-->
                        <div class="col-6 form-group">
                            <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vSignup_country; ?></label>
                            <select name="country" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6">
                                <option value=""><?php echo $vSignup_country; ?></option>
                                <?php foreach($arrCountry as $country) : ?>
                                    <option value="<?php echo $country['id']; ?>"><?php echo $country['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <!--end::Form Group-->
                        <!--begin::Form Group-->
                        <div class="col-6 form-group">
                            <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vSignup_city; ?></label>
                            <select name="city" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" disabled>
                                <option value=""><?php echo $vSignup_city; ?></option>
                            </select>
                        </div>
                        <!--end::Form Group-->
                        <!--begin::Form Group-->
                        <div class="col-6 form-group">
                            <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vSignup_passwordConfirmation ?></label>
                            <input type="password" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="passwordConfirmation" placeholder="<?php echo $vSignup_passwordConfirmation ?>" value="" style="direction: ltr;" />
                        </div>
                        <!--end::Form Group-->
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        Profile.init();
    })
</script>