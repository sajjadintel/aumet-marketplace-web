'use strict';
// Class definition
var DistributorSingleProduct = (function () {

	// Classes
	var _buttonSpinnerClasses = 'spinner spinner-right spinner-white pr-15';
	
	// All validators
	var _generalTabValidator;
	var _imagesTabValidator;
	var _pricesTabValidator;
	var _stockSettingsTabValidator;
	var _fullValidator;

	var _mapUuidSubimage = {};
	
	var _init = function (mode) {
		_initGeneral(mode);
		_initImages(mode);
		_initPrices(mode);
		_initStockSettings(mode);
		if(mode === "add") _initFullValidator();
	};

	var _initGeneral = function (mode) {
		$('#scientificName').select2({
            placeholder: WebAppLocals.getMessage("productScientificName"),

            ajax: {
                url: '/web/product/scientificname/list',
                dataType: 'json',
                processResults: function(response) {
                    return {
                        results: response.data.results,
                        pagination: {
                            more: response.data.pagination
                        }
                    }
                }
            }
        });
		$('#scientificName').empty();
		if($('#scientificName').attr("data-scientificNameId")) {
			$('#scientificName').append(new Option($('#scientificName').attr("data-scientificName"), $('#scientificName').attr("data-scientificNameId")));
			$('#scientificName').val($('#scientificName').attr("data-scientificNameId"));
		}

		$('#country').select2({
            placeholder: WebAppLocals.getMessage("madeInCountry"),

            ajax: {
                url: '/web/product/country/list',
                dataType: 'json',
                processResults: function(response) {
                    return {
                        results: response.data.results,
                        pagination: {
                            more: response.data.pagination
                        }
                    }
                }
            }
        });
		$('#country').empty();
		if($('#country').attr("data-countryId")) {
			$('#country').append(new Option($('#country').attr("data-countryName"), $('#country').attr("data-countryId")));
			$('#country').val($('#country').attr("data-countryId"));
		}

		$('#activeIngredients').select2({
            multiple: true,
            placeholder: WebAppLocals.getMessage("activeIngredients"),

            ajax: {
                url: '/web/product/ingredient/list',
                dataType: 'json',
                processResults: function(response) {
                    return {
                        results: response.data.results,
                        pagination: {
                            more: response.data.pagination
                        }
                    }
                }
            }
        });

		if($('#activeIngredientsId').attr("data-arrActiveIngredients")) {
			var arrActiveIngredients = JSON.parse($('#activeIngredientsId').attr("data-arrActiveIngredients"));
			var arrActiveIngredientsId = [];
			$('#activeIngredients').empty();
			arrActiveIngredients.forEach((activeIngredient) => {
				$('#activeIngredients').append(new Option(activeIngredient.name, activeIngredient.id));
				arrActiveIngredientsId.push(activeIngredient.id);
			});
			$('#activeIngredients').val(arrActiveIngredientsId);
			$('#activeIngredientsId').val(arrActiveIngredientsId);
		}

		$('#activeIngredients').on('change', function() {
			$('#activeIngredientsId').val($('#activeIngredients').val());
		});

		if(mode === "edit") {
			var form = KTUtil.getById('generalForm');
			var formSubmitUrl = KTUtil.attr(form, 'action');
			var formSubmitButton = KTUtil.getById('generalSubmitButton');

			$('#generalForm select[name=countryId]').on('change', function (ev) {
				var field = $(this).attr('name');
				_generalTabValidator.revalidateField(field);
			});

			if (_generalTabValidator) {
				_generalTabValidator.resetForm();
				_generalTabValidator.destroy();
			}

			var mandatoryFields = [
				"countryId",
				"nameAr",
				"nameEn"
			]

			// Structure: field: [minLength, maxLength]
			var mapFieldStrRangeLength = {
				nameAr: [4, 200],
				nameEn: [4, 200],
				descriptionAr: [4, 5000],
				descriptionEn: [4, 5000],
				subtitleAr: [4, 200],
				subtitleEn: [4, 200],
				manufacturerName: [4, 200],
				strength: [4, 200]
			}

			var allFields = new Set([...mandatoryFields, ...Object.keys(mapFieldStrRangeLength)]);
			var validatorFields = {};
			allFields.forEach((field) => {
				var fieldValidators = {};

				if (mandatoryFields.includes(field)) {
					fieldValidators.notEmpty = {
						message: WebAppLocals.getMessage('required'),
					};
				}

				if (field in mapFieldStrRangeLength) {
					var strRangeLength = mapFieldStrRangeLength[field];
					var message =
						WebAppLocals.getMessage('lengthError') +
						' ' +
						strRangeLength[0] +
						' ' +
						WebAppLocals.getMessage('and') +
						' ' +
						strRangeLength[1] +
						' ' +
						WebAppLocals.getMessage('characters');
					fieldValidators.stringLength = {
						min: strRangeLength[0],
						max: strRangeLength[1],
						message: message,
					};
				}

				validatorFields[field] = {
					validators: fieldValidators,
				};
			});

			_generalTabValidator = FormValidation.formValidation(form, {
				fields: validatorFields,
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					submitButton: new FormValidation.plugins.SubmitButton(),
					// Bootstrap Framework Integration
					bootstrap: new FormValidation.plugins.Bootstrap({ eleValidClass: '' }),
				},
			});

			_generalTabValidator.on('core.form.valid', function () {
				var data = $(form).serializeJSON();

				// Show loading state on button
				KTUtil.btnWait(formSubmitButton, _buttonSpinnerClasses, WebAppLocals.getMessage('pleaseWait'));
				$(formSubmitButton).prop('disabled', true);

				WebApp.post(formSubmitUrl, data, null, formSubmitButton);
			});

			_generalTabValidator.on('core.form.invalid', function () {
				KTUtil.scrollTop();
			});
		}
	};

	var _initImages = function (mode) {
		_changeImageHolder($('#image').attr("data-value"));
		$('#image').on('change', (ev) => _changeProductImage(ev));

		var initialSubimages = [];
		if($('#subimages').attr("data-arrSubimages")) {
			initialSubimages = JSON.parse($('#subimages').attr("data-arrSubimages"));
		}
		_initializeSubimagesDropzone(initialSubimages);
		
		if(mode === "edit") {
			var form = KTUtil.getById('imagesForm');
			var formSubmitUrl = KTUtil.attr(form, 'action');
			var formSubmitButton = KTUtil.getById('imagesSubmitButton');

			if (_imagesTabValidator) {
				_imagesTabValidator.resetForm();
				_imagesTabValidator.destroy();
			}

			_imagesTabValidator = FormValidation.formValidation(form, {
				fields: {},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					submitButton: new FormValidation.plugins.SubmitButton(),
					// Bootstrap Framework Integration
					bootstrap: new FormValidation.plugins.Bootstrap({ eleValidClass: '' }),
				},
			});

			_imagesTabValidator.on('core.form.valid', function () {
				var data = $(form).serializeJSON();

				// Show loading state on button
				KTUtil.btnWait(formSubmitButton, _buttonSpinnerClasses, WebAppLocals.getMessage('pleaseWait'));
				$(formSubmitButton).prop('disabled', true);

				WebApp.post(formSubmitUrl, data, null, formSubmitButton);
			});

			_imagesTabValidator.on('core.form.invalid', function () {
				KTUtil.scrollTop();
			});
		}
	};

	var _removeMainPhoto = function () {
		_changeImageHolder('');
	};

	var _initPrices = function (mode) {
		$('#vat').on('change', function () {
			_handlePercentageField(this, true);
		});


		$('#unitPricesCheckbox').unbind();
		$('#unitPricesCheckbox').change(function () {
			if ($('#unitPricesCheckbox').is(':checked')) {
				$('#unitPricesRepeater').show();
			} else {
				$('#unitPricesRepeater').hide();
			}
		});

		var unitPricesRepeaterElementTemplate = null;
		$('#unitPricesList > div').each(function (index, element) {
			unitPricesRepeaterElementTemplate = $(element).clone();
		});

		var arrPaymentMethod = [];
		if($('#unitPricesList').attr("data-arrPaymentMethod")) {
			arrPaymentMethod = JSON.parse($('#unitPricesList').attr("data-arrPaymentMethod"));
		}

		$('#unitPricesRepeater').repeater({
			initEmpty: true,
			show: function () {
				_initializeRepeaterElements(arrPaymentMethod);
				$(this).slideDown();
			},
			hide: function (deleteElement) {
				$(this).slideUp(deleteElement);
			},
		});

		var arrProductUnitPrice = [];
		if($('#unitPricesList').attr("data-arrProductUnitPrice")) {
			arrProductUnitPrice = JSON.parse($('#unitPricesList').attr("data-arrProductUnitPrice"));
		}
		
		if (arrProductUnitPrice.length > 0) {
			$('#unitPricesCheckbox').prop('checked', true);
			arrProductUnitPrice.forEach((repeaterData) => {
				var repeaterRow = $(unitPricesRepeaterElementTemplate).clone();
				$(repeaterRow).find('#unitPricesId').val(repeaterData.id);
				$(repeaterRow).find('#paymentMethodId').attr('data-value', repeaterData.paymentMethodId);
				$(repeaterRow).find('#unitPrice').val(repeaterData.unitPrice);
				$('#unitPricesList').append(repeaterRow);
			});
			$('#unitPricesRepeater').show();
		} else {
			$('#unitPricesCheckbox').prop('checked', false);
			$('#unitPricesRepeater').hide();
		}

		_initializeRepeaterElements(arrPaymentMethod);
		
		if(mode === "edit") {
			var form = KTUtil.getById('pricesForm');
			var formSubmitUrl = KTUtil.attr(form, 'action');
			var formSubmitButton = KTUtil.getById('pricesSubmitButton');

			if (_pricesTabValidator) {
				_pricesTabValidator.resetForm();
				_pricesTabValidator.destroy();
			}

			var mandatoryFields = [
				"vat",
			]

			var validatorFields = {};
			mandatoryFields.forEach((field) => {
				validatorFields[field] = {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('required'),
						}
					}
				};
			});

			_pricesTabValidator = FormValidation.formValidation(form, {
				fields: validatorFields,
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					submitButton: new FormValidation.plugins.SubmitButton(),
					// Bootstrap Framework Integration
					bootstrap: new FormValidation.plugins.Bootstrap({ eleValidClass: '' }),
				},
			});

			_pricesTabValidator.on('core.form.valid', function () {
				var valid = true;
				if ($('#unitPricesCheckbox').is(':checked')) {
					$('#unitPricesList > div').each(function (index, element) {
						var paymentMethodElement = $(element).find('#paymentMethodId');
						var paymentMethodId = $(paymentMethodElement).val();
						if (!paymentMethodId) {
							if (!$(paymentMethodElement).parent().hasClass('is-invalid')) {
								$(paymentMethodElement).parent().addClass('is-invalid');
								$(paymentMethodElement).parent().css('border', '1px solid #F64E60');
							}
						} else {
							$(paymentMethodElement).parent().removeClass('is-invalid');
							$(paymentMethodElement).parent().css('border', '');
						}

						var unitPriceElement = $(element).find('#unitPrice');
						var unitPrice = $(unitPriceElement).val();
						if (!unitPrice) {
							if (!$(unitPriceElement).hasClass('is-invalid')) {
								$(unitPriceElement).addClass('is-invalid');
							}
						} else {
							$(unitPriceElement).removeClass('is-invalid');
						}

						valid = paymentMethodId && unitPrice;
					});
				}

				$('.selectpicker.paymentMethodSelect').on('change', function () {
					var value = $(this).val();
					if (value) {
						$(this).parent().removeClass('is-invalid');
						$(this).parent().css('border', '');
					} else {
						$(this).parent().addClass('is-invalid');
						$(this).parent().css('border', '1px solid #F64E60');
					}
				});

				$('.unitPriceInput').on('change', function () {
					var value = $(this).val();
					if (value) {
						$(this).removeClass('is-invalid');
					} else {
						$(this).addClass('is-invalid');
					}
				});

				if (valid) {
					var data = $(form).serializeJSON();
		
					var arrProductUnitPrice = [];
					if ($('#unitPricesCheckbox').is(':checked')) {
						$('#unitPricesList > div').each(function (index, element) {
							var paymentMethodId = $(element).find('#paymentMethodId').val();
							var unitPrice = $(element).find('#unitPrice').val();
							arrProductUnitPrice.push({
								paymentMethodId,
								unitPrice,
							});
						});
					}

					data = {
						...data,
						arrProductUnitPrice
					};

					// Show loading state on button
					KTUtil.btnWait(formSubmitButton, _buttonSpinnerClasses, WebAppLocals.getMessage('pleaseWait'));
					$(formSubmitButton).prop('disabled', true);

					WebApp.post(formSubmitUrl, data, null, formSubmitButton);
				} else {
					KTUtil.scrollTop();
				}
			});

			_pricesTabValidator.on('core.form.invalid', function () {
				KTUtil.scrollTop();
			});
		}
	};

	var _initializeRepeaterElements = function (arrPaymentMethod) {
		$('.selectpicker.paymentMethodSelect').each(function (index, element) {
			if (!$(element).parent().is('.dropdown.bootstrap-select.form-control.paymentMethodSelect')) {
				var value = $(element).attr('data-value');
				if (element.options.length === 0) {
					arrPaymentMethod.forEach((paymentMethod) => {
						var selected = paymentMethod.id == value;
						$(element).append(new Option(paymentMethod.name, paymentMethod.id, false, selected));
					});
					$(element).selectpicker();
				}
				$(element).selectpicker('val', value ? value : null);
				$(element).selectpicker('refresh');
			}
		});
	};

	var _initStockSettings = function (mode) {
		var arrows;
        if (KTUtil.isRTL()) {
            arrows = {
                leftArrow: '<i class="la la-angle-right"></i>',
                rightArrow: '<i class="la la-angle-left"></i>'
            }
        } else {
            arrows = {
                leftArrow: '<i class="la la-angle-left"></i>',
                rightArrow: '<i class="la la-angle-right"></i>'
            }
        }
		
        $('#expiryDate').datepicker({
            rtl: KTUtil.isRTL(),
            todayHighlight: true,
            orientation: "top left",
            templates: arrows
        });

		if(mode === "edit") {
			var form = KTUtil.getById('stockSettingsForm');
			var formSubmitUrl = KTUtil.attr(form, 'action');
			var formSubmitButton = KTUtil.getById('stockSettingsSubmitButton');
	
			if (_stockSettingsTabValidator) {
				_stockSettingsTabValidator.resetForm();
				_stockSettingsTabValidator.destroy();
			}
	
			_stockSettingsTabValidator = FormValidation.formValidation(form, {
				fields: {},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					submitButton: new FormValidation.plugins.SubmitButton(),
					// Bootstrap Framework Integration
					bootstrap: new FormValidation.plugins.Bootstrap({ eleValidClass: '' }),
				},
			});
	
			_stockSettingsTabValidator.on('core.form.valid', function () {
				var data = $(form).serializeJSON();
				var stock = $("#stock").val();
				var arrMsg = [];
				if(data.minimumOrderQuantity == data.maximumOrderQuantity) {
					arrMsg.push(WebAppLocals.getMessage("minOrderEqMaxOrderPt1") + data.minimumOrderQuantity + WebAppLocals.getMessage("minOrderEqMaxOrderPt2"));
				}
				
				if(data.minimumOrderQuantity == stock) {
					arrMsg.push(WebAppLocals.getMessage("minOrderEqStockPt1") + data.minimumOrderQuantity + WebAppLocals.getMessage("minOrderEqStockPt2"));
				}
	
				if(arrMsg.length > 0) {
					var msg = arrMsg.join("<br>");
					_alertInfo(msg, function() {
						// Show loading state on button
						KTUtil.btnWait(formSubmitButton, _buttonSpinnerClasses, WebAppLocals.getMessage('pleaseWait'));
						$(formSubmitButton).prop('disabled', true);
			
						WebApp.post(formSubmitUrl, data, null, formSubmitButton);
					})
				} else {
					// Show loading state on button
					KTUtil.btnWait(formSubmitButton, _buttonSpinnerClasses, WebAppLocals.getMessage('pleaseWait'));
					$(formSubmitButton).prop('disabled', true);
		
					WebApp.post(formSubmitUrl, data, null, formSubmitButton);
				}
			});
	
			_stockSettingsTabValidator.on('core.form.invalid', function () {
				KTUtil.scrollTop();
			});
		}

	};

	var _alertInfo = function (msg, isConfirmedFunction) {
		Swal.fire({
			html: msg,
			icon: "warning",
			buttonsStyling: false,
			confirmButtonText: WebAppLocals.getMessage('save'),
			showCloseButton: true,
			showCancelButton: true,
			cancelButtonText: WebAppLocals.getMessage('backToEdit'),
			customClass: {
				confirmButton: 'btn font-weight-bold btn-primary',
				cancelButton: 'btn font-weight-bold btn-secondary',
			},
		}).then(function (result) {
			if (result.isConfirmed) {
				isConfirmedFunction();
			}
		});
	}

	var _initFullValidator = function () {
		var form = KTUtil.getById('addForm');
		var formSubmitUrl = KTUtil.attr(form, 'action');
		var formSubmitButton = KTUtil.getById('addSubmitButton');

		if (_fullValidator) {
			_fullValidator.resetForm();
			_fullValidator.destroy();
		}

		var mandatoryFields = [
			"countryId",
			"nameAr",
			"nameEn",
			"vat",
			"stock"
		]

		// Structure: field: [minLength, maxLength]
		var mapFieldStrRangeLength = {
			nameAr: [4, 200],
			nameEn: [4, 200],
			descriptionAr: [4, 5000],
			descriptionEn: [4, 5000],
			subtitleAr: [4, 200],
			subtitleEn: [4, 200],
			manufacturerName: [4, 200],
			strength: [4, 200]
		}

		var allFields = new Set([...mandatoryFields, ...Object.keys(mapFieldStrRangeLength)]);
		var validatorFields = {};
		allFields.forEach((field) => {
			var fieldValidators = {};

			if (mandatoryFields.includes(field)) {
				fieldValidators.notEmpty = {
					message: WebAppLocals.getMessage('required'),
				};
			}

			if (field in mapFieldStrRangeLength) {
				var strRangeLength = mapFieldStrRangeLength[field];
				var message =
					WebAppLocals.getMessage('lengthError') +
					' ' +
					strRangeLength[0] +
					' ' +
					WebAppLocals.getMessage('and') +
					' ' +
					strRangeLength[1] +
					' ' +
					WebAppLocals.getMessage('characters');
				fieldValidators.stringLength = {
					min: strRangeLength[0],
					max: strRangeLength[1],
					message: message,
				};
			}

			validatorFields[field] = {
				validators: fieldValidators,
			};
		});

		_fullValidator = FormValidation.formValidation(form, {
			fields: validatorFields,
			plugins: {
				trigger: new FormValidation.plugins.Trigger(),
				submitButton: new FormValidation.plugins.SubmitButton(),
				// Bootstrap Framework Integration
				bootstrap: new FormValidation.plugins.Bootstrap({ eleValidClass: '' }),
			},
		});

		_fullValidator.on('core.form.valid', function () {
			var valid = true;
			if ($('#unitPricesCheckbox').is(':checked')) {
				$('#unitPricesList > div').each(function (index, element) {
					var paymentMethodElement = $(element).find('#paymentMethodId');
					var paymentMethodId = $(paymentMethodElement).val();
					if (!paymentMethodId) {
						if (!$(paymentMethodElement).parent().hasClass('is-invalid')) {
							$(paymentMethodElement).parent().addClass('is-invalid');
							$(paymentMethodElement).parent().css('border', '1px solid #F64E60');
						}
					} else {
						$(paymentMethodElement).parent().removeClass('is-invalid');
						$(paymentMethodElement).parent().css('border', '');
					}

					var unitPriceElement = $(element).find('#unitPrice');
					var unitPrice = $(unitPriceElement).val();
					if (!unitPrice) {
						if (!$(unitPriceElement).hasClass('is-invalid')) {
							$(unitPriceElement).addClass('is-invalid');
						}
					} else {
						$(unitPriceElement).removeClass('is-invalid');
					}

					valid = paymentMethodId && unitPrice;
				});
			}

			$('.selectpicker.paymentMethodSelect').on('change', function () {
				var value = $(this).val();
				if (value) {
					$(this).parent().removeClass('is-invalid');
					$(this).parent().css('border', '');
				} else {
					$(this).parent().addClass('is-invalid');
					$(this).parent().css('border', '1px solid #F64E60');
				}
			});

			$('.unitPriceInput').on('change', function () {
				var value = $(this).val();
				if (value) {
					$(this).removeClass('is-invalid');
				} else {
					$(this).addClass('is-invalid');
				}
			});

			if (valid) {
				var data = $(form).serializeJSON();
	
				var arrProductUnitPrice = [];
				if ($('#unitPricesCheckbox').is(':checked')) {
					$('#unitPricesList > div').each(function (index, element) {
						var paymentMethodId = $(element).find('#paymentMethodId').val();
						var unitPrice = $(element).find('#unitPrice').val();
						arrProductUnitPrice.push({
							paymentMethodId,
							unitPrice,
						});
					});
				}

				data = {
					...data,
					arrProductUnitPrice
				};

				var arrMsg = [];
				if(data.minimumOrderQuantity == data.maximumOrderQuantity) {
					arrMsg.push(WebAppLocals.getMessage("minOrderEqMaxOrderPt1") + data.minimumOrderQuantity + WebAppLocals.getMessage("minOrderEqMaxOrderPt2"));
				}
				
				if(data.minimumOrderQuantity == data.stock) {
					arrMsg.push(WebAppLocals.getMessage("minOrderEqStockPt1") + data.minimumOrderQuantity + WebAppLocals.getMessage("minOrderEqStockPt2"));
				}
	
				if(arrMsg.length > 0) {
					var msg = arrMsg.join("<br>");
					_alertInfo(msg, function() {
						// Show loading state on button
						KTUtil.btnWait(formSubmitButton, _buttonSpinnerClasses, WebAppLocals.getMessage('pleaseWait'));
						$(formSubmitButton).prop('disabled', true);
		
						WebApp.post(formSubmitUrl, data, _addProductSuccessCallback, formSubmitButton);
					})
				} else {
					// Show loading state on button
					KTUtil.btnWait(formSubmitButton, _buttonSpinnerClasses, WebAppLocals.getMessage('pleaseWait'));
					$(formSubmitButton).prop('disabled', true);
	
					WebApp.post(formSubmitUrl, data, _addProductSuccessCallback, formSubmitButton);
				}
			} else {
				WebApp.alertError(WebAppLocals.getMessage("pricesTabInvalid"));
				KTUtil.scrollTop();
			}
		});

		_fullValidator.on('core.form.invalid', function () {
			var arrErrors = [];
			allFields.forEach((field) => {
				var value = $("[name=" + field + "]").val();
				var valid = true;
				if (mandatoryFields.includes(field)) {
					if(!value) valid = false;
				}
	
				if (field in mapFieldStrRangeLength && valid) {
					var strRangeLength = mapFieldStrRangeLength[field];
					if(value) {
						var minLength = strRangeLength[0];
						var maxLength = strRangeLength[1];
						if(value.length < minLength || value.length > maxLength) valid = false;
					}
				}

				if(!valid) {
					arrErrors.push(WebAppLocals.getMessage(field) + " " + WebAppLocals.getMessage("invalid"));
				}
			})
			WebApp.alertError(arrErrors.join("<br>"));

			KTUtil.scrollTop();
		});
	}

	var _addProductSuccessCallback = function (webResponse) {
		WebApp.loadPage('/web/distributor/product');
	};

	var _handlePercentageField = function (inputElement, float = false) {
		var newValue = $(inputElement).val().toString().replace('%', '');
		if (float) {
			newValue = newValue > 0 ? parseFloat(newValue).toFixed(2) : !newValue ? newValue : 0;
		} else {
			newValue = newValue > 0 ? newValue : !newValue ? newValue : 0;
		}
		if ($(inputElement).attr('type') == 'text') {
			if (newValue > 100) newValue = 100;
			if (newValue) {
				newValue += '%';
			}
		}
		$(inputElement).val(newValue);
	};

	var _changeImageHolder = function (image) {
		let backgroundImageVal = '/assets/img/default-product-image.png';
		if (image) {
			backgroundImageVal = image;
		}
		$('#imageHolder').css('background-image', 'url(' + backgroundImageVal + ')');
		$('#imageInput').val(image);
	};

	var _changeProductImage = function (ev) {
		let formData = new FormData();
		formData.append('product_image', ev.target.files[0]);

		$('#imageError').html('');
		$.ajax({
			url: '/web/distributor/product/image',
			data: formData,
			type: 'POST',
			contentType: false,
			processData: false,
		}).done(function (webResponse) {
			if(webResponse.errorCode == 2) {
				$('#imageError').html(webResponse.message);
			} else {
				_changeImageHolder(webResponse.data);
			}
		});
	};

	var _initializeSubimagesDropzone = function (initialSubimages = []) {
		// Set the dropzone container id
		var id = '#subimagesDropzone';

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
			maxFilesize: 2, // Max filesize in MB
			maxFiles: 6,
			previewTemplate: previewTemplate,
			previewsContainer: id + ' .dropzone-items', // Define the container to display the previews
			clickable: id + ' .dropzone-select', // Define the element that should be used as click trigger to select files.
		});

		myDropZone.on('addedfile', function (file) {
			// Hookup the start button
			$(document)
				.find(id + ' .dropzone-item')
				.css('display', 'block');
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
		});

		// Add file to the list if success
		myDropZone.on('success', function (file, response) {
			var imageUrl = response;

			_mapUuidSubimage[file.upload.uuid] = imageUrl;

			var arrSubimages = Object.keys(_mapUuidSubimage).map(key => _mapUuidSubimage[key]);
			$("#subimages").val(arrSubimages.join(","));
			$('.gallery').each(function() {
				$(this).magnificPopup({
					delegate: 'a[href]',
					type: 'image',
					gallery: {
						enabled: true
					}
				});
			});
			
			var dropzoneFilenameContainerElement = $(file.previewTemplate).find('#dropzoneImageContainer');
			$(dropzoneFilenameContainerElement).attr('href', imageUrl);

			var dropzoneFilenameElement = $(file.previewTemplate).find('#dropzoneImage');
			$(dropzoneFilenameElement).css('background-image', 'url("' + imageUrl + '")');
			$(dropzoneFilenameElement).css('cursor', 'pointer');
		});

		// Remove file from the list
		myDropZone.on('removedfile', function (file) {
			delete _mapUuidSubimage[file.upload.uuid];

			var arrSubimages = Object.keys(_mapUuidSubimage).map(key => _mapUuidSubimage[key]);
			$("#subimages").val(arrSubimages.join(","));
			$('.gallery').each(function() {
				$(this).magnificPopup({
					delegate: 'a[href]',
					type: 'image',
					gallery: {
						enabled: true
					}
				});
			});
		});

		myDropZone.on('maxfilesexceeded', function (file) {
			myDropZone.removeFile(file);
			$('#subimagesErrorLabel').text(WebAppLocals.getMessage('subimagesExceeded'));
			$('#subimagesErrorLabel').show();
		});

		myDropZone.on('error', function (file, errorMessage) {
			var errorLabelText = '';
			if (errorMessage.includes('too big')) {
				errorLabelText = WebAppLocals.getMessage('subimagesMaximumSize');
			} else if (errorMessage.includes('type')) {
				errorLabelText = WebAppLocals.getMessage('subimagesWrongFormat');
			}

			myDropZone.removeFile(file);
			$('#subimagesErrorLabel').text(errorLabelText);
			$('#subimagesErrorLabel').show();

			setTimeout(() => {
				$('#subimagesErrorLabel').text('');
				$('#subimagesErrorLabel').hide();
			}, 3000);
		});

		if (initialSubimages.length > 0) {
			initialSubimages.forEach((subimage) => {
				var fileUrl = subimage;
				var file = {
					status: 'success',
					accepted: true,
					url: fileUrl,
					upload: {
						uuid: Math.random().toString(36).substring(2, 8),
					},
				};

				myDropZone.files.push(file);
				myDropZone.emit('addedfile', file);
				myDropZone.emit('complete', file);
				myDropZone.emit('success', file, subimage);
			});
		}
	};

	var _changeTab = function(element) {
		var tab = $(element).attr("data-tab");
		$(".single-product-tab").removeClass("bg-white");
		$(element).addClass("bg-white");
		
		$(".tab-body").addClass("d-none");
		$("#"+ tab + "-body").removeClass("d-none");
	};

	return {
		// public functions
		init: function (mode) {
			_init(mode);
		},
		changeTab: function (element) {
			_changeTab(element);
		},
		removeMainPhoto: function () {
			_removeMainPhoto();
		}
	};
})();
