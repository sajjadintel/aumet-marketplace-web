<div class="topbar">
	<?php if($objUser->menuId !== 1): ?>  <!--If::Check if not distributor-->
	<!--begin::CartCheckout-->
	<div class="topbar-item">
		<div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1" id="kt_quick_cart_toggle" onclick="window.location.href = '/web/cart/checkout'">
			<span class="svg-icon svg-icon-xl svg-icon-primary" style="display: flex; align-items: center;">
				<!--begin::Svg Icon | path:assets/media/svg/icons/Shopping/Cart3.svg-->
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<rect x="0" y="0" width="24" height="24" />
						<path d="M12,4.56204994 L7.76822128,9.6401844 C7.4146572,10.0644613 6.7840925,10.1217854 6.3598156,9.76822128 C5.9355387,9.4146572 5.87821464,8.7840925 6.23177872,8.3598156 L11.2317787,2.3598156 C11.6315738,1.88006147 12.3684262,1.88006147 12.7682213,2.3598156 L17.7682213,8.3598156 C18.1217854,8.7840925 18.0644613,9.4146572 17.6401844,9.76822128 C17.2159075,10.1217854 16.5853428,10.0644613 16.2317787,9.6401844 L12,4.56204994 Z" fill="#000000" fill-rule="nonzero" opacity="0.3" />
						<path d="M3.5,9 L20.5,9 C21.0522847,9 21.5,9.44771525 21.5,10 C21.5,10.132026 21.4738562,10.2627452 21.4230769,10.3846154 L17.7692308,19.1538462 C17.3034221,20.271787 16.2111026,21 15,21 L9,21 C7.78889745,21 6.6965779,20.271787 6.23076923,19.1538462 L2.57692308,10.3846154 C2.36450587,9.87481408 2.60558331,9.28934029 3.11538462,9.07692308 C3.23725479,9.02614384 3.36797398,9 3.5,9 Z M12,17 C13.1045695,17 14,16.1045695 14,15 C14,13.8954305 13.1045695,13 12,13 C10.8954305,13 10,13.8954305 10,15 C10,16.1045695 10.8954305,17 12,17 Z" fill="#000000" />
					</g>
				</svg>
				<!--end::Svg Icon-->
				<span class="label label-danger ml-2" id="cartCount"
					style="<?php if($objUser->cartCount > 0) : ?>display: flex;<?php else : ?>display: none;<?php endif; ?>"
				>
					<?php if($objUser->cartCount > 9) : ?>
						9+
					<?php else : ?>
						<?php echo $objUser->cartCount ?>
					<?php endif; ?>
				</span>
			</span>
		</div>
	</div>
	<!--end::CartCheckout-->
	<?php endif; ?>
    <!--begin::Chat-->
    <?php if (getenv('ENV') == Constants::ENV_LOC) { ?>
        <div class="topbar-item">
            <div class="btn btn-icon btn-clean btn-lg mr-1" data-toggle="modal" data-target="#kt_chat_modal" onclick="dataLayer.push({'event': 'chat_click'});">
			<span class="svg-icon svg-icon-xl svg-icon-primary">
				<!--begin::Svg Icon | path:assets/media/svg/icons/Communication/Group-chat.svg-->
				<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
					<g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
						<rect x="0" y="0" width="24" height="24" />
						<path d="M16,15.6315789 L16,12 C16,10.3431458 14.6568542,9 13,9 L6.16183229,9 L6.16183229,5.52631579 C6.16183229,4.13107011 7.29290239,3 8.68814808,3 L20.4776218,3 C21.8728674,3 23.0039375,4.13107011 23.0039375,5.52631579 L23.0039375,13.1052632 L23.0206157,17.786793 C23.0215995,18.0629336 22.7985408,18.2875874 22.5224001,18.2885711 C22.3891754,18.2890457 22.2612702,18.2363324 22.1670655,18.1421277 L19.6565168,15.6315789 L16,15.6315789 Z" fill="#000000" />
						<path d="M1.98505595,18 L1.98505595,13 C1.98505595,11.8954305 2.88048645,11 3.98505595,11 L11.9850559,11 C13.0896254,11 13.9850559,11.8954305 13.9850559,13 L13.9850559,18 C13.9850559,19.1045695 13.0896254,20 11.9850559,20 L4.10078614,20 L2.85693427,21.1905292 C2.65744295,21.3814685 2.34093638,21.3745358 2.14999706,21.1750444 C2.06092565,21.0819836 2.01120804,20.958136 2.01120804,20.8293182 L2.01120804,18.32426 C1.99400175,18.2187196 1.98505595,18.1104045 1.98505595,18 Z M6.5,14 C6.22385763,14 6,14.2238576 6,14.5 C6,14.7761424 6.22385763,15 6.5,15 L11.5,15 C11.7761424,15 12,14.7761424 12,14.5 C12,14.2238576 11.7761424,14 11.5,14 L6.5,14 Z M9.5,16 C9.22385763,16 9,16.2238576 9,16.5 C9,16.7761424 9.22385763,17 9.5,17 L11.5,17 C11.7761424,17 12,16.7761424 12,16.5 C12,16.2238576 11.7761424,16 11.5,16 L9.5,16 Z" fill="#000000" opacity="0.3" />
					</g>
				</svg>
                <!--end::Svg Icon-->
			</span>
            </div>
        </div>
    <?php } ?>
	<!--end::Chat-->
    <!--begin::Languages-->
    <?php if (getenv('ENV') == Constants::ENV_LOC) { ?>
        <div class="dropdown">
            <!--begin::Toggle-->
            <div class="topbar-item" data-toggle="dropdown" data-offset="10px,0px">
                <div class="btn btn-icon btn-clean btn-dropdown btn-lg mr-1">
                    <img class="h-20px w-20px rounded-sm" src="/theme/assets/media/svg/flags/<?php echo $langActiveFlag ?>" alt="" />
                </div>
            </div>
            <!--end::Toggle-->
            <!--begin::Dropdown-->
            <div class="dropdown-menu p-0 m-0 dropdown-menu-anim-up dropdown-menu-sm dropdown-menu-right">
                <!--begin::Nav-->
                <ul class="navi navi-hover py-4">
                    <!--begin::Item-->
                    <li class="navi-item">
                        <a href="/web/me/switchLanguage/ar" class="navi-link">
						<span class="symbol symbol-20 mr-3">
							<img src="/theme/assets/media/svg/flags/008-saudi-arabia.svg" alt="" />
						</span>
                            <span class="navi-text">العربية</span>
                        </a>
                    </li>
                    <!--end::Item-->
                    <!--begin::Item-->
                    <li class="navi-item">
                        <a href="/web/me/switchLanguage/en" class="navi-link">
						<span class="symbol symbol-20 mr-3">
							<img src="/theme/assets/media/svg/flags/260-united-kingdom.svg" alt="" />
						</span>
                            <span class="navi-text">English</span>
                        </a>
                    </li>
                </ul>
                <!--end::Nav-->
            </div>
            <!--end::Dropdown-->
        </div>
    <?php } ?>
    <!--end::Languages-->
	<!--begin::User-->
	<div class="topbar-item">
		<div class="btn btn-icon btn-icon-mobile w-auto btn-clean d-flex align-items-center btn-lg px-2" id="kt_quick_user_toggle" onclick="$('#kt_chat_modal').modal('hide');">
			<span class="text-muted font-weight-bold font-size-base d-none d-md-inline mr-1"><?php echo "$vGreeting," ?></span>
			<span class="text-dark-50 font-weight-bolder font-size-base d-none d-md-inline mr-3"><?php echo $objUser->fullname ?></span>
			<span class="symbol symbol-lg-35 symbol-25 symbol-light-success">
				<span class="symbol-label font-size-h5 font-weight-bold">...</span>
			</span>
		</div>
	</div>
	<!--end::User-->
</div>