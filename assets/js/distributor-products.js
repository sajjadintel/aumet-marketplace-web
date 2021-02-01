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
        "strength": [4, 200],
    };
  
    var mapUuidSubimage = {};
    var imageModal;
    var _validator;
    var _validatorFields = {};

    var _productEditModal = function (productId) {
        WebApp.get('/web/distributor/product/' + productId, _productEditModalOpen);
    };

    var _productEditStockModal = function (productId) {
        WebApp.get('/web/distributor/product/stock/' + productId, _productEditStockModalOpen);
    };

    var _productAddModal = function () {
        _productAddModalOpen();
    };

    var _productAddModalOpen = function () {
        _clearAddModal();
        if(_validator) {
            _validator.resetForm();
            _validator.destroy();
        }

        $('#addModalForm').attr('action', '/web/distributor/product/add');
        $('#addProductCategory').on('change', () => _updateSubcategorySelect('add'));

        _changeImageHolder('', 'add');
        $('#addProductImage').on('change', (ev) => _changeProductImage(ev, 'add'));

        $('#addActiveIngredients').on('change', (ev) => _updateActiveIngredientsVal('add'));
        
        _initializeSubimagesDropzone('add');

        $('#addModal').appendTo('body').modal('show');
    };

    var _productEditModalOpen = function (webResponse) {
        if(_validator) {
            _validator.resetForm();
            _validator.destroy();
        }
        
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
      
        _changeImageHolder(webResponse.data.product.image, "edit");
        $('#editProductImage').on("change", (ev) => _changeProductImage(ev, "edit"));

        _initializeSubimagesDropzone('edit', webResponse.data.subimages);
        
        $('#editModal').appendTo('body').modal('show');
    };

    var _productEditStockModalOpen = function (webResponse) {
        if(_validator) {
            _validator.resetForm();
            _validator.destroy();
        }

        $('#editStockModalForm').attr('action', '/web/distributor/product/editStock');
        $('#editStockProductId').val(webResponse.data.product.productId);

        var arrBonus = [];
        var arrSpecialBonus = [];
        webResponse.data.arrBonus.forEach((bonus) => {
            if(bonus.arrRelationGroup && bonus.arrRelationGroup.length > 0) arrSpecialBonus.push(bonus);
            else arrBonus.push(bonus);
        });
        
        $('#editStockBonusCheckbox').change(function () {
            if($('#editStockBonusCheckbox').is(":checked")) {
                $("#editStockBonusRepeater").show();
            } else {
                $("#editStockBonusRepeater").hide();
            }
        });
        
        $('#editStockSpecialBonusCheckbox').change(function () {
            if($('#editStockSpecialBonusCheckbox').is(":checked")) {
                $("#editStockSpecialBonusRepeater").show();
            } else {
                $("#editStockSpecialBonusRepeater").hide();
            }
        });
        
        _initializeBonusSection(arrBonus, webResponse.data.arrBonusType);
        _initializeSpecialBonusSection(arrSpecialBonus, webResponse.data.arrBonusType, webResponse.data.arrRelationGroup);

        $('#editStockStock').val(webResponse.data.product.stock);
        $('#editStockModal').appendTo('body').modal('show');
    };

    var _initializeBonusSection = function (arrBonus, arrBonusType) {
		var repeaterElementTemplate;
		$("#editStockBonusList > div").each(function(index, element) {
			repeaterElementTemplate = $(element).clone();
		});

		$('#editStockBonusRepeater').repeater({
            initEmpty: true,
            show: function() {
				_initializeBonusRepeaterElements(arrBonusType);
                $(this).slideDown();
            },
            hide: function(deleteElement) {
				$(this).slideUp(deleteElement);
            }
        });

        if(arrBonus.length > 0) {
            $("#editStockBonusCheckbox").prop('checked', true);
            arrBonus.forEach((repeaterData) => {
                var repeaterRow = $(repeaterElementTemplate).clone();
                $(repeaterRow).find("#editStockBonusId").val(repeaterData.bonusId);
                $(repeaterRow).find("#editStockBonusTypeId").attr("data-value", repeaterData.bonusTypeId);
                $(repeaterRow).find("#editStockBonusQuantity").val(repeaterData.minOrder);
                $(repeaterRow).find("#editStockBonus").val(repeaterData.bonus);
                $("#editStockBonusList").append(repeaterRow);
            })
        } else {
            $("#editStockBonusCheckbox").prop('checked', false);
            $("#editStockBonusRepeater").hide();
        }

        _initializeBonusRepeaterElements(arrBonusType);
	}

	var _initializeBonusRepeaterElements = function (arrBonusType) {
		$('.selectpicker.bonusTypeSelect').each(function(index, element) {
            var value = $(element).attr("data-value");
			if(element.options.length === 0) {
				arrBonusType.forEach((bonusType) => {
                    var selected = bonusType.id == value; 
					$(element).append(new Option(bonusType.name, bonusType.id, false, selected));
				})
				$(element).selectpicker();
				$(element).selectpicker('val', value);
				$(element).selectpicker('refresh');
			}
		})
	}

    var _initializeSpecialBonusSection = function (arrSpecialBonus, arrBonusType, arrRelationGroup) {
		var repeaterElementTemplate;
		$("#editStockSpecialBonusList > div").each(function(index, element) {
			repeaterElementTemplate = $(element).clone();
		});

		$('#editStockSpecialBonusRepeater').repeater({
            initEmpty: true,
            show: function() {
				_initializeSpecialBonusRepeaterElements(arrBonusType, arrRelationGroup);
                $(this).slideDown();
            },
            hide: function(deleteElement) {
				$(this).slideUp(deleteElement);
            }
        });
        
        if(arrSpecialBonus.length > 0) {
            $("#editStockSpecialBonusCheckbox").prop('checked', true);
            arrSpecialBonus.forEach((repeaterData) => {
                var repeaterRow = $(repeaterElementTemplate).clone();
                $(repeaterRow).find("#editStockSpecialBonusId").val(repeaterData.bonusId);
                $(repeaterRow).find("#editStockSpecialBonusTypeId").attr("data-value", repeaterData.bonusTypeId);
                $(repeaterRow).find("#editStockSpecialBonusQuantity").val(repeaterData.minOrder);
                $(repeaterRow).find("#editStockSpecialBonus").val(repeaterData.bonus);
                $(repeaterRow).find("#editStockSpecialRelationGroupId").attr("data-values", repeaterData.arrRelationGroup);
                $("#editStockSpecialBonusList").append(repeaterRow);
            })
        } else {
            $("#editStockSpecialBonusCheckbox").prop('checked', false);
            $("#editStockSpecialBonusRepeater").hide();
        }

        _initializeSpecialBonusRepeaterElements(arrBonusType, arrRelationGroup);
	}

	var _initializeSpecialBonusRepeaterElements = function (arrBonusType, arrRelationGroup) {
		$('.selectpicker.specialBonusTypeSelect').each(function(index, element) {
            var value = $(element).attr("data-value");
			if(element.options.length === 0) {
				arrBonusType.forEach((bonusType) => {
                    var selected = bonusType.id == value;
					$(element).append(new Option(bonusType.name, bonusType.id, false, selected));
				})
				$(element).selectpicker();
				$(element).selectpicker('val', value);
				$(element).selectpicker('refresh');
			}
		})

		$('.selectpicker.specialRelationGroupSelect').each(function(index, element) {
            var allValues = $(element).attr("data-values");
            if(!allValues) allValues = [];
            else allValues = allValues.split(",");

			if(element.options.length === 0) {
				arrRelationGroup.forEach((relationGroup) => {
					var selected = allValues.includes(relationGroup.id);
					$(element).append(new Option(relationGroup.name, relationGroup.id, false, selected));
				})
				$(element).selectpicker();
				$(element).selectpicker('val', allValues);
				$(element).selectpicker('refresh');
			}
		})
	}

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
        $('#addVat').val('');
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
        $('#editProductExpiryDate').val('');
    }

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
        if(_validator) {
            _validator.resetForm();
            _validator.destroy();
        }
        
        if(mode === "editStock") {
            _validatorFields = {
                stock: {
                    validators: {
                        notEmpty: {
                            message: WebAppLocals.getMessage('required')
                        }
                    }
                }
            };
        } else {
            var _mandatoryFields = [...mandatoryFields];
            if (mode === "add") _mandatoryFields.push("stock");
    
            var allFields = new Set([ ..._mandatoryFields, ...Object.keys(mapFieldStrRangeLength)]);
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
        }
        
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

        $(".select2").on("change", function(ev) {
            var field = $(this).attr("name");
            if(field in _validatorFields) {
                _validator.revalidateField(field);
            }
        });
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
                _validator.resetForm();
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
        _addModalValidation("add");

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

                WebApp.post('/web/distributor/product/add', body, _productAddSuccessCallback);
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

    var _productAddSuccessCallback = function () {
        $("#addModal").modal('hide');
        DistributorProductsDataTable.reloadDatatable();
    }

    var _productEdit = function () {
        _addModalValidation("edit");

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

                WebApp.post('/web/distributor/product/edit', body, _productEditSuccessCallback);
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

    var _productEditSuccessCallback = function () {
        $("#editModal").modal('hide');
        DistributorProductsDataTable.reloadDatatable();
    }

    var _productEditStock = function () {
        _addModalValidation("editStock");

        _validator.validate().then(function (status) {
            let valid = true;
            if(status != 'Valid') valid = false;
            if (valid) {
                var arrBonus = [];
                if($('#editStockBonusCheckbox').is(":checked")) {
                    $("#editStockBonusList > div").each(function(index, element) {
                        var id = $(element).find("#editStockBonusId").val();
                        var bonusTypeId = $(element).find("#editStockBonusTypeId").val();
                        var minOrder = $(element).find("#editStockBonusQuantity").val();
                        var bonus = $(element).find("#editStockBonus").val();
                        arrBonus.push({
                            id,
                            bonusTypeId,
                            minOrder,
                            bonus,
                        })
                    });
                }

                if($('#editStockSpecialBonusCheckbox').is(":checked")) {
                    $("#editStockSpecialBonusList > div").each(function(index, element) {
                        var id = $(element).find("#editStockSpecialBonusId").val();
                        var bonusTypeId = $(element).find("#editStockSpecialBonusTypeId").val();
                        var minOrder = $(element).find("#editStockSpecialBonusQuantity").val();
                        var bonus = $(element).find("#editStockSpecialBonus").val();
                        var arrRelationGroup = $(element).find("#editStockSpecialRelationGroupId").val();
                        arrBonus.push({
                            id,
                            bonusTypeId,
                            minOrder,
                            bonus,
                            arrRelationGroup,
                        })
                    });
                }

                let body = {
                    arrBonus
                };
    
                let mapKeyElement = {
                    id: 'input',
                    stock: 'input',
                };
    
                Object.keys(mapKeyElement).forEach((key) => {
                    body[key] = $('#editStockModalForm ' + mapKeyElement[key] + '[name=' + key + ']').val();
                });
                
                WebApp.post('/web/distributor/product/editStock', body, _productEditStockSuccessCallback);
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

    var _productEditStockSuccessCallback = function () {
        $("#editStockModal").modal('hide');
        // DistributorProductsDataTable.reloadDatatable();
        window.location.reload();
    }

    return {
        // public functions
        reloadDatatable: function () {
            WebApp.reloadDatatable();
        },
        productAddModal: function () {
            _productAddModal();
        },
        productAdd: function () {
            _productAdd();
        },
        productEditModal: function (productId) {
            _productEditModal(productId);
        },
        productEdit: function () {
            _productEdit();
        },
        productEditStockModal: function (productId) {
            _productEditStockModal(productId);
        },
        productEditStock: function () {
            _productEditStock();
        },
        closeImageModal: function() {
            _closeImageModal();
        }
    };
})();
