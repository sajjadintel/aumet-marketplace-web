<div class="aside-menu-wrapper flex-column-fluid" id="kt_aside_menu_wrapper">
	<!--begin::Menu Container-->
	<div id="kt_aside_menu" class="aside-menu my-4" data-menu-vertical="1" data-menu-scroll="1" data-menu-dropdown-timeout="500">
		<!--begin::Menu Nav-->
		<div id="kt_aside_menu" class="kt-aside-menu " data-ktmenu-vertical="1" data-ktmenu-scroll="1" data-ktmenu-dropdown-timeout="500">
			<ul class="menu-nav ">
				<?php foreach ($_SESSION['mainMenu'] as $menuItem) : ?>

					<?php if ($menuItem['type'] == "menu") : ?>

						<li class="menu-item menu-item-submenu" aria-haspopup="true" data-ktmenu-submenu-toggle="hover" id='kt-menu-item-<?php echo $menuItem['id']; ?>'>
							<a href="javascript:;" class="menu-link menu-toggle">
								<span class="svg-icon menu-icon">
									<?php echo htmlspecialchars_decode($menuItem['svgIcon']); ?>
								</span>
								<span class="menu-text"><?php echo $menuItem["name_" . $_SESSION['userLang']] ?></span>
								<i class="menu-arrow"></i>
							</a>
							<div class="menu-submenu ">
								<i class="menu-arrow"></i>
								<ul class="menu-subnav">
									<li class="menu-item menu-item-parent" aria-haspopup="true">
										<span class="menu-link">
											<span class="menu-text"><?php echo $menuItem["name_" . $_SESSION['userLang']] ?></span>
										</span>
									</li>
									<?php foreach ($menuItem['items'] as $subMenuItem) : ?>

										<li class="menu-item" aria-haspopup="true" id='kt-menu-item-<?php echo $subMenuItem['id']; ?>'>
											<a class="menu-link" href="javascript:;" onclick="WebApp.loadPage('<?php echo $subMenuItem['url']; ?>')">
												<i class="menu-bullet menu-bullet-dot">
													<span></span>
												</i>
												<span class="menu-text"><?php echo $subMenuItem["name_" . $_SESSION['userLang']] ?></span>
											</a>
										</li>

									<?php endforeach; ?>
								</ul>
							</div>
						</li>

					<?php elseif ($menuItem['type'] == "section") : ?>
						<li class="menu-section " id='kt-menu-item-<?php echo $menuItem['id']; ?>'>
							<h4 class="menu-text"><?php echo $menuItem["name_" . $_SESSION['userLang']] ?></h4>
							<i class="menu-icon ki ki-bold-more-hor icon-md"></i>
						</li>
					<?php elseif ($menuItem['type'] == "link") : ?>

						<li class="menu-item" aria-haspopup="true" id='kt-menu-item-<?php echo $menuItem['id']; ?>'>
							<a href="javascript:;" class="menu-link" onclick="WebApp.loadPage('<?php echo $menuItem['url']; ?>')">
								<span class="svg-icon menu-icon">
									<!--begin::Svg Icon | path:assets/media/svg/icons/Design/Layers.svg-->
									<?php echo htmlspecialchars_decode($menuItem['svgIcon']); ?>
									<!--end::Svg Icon-->
								</span>
								<span class="menu-text"><?php echo $menuItem["name_" . $_SESSION['userLang']] ?></span>
							</a>
						</li>
					<?php endif; ?>

				<?php endforeach; ?>
			</ul>
		</div>
		<!--end::Menu Nav-->
	</div>
	<!--end::Menu Container-->
</div>