<style>
	.dropzone-item {
		background-color: #FFF !important; 
	}

	textarea {
		resize: none;
	}
</style>
<!--begin::Entry-->
<div class="d-flex flex-column-fluid">
    <!--begin::Container-->
    <div class="container-fluid">
        <!--begin::Profile-->
        <div class="row">
            <!--begin::Left Side-->
            <div class="col-3">
                <div class="card card-custom card-body card-stretch gutter-b px-0 pt-0" style="height: 550px;">
                    <div style="background-color: #D8D8D8; height: 120px;"></div>   
                    <div class="symbol symbol-150 mt-n30" style="display: flex; justify-content: center;">
                        <img src="/theme/assets/media/users/300_21.jpg" style="border: 4px solid #FFF;"/>
                    </div>
                    <div class="pt-8" style="text-align: center;">
                        <h3 class="text-muted m-0"><?php echo $user->userFullName; ?></h3>
                    </div>
                    <div class="pt-8 pb-15" style="text-align: center;">
                        <a class="btn btn-outline-primary px-10" href="/web/auth/signout"><?php echo $vModule_profile_signout; ?></a>
                    </div>
                    <div id="myProfileButton" class="py-3 pl-20" onclick="Profile.handleMenuChange('myProfile')">
                        <h4><?php echo $vModule_profile_myProfileButton; ?></h4>
                    </div>
                    <div id="accountSettingButton" class="py-3 pl-20" onclick="Profile.handleMenuChange('accountSetting')">
                        <h4><?php echo $vModule_profile_accountSettingButton; ?></h4>
                    </div>
                </div>
            </div>
            <!--begin::Main-->
            <div class="col-9 px-10 py-5">
                <input id="profileEntityType" type="hidden" name="entityType" value="pharmacy"/>
                <div id="myProfileSection">
                    <div class="card-label font-weight-bolder font-size-h1"><?php echo $vModule_profile_myProfileTitle ?></div>
                    <div class="card-label font-size-h5 text-muted"><?php echo $vModule_profile_myProfileSubtitle ?></div>
                    <form class="form pt-5" novalidate="novalidate" id="myProfileForm">
                        <input type="hidden" name="userId" value="<?php echo $user->userId; ?>"/>
                        <div class="row">
                            <!--begin::Form Group-->
                            <div class="col-6 form-group">
                                <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vModule_profile_pharmacyName ?></label>
                                <input type="text" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="entityName" value="<?php echo $user->entityName; ?>" />
                            </div>
                            <!--end::Form Group-->
                            <!--begin::Form Group-->
                            <div class="col-6 form-group">
                                <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vModule_profile_tradeLicenseNumber ?></label>
                                <input type="tel" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="tradeLicenseNumber" value="<?php echo $user->entityBranchTradeLicenseNumber; ?>" />
                            </div>
                            <!--end::Form Group-->
                            <!--begin::Form Group-->
                            <div class="col-6 form-group">
                                <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vModule_profile_country; ?></label>
                                <input disabled type="text" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="country" value="<?php echo $user->entityCountryName; ?>" />
                            </div>
                            <!--end::Form Group-->
                            <!--begin::Form Group-->
                            <div class="col-6 form-group">
                                <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vModule_profile_city; ?></label>
                                <input disabled type="text" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="city" value="<?php echo $user->entityBranchCityName; ?>" />
                            </div>
                            <!--end::Form Group-->
                            <!--begin::Form Group-->
                            <div class="col-12 form-group">
                                <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vModule_profile_address; ?></label>
                                <textarea class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="address" rows="4"><?php echo $user->entityBranchAddress; ?></textarea>
                            </div>
                            <!--end::Form Group-->
                            <!--begin::Form Group-->
                            <div class="col-6 form-group">
                                <input type="hidden" name="entityBranchTradeLicenseUrl" value="<?php echo $user->entityBranchTradeLicenseUrl; ?>"/>
                                <div>
                                    <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vModule_profile_pharmacyTradeLicenseDocument; ?></label>
                                </div>
                                
                                <div class="dropzone dropzone-multi" id="kt_dropzone" style="background-color: unset;">
                                    <div class="dropzone-panel mb-lg-0 mb-2">
                                        <a class="dropzone-select btn btn-light-primary font-weight-bolder font-size-h6 pl-6 pr-8 py-4 my-3 mr-3">
                                            <span class="svg-icon menu-icon">
                                                <!--begin::Svg Icon | path:assets/media/svg/icons/Files/Upload.svg-->
                                                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                                    <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                        <rect x="0" y="0" width="24" height="24"></rect>
                                                        <path d="M2,13 C2,12.5 2.5,12 3,12 C3.5,12 4,12.5 4,13 C4,13.3333333 4,15 4,18 C4,19.1045695 4.8954305,20 6,20 L18,20 C19.1045695,20 20,19.1045695 20,18 L20,13 C20,12.4477153 20.4477153,12 21,12 C21.5522847,12 22,12.4477153 22,13 L22,18 C22,20.209139 20.209139,22 18,22 L6,22 C3.790861,22 2,20.209139 2,18 C2,15 2,13.3333333 2,13 Z" fill="#000000" fill-rule="nonzero" opacity="0.3"></path>
                                                        <rect fill="#000000" opacity="0.3" x="11" y="2" width="2" height="14" rx="1"></rect>
                                                        <path d="M12.0362375,3.37797611 L7.70710678,7.70710678 C7.31658249,8.09763107 6.68341751,8.09763107 6.29289322,7.70710678 C5.90236893,7.31658249 5.90236893,6.68341751 6.29289322,6.29289322 L11.2928932,1.29289322 C11.6689749,0.916811528 12.2736364,0.900910387 12.6689647,1.25670585 L17.6689647,5.75670585 C18.0794748,6.12616487 18.1127532,6.75845471 17.7432941,7.16896473 C17.3738351,7.57947475 16.7415453,7.61275317 16.3310353,7.24329415 L12.0362375,3.37797611 Z" fill="#000000" fill-rule="nonzero"></path>
                                                    </g>
                                                </svg>
                                                <!--end::Svg Icon-->
                                            </span><?php echo $vModule_profile_uploadButton; ?>
                                        </a>
                                    </div>
                                    <div class="dropzone-items">
                                        <div class="dropzone-item" style="display:none">
                                            <div class="dropzone-file">
                                                <a class="dropzone-filename" id="dropzoneFilename">
                                                    <span data-dz-name="">some_image_file_name.jpg</span>
                                                    <strong>(<span data-dz-size="">340kb</span>)</strong>
                                                </a>
                                                <div class="dropzone-error" data-dz-errormessage=""></div>
                                            </div>
                                            <div class="dropzone-progress">
                                                <div class="progress">
                                                    <div class="progress-bar bg-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress=""></div>
                                                </div>
                                            </div>
                                            <div class="dropzone-toolbar">
                                                <span class="dropzone-delete" data-dz-remove="">
                                                    <i class="flaticon2-cross"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5">
                                    <div class="font-size-h6 font-weight-bolder text-muted">Max file size is 10mb and </div>
                                    <div class="font-size-h6 font-weight-bolder text-muted">File types allowed are .pdf, .ppt, .xcl, .docx, .jpeg, .jpg, .png </div>
                                </div>
                            </div>
                            <!--end::Form Group-->
                        </div>
                        <!--begin::Save Button-->
                        <div>        
                            <a class="btn btn-primary font-weight-bolder font-size-h5 pl-12 pr-12 py-4 my-3 mr-3" onclick="Profile.savePharmacyMyProfile();">
                                <?php echo $vModule_profile_saveButton; ?>
                            </a>
                        </div>
                        <!--end::Save Button-->
                    </form>
                </div>
                <div id="accountSettingSection" style="display: none;">
                    <div class="card-label font-weight-bolder font-size-h1"><?php echo $vModule_profile_accountSettingTitle ?></div>
                    <div class="card-label font-size-h5 text-muted"><?php echo $vModule_profile_accountSettingSubtitle ?></div>
                    <form class="form pt-5" novalidate="novalidate" id="accountSettingForm">
                        <input type="hidden" name="userId" value="<?php echo $user->userId; ?>"/>
                        <div class="row">
                            <!--begin::Form Group-->
                            <div class="col-6 form-group">
                                <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vModule_profile_email ?></label>
                                <input disabled type="text" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="email" value="<?php echo $user->userEmail; ?>" />
                            </div>
                            <!--end::Form Group-->
                            <!--begin::Form Group-->
                            <div class="col-6 form-group">
                                <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vModule_profile_mobile ?></label>
                                <input disabled type="tel" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="mobile" value="<?php echo $user->userMobile; ?>" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 form-group">
                                <p class="card-label font-size-h4 text-muted"><?php echo $vModule_profile_changePasswordTitle ?></p>
                                <div style="border: 1px solid #CCCCDB;"></div>
                            </div>
                        </div>
                        <div class="row">
                            <!--begin::Form Group-->
                            <div class="col-6 form-group">
                                <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vModule_profile_currentPassword ?></label>
                                <input type="password" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="oldPassword" />
                            </div>
                            <!--end::Form Group-->
                        </div>
                        <div class="row">
                            <!--begin::Form Group-->
                            <div class="col-6 form-group">
                                <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vModule_profile_newPassword ?></label>
                                <input type="password" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="newPassword" />
                            </div>
                            <!--end::Form Group-->
                        </div>
                        <div class="row">
                            <!--begin::Form Group-->
                            <div class="col-6 form-group">
                                <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vModule_profile_confirmNewPassword ?></label>
                                <input type="password" class="form-control h-auto py-7 px-6 border-0 rounded-lg font-size-h6" name="newPasswordConfirmation" />
                            </div>
                            <!--end::Form Group-->
                        </div>
                        <!--begin::Save Button-->
                        <div>        
                            <a class="btn btn-primary font-weight-bolder font-size-h5 pl-12 pr-12 py-4 my-3 mr-3" onclick="Profile.savePharmacyAccountSetting();">
                                <?php echo $vModule_profile_saveButton; ?>
                            </a>
                        </div>
                        <!--end::Save Button-->
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        Profile.init();
    })
</script>