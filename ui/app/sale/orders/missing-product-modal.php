<!-- Modal-->
<div class="modal fade" id="missingProductModal" tabindex="-1" role="dialog" aria-labelledby="missingProductModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" action="/web/pharmacy/order/missingProducts" class="modalForm" id="missingProductModalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="missingProductModalTitle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control" name="orderId" id="missingProductOrderId">
                    <input type="hidden" name="fnCallback" class="modalValueCallback" id="editQuantityProductCallback" value="WebApp.reloadDatatable" />


                    <div class="row">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row form-group">
                                    <div class="col-md-6 form-label" id="missingProductModalCustomerNameLabel"></div>
                                    <div class="col-md-6 form-text" id="missingProductModalCustomerNameText"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row form-group">
                                    <div class="col-md-6 form-label" id="missingProductModalStatusLabel"></div>
                                    <div class="col-md-6 form-text" id="missingProductModalStatusText"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row form-group">
                                    <div class="col-md-6 form-label" id="missingProductModalTotalLabel"></div>
                                    <div class="col-md-6 form-text" id="missingProductModalTotalText"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row form-group">
                                    <div class="col-md-6 form-label" id="missingProductModalDateLabel"></div>
                                    <div class="col-md-6 form-text" id="missingProductModalDateText"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row form-group">
                                    <div class="col-md-6 form-label" id="missingProductModalBranchLabel"></div>
                                    <div class="col-md-6 form-text" id="missingProductModalBranchText"></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row form-group">
                                    <div class="col-md-6 form-label" id="missingProductModalAddressLabel"></div>
                                    <div class="col-md-6 form-text" id="missingProductModalAddressText"></div>
                                </div>
                            </div>

                            <hr>


                        </div>

                        <div class="col-md-12 form-group">
                            <div id="missingProductListRepeater">
                                <div class="row">
                                    <div data-repeater-list="missingProductsRepeater" class="col-lg-12">
                                        <div data-repeater-item="" class="form-group row align-items-end">
                                            <!--                                            <input type="hidden" id="missingProductId" name="productId" class="form-control">-->
                                            <div class="col-md-4">
                                                <label>Product:</label>
                                                <select
                                                    class="select2 form-control"
                                                    name="productId"
                                                    tabindex="-1"
                                                >
                                                </select>
                                                <div class="d-md-none mb-2"></div>
                                            </div>
                                            <div class="col-md-4">
                                                <label>Quantity:</label>
                                                <input type="number" min="1" max="100000" id="missingProductQuantity" name="quantity" class="form-control missingProductQuantity" placeholder="Enter Missing Products Number">
                                                <div class="d-md-none mb-2"></div>
                                            </div>
                                            <div class="col-md-2">
                                                <a href="javascript:;" id="missingProductDelete" data-repeater-delete="" class="btn btn-sm font-weight-bolder btn-light-danger">
                                                    <i class="la la-trash-o"></i>Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <a href="javascript:;" id="missingProductAdd" data-repeater-create="" class="btn btn-sm font-weight-bolder btn-light-primary">
                                            <i class="la la-plus"></i>Add</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="modal-editQuantity-button">
                        <button type="button" class="btn btn-primary font-weight-bold modalAction" id="missingProductModalAction">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    var $repeater;
    var $products;

    $repeater = $('#missingProductListRepeater').repeater({
        isFirstItemUndeletable: true,
        show: function () {
            $(this).slideDown();
            validateInput(this);
            initSelect2(this);
        },
        hide: function (deleteElement) {
            if (confirm('Are you sure you want to delete this element?')) {
                $(this).slideUp(deleteElement);
            }
        },
    });

    function initSelect2(input) {
        $(input).find('.select2').select2({
            placeholder: "<?php echo $vModule_search_productPlaceholder ?>",
            data: $products,
        });
    }

    function validateInput(input) {
        $(input).find('.missingProductQuantity').keydown(function () {
            // Save old value.
            if (!$(this).val() || (parseInt($(this).val()) <= $(this).attr('max') && parseInt($(this).val()) >= $(this).attr('min')))
                $(this).data("old", $(this).val());
        });
        $(input).find('.missingProductQuantity').keyup(function () {
            // Check correct, else revert back to old value.
            if (!$(this).val() || (parseInt($(this).val()) <= $(this).attr('max') && parseInt($(this).val()) >= $(this).attr('min')))
                ;
            else
                $(this).val($(this).data("old"));
        });
    }

</script>