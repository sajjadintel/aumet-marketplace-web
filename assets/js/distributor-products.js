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
        "vat",
        "maximumOrderQuantity",
        "categoryId",
        "subcategoryId",
    ];
    // Structure: field: [minLength, maxLength] 
    var mapFieldStrRangeLength = {
        "name_en": [4, 200],
        "name_ar": [4, 200],
        "name_fr": [4, 200],
        "description_ar": [4, 1000],
        "description_en": [4, 1000],
        "description_fr": [4, 1000],
        "subtitle_ar": [4, 200],
        "subtitle_en": [4, 200],
        "subtitle_fr": [4, 200],
        "manufacturerName": [4, 200],
        "batchNumber": [4, 200],
        "itemCode": [4, 200],
        "strength": [4, 200],
    };
    var mapUuidSubimage = {};
    var imageModal;
    var _validator;
    var _validatorFields = {};

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
        $('#editVat').val(webResponse.data.product.vat);
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

        $('#editActiveIngredients').on('change', (ev) => _updateActiveIngredientsVal('edit'));

        _changeImageHolder(webResponse.data.product.image, 'edit');
        $('#editProductImage').on('change', (ev) => _changeProductImage(ev, 'edit'));

        _initializeSubimagesDropzone('edit', webResponse.data.subimages);
        
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
        $('#addModalForm').attr('action', '/web/distributor/product/add');
        
        $('#addProductCategory').on('change', () => _updateSubcategorySelect('add'));

        _changeImageHolder('', 'add');
        $('#addProductImage').on('change', (ev) => _changeProductImage(ev, 'add'));

        $('#addActiveIngredients').on('change', (ev) => _updateActiveIngredientsVal('add'));
        
        _initializeSubimagesDropzone('add');

        $('#addModal').appendTo('body').modal('show');
        _addModalValidation('add');
    };

    var _productImageUpload = function (webResponse, mode) {
        _changeImageHolder(webResponse.data, mode);
    }

    var _changeImageHolder = function (image, mode) {
        let backgroundImageVal = '/theme/assets/media/users/blank.png';
        if (image) {
            backgroundImageVal = image;
        }
        $('#' + mode + 'ProductImageHolder').css('background-image', 'url(' + backgroundImageVal + ')');
        $('#' + mode + 'ProductImageInput').val(image);
    }

    var _changeProductImage = function (ev, mode) {
        let file = ev.target.files[0];
        let ext = file.type.split('/')[1];

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
        var _mandatoryFields = [ ...mandatoryFields ];
        if(mode === "add") _mandatoryFields.push("stock");

        var allFields = new Set([ ..._mandatoryFields, ...Object.keys(mapFieldStrRangeLength)]);
        console.log("allFields");
        console.log(allFields);
        _validatorFields = {};
        allFields.forEach((field) => {
            var fieldValidators = {};

            if(_mandatoryFields.includes(field)) {
                fieldValidators.notEmpty = {
                    message: WebAppLocals.getMessage('required')
                }
            }

            if(field in mapFieldStrRangeLength) {
                var strRangeLength = mapFieldStrRangeLength[field];
                var message = WebAppLocals.getMessage('lengthError') + " " + strRangeLength[0] + " " + WebAppLocals.getMessage('and') + " " + strRangeLength[1] + " " + WebAppLocals.getMessage('characters');
                fieldValidators.stringLength = {
                    min: strRangeLength[0],
                    max: strRangeLength[1],
                    message: message,
                }
            }

            _validatorFields[field] = {
                validators: fieldValidators
            }
        })
        
        var form = KTUtil.getById(mode + "ModalForm");
        _validator = FormValidation.formValidation(form, {
            fields: _validatorFields,
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                // Bootstrap Framework Integration
                bootstrap: new FormValidation.plugins.Bootstrap({
                    //eleInvalidClass: '',
                    eleValidClass: '',
                }),
            },
        })
    }

    var _updateSubcategorySelect = function (mode) {
        var categoryId = $("#" + mode + "ProductCategory").val();
        $("#" + mode + "ProductSubcategory").empty();
        $("#" + mode + "ProductSubcategory").select2({
            placeholder: WebAppLocals.getMessage("subcategory"),

            ajax: {
                url: function() {
                    var _url = '/web/product/subcategory/list/';
                    _url += $("#" + mode + "ProductCategory").val();
                    return _url;
                },
                dataType: 'json',
                processResults: function(response) {
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
        $("#" + mode + "ProductSubcategory").prop('disabled', categoryId? false : true);
    }

    var _updateActiveIngredientsVal = function (mode) {
        $("#" + mode + "ActiveIngredientsVal").val($("#" + mode + "ActiveIngredients").val());
    }

    var _initializeSubimagesDropzone = function (mode, initialSubimages = []) {
        // Set the dropzone container id
        var id = '#' + mode + 'ProductSubimagesDropzone';
        
        let dropzoneControl = $(id)[0].dropzone;
        if (dropzoneControl) {
            dropzoneControl.destroy();
        }

		// Set the preview element template
		var previewNode = $(id + ' .dropzone-item');
		previewNode.id = '';
		var previewTemplate = previewNode.parent('.dropzone-items').html();
		previewNode.remove();

		var myDropZone = new Dropzone(id, {
			// Make the whole body a dropzone
			url: '/web/distributor/product/subimage', // Set the url for your upload script location
			acceptedFiles: '.jpeg, .jpg, .png',
			maxFilesize: 10, // Max filesize in MB
			maxFiles: 6,
			previewTemplate: previewTemplate,
			previewsContainer: id + ' .dropzone-items', // Define the container to display the previews
			clickable: id + ' .dropzone-select', // Define the element that should be used as click trigger to select files.
		});

		myDropZone.on('addedfile', function (file) {
			// Hookup the start button
            $(document).find(id + ' .dropzone-item').css('display', 'block');
            $("#" + mode + "MaxFilesExceededLabel").hide();
        });

		// Update the total progress bar
		myDropZone.on('totaluploadprogress', function (progress) {
			$(id + ' .progress-bar').css('width', progress + '%');
		});

		myDropZone.on('sending', function (file) {
			// Show the total progress bar when upload starts
			$(id + ' .progress-bar').css('opacity', '1');
		});

		// Hide the total progress bar when nothing's uploading anymore
		myDropZone.on('complete', function (file) {
			var thisProgressBar = id + ' .dz-complete';
			setTimeout(function () {
                $(thisProgressBar + ' .progress-bar, ' + thisProgressBar + ' .progress').css('opacity', '0');
			}, 300);
		});;

		// Add file to the list if success
		myDropZone.on('success', function (file, response) {
            var imageUrl = response;

			mapUuidSubimage[file.upload.uuid] = imageUrl;
            
			var dropzoneFilenameElement = $(file.previewTemplate).find("#dropzoneImage");
			$(dropzoneFilenameElement).css("background-image", 'url("/'+ imageUrl + '")');
			$(dropzoneFilenameElement).css("cursor", "pointer");
			$(dropzoneFilenameElement).click(function() {
                _openImageModal(imageUrl);
            });
		});

		// Remove file from the list
		myDropZone.on('removedfile', function (file) {
            delete mapUuidSubimage[file.upload.uuid];
        });

		myDropZone.on('maxfilesexceeded', function (file) {
            myDropZone.removeFile(file);
            $("#" + mode + "MaxFilesExceededLabel").show();
        });

		if(initialSubimages.length > 0) {
            initialSubimages.forEach((subimageObj) => {
                var subimage = subimageObj.subimage;
                var fileUrl = _getFullUrl(subimage);
                var fileName = fileUrl.substring(fileUrl.lastIndexOf('/') + 1);
                var fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
                var fileNameWithoutTimestamp = fileName.substring(0, fileName.lastIndexOf('-')) + "." + fileExtension;
                var file = {
                    status: 'success',
                    accepted: true,
                    name: fileNameWithoutTimestamp,
                    url: fileUrl,
                    upload: {
                        uuid: Math.random().toString(36).substring(2, 8)
                    }
                };

                myDropZone.files.push(file);
                myDropZone.emit('addedfile', file);
                myDropZone.emit('complete', file);
                myDropZone.emit('success', file, subimage);
            });
        }
        

        $('#' + mode + 'Modal').on('shown.bs.modal', function () {
            $('.wrap-modal-slider').addClass('open');
        })
        
        $('#' + mode + 'Modal').on('hidden.bs.modal', function () {
            myDropZone.removeAllFiles();
            $('.wrap-modal-slider').removeClass('open');
            $('#' + mode + 'DropzoneItems').append(previewTemplate);
            
            if(_validator) {
                _validator.destroy();
                _validatorFields = {};
            }
        })
    }

	var _getFullUrl = function (filePath) {
		return window.location.protocol + "//" + window.location.hostname + "/" + filePath;
    }

    var _openImageModal = function (imageUrl) {
        $("#imageUrl").attr("src", '/'+ imageUrl);
        imageModal = $("#imageModal").clone();
        $(imageModal).appendTo('body').modal('show');
        $(imageModal).show();
    }
    
    var _closeImageModal = function () {
        $("#imageUrl").attr("src", '/theme/assets/media/users/blank.png');
        $(imageModal).remove();
        $(".modal-backdrop.fade.show").slice(1).remove();
    }

    var _productAdd = function () {
        $(".select2").each(function(index, element) {
            var field = $(element).attr("name");
            $(element).on('change.select2', function() {
                if(field in _validatorFields) {
                    _validator.revalidateField(field);
                }
            });
        });

        _validator.validate().then(function (status) {
            if (status == 'Valid') {
                let body = {
                    subimages: Object.keys(mapUuidSubimage).map((key) => mapUuidSubimage[key]),
                };

                let mapKeyElement = {
                    scientificNameId: 'select',
                    madeInCountryId: 'select',
                    name_en: 'input',
                    name_ar: 'input',
                    name_fr: 'input',
                    image: 'input',
                    stock: 'input',
                    maximumOrderQuantity: 'input',
                    subtitle_ar: 'input',
                    subtitle_en: 'input',
                    subtitle_fr: 'input',
                    description_ar: 'textarea',
                    description_en: 'textarea',
                    description_fr: 'textarea',
                    unitPrice: 'input',
                    vat: 'input',
                    manufacturerName: 'input',
                    batchNumber: 'input',
                    itemCode: 'input',
                    categoryId: 'select',
                    subcategoryId: 'select',
                    activeIngredientsId: 'input',
                    expiryDate: 'input',
                    strength: 'input',
                };

                Object.keys(mapKeyElement).forEach((key) => {
                    body[key] = $('#addModalForm ' + mapKeyElement[key] + '[name=' + key + ']').val();
                });

                $("#addModal").modal('hide');
                WebApp.post('/web/distributor/product/add', body, DistributorProductsDataTable.reloadDatatable);
            } else {
                Swal.fire({
                    text: WebAppLocals.getMessage('validationError'),
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: WebAppLocals.getMessage('validationErrorOk'),
                    customClass: {
                        confirmButton: 'btn font-weight-bold btn-light',
                    },
                }).then(function () {
                    KTUtil.scrollTop();
                });
            }
        });
    }

    var _productEdit = function () {
        $(".select2").each(function(index, element) {
            var field = $(element).attr("name");
            $(element).on('change.select2', function() {
                if(field in _validatorFields) {
                    _validator.revalidateField(field);
                }
            });
        });

        _validator.validate().then(function (status) {
            if (status == 'Valid') {
                let body = {
                    subimages: Object.keys(mapUuidSubimage).map((key) => mapUuidSubimage[key]),
                };

                let mapKeyElement = {
                    id: 'input',
                    scientificNameId: 'select',
                    madeInCountryId: 'select',
                    name_en: 'input',
                    name_ar: 'input',
                    name_fr: 'input',
                    image: 'input',
                    maximumOrderQuantity: 'input',
                    subtitle_ar: 'input',
                    subtitle_en: 'input',
                    subtitle_fr: 'input',
                    description_ar: 'textarea',
                    description_en: 'textarea',
                    description_fr: 'textarea',
                    unitPrice: 'input',
                    vat: 'input',
                    manufacturerName: 'input',
                    batchNumber: 'input',
                    itemCode: 'input',
                    categoryId: 'select',
                    subcategoryId: 'select',
                    activeIngredientsId: 'input',
                    expiryDate: 'input',
                    strength: 'input',
                };

                Object.keys(mapKeyElement).forEach((key) => {
                    body[key] = $('#editModalForm ' + mapKeyElement[key] + '[name=' + key + ']').val();
                });

                $("#editModal").modal('hide');
                WebApp.post('/web/distributor/product/edit', body, DistributorProductsDataTable.reloadDatatable);
            } else {
                Swal.fire({
                    text: WebAppLocals.getMessage('validationError'),
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: WebAppLocals.getMessage('validationErrorOk'),
                    customClass: {
                        confirmButton: 'btn font-weight-bold btn-light',
                    },
                }).then(function () {
                    KTUtil.scrollTop();
                });
            }
        });
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
        productEdit: function () {
            _productEdit();
        },
        productAddModal: function () {
            _productAddModal();
        },
        productAdd: function () {
            _productAdd();
        },
        closeImageModal: function() {
            _closeImageModal();
        }
    };
})();
