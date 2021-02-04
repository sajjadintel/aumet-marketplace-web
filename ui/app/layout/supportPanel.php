<div class="modal" id="support_modal">
    <div class="modal-dialog chat-modal-class-document" role="document">
        <div class="modal-content">
            <!--begin::Card-->
            <div class="card card-custom">
                <!--begin::Header-->
                <div class="card-header align-items-center px-4 py-3">
                    <div class=" flex-grow-1">
                        <div class="text-dark-75 font-weight-bold font-size-h5 support-modal-title"><?php echo $vSupport_customerSupport ?></div>
                    </div>
                    <div class="text-right flex-grow-1">
                        <button type="button" class="btn btn-clean btn-sm btn-icon btn-icon-md" data-dismiss="modal">
                            <i class="ki ki-close icon-1x"></i>
                        </button>
                    </div>
                </div>
                <!--end::Header-->
                <form method="POST" action="/web/notification/support" class="modalForm" id="supportModalForm">
                    <!--begin::Body-->
                    <div class="card-body">
                        <div class="form-group">
                            <label class="support-modal-text"><?php echo $vSupport_email ?></label>
                            <input name="supportEmail" type="email" value="<?php echo $isAuth ? $_SESSION['objUser']->email : '' ?>" class="form-control" placeholder="jogndoe@gmail.com" <?php echo $isAuth ? 'disabled' : '' ?> />
                        </div>

                        <div class="form-group">
                            <label class="support-modal-text"><?php echo $vSupport_telephone ?></label>
                            <input name="phone" type="number" class="form-control" placeholder="eg 00977442424242" />
                        </div>

<!--                        <div>-->
<!--                            <label class="checkbox checkbox-disabled">-->
<!--                                <input type="checkbox" disabled="disabled" checked="checked" name="typeId" required />-->
<!--                                <span class="support-modal-request-call-icon"></span>-->
<!--                                --><?php //echo $vSupport_request_call ?>
<!--                            </label>-->
<!--                        </div>-->

                        <div class="form-group support-modal-select">
                            <label class="col-form-label text-right support-modal-text"><?php echo $vSupport_title ?></label>
                            <div class="">
                                <select class="form-control select2" id="supportReason" name="supportReasonId">
                                    <option selected></option>
                                    <?php foreach ($supportReasons as $supportReason) { ?>
                                        <option value="<?php echo $supportReason['id'] ?>"><?php echo $supportReason['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!--end::Body-->
                    <!--begin::Footer-->
                    <div class="card-footer align-items-center">
                        <!--begin::Compose-->
                        <div class="d-flex align-items-center justify-content-end">
                            <button type="submit" id="kt_cs_form_submit_button" class="btn btn-primary btn-lg text-uppercase font-weight-bold chat-send py-2 px-6"><?php echo $vSupport_submit ?></button>
                        </div>
                        <!--begin::Compose-->
                    </div>
                    <!--end::Footer-->
                </form>
            </div>
            <!--end::Card-->
        </div>
    </div>
</div>

<script>

    var _supportModalForm = function () {
        var _buttonSpinnerClasses = 'spinner spinner-right spinner-white pr-15';
        var form = KTUtil.getById('supportModalForm');
        var formSubmitUrl = KTUtil.attr(form, 'action');
        var data = $(form).serializeJSON();
        var formSubmitButton = KTUtil.getById('kt_cs_form_submit_button');

        if (!form) {
            return;
        }

        FormValidation.formValidation(form, {
            fields: {
                supportEmail: {
                    validators: {
                        notEmpty: {
                            message: 'Email is required',
                        },
                        emailAddress: {
                            message: 'The value is not a valid email address',
                        },
                    },
                },
                phone: {
                    validators: {
                        notEmpty: {
                            message: 'Phone Number is required',
                        },
                    },
                },
                supportReasonId: {
                    validators: {
                        notEmpty: {
                            message: 'Reason is required',
                        },
                    },
                },
            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                submitButton: new FormValidation.plugins.SubmitButton(),
                //defaultSubmit: new FormValidation.plugins.DefaultSubmit(), // Uncomment this line to enable normal button submit after form validation
                bootstrap: new FormValidation.plugins.Bootstrap({
                    //	eleInvalidClass: '', // Repace with uncomment to hide bootstrap validation icons
                    //	eleValidClass: '',   // Repace with uncomment to hide bootstrap validation icons
                }),
            },
        })
            .on('core.form.valid', function () {
                // Show loading state on button
                KTUtil.btnWait(formSubmitButton, _buttonSpinnerClasses, 'Please wait');

                var url = KTUtil.attr(form, 'action');
                var data = $(form).serializeJSON();

                if (!form) {
                    console.log('No Form');
                    return;
                }
                $('#support_modal').modal('hide');
                WebApp.post(url, data);
            })
            .on('core.form.invalid', function () {
                Swal.fire({
                    text: 'Sorry, looks like there are some errors detected, please try again.',
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'Ok, got it!',
                    customClass: {
                        confirmButton: 'btn font-weight-bold btn-light-primary',
                    },
                }).then(function () {
                    KTUtil.scrollTop();
                });
            });
    };

    jQuery(document).ready(function () {
        $('#supportReason').select2({
            placeholder: 'Please Select',
            minimumResultsForSearch: -1,
        });

        _supportModalForm();
    });
</script>
