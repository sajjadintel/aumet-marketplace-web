'use strict';
// Class definition
var DistributorProductsDataTable = (function () {

    var repeater;

    var mandatoryFields = [
        "scientificNameId",
        "madeInCountryId",
        "name_ar",
        "name_en",
        "name_fr",
        "description_ar",
        "description_en",
        "description_fr",
        "unitPrice",
        "maximumOrderQuantity",
        "categoryId",
        "subcategoryId",
    ];

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
        $('#editProductNameAr').val(webResponse.data.product.productName_ar);
        $('#editProductNameEn').val(webResponse.data.product.productName_en);
        $('#editProductNameFr').val(webResponse.data.product.productName_fr);
        $('#editUnitPrice').val(webResponse.data.product.unitPrice);
        $('#editMaximumOrderQuantity').val(webResponse.data.product.maximumOrderQuantity);
        $('#editProductSubtitleAr').val(webResponse.data.product.subtitle_ar);
        $('#editProductSubtitleEn').val(webResponse.data.product.subtitle_en);
        $('#editProductSubtitleFr').val(webResponse.data.product.subtitle_fr);
        $('#editProductDescriptionAr').val(webResponse.data.product.description_ar);
        $('#editProductDescriptionEn').val(webResponse.data.product.description_en);
        $('#editProductDescriptionFr').val(webResponse.data.product.description_fr);
        $('#editProductManufacturerName').val(webResponse.data.product.manufacturerName);
        $('#editProductBatchNumber').val(webResponse.data.product.batchNumber);
        $('#editProductItemCode').val(webResponse.data.product.itemCode);
        $('#editProductExpiryDate').val(webResponse.data.product.productExpiryDate);
        $('#editProductStrength').val(webResponse.data.product.strength);

        $('#editProductScientificName').empty();
        $('#editProductScientificName').append(new Option(webResponse.data.product.scientificName, webResponse.data.product.scientificNameId));
        $('#editProductScientificName').val(webResponse.data.product.scientificNameId);

        $('#editProductCountry').empty();
        $('#editProductCountry').append(new Option(webResponse.data.product['madeInCountryName_' + docLang], webResponse.data.product.madeInCountryId));
        $('#editProductCountry').val(webResponse.data.product.madeInCountryId);

        $('#editProductCategory').empty();
        $('#editProductCategory').append(new Option(webResponse.data.product['productCategoryName_' + docLang], webResponse.data.product.productCategoryId));
        $('#editProductCategory').val(webResponse.data.product.productCategoryId);
        $('#editProductCategory').on("change", () => _updateSubcategorySelect("edit"));

        $('#editProductSubcategory').empty();
        $('#editProductSubcategory').append(new Option(webResponse.data.product['productSubcategoryName_' + docLang], webResponse.data.product.productSubcategoryId));
        $('#editProductSubcategory').val(webResponse.data.product.productSubcategoryId);

        var allActiveIngredients = webResponse.data.activeIngredients || [];
        var allActiveIngredientsId = [];

        $('#editActiveIngredients').empty();
        allActiveIngredients.forEach((activeIngredient) => {
            $('#editActiveIngredients').append(new Option(activeIngredient['ingredientName_' + docLang], activeIngredient.ingredientId));
            allActiveIngredientsId.push(activeIngredient.ingredientId);
        })
        $('#editActiveIngredients').val(allActiveIngredientsId);
        $('#editActiveIngredientsVal').val(allActiveIngredientsId);

        $('#editActiveIngredients').on("change", (ev) => _updateActiveIngredientsVal("edit"));

        _changeImageHolder(webResponse.data.product.image, "edit");
        $('#editProductImage').on("change", (ev) => _changeProductImage(ev, "edit"));

        $('#editModal').appendTo('body').modal('show');
        _addModalValidation('edit');
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

        repeater = $('#editQuantityBonusListRepeater').repeater({
            isFirstItemUndeletable: true,
            show: function () {
                $(this).slideDown();
            },
            hide: function (deleteElement) {
                if (confirm('Are you sure you want to delete this element?')) {
                    $(this).slideUp(deleteElement);
                }
            },
        });

        repeater.setList(webResponse.data.bonus);


        $('#editQuantityModal').appendTo('body').modal('show');
    };

    var _productAddModalOpen = function () {

        _clearAddModal();

        $('#addModalForm').attr('action', '/web/distributor/product/add');

        $("#addProductCategory").on("change", () => _updateSubcategorySelect("add"));

        _changeImageHolder('', "add");
        $('#addProductImage').on("change", (ev) => _changeProductImage(ev, "add"));

        $('#addActiveIngredients').on("change", (ev) => _updateActiveIngredientsVal("add"));

        $('#addModal').appendTo('body').modal('show');
        _addModalValidation('add');
    };

    var _clearAddModal = function () {
        $('#addProductImage').val('');
        $('#addProductImageInput').val('');
        $('#addProductScientificName').val('').change();
        $('#addProductCountry').val('').change();
        $('#addProductNameAr').val('');
        $('#addProductNameEn').val('');
        $('#addProductNameFr').val('');
        $('#addProductSubtitleAr').val('');
        $('#addProductSubtitleEn').val('');
        $('#addProductSubtitleFr').val('');
        $('#addProductDescriptionAr').val('');
        $('#addProductDescriptionEn').val('');
        $('#addProductDescriptionFr').val('');
        $('#addUnitPrice').val('');
        $('#addStock').val('');
        $('#addMaximumOrderQuantity').val('');
        $('#addProductManufacturerName').val('');
        $('#addProductBatchNumber').val('');
        $('#addProductItemCode').val('');
        $('#addProductCategory').val('').change();
        $('#addActiveIngredients').val('').change();
        $('#addProductExpiryDate').val('');
        $('#addProductStrength').val('');
        $('#addProductSubcategory').val('').change();
        $('#addProductImageHolder').val('');
        $('#addProductExpiryDate').val('');
        $('#editProductExpiryDate').val('');
    }

    var _productImageUpload = function (webResponse, mode) {
        _changeImageHolder(webResponse.data, mode);
    }

    var _changeImageHolder = function (image, mode) {
        let backgroundImageVal = "/theme/assets/media/users/blank.png";
        if (image) {
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
        var _mandatoryFields = [...mandatoryFields];
        if (mode === "add") _mandatoryFields.push("stock");

        var validatorFields = {};
        _mandatoryFields.forEach((field) => {
            validatorFields[field] = {
                validators: {
                    notEmpty: {
                        message: WebAppLocals.getMessage('required')
                    }
                }
            }
        })
        $("#" + mode + "ModalAction").attr("data-modalValidatorFields", JSON.stringify(validatorFields));
    }

    var _updateSubcategorySelect = function (mode) {
        var categoryId = $("#" + mode + "ProductCategory").val();
        $("#" + mode + "ProductSubcategory").empty();
        $("#" + mode + "ProductSubcategory").select2({
            placeholder: WebAppLocals.getMessage("subcategory"),

            ajax: {
                url: function () {
                    var _url = '/web/product/subcategory/list/';
                    _url += $("#" + mode + "ProductCategory").val();
                    return _url;
                },
                dataType: 'json',
                processResults: function (response) {
                    return {
                        results: response.data.results,
                        pagination: {
                            more: response.data.pagination
                        }
                    }
                }
            },

            data: []
        });
        $("#" + mode + "ProductSubcategory").prop('disabled', categoryId ? false : true);
    }

    var _updateActiveIngredientsVal = function (mode) {
        $("#" + mode + "ActiveIngredientsVal").val($("#" + mode + "ActiveIngredients").val());
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
