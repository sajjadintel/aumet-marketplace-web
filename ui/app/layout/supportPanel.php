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
                            <input name="supportEmail" type="email" value="<?php echo $isAuth ? $_SESSION['objUser']->email : '' ?>" class="form-control" placeholder="john@example.com" <?php echo $isAuth ? 'disabled' : '' ?> />
                        </div>

                        <div class="form-group">
                            <label class="support-modal-text"><?php echo $vSupport_telephone ?></label>
                            <input name="supportPhone" type="number" value="<?php echo $isAuth ? $_SESSION['objUser']->mobile : '' ?>" class="form-control" placeholder="eg 00977442424242" />
                        </div>

                        <div class="form-group row" style="margin-bottom: 0px;">
                            <label class="col-5 col-form-label support-modal-text"> <?php echo $vSupport_request_call ?>: </label>
                            <div class="col-3">
                                    <span class="switch switch-icon">
                                        <label>
                                            <input type="checkbox"  name="requestCall" id="requestCall" />
                                            <span></span>
                                        </label>
                                    </span>
                            </div>
                        </div>

                        <div class="form-group support-modal-select">
                            <label class="col-form-label text-right support-modal-text"><?php echo $vSupport_reason ?></label>
                            <div class="">
                                <select class="form-control select2" id="supportReason" name="supportReasonId">
                                    <option selected></option>
                                    <?php foreach ($supportReasons as $supportReason) { ?>
                                        <option value="<?php echo $supportReason['id'] ?>"><?php echo $supportReason['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <?PHP if(isset($supportCustomers) && sizeof($supportCustomers) > 0){?>
                        <div class="form-group support-modal-select">
                            <label class="col-form-label text-right support-modal-text"><?php echo $vSupport_customer ?></label>
                            <div class="">
                                <select class="form-control select2" id="supportCustomer" name="supportCustomer">
                                    <option selected></option>
                                    <?php foreach ($supportCustomers as $supportCustomer) { ?>
                                        <option value="<?php echo $supportCustomer['entityBuyerId']; ?>"><?php echo $supportCustomer['buyerName']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <?PHP }?>
                        <?PHP if(isset($supportOrders) && sizeof($supportOrders) > 0){?>
                        <div class="form-group support-modal-select">
                            <label class="col-form-label text-right support-modal-text"><?php echo $vSupport_order ?></label>
                            <div class="">
                                <select class="form-control select2" id="supportOrder" name="supportOrder">
                                    <option selected></option>
                                    <?php foreach ($supportOrders as $supportOrder) {
                                        $user_name = (Helper::isPharmacy($objUser->roleId)) ? $supportOrder['entitySeller'] : $supportOrder['userBuyer'];
                                        ?>
                                        <option value="<?php echo $supportOrder['id'];?>"><?php echo $supportOrder['id'] ." : ". $user_name; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <?PHP }?>

                        <div class="form-group">
                            <label class="col-form-label text-right support-modal-text"><?php echo $vSupport_message ?></label>
                            <textarea class="form-control h-auto py-7 px-6 rounded-lg" name="message" placeholder="<?php echo $vSupport_message ?>" value="" style="direction: ltr;" rows="3"></textarea>
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
    $(document).ready(function () {
        $('#supportReason').select2({
            placeholder: 'Please Select',
            minimumResultsForSearch: -1,
        });

        $('#supportCustomer').select2({
            placeholder: 'Please Select',
            minimumResultsForSearch: -1,
        });

        $('#supportOrder').select2({
            placeholder: 'Please Select',
            minimumResultsForSearch: -1,
        });
        WebApp.initSupportModalForm();
    });
</script>
