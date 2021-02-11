<style>
    .dropzone-item {
        background-color: #FFF !important;
    }

    textarea {
        resize: none;
    }

    .mfp-iframe-holder .mfp-content {
        max-width: 100% !important;
        height: 100%;
    }

    .mfp-iframe-holder {
        padding-top: 40px !important;
        padding-bottom: 10px !important;
    }

    .checkbox-inline .checkbox {
        margin-right: 0 !important;
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
                    <div class="d-flex justify-content-center mt-n20" id="profile-image-form">
                        <div class="image-input image-input-empty image-input-outline" id="profile-image" style="background-color:#fff">
                            <div class="image-input-wrapper" style="background-image: url(<?php echo $objUser->entityImage ?? '/assets/img/profile.png' ?>);background-size: contain;background-position: center;"></div>

                            <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change avatar">
                                <i class="fa fa-pen icon-sm text-muted"></i>
                                <input type="file" name="profile_avatar" accept=".png, .jpg, .jpeg" />
                                <input type="hidden" name="profile_avatar_remove" />
                            </label>

                            <span class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="remove" data-toggle="tooltip" title="Remove avatar">
                                <i class="ki ki-bold-close icon-xs text-muted"></i>
                            </span>
                        </div>
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
                    <div id="paymentSettingButton" class="py-3 pl-20" onclick="Profile.handleMenuChange('paymentSetting')">
                        <h4><?php echo $vModule_profile_paymentSettingButton; ?></h4>
                    </div>
                </div>
            </div>
            <!--begin::Main-->
            <div class="col-9 px-10 py-5">
                <input id="profileEntityType" type="hidden" name="entityType" value="distributor" />
                <div id="myProfileSection">
                    <div class="card-label font-weight-bolder font-size-h1"><?php echo $vModule_profile_myProfileTitle ?></div>
                    <div class="card-label font-size-h5 text-muted"><?php echo $vModule_profile_myProfileSubtitle ?></div>
                    <form class="form pt-5" novalidate="novalidate" id="myProfileForm">
                        <input type="hidden" name="userId" value="<?php echo $user->userId; ?>" />
                        <div class="row">
                            <!--begin::Form Group-->
                            <div class="col-6 form-group">
                                <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vModule_profile_distributorName ?></label>
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
                                <input type="hidden" name="entityBranchTradeLicenseUrl" value="<?php echo $user->entityBranchTradeLicenseUrl; ?>" />
                                <input type="hidden" name="entityBranchTradeLicenseUrlDecoded" value="<?php echo $entityBranchTradeLicenseUrlDecoded; ?>" />
                                <div>
                                    <label class="font-size-h6 font-weight-bolder text-dark"><?php echo $vModule_profile_distributorTradeLicenseDocument; ?></label>
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
                                            </span><?php echo $user->entityBranchTradeLicenseUrl == null ? $vModule_profile_uploadButton : $vModule_profile_uploadReplaceButton; ?>
                                        </a>
                                    </div>
                                    <div class="dropzone-items">
                                        <div class="dropzone-item" style="display:none">
                                            <div class="dropzone-file">
                                                <a class="dropzone-filename" id="dropzoneFilename">
                                                    <span data-dz-name="">some_image_file_name.jpg</span>
                                                    <strong id="dropzoneFilesize" style="display: none;">(<span data-dz-size="">340kb</span>)</strong>
                                                    <img id="dropzoneFilenameImage" src="" style="width:300px;height:200px; object-fit: cover;">
                                                </a>
                                                <div class="dropzone-error" data-dz-errormessage=""></div>
                                            </div>
                                            <div class="dropzone-progress">
                                                <div class="progress">
                                                    <div class="progress-bar bg-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" data-dz-uploadprogress=""></div>
                                                </div>
                                            </div>
                                            <?php if ($user->entityBranchTradeLicenseUrl == null) { ?>
                                                <div class="dropzone-toolbar">
                                                    <span class="dropzone-delete" data-dz-remove="">
                                                        <i class="flaticon2-cross"></i>
                                                    </span>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-5">
                                    <div class="font-size-h6 font-weight-bolder text-muted">Max file size is 10mb and </div>
                                    <div class="font-size-h6 font-weight-bolder text-muted">File types allowed are .pdf, .ppt, .docx, .jpeg, .jpg, .png </div>
                                </div>
                            </div>
                            <!--end::Form Group-->
                        </div>
                        <!--begin::Save Button-->
                        <div>
                            <a class="btn btn-primary font-weight-bolder font-size-h5 pl-12 pr-12 py-4 my-3 mr-3" onclick="Profile.saveDistributorMyProfile();">
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
                        <input type="hidden" name="userId" value="<?php echo $user->userId; ?>" />
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
                            <a class="btn btn-primary font-weight-bolder font-size-h5 pl-12 pr-12 py-4 my-3 mr-3" onclick="Profile.saveDistributorAccountSetting();">
                                <?php echo $vModule_profile_saveButton; ?>
                            </a>
                        </div>
                        <!--end::Save Button-->
                    </form>
                </div>
                <div id="paymentSettingSection" style="display: none;">
                    <div class="card-label font-weight-bolder font-size-h1"><?php echo $vModule_profile_paymentSettingTitle ?></div>
                    <form class="form pt-5" novalidate="novalidate" id="paymentSettingForm">
                        <input type="hidden" name="userId" value="<?php echo $user->userId; ?>" />
                        <input type="hidden" name="countryId" value="<?php echo $user->entityCountryId; ?>" />
                        <div class="row">
                            <div class="col-12 form-group">
                                <p class="card-label font-size-h4"><?php echo $vModule_profile_paymentOptionTitle ?></p>
                                <div id="paymentMethodContainer" class="row checkbox-inline my-5">
                                    <?php foreach ($arrPaymentMethod as $paymentMethod) : ?>
                                        <label class="col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3 checkbox checkbox-outline checkbox-dark">

                                            <input type="checkbox" name="paymentMethodCheckbox" value="<?php echo $paymentMethod['id']; ?>" <?php echo in_array($paymentMethod['id'], $arrEntityPaymentMethodId) ? 'checked' : '' ?> />
                                            <span style="background-color: white; border: unset;"></span>
                                            <?php echo $paymentMethod['name']; ?>
                                        </label>
                                    <?php endforeach; ?>
                                </div>
                                <div style="border-bottom: 1px solid #333333;"></div>
                            </div>
                        </div>
                        <div class="py-5">
                            <p class="card-label font-size-h4"><?php echo $vModule_profile_minimumValueOrderTitle ?></p>
                            <div id="minimumValueOrderRepeater">
                                <div class="row">
                                    <div id="minimumValueOrderList" data-repeater-list="minimumValueOrderList" data-repeaterdata='<?php echo json_encode($arrEntityMinimumValueOrderGrouped); ?>' class="col-lg-12">
                                        <div data-repeater-item="" class="form-group row align-items-start">
                                            <input type="hidden" id="minimumValueOrderId" name="id" class="form-control">
                                            <div class="col-md-4">
                                                <input type="number" id="minimumValueOrder" name="minimumValueOrder" class="form-control minimumValueOrderInput" placeholder="<?php echo $vModule_profile_minimumValueOrder ?>" min="0" pattern="^\d*(\.\d{0,2})?$" step="0.01" onchange="this.value = this.value > 0? parseFloat(this.value).toFixed(2) : !this.value? this.value : 0;">
                                                <div class="d-md-none mb-2"></div>
                                            </div>
                                            <div class="col-md-4">
                                                <select id="minimumValueOrderCityId" name="city" class="form-control selectpicker" title="<?php echo $vModule_profile_city; ?>" data-live-search="true" multiple>
                                                </select>
                                                <div class="d-md-none mb-2"></div>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="javascript:;" id="minimumValueOrderDelete" data-repeater-delete="" class="btn btn-sm font-weight-bolder btn-light-danger">
                                                    <i class="la la-trash-o"></i><?php echo $vButton_delete; ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <a href="javascript:;" id="minimumValueOrderAdd" data-repeater-create="" class="btn btn-sm font-weight-bolder btn-light-primary">
                                            <i class="la la-plus"></i><?php echo $vButton_add; ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--begin::Save Button-->
                        <div>
                            <a class="btn btn-primary font-weight-bolder font-size-h5 pl-12 pr-12 py-4 my-3 mr-3" onclick="Profile.saveDistributorPaymentSetting();">
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