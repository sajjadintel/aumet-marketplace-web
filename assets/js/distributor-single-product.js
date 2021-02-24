'use strict';
// Class definition
var DistributorSingleProduct = (function () {

	// All validators
	var _generalTabValidator;
	var _imagesTabValidator;
	var _pricesTabValidator;
	var _stockSettingsTabValidator;

	// Classes
	var _buttonSpinnerClasses = 'spinner spinner-right spinner-white pr-15';

	
	var _init = function () {
		_initGeneral();
		// _initImages();
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

		$('#activeIngredients').on('change', (ev) => function() {
			$('#activeIngredientsVal').val($('#activeIngredients').val());
		});

		var form = KTUtil.getById('generalForm');
		var formSubmitUrl = KTUtil.attr(form, 'action');
		var formSubmitButton = KTUtil.getById('generalSubmitButton');

		// $('#generalForm select[name=supportReasonId]').on('change', function (ev) {
		// 	var field = $(this).attr('name');
		// 	_generalTabValidator.revalidateField(field);
		// });

		if (_generalTabValidator) {
			_generalTabValidator.resetForm();
			_generalTabValidator.destroy();
		}

		_generalTabValidator = FormValidation.formValidation(form, {
			fields: {

			},
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
			KTUtil.btnWait(formSubmitButton, _buttonSpinnerClasses, 'Please wait');
			$(formSubmitButton).prop('disabled', true);

			WebApp.post(formSubmitUrl, data, null, formSubmitButton);
		});
	}

	var _initImages = function () {
		_changeImageHolder('');
		$('#image').on('change', (ev) => _changeProductImage(ev));

		_initializeSubimagesDropzone();
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

		$.ajax({
			url: '/web/distributor/product/image',
			data: formData,
			type: 'POST',
			contentType: false,
			processData: false,
		}).done(function (webResponse) {
			if(webResponse.errorCode == 2) {
				$('.imageErr').html(webResponse.message);
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

			mapUuidSubimage[file.upload.uuid] = imageUrl;

			var dropzoneFilenameElement = $(file.previewTemplate).find('#dropzoneImage');
			$(dropzoneFilenameElement).css('background-image', 'url("' + imageUrl + '")');
			$(dropzoneFilenameElement).css('cursor', 'pointer');
			$(dropzoneFilenameElement).click(function () {
				_openImageModal(imageUrl);
			});
		});

		// Remove file from the list
		myDropZone.on('removedfile', function (file) {
			delete mapUuidSubimage[file.upload.uuid];
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
			initialSubimages.forEach((subimageObj) => {
				var subimage = subimageObj.subimage;
				var fileUrl = _getFullUrl(subimage);
				var fileName = fileUrl.substring(fileUrl.lastIndexOf('/') + 1);
				var fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
				var fileNameWithoutTimestamp = fileName.substring(0, fileName.lastIndexOf('-')) + '.' + fileExtension;
				var file = {
					status: 'success',
					accepted: true,
					name: fileNameWithoutTimestamp,
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
		}
	};
})();
