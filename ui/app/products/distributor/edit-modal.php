<!-- Modal-->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content">
            <form method="POST" action="/web/distributor/product/edit" class="modalForm" id="editModalForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalTitle"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <i aria-hidden="true" class="ki ki-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" class="form-control" name="id" id="editProductId">
                    <input type="hidden" name="fnCallback" class="modalValueCallback" id="editProductCallback" value="DistributorProductsDataTable.reloadDatatable" />
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label for="editProductScientificName" class="form-control-label">Scientific Name</label>
                            <select class="select2 form-control" id="editProductScientificName" name="scientificNameId" data-select2-id="editProductScientificName" tabindex="-1" aria-hidden="true">
                                <option disabled>Scientific Name</option>
                            </select>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="editProductCountry" class="form-control-label">Country</label>
                            <select class="select2 form-control" id="editProductCountry" name="madeInCountryId" data-select2-id="editProductCountry" tabindex="-1" aria-hidden="true">
                                <option disabled>Made In Country</option>
                            </select>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="editProductNameAr" class="form-control-label">Product Name Ar</label>
                            <input type="text" class="form-control" name="name_ar" id="editProductNameAr">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="editProductNameEn" class="form-control-label">Product Name En</label>
                            <input type="text" class="form-control" name="name_en" id="editProductNameEn">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="editProductNameFr" class="form-control-label">Product Name Fr</label>
                            <input type="text" class="form-control" name="name_fr" id="editProductNameFr">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="editUnitPrice" class="form-control-label">Unit Price</label>
                            <input type="text" class="form-control" name="unitPrice" id="editUnitPrice">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="editStock" class="form-control-label">Stock</label>
                            <input type="number" class="form-control" name="stock" id="editStock">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="modal-edit-button">
                        <button type="button" class="btn btn-primary font-weight-bold modalAction" id="editModalAction">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>