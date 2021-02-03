<script>
    firebase.analytics().setCurrentScreen('signin');
</script>
<div class="d-flex flex-column flex-root">
    <!--begin::Login-->
    <div class="login login-3 wizard d-flex flex-column flex-lg-row flex-column-fluid">

        <!--begin::Content-->
        <div class="login-content flex-row-fluid d-flex flex-column p-10">

            <div class="d-flex flex-column-auto flex-column pt-lg-40 pt-5 show-mobile">

                <!--begin::Aside header-->
                <a href="/" class="login-logo text-center pt-lg-25 pb-10">
                    <img src="/assets/img/aumet-logo.svg" class="max-h-70px" alt="" />
                </a>
                <!--end::Aside header-->
                <!--begin::Aside Title-->
                <h5 class="font-weight-bolder text-center font-size-h6 text-dark line-height-xl">
                    <?php echo $vLogin_slogan ?></h5>
                <!--end::Aside Title-->
            </div>
            <!--begin::Wrapper-->
            <div class="d-flex flex-row-fluid flex-center">
                <!--begin::Signin-->
                <div class="login-form">
                    <!--begin::Form-->
                    <form class="form" id="kt_login_singin_form" action="/web/auth/signin_nofirebase">
                        <!--begin::Title-->
                        <div class="pb-5 pb-lg-15">
                            <h3 class="font-weight-bolder text-primary font-size-h1 font-size-h1-lg"><?php echo $vLogin_signin ?></h3>
                            <div class="text-dark-75 font-weight-bold font-size-h4 mt-5"><?php echo $vLogin_noAccountAdv ?>
                                <a href="/web/auth/signup" class="text-success font-weight-bolder"><?php echo $vLogin_signupAdv ?></a>
                            </div>
                        </div>
                        <!--begin::Title-->
                        <!--begin::Form group-->
                        <div class="form-group">
                            <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vLogin_email ?></label>
                            <input class="form-control h-auto py-7 px-6 rounded-lg border-0" type="email" name="email" />
                        </div>
                        <!--end::Form group-->
                        <!--begin::Form group-->
                        <div class="form-group">
                            <div class="d-flex justify-content-between mt-n5">
                                <label class="font-size-h6 font-weight-bolder text-dark pt-5"><?php echo $vLogin_password ?></label>
                                <a href="/web/auth/forgot" class="text-success font-size-h6 font-weight-bolder pt-5"><?php echo $vLogin_forgotPassword ?></a>
                            </div>
                            <input class="form-control h-auto py-7 px-6 rounded-lg border-0" type="password" name="password" />
                        </div>
                        <!--end::Form group-->
                        <!--begin::Action-->
                        <div class="pb-lg-0 pb-5">
                            <button type="submit" id="kt_login_singin_form_submit_button" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3"><?php echo $vLogin_signin ?></button>
                            <button type="button" id="kt_login_singin_google_submit_button" onclick="WebAuth.googleSignIn()" class="btn btn-outline-primary font-weight-bolder px-8 py-4 my-3 mr-3 font-size-lg">
                                <span class="svg-icon svg-icon-md">

                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                        <path d="M19.9895 10.1871C19.9895 9.36767 19.9214 8.76973 19.7742 8.14966H10.1992V11.848H15.8195C15.7062 12.7671 15.0943 14.1512 13.7346 15.0813L13.7155 15.2051L16.7429 17.4969L16.9527 17.5174C18.879 15.7789 19.9895 13.221 19.9895 10.1871Z" fill="#4285F4" />
                                        <path d="M10.1993 19.9313C12.9527 19.9313 15.2643 19.0454 16.9527 17.5174L13.7346 15.0813C12.8734 15.6682 11.7176 16.0779 10.1993 16.0779C7.50243 16.0779 5.21352 14.3395 4.39759 11.9366L4.27799 11.9466L1.13003 14.3273L1.08887 14.4391C2.76588 17.6945 6.21061 19.9313 10.1993 19.9313Z" fill="#34A853" />
                                        <path d="M4.39748 11.9366C4.18219 11.3166 4.05759 10.6521 4.05759 9.96565C4.05759 9.27909 4.18219 8.61473 4.38615 7.99466L4.38045 7.8626L1.19304 5.44366L1.08875 5.49214C0.397576 6.84305 0.000976562 8.36008 0.000976562 9.96565C0.000976562 11.5712 0.397576 13.0882 1.08875 14.4391L4.39748 11.9366Z" fill="#FBBC05" />
                                        <path d="M10.1993 3.85336C12.1142 3.85336 13.406 4.66168 14.1425 5.33717L17.0207 2.59107C15.253 0.985496 12.9527 0 10.1993 0C6.2106 0 2.76588 2.23672 1.08887 5.49214L4.38626 7.99466C5.21352 5.59183 7.50242 3.85336 10.1993 3.85336Z" fill="#EB4335" />
                                    </svg>
                                </span><?php echo $vLogin_signinWithGoogle ?></button>
                            <!--                            <button type="button" id="kt_login_singin_phone_submit_button" onclick="WebAuth.googleSignIn()" class="btn btn-outline-primary font-weight-bolder px-8 py-4 my-3 font-size-lg">-->
                            <!--                                <span class="svg-icon svg-icon-lg">-->
                            <!---->
                            <!--                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">-->
                            <!--                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">-->
                            <!--                                            <rect x="0" y="0" width="24" height="24" />-->
                            <!--                                            <path d="M12,22 C6.4771525,22 2,17.5228475 2,12 C2,6.4771525 6.4771525,2 12,2 C17.5228475,2 22,6.4771525 22,12 C22,17.5228475 17.5228475,22 12,22 Z M11.613922,13.2130341 C11.1688026,13.6581534 10.4887934,13.7685037 9.92575695,13.4869855 C9.36272054,13.2054673 8.68271128,13.3158176 8.23759191,13.760937 L6.72658218,15.2719467 C6.67169475,15.3268342 6.63034033,15.393747 6.60579393,15.4673862 C6.51847004,15.7293579 6.66005003,16.0125179 6.92202169,16.0998418 L8.27584113,16.5511149 C9.57592638,16.9844767 11.009274,16.6461092 11.9783003,15.6770829 L15.9775173,11.6778659 C16.867756,10.7876271 17.0884566,9.42760861 16.5254202,8.3015358 L15.8928491,7.03639343 C15.8688153,6.98832598 15.8371895,6.9444475 15.7991889,6.90644684 C15.6039267,6.71118469 15.2873442,6.71118469 15.0920821,6.90644684 L13.4995401,8.49898884 C13.0544207,8.94410821 12.9440704,9.62411747 13.2255886,10.1871539 C13.5071068,10.7501903 13.3967565,11.4301996 12.9516371,11.8753189 L11.613922,13.2130341 Z" fill="#000000" />-->
                            <!--                                        </g>-->
                            <!--                                    </svg>-->
                            <!--                                </span>--><?php //echo $vLogin_signinWithPhone
                                                                            ?>
                            <!--</button>-->
                        </div>
                        <!--end::Action-->
                        <div class="text-right d-flex justify-content-end">
                            <div class="top-signin text-right d-flex justify-content-end pt-10 pb-lg-0 pb-10">
                                <span class="font-weight-bold text-dark-75 font-size-h4"><?php echo $vLogin_helpAdv ?></span>
                                <a href="javascript:;" class="font-weight-bold text-success font-size-h4 ml-2" id="kt_login_signup" data-toggle="modal" data-target="#support_modal"><?php echo $vLogin_help ?></a>
                            </div>
                        </div>

                        <div class="text-left d-flex justify-content-start pt-20">
                            <a href="https://aumet.com" target="_blank">

                            </a>

                        </div>

                        <a href="/" class="text-left justify-content-start pt-5 ">
                            <img src="/assets/img/aumet-logo.svg" class="max-h-25px max-w-150px mr-1" />

                            <h4 class="text-left font-size-h4 text-dark pl-10 pr-10 pt-5" style="margin-bottom: 0; margin-top: 0.25rem;">Copyright 2021 Aumet, Inc. All Rights Reserved.</h4>
                        </a>
                    </form>
                    <!--end::Form-->
                </div>
                <!--end::Signin-->
            </div>
            <!--end::Wrapper-->
            <!--begin::Top-->

            <!--end::Top-->
        </div>
        <!--end::Content-->
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
    </div>
    <!--end::Login-->
</div>
<script>
    /*jQuery(document).ready(function() {
        $('#kt_login_singin_form').submit(function(e) {
            e.preventDefault();
            WebAuth.signIn()
        });
    });*/
</script>
