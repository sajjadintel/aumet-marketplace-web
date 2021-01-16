'use strict';
// Class definition
var DistributorProductsDataTable = (function () {
    // Private functions

    var _productEditModal = function (productId) {
        WebApp.get('/web/distributor/product/' + productId, _productEditModalOpen);
    };

    var _productEditQuantityModal = function (productId) {
        WebApp.get('/web/distributor/product/quantity/' + productId, _productEditQuantityModalOpen);
    };

    var _productAddModal = function () {
        _productAddModalOpen();
    };

    var _productEditModalOpenNew = function (webResponse) {
        $('#genericModalContent').html(webResponse.data);
        $('#genericModal').modal('show');
    };

    var _productEditModalOpen = function (webResponse) {
        $('#editModalForm').attr('action', '/web/distributor/product/edit');
        $('#editProductId').val(webResponse.data.product.productId);

        $('#editModalTitle').html(WebAppLocals.getMessage('edit'));
        $("label[for='editProductScientificName']").text(WebAppLocals.getMessage('productScientificName'));
        $("label[for='editProductCountry']").text(WebAppLocals.getMessage('madeInCountry'));
        $("label[for='editProductNameAr']").text(WebAppLocals.getMessage('productName') + ' AR');
        $("label[for='editProductNameEn']").text(WebAppLocals.getMessage('productName') + ' EN');
        $("label[for='editProductNameFr']").text(WebAppLocals.getMessage('productName') + ' FR');
        $("label[for='editUnitPrice']").text(WebAppLocals.getMessage('unitPrice'));
        $("label[for='editMaximumOrderQuantity']").text(WebAppLocals.getMessage('maximumOrderQuantity'));

        $('#editProductScientificName').append(new Option(webResponse.data.product.scientificName, webResponse.data.product.scientificNameId));
        $('#editProductScientificName').val(webResponse.data.product.scientificNameId);
        $('#editProductCountry').append(new Option(webResponse.data.product['madeInCountryName_' + docLang], webResponse.data.product.madeInCountryId));
        $('#editProductCountry').val(webResponse.data.product.madeInCountryId);
        $('#editProductNameAr').val(webResponse.data.product.productName_ar);
        $('#editProductNameEn').val(webResponse.data.product.productName_en);
        $('#editProductNameFr').val(webResponse.data.product.productName_fr);
        $('#editUnitPrice').val(webResponse.data.product.unitPrice);
        $('#editMaximumOrderQuantity').val(webResponse.data.product.maximumOrderQuantity);
        $('#editModalAction').html(WebAppLocals.getMessage('edit'));
        $('#editModal').appendTo('body').modal('show');

        _changeImageHolder(webResponse.data.product.image, "edit");
        $('#editProductImage').on("change",  (ev) => _changeProductImage(ev, "edit"));

        _addModalValidation('edit');
        _checkModalForm('edit');
    };

    var _productEditQuantityModalOpen = function (webResponse) {
        $('#editQuantityModalForm').attr('action', '/web/distributor/product/editQuantity');
        $('#editQuantityProductId').val(webResponse.data.product.productId);

        $('#editQuantityModalTitle').html(WebAppLocals.getMessage('editQuantity'));

        $("label[for='editQuantityStock']").text(WebAppLocals.getMessage('quantityAvailable'));
        $('#editQuantityStock').val(webResponse.data.product.stock);

        switch (webResponse.data.product.stockStatusId) {
            case 1:
                $('#editQuantityStockAvailability').bootstrapSwitch('state', true);
                $('#editQuantityStockAvailability').bootstrapSwitch('disabled', true);
                break;
            case 2:
                $('#editQuantityStockAvailability').bootstrapSwitch('state', false);
                break;
            case 3:
                $('#editQuantityStockAvailability').bootstrapSwitch('state', true);
                break;
            default:
                break;
        }
        $('#editQuantityStock').on('change paste keyup', function () {
            if ($(this).val() > 0) {
                $('#editQuantityStockAvailability').bootstrapSwitch('disabled', true);
            } else {
                $('#editQuantityStockAvailability').bootstrapSwitch('disabled', false);
            }
        });

        $("label[for='editQuantityBonusMinOrder']").text(WebAppLocals.getMessage('minOrder'));
        $("label[for='editQuantityBonusQuantity']").text(WebAppLocals.getMessage('bonus'));
        $("label[for='editQuantityBonusDelete']").text(WebAppLocals.getMessage('delete'));
        $("label[for='editQuantityBonusAdd']").text(WebAppLocals.getMessage('add'));

        $repeater.setList(webResponse.data.bonus);

        $("label[for='editQuantityBonusType']").text(WebAppLocals.getMessage('bonus'));

        switch (webResponse.data.product.bonusTypeId) {
            case 1:
                $('#editQuantityBonusListRepeater').hide();
                $('#editQuantityBonusType').bootstrapSwitch('state', false);
                break;
            case 2:
                $('#editQuantityBonusListRepeater').show();
                $('#editQuantityBonusType').bootstrapSwitch('state', true);
                break;
            default:
                break;
        }

        $('#editQuantityBonusType')
            .bootstrapSwitch()
            .on('switchChange.bootstrapSwitch', function (event, state) {
                if (state) {
                    $('#editQuantityBonusListRepeater').show();
                } else {
                    $('#editQuantityBonusListRepeater').hide();
                }
            });

        $('#editQuantityModalAction').html(WebAppLocals.getMessage('editQuantity'));
        $('#editQuantityModal').appendTo('body').modal('show');
    };

    var _productAddModalOpen = function () {
        $('#addModalForm').attr('action', '/web/distributor/product/add');

        $('#addModalTitle').html(WebAppLocals.getMessage('add'));
        $("label[for='addProductScientificName']").text(WebAppLocals.getMessage('productScientificName'));
        $("label[for='addProductCountry']").text(WebAppLocals.getMessage('madeInCountry'));
        $("label[for='addProductNameAr']").text(WebAppLocals.getMessage('productName') + ' AR');
        $("label[for='addProductNameEn']").text(WebAppLocals.getMessage('productName') + ' EN');
        $("label[for='addProductNameFr']").text(WebAppLocals.getMessage('productName') + ' FR');
        $("label[for='addUnitPrice']").text(WebAppLocals.getMessage('unitPrice'));
        $("label[for='addStock']").text(WebAppLocals.getMessage('quantityAvailable'));
        $("label[for='addMaximumOrderQuantity']").text(WebAppLocals.getMessage('maximumOrderQuantity'));

        $('#addModalAction').html(WebAppLocals.getMessage('add'));
        $('#addModal').appendTo('body').modal('show');

        _changeImageHolder('', "add");
        $('#addProductImage').on("change", (ev) => _changeProductImage(ev, "add"));

        _addModalValidation('add');
        _checkModalForm('add');
    };

    var _productImageUpload = function (webResponse, mode) {
        _changeImageHolder(webResponse.data, mode);
    }

    var _changeImageHolder = function (image, mode) {
        let backgroundImageVal = "/theme/assets/media/users/blank.png";
        if(image) {
            backgroundImageVal = image;
        }
        $('#' + mode + 'ProductImageHolder').css("background-image", "url(" + backgroundImageVal + ")");
        $('#' + mode + 'ProductImageInput').val(image);
    }

    var _changeProductImage = function (ev, mode) {
        let file = ev.target.files[0];
        let ext = file.type.split("/")[1];

        let imageName = new Date().getTime() + "." + ext;
        let image = new File([file], imageName, {type: file.type});

        let formData = new FormData();
        formData.append('image', image);
        formData.append('imageName', imageName);

        $.ajax({
            url: '/web/distributor/product/image',
            data: formData,
            type: 'POST',
            contentType: false,
            processData: false,
        }).done(function (webResponse) {
            _productImageUpload(webResponse, mode);
        });
    }

    var _addModalValidation = function (mode) {
        $('#' + mode + 'ProductScientificName').on('change', (ev) => _checkModalForm(mode));
        $('#' + mode + 'ProductCountry').on('change', (ev) => _checkModalForm(mode));
        $('#' + mode + 'ProductNameAr').on('change', (ev) => _checkModalForm(mode));
        $('#' + mode + 'ProductNameEn').on('change', (ev) => _checkModalForm(mode));
        $('#' + mode + 'ProductNameFr').on('change', (ev) => _checkModalForm(mode));
        $('#' + mode + 'UnitPrice').on('change', (ev) => _checkModalForm(mode));
        $('#' + mode + 'MaximumOrderQuantity').on('change', (ev) => _checkModalForm(mode));

        if(mode === "add") {
            $('#' + mode + 'Stock').on('change', (ev) => _checkModalForm(mode));
        }
    }

    var _checkModalForm = function (mode) {
        let valid = true;

        let scientificName = $('#' + mode + 'ProductScientificName').val();
        let productCountry = $('#' + mode + 'ProductCountry').val();
        let productNameAr = $('#' + mode + 'ProductNameAr').val();
        let productNameEn = $('#' + mode + 'ProductNameEn').val();
        let productNameFr = $('#' + mode + 'ProductNameFr').val();
        let unitPrice = $('#' + mode + 'UnitPrice').val();
        let maximumOrderQuantity = $('#' + mode + 'MaximumOrderQuantity').val();

        if(!scientificName || !productCountry || !productNameAr || !productNameEn || !productNameFr || !unitPrice || !maximumOrderQuantity) {
            valid = false;
        }

        if(valid && mode === "add") {
            let stock = $('#' + mode + 'Stock').val();
            if(!stock) valid = false;
        }

        $('#' + mode + 'ModalAction').prop("disabled", !valid);
    }

    return {
        // public functions
        reloadDatatable: function () {
            WebApp.reloadDatatable();
        },
        productEditQuantityModal: function (productId) {
            _productEditQuantityModal(productId);
        },
        productEditModal: function (productId) {
            _productEditModal(productId);
        },
        productAddModal: function () {
            _productAddModal();
        },
    };
})();
