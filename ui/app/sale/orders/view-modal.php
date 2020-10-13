<!-- Modal-->
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog" aria-labelledby="viewModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewModalTitle"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <i aria-hidden="true" class="ki ki-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <a id="modalPrint" target="_blank" href="web/distributor/order/print" class="btn btn-sm btn-primary btn-hover-primary  mr-2 float-left" title="Print Order">
                            <i class="nav-icon la la-print p-0"></i>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <div class="row form-group">
                            <div class="col-md-6 form-label" id="modalCustomerNameLabel"></div>
                            <div class="col-md-6 form-text" id="modalCustomerNameText"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row form-group">
                            <div class="col-md-6 form-label" id="modalStatusLabel"></div>
                            <div class="col-md-6 form-text" id="modalStatusText"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row form-group">
                            <div class="col-md-6 form-label" id="modalTotalLabel"></div>
                            <div class="col-md-6 form-text" id="modalTotalText"></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row form-group">
                            <div class="col-md-6 form-label" id="modalDateLabel"></div>
                            <div class="col-md-6 form-text" id="modalDateText"></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="sub-header" id="modalOrderDetailLabel"></h3>
                    </div>
                    <div class="col-md-12">
                        <!--begin: Datatable-->
                        <div class="datatable datatable-bordered datatable-head-custom" id="kt_datatable_detail"></div>
                        <!--end: Datatable-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>