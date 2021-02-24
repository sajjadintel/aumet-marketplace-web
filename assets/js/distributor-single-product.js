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

	var _mapUuidSubimage = {};
	
	var _init = function () {
		_initGeneral();
		_initImages();
		// _initPrices();
		// _initStockSettings();
	}

	var _initGeneral = function () {
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
		$('#scientificName').append(new Option($('#scientificName').attr("data-scientificName"), $('#scientificName').attr("data-scientificNameId")));
		$('#scientificName').val($('#scientificName').attr("data-scientificNameId"));

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
		$('#country').append(new Option($('#country').attr("data-countryName"), $('#country').attr("data-countryId")));
		$('#country').val($('#country').attr("data-countryId"));

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
		var arrActiveIngredients = JSON.parse($('#activeIngredientsId').attr("data-arrActiveIngredients"));
		var arrActiveIngredientsId = [];
		$('#activeIngredients').empty();
		arrActiveIngredients.forEach((activeIngredient) => {
			$('#activeIngredients').append(new Option(activeIngredient.name, activeIngredient.id));
			arrActiveIngredientsId.push(activeIngredient.id);
		});
		$('#activeIngredients').val(arrActiveIngredientsId);
		$('#activeIngredientsId').val(arrActiveIngredientsId);

		$('#activeIngredients').on('change', function() {
			$('#activeIngredientsId').val($('#activeIngredients').val());
		});

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

	var _initImages = function () {
		_changeImageHolder($('#image').attr("data-value"));
		$('#image').on('change', (ev) => _changeProductImage(ev));

		var initialSubimages = JSON.parse($('#subimages').attr("data-arrSubimages"));
		_initializeSubimagesDropzone(initialSubimages);
		
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

	var _removeMainPhoto = function () {
		_changeImageHolder('');
	}

	var _initPrices = function () {
		$('#vat').on('change', function () {
			_handlePercentageField(this, true);
		});
	}

	var _initStockSettings = function () {

	}


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
	}

	return {
		// public functions
		init: function () {
			_init();
		},
		changeTab: function (element) {
			_changeTab(element);
		},
		removeMainPhoto: function () {
			_removeMainPhoto();
		}
	};
})();
