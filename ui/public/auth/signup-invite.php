<style>
	.dropzone-item {
		background-color: #FFF !important;
	}

	textarea {
		resize: none;
	}
</style>
<div class="d-flex flex-column flex-root">
	<!--begin::Login-->
	<div class="login login-3 wizard d-flex flex-column flex-lg-row flex-column-fluid wizard" id="kt_login">

		<!--begin::Content-->
		<div class="login-content flex-column-fluid d-flex flex-column p-10">
			<!--begin::Wrapper-->
			<div class="d-flex flex-row-fluid flex-center">
				<!--begin::Signin-->
				<div id="signupContainer" class="login-form login-form-signup">
					<!--begin::Form-->
					<form class="form" novalidate="novalidate" id="kt_login_signup_form">
						<input type="hidden" name="token" value="<?php echo $invite->token; ?>" />
						<!--begin: Wizard Step 1-->
						<div class="pb-5" data-wizard-type="step-content" data-wizard-state="current">
							<!--begin::Title-->
							<div class="pb-10 pb-lg-15">
								<h3 class="font-weight-bolder text-dark display5"><?php echo $vLogin_signupPharmacy ?></h3>
								<div class="text-dark-50 font-weight-bold font-size-h4"><?php echo $vSignup_AlreadyHaveAccount ?>
									<a href="/web/auth/signin" class="text-primary font-weight-bolder"><?php echo $vLogin_signin ?></a>
								</div>
							</div>

							<div class="form-group">
								<label class="font-size-h2 font-weight-bolder text-dark">I'm a</label>
								<div class="radio-inline">
									<label class="radio radio-square radio-lg font-size-h4 font-weight-bolder text-dark mr-5 companyType">
										<input type="radio" disabled name="companyType" value="pharmacy">
										<span class=""></span>Pharmacy</label>
									<label class="radio radio-square radio-lg font-size-h4 font-weight-bolder text-dark mr-5 companyType">
										<input type="radio" disabled checked name="companyType" value="distributor">
										<span></span>Distributor</label>

								</div>
							</div>

							<!--begin::Title-->
							<!--begin::Form Group-->
							<div class="form-group">
								<label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vSignup_name ?></label>
								<input type="text" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="name" placeholder="<?php echo $vSignup_name ?>" value="" />
							</div>
							<!--end::Form Group-->
							<!--begin::Form Group-->
							<div class="form-group">
								<label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vSignup_mobile ?></label>
								<input type="tel" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="mobile" placeholder="<?php echo $vSignup_mobile ?>" value="" style="direction: ltr;" />
							</div>
							<!--end::Form Group-->
							<!--begin::Form Group-->
							<div class="form-group">
								<label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vSignup_email ?></label>
								<input type="email" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="email" disabled value="<?php echo $invite->email; ?>" style="direction: ltr;" id="email"/>
							</div>
							<!--end::Form Group-->
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
						</div>
						<!--end: Wizard Step 1-->
						<!--begin: Wizard Actions-->
						<div class="d-flex justify-content-between pt-3">
							<div>
								<button class="btn btn-primary font-weight-bolder font-size-h6 pl-5 pr-8 py-4 my-3" data-wizard-type="action-submit" type="submit" id="kt_login_signup_form_submit_button"><?php echo $vSignup_wizardSubmit ?>
									<span class="svg-icon svg-icon-md ml-2">
										<!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Right-2.svg-->
										<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
											<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
												<rect x="0" y="0" width="24" height="24" />
												<path d="M8,13.1668961 L20.4470385,11.9999863 L8,10.8330764 L8,5.77181995 C8,5.70108058 8.01501031,5.63114635 8.04403925,5.56663761 C8.15735832,5.31481744 8.45336217,5.20254012 8.70518234,5.31585919 L22.545552,11.5440255 C22.6569791,11.5941677 22.7461882,11.6833768 22.7963304,11.794804 C22.9096495,12.0466241 22.7973722,12.342628 22.545552,12.455947 L8.70518234,18.6841134 C8.64067359,18.7131423 8.57073936,18.7281526 8.5,18.7281526 C8.22385763,18.7281526 8,18.504295 8,18.2281526 L8,13.1668961 Z" fill="#000000" />
												<path d="M4,16 L5,16 C5.55228475,16 6,16.4477153 6,17 C6,17.5522847 5.55228475,18 5,18 L4,18 C3.44771525,18 3,17.5522847 3,17 C3,16.4477153 3.44771525,16 4,16 Z M1,11 L5,11 C5.55228475,11 6,11.4477153 6,12 C6,12.5522847 5.55228475,13 5,13 L1,13 C0.44771525,13 6.76353751e-17,12.5522847 0,12 C-6.76353751e-17,11.4477153 0.44771525,11 1,11 Z M4,6 L5,6 C5.55228475,6 6,6.44771525 6,7 C6,7.55228475 5.55228475,8 5,8 L4,8 C3.44771525,8 3,7.55228475 3,7 C3,6.44771525 3.44771525,6 4,6 Z" fill="#000000" opacity="0.3" />
											</g>
										</svg>
										<!--end::Svg Icon-->
									</span>
                                </button>
								<button type="button" class="btn btn-primary font-weight-bolder font-size-h6 pl-8 pr-4 py-4 my-3" data-wizard-type="action-next"><?php echo $vSignup_wizardNext ?>
									<span class="svg-icon svg-icon-md ml-1">
										<!--begin::Svg Icon | path:assets/media/svg/icons/Navigation/Right-2.svg-->
										<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
											<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
												<polygon points="0 0 24 0 24 24 0 24" />
												<rect fill="#000000" opacity="0.3" transform="translate(8.500000, 12.000000) rotate(-90.000000) translate(-8.500000, -12.000000)" x="7.5" y="7.5" width="2" height="9" rx="1" />
												<path d="M9.70710318,15.7071045 C9.31657888,16.0976288 8.68341391,16.0976288 8.29288961,15.7071045 C7.90236532,15.3165802 7.90236532,14.6834152 8.29288961,14.2928909 L14.2928896,8.29289093 C14.6714686,7.914312 15.281055,7.90106637 15.675721,8.26284357 L21.675721,13.7628436 C22.08284,14.136036 22.1103429,14.7686034 21.7371505,15.1757223 C21.3639581,15.5828413 20.7313908,15.6103443 20.3242718,15.2371519 L15.0300721,10.3841355 L9.70710318,15.7071045 Z" fill="#000000" fill-rule="nonzero" transform="translate(14.999999, 11.999997) scale(1, -1) rotate(90.000000) translate(-14.999999, -11.999997)" />
											</g>
										</svg>
										<!--end::Svg Icon-->
									</span>
                                </button>
							</div>
						</div>
						<!--end: Wizard Actions-->
					</form>
					<!--end::Form-->
				</div>
				<!--end::Signin-->
				<!--begin::Thankyou-->
				<div id="thankyouContainer" class="login-form login-form-signup pb-5" style="display: none;">
					<div class="pb-10 pb-lg-15">
						<h3 class="font-weight-bolder text-dark display5"><?php echo $vSignup_thankyouTitle ?></h3>
						<div class="text-dark-50 font-weight-bold font-size-h4"><?php echo $vSignup_thankyouInviteSubtitle ?></div>

						<a href="/web/auth/signin">
							<button class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3"><?php echo $vSignup_thankyouHome ?></button>
						</a>
					</div>
					<div>

						<table width="100%" cellspacing="0" cellpadding="0" border="0" role="presentation">
							<tbody>
								<tr>
									<td class="o_bg-light o_px-xs" align="center" style="padding-left: 8px;padding-right: 8px;">
										<!--[if mso]><table width="800" cellspacing="0" cellpadding="0" border="0" role="presentation"><tbody><tr><td><![endif]-->
										<table class="o_block-lg" width="100%" cellspacing="0" cellpadding="0" border="0" role="presentation" style="max-width: 800px;margin: 0 auto;">
											<tbody id="signupDetailData">
											</tbody>
										</table>
										<!--[if mso]></td></tr></table><![endif]-->
									</td>
								</tr>
							</tbody>
						</table>

					</div>
				</div>
				<!--end::Thankyou-->
			</div>
			<!--end::Wrapper-->
		</div>
		<!--end::Content-->
		<!--begin::Aside-->
		<div class="login-aside d-flex flex-column flex-row-auto">
			<!--begin::Aside Top-->
			<div class="d-flex flex-column-auto flex-column pt-15 px-30">

				<!--begin::Aside Top-->
				<!--begin::Aside header-->
				<a href="/" class="login-logo text-center pt-lg-25 pb-10">
					<img src="/assets/img/aumet-logo.svg" class="max-h-70px" alt="" />
				</a>
				<!--end::Aside header-->
				<!--begin::Aside Title-->
				<h3 class="font-weight-bolder text-center font-size-h4 text-dark-50 line-height-xl">
					<?php echo $vLogin_slogan ?></h3>
				<!--end::Aside Title-->
				<!--end::Aside Top-->
				<!--end::Aside header-->
				<!--begin: Wizard Nav-->
				<div class="wizard-nav pt-5 pt-lg-15">
					<!--begin::Wizard Steps-->
					<div class="wizard-steps">
						<!--begin::Wizard Step 1 Nav-->
						<div class="wizard-step" data-wizard-type="step" data-wizard-state="current">
							<div class="wizard-wrapper">
								<div class="wizard-icon">
									<i class="wizard-check flaticon2-user-outline-symbol text-primary"></i>
									<span class="wizard-number">
										<i class="flaticon2-user-outline-symbol text-dark"></i>
									</span>
								</div>
								<div class="wizard-label">
									<h3 class="wizard-title"><?php echo $vSignup_wizardPersonalInfo ?></h3>
									<div class="wizard-desc"><?php echo $vSignup_wizardPersonalInfoDesc ?></div>
								</div>
							</div>
						</div>
						<!--end::Wizard Step 1 Nav-->
					</div>
					<!--end::Wizard Steps-->
				</div>
				<!--end: Wizard Nav-->
			</div>
			<!--end::Aside Top-->
			<!--begin::Aside Bottom-->
			<div class="aside-img-wizard d-flex flex-row-fluid bgi-no-repeat pt-2 pt-lg-5" style="background-position: center; background-size: 70%; background-image: url(/assets/img/undraw_steps_ngvm.svg)"></div>
			<!--end::Aside Bottom-->
		</div>
		<!--begin::Aside-->
	</div>
	<!--end::Login-->
</div>
<script>
    const setEmail = '<?php echo $invite->email;?>';
    $(document).ready(() => {
        const email = $('#email');
        email.val(setEmail);
    });
</script>