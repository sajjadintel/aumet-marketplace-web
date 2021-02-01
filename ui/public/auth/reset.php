<div class="d-flex flex-column flex-root">
    <!--begin::Login-->
    <div class="login login-3 wizard d-flex flex-column flex-lg-row flex-column-fluid">
        <!--begin::Aside-->
        <div class="login-aside d-flex flex-column flex-row-auto hide-mobile">
            <!--begin::Aside Top-->
            <div class="d-flex flex-column-auto flex-column pt-lg-20 pt-10">
                <h1 class="font-weight-bolder text-center font-size-h1 text-primary line-height-0 pt-lg-15 pb-8" style="font-size: 3.5rem !important;">
                    Aumet <span class="font-weight-bolder text-dark">Marketplace</span></h1>
                <h3 class="font-weight-light text-center font-size-h2 text-dark-75 line-height-lg p-20">
                    <?php echo $vLogin_slogan ?></h3>
                <!--begin::Aside header-->


                <!--end::Aside header-->
                <!--begin::Aside Title-->

                <!--end::Aside Title-->
            </div>
            <!--end::Aside Top-->
            <!--begin::Aside Bottom-->
            <div class="aside-img d-flex flex-row-fluid bgi-no-repeat bgi-position-x-center p-10" style="background-position-y: 100%; background-image: url('/assets/img/undraw_medicine_b1ol.svg');">
            </div>
            <!--end::Aside Bottom-->
        </div>
        <!--begin::Aside-->
        <!--begin::Content-->
        <div class="login-content flex-column-fluid d-flex flex-column p-10">
            <!--begin::Top-->
            <div class="text-right d-flex justify-content-center">
                <div class="top-forgot text-right d-flex justify-content-end pt-5 pb-lg-0 pb-10">
                    <span class="font-weight-bold text-muted font-size-h4"><?php echo $vForgot_havingIssues ?></span>
                    <a href="javascript:;" class="font-weight-bold text-primary font-size-h4 ml-2" id="kt_login_signup" data-toggle="modal" data-target="#support_modal"><?php echo $vForgot_getHelp ?></a>
                </div>
            </div>
            <!--end::Top-->
            <!--begin::Wrapper-->
            <div class="d-flex flex-row-fluid flex-center">
                <!--begin::Forgot-->
                <div class="login-form">
                    <!--begin::Form-->
                    <form class="form" id="kt_login_reset_form" action="/web/auth/reset">
                        <!--begin::Title-->
                        <div class="pb-5 pb-lg-15">
                            <h3 class="font-weight-bolder text-dark font-size-h2 font-size-h1-lg"></h3>
                            <p class="text-muted font-weight-bold font-size-h4"><?php echo $vForgot_enterPasswords ?></p>
                        </div>
                        <!--end::Title-->
                        <input type="hidden" value="<?php echo $_GET['token'] ?>" name="token">

                        <!--begin::Form Group-->
                        <div class="form-group">
                            <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vSignup_password ?></label>
                            <input type="password" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="password" placeholder="<?php echo $vSignup_password ?>" value="" style="direction: ltr;" />
                        </div>
                        <!--end::Form Group-->
                        <!--begin::Form Group-->
                        <div class="form-group">
                            <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vSignup_passwordConfirmation ?></label>
                            <input type="password" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="passwordConfirmation" placeholder="<?php echo $vSignup_passwordConfirmation ?>" value="" style="direction: ltr;" />
                        </div>
                        <!--end::Form Group-->


                        <!--begin::Form group-->
                        <div class="form-group d-flex flex-wrap">
                            <button type="submit" id="kt_login_reset_form_submit_button" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-4"><?php echo $vForgot_submit ?></button>
                            <a href="javascript;;" onclick="WebApp.loadPage('/web/auth/signin')" id="kt_login_forgot_cancel" class="btn btn-light-primary font-weight-bolder font-size-h6 px-8 py-4 my-3"><?php echo $vForgot_cancel ?></a>
                        </div>
                        <!--end::Form group-->
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Forgot-->
            </div>
            <!--end::Wrapper-->
        </div>
        <!--end::Content-->
    </div>
    <!--end::Login-->
</div>