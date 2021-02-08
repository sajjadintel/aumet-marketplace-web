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
				<!--begin::Thankyou-->
				<div id="main" class="login-form login-form-signup">
					<div class="pb-10 pb-lg-15">
						<h3 class="font-weight-bolder text-dark display5"><?php echo $vSignup_isApprovedTitle ?></h3>
						<div class="text-dark-50 font-weight-bold font-size-h4"><?php echo htmlspecialchars_decode($vSignup_isApprovedSubtitle); ?></div>

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
			</div>
			<!--end::Aside Top-->
			<!--begin::Aside Bottom-->
			<div class="aside-img-wizard d-flex flex-row-fluid bgi-no-repeat pt-2 pt-lg-5" style="background-position: center; background-size: 70%; background-image: url(/assets/img/registration-verified.png)"></div>
			<!--end::Aside Bottom-->
		</div>
		<!--begin::Aside-->
	</div>
	<!--end::Login-->
</div>