<script>
	firebase.analytics().setCurrentScreen('signin');
</script>
<div class="d-flex flex-column flex-root">
	<!--begin::Login-->
	<div class="login login-3 wizard d-flex flex-column flex-lg-row flex-column-fluid">
		<!--begin::Aside-->
		<div class="login-aside d-flex flex-column flex-row-auto hide-mobile">
			<!--begin::Aside Top-->
			<div class="d-flex flex-column-auto flex-column pt-lg-40 pt-15">
				<!--begin::Aside header-->
				<a href="/" class="login-logo text-center pt-lg-25 pb-10">
					<img src="/assets/img/aumet-logo.svg" class="max-h-70px" alt="" />
				</a>
				<!--end::Aside header-->
				<!--begin::Aside Title-->
				<h3 class="font-weight-bolder text-center font-size-h4 text-dark-50 line-height-xl">
					<?php echo $vLogin_slogan ?></h3>
				<!--end::Aside Title-->
			</div>
			<!--end::Aside Top-->
			<!--begin::Aside Bottom-->
			<div class="aside-img d-flex flex-row-fluid bgi-no-repeat bgi-position-x-center" style="background-position-y: calc(100% - 5rem); background-image: url(/assets/img/undraw_medicine_b1ol.svg)"></div>
			<!--end::Aside Bottom-->
		</div>
		<!--begin::Aside-->
		<!--begin::Content-->
		<div class="login-content flex-row-fluid d-flex flex-column p-10">

			<div class="d-flex flex-column-auto flex-column pt-lg-40 pt-5 show-mobile">
				<!--begin::Aside header-->
				<a href="/" class="login-logo text-center pt-lg-25 pb-10">
					<img src="/assets/img/aumet-logo.svg" class="max-h-70px" alt="" />
				</a>
				<!--end::Aside header-->
				<!--begin::Aside Title-->
				<h5 class="font-weight-bolder text-center font-size-h6 text-dark-50 line-height-xl">
					<?php echo $vLogin_slogan ?></h5>
				<!--end::Aside Title-->
			</div>
			<!--begin::Wrapper-->
			<div class="d-flex flex-row-fluid flex-center">
				<!--begin::Signin-->
				<div class="login-form">
					<!--begin::Form-->
					<form class="form" id="kt_login_singin_form" action="/web/auth/signin">
						<!--begin::Title-->
						<div class="pb-5 pb-lg-15">
							<h3 class="font-weight-bolder text-dark font-size-h2 font-size-h1-lg"><?php echo $vLogin_signin ?></h3>
							<div class="text-muted font-weight-bold font-size-h4"><?php echo $vLogin_noAccountAdv ?>
								<a href="/web/auth/signup" class="text-primary font-weight-bolder"><?php echo $vLogin_signupAdv ?></a></div>
						</div>
						<!--begin::Title-->
						<!--begin::Form group-->
						<div class="form-group">
							<label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vLogin_email ?></label>
							<input class="form-control h-auto py-7 px-6 rounded-lg border-0" type="text" name="email" />
						</div>
						<!--end::Form group-->
						<!--begin::Form group-->
						<div class="form-group">
							<div class="d-flex justify-content-between mt-n5">
								<label class="font-size-h6 font-weight-bolder text-dark pt-5"><?php echo $vLogin_password ?></label>
								<a href="/web/auth/forgot" class="text-primary font-size-h6 font-weight-bolder text-hover-primary pt-5"><?php echo $vLogin_forgotPassword ?></a>
							</div>
							<input class="form-control h-auto py-7 px-6 rounded-lg border-0" type="password" name="password" />
						</div>
						<!--end::Form group-->
						<!--begin::Action-->
						<div class="pb-lg-0 pb-5">
							<button type="submit" id="kt_login_singin_form_submit_button" class="btn btn-primary font-weight-bolder font-size-h6 px-8 py-4 my-3 mr-3"><?php echo $vLogin_signin ?></button>
							<!--<button type="button" class="btn btn-light-primary font-weight-bolder px-8 py-4 my-3 font-size-lg">
									<span class="svg-icon svg-icon-md">
										
										<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
											<path d="M19.9895 10.1871C19.9895 9.36767 19.9214 8.76973 19.7742 8.14966H10.1992V11.848H15.8195C15.7062 12.7671 15.0943 14.1512 13.7346 15.0813L13.7155 15.2051L16.7429 17.4969L16.9527 17.5174C18.879 15.7789 19.9895 13.221 19.9895 10.1871Z" fill="#4285F4" />
											<path d="M10.1993 19.9313C12.9527 19.9313 15.2643 19.0454 16.9527 17.5174L13.7346 15.0813C12.8734 15.6682 11.7176 16.0779 10.1993 16.0779C7.50243 16.0779 5.21352 14.3395 4.39759 11.9366L4.27799 11.9466L1.13003 14.3273L1.08887 14.4391C2.76588 17.6945 6.21061 19.9313 10.1993 19.9313Z" fill="#34A853" />
											<path d="M4.39748 11.9366C4.18219 11.3166 4.05759 10.6521 4.05759 9.96565C4.05759 9.27909 4.18219 8.61473 4.38615 7.99466L4.38045 7.8626L1.19304 5.44366L1.08875 5.49214C0.397576 6.84305 0.000976562 8.36008 0.000976562 9.96565C0.000976562 11.5712 0.397576 13.0882 1.08875 14.4391L4.39748 11.9366Z" fill="#FBBC05" />
											<path d="M10.1993 3.85336C12.1142 3.85336 13.406 4.66168 14.1425 5.33717L17.0207 2.59107C15.253 0.985496 12.9527 0 10.1993 0C6.2106 0 2.76588 2.23672 1.08887 5.49214L4.38626 7.99466C5.21352 5.59183 7.50242 3.85336 10.1993 3.85336Z" fill="#EB4335" />
										</svg>
									</span><?php //echo $vLogin_signinWithGoole 
											?></button>-->
						</div>
						<!--end::Action-->
						<div class="text-right d-flex justify-content-center">
							<div class="top-signin text-right d-flex justify-content-end pt-5 pb-lg-0 pb-10">
								<span class="font-weight-bold text-muted font-size-h4"><?php echo $vLogin_helpAdv ?></span>
								<a href="javascript:;" class="font-weight-bold text-primary font-size-h4 ml-2" id="kt_login_signup"><?php echo $vLogin_help ?></a>
							</div>
						</div>
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
	</div>
	<!--end::Login-->
</div>