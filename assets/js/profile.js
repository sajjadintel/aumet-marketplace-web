'use strict';

// Class Definition
var Profile = (function () {
	var _entityDocument;
	var _myDropZone;
	var _validator;
	var _mapEntityTypeMenus = {
		pharmacy: ['myProfile', 'accountSetting'],
		distributor: ['myProfile', 'accountSetting', 'paymentSetting', 'manageUsers'],
	};
	var _mapEntityTypeMenuValidatorFields = {
		pharmacy: {
			myProfile: {
				entityName: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('pharmacyNameRequired'),
						},
						stringLength: {
							min: 4,
							message: WebAppLocals.getMessage('pharmacyNameTooShort'),
						},
					},
				},
				address: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('addressRequired'),
						},
						stringLength: {
							min: 4,
							message: WebAppLocals.getMessage('addressTooShort'),
						},
					},
				},
				tradeLicenseNumber: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('required'),
						},
						stringLength: {
							min: 4,
							message: WebAppLocals.getMessage('tradeLicenseNumberTooShort'),
						},
					},
				},
			},
			accountSetting: {
				oldPassword: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('oldPasswordRequired'),
						},
					},
				},
				newPassword: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('newPasswordRequired'),
						},
					},
				},
				newPasswordConfirmation: {
					validators: {
						identical: {
							compare: function () {
								return accountSettingForm.querySelector('[name=newPassword]').value;
							},
							message: WebAppLocals.getMessage('wrongPasswordConfirmation'),
						},
					},
				},
			},
		},
		distributor: {
			myProfile: {
				entityName: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('distributorNameRequired'),
						},
						stringLength: {
							min: 4,
							message: WebAppLocals.getMessage('distributorNameTooShort'),
						},
					},
				},
				address: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('addressRequired'),
						},
						stringLength: {
							min: 4,
							message: WebAppLocals.getMessage('addressTooShort'),
						},
					},
				},
				tradeLicenseNumber: {
					validators: {
						stringLength: {
							min: 4,
							message: WebAppLocals.getMessage('tradeLicenseNumberTooShort'),
						},
					},
				},
			},
			accountSetting: {
				oldPassword: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('oldPasswordRequired'),
						},
					},
				},
				newPassword: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('newPasswordRequired'),
						},
					},
				},
				newPasswordConfirmation: {
					validators: {
						identical: {
							compare: function () {
								return accountSettingForm.querySelector('[name=newPassword]').value;
							},
							message: WebAppLocals.getMessage('wrongPasswordConfirmation'),
						},
					},
				},
			},
		},
	};

	var _init = function () {
		_initializeDropzone();

		_initializePasswordFields();

		var entityType = $('#profileEntityType').val();
		if (entityType === 'distributor') {
			_initializePaymentMethodCheckboxes();

			var countryId = $('#paymentSettingSection input[name=countryId]').val();
			WebApp.get('/web/city/list/' + countryId, _initializeMinimumValueOrderSection);
		}

		_handleMenuChange('myProfile');

		_handleChangeProfileImage();
	};

	var _initializeDropzone = function () {
		// Set the dropzone container id
		var id = '#kt_dropzone';

		// Set the preview element template
		var previewNode = $(id + ' .dropzone-item');
		previewNode.id = '';
		var previewTemplate = previewNode.parent('.dropzone-items').html();
		previewNode.remove();

		_myDropZone = new Dropzone(id, {
			// Make the whole body a dropzone
			url: '/web/profile/document/upload', // Set the url for your upload script location
			acceptedFiles: '.pdf, .ppt, .docx, .jpeg, .jpg, .png',
			maxFilesize: 10, // Max filesize in MB
			maxFiles: 1,
			previewTemplate: previewTemplate,
			previewsContainer: id + ' .dropzone-items', // Define the container to display the previews
			clickable: id + ' .dropzone-select', // Define the element that should be used as click trigger to select files.
			init: function () {
				this.on("error", function (file, message, xhr) {
					if (!file.accepted) this.removeFile(file);
					if (message == "You can not upload any more files.") {
						message = null;
					} else if(message.includes("too big")) {
						message = "File is too big (" + _bytesToSize(file.upload.total) + "). Max file size: 10MB."
					}
					$('#errorMessage').html(message);
				});
			},
		});

		_myDropZone.on('addedfile', function (file) {
			// Hookup the start button
			$(document)
				.find(id + ' .dropzone-item')
				.css('display', '');
		});

		// Update the total progress bar
		_myDropZone.on('totaluploadprogress', function (progress) {
			$(id + ' .progress-bar').css('width', progress + '%');
		});

		_myDropZone.on('sending', function (file) {
			// Show the total progress bar when upload starts
			$(id + ' .progress-bar').css('opacity', '1');
			$('#dropzoneFilenameImage').hide();
		});

		// Hide the total progress bar when nothing's uploading anymore
		_myDropZone.on('complete', function (file) {
			var thisProgressBar = id + ' .dz-complete';
			setTimeout(function () {
				$(thisProgressBar + ' .progress-bar, ' + thisProgressBar + ' .progress').css('opacity', '0');
			}, 300);
		});

		// Add file to the list if success
		_myDropZone.on('success', function (file, response) {
			$('#errorMessage').html('');

			_entityDocument = response;

			console.debug({ ...file });
			var dropzoneFilesizeElement = $(file.previewTemplate).find('#dropzoneFilesize');
			if(file.size > 0) {
				$(dropzoneFilesizeElement).show();
			}

			var dropzoneFilenameElement = $(file.previewTemplate).find('#dropzoneFilename');
			$(dropzoneFilenameElement).attr('target', '_blank');
			$(dropzoneFilenameElement).attr('href', _getFullUrl(response));
			var dropzoneFilenameImage = $(file.previewTemplate).find('#dropzoneFilenameImage');
			if (_isImageFile(response)) {
				$(dropzoneFilenameImage).attr('src', _getFullUrl(response));
				$('.dropzone-filename').magnificPopup({
					type: 'image',
				});
				$(dropzoneFilenameImage).show();
			} else if (_isPdfFile(response)) {
				var link = _getFullUrl(response) + '#toolbar=0&navpanes=0';
				console.log('is pdf', link);
				$(dropzoneFilenameImage).attr('src', link);
				$('.dropzone-filename').magnificPopup({
					items: [
						{
							src: link,
							type: 'iframe',
						},
					],
				});
				$(dropzoneFilenameImage).hide();
			} else {
				$(dropzoneFilenameImage).hide();
			}
		});

		// Remove file from the list
		_myDropZone.on('removedfile', function (file) {
			if (file.status === 'success') {
				_entityDocument = null;
			}
		});

		// Overwrite previous file
		_myDropZone.on('maxfilesexceeded', function (file) {
			_myDropZone.removeAllFiles();
			_myDropZone.addFile(file);
		});

		var initialFile = $('#myProfileForm input[name=entityBranchTradeLicenseUrl]').val();
		if (initialFile) {
			var initialFileDecoded = $('#myProfileForm input[name=entityBranchTradeLicenseUrlDecoded]').val();
			var fileName = initialFileDecoded.substring(initialFileDecoded.lastIndexOf('/') + 1);
			var fileExtension = fileName.substring(fileName.lastIndexOf('.') + 1);
			var fileNameWithoutTimestamp = fileName.substring(0, fileName.lastIndexOf('-')) + '.' + fileExtension;

			var fileUrl = _getFullUrl(initialFile);

			var file = {
				status: 'success',
				accepted: true,
				// name: fileNameWithoutTimestamp,
				size: _getFileSize(fileUrl),
				url: fileUrl,
			};

			_myDropZone.files.push(file);
			_myDropZone.emit('addedfile', file);
			_myDropZone.emit('complete', file);
			_myDropZone.emit('success', file, initialFile);
		}
	};

	var _bytesToSize = function(bytes) {
		var sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
		if (bytes == 0) return '0B';
		var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
		return Math.round(bytes / Math.pow(1024, i), 2) + sizes[i];
	}

	var _getFileSize = function (url) {
		var fileSize = 0;
		var http = new XMLHttpRequest();
		try {
			http.open('HEAD', url, false);
			http.send(null);
	
			if (http.status === 200) {
				fileSize = http.getResponseHeader('content-length');
			}
		} catch(err) {
		}

		return fileSize;
	};

	var _getFullUrl = function (filePath) {
		return filePath;
		return window.location.protocol + '//' + window.location.hostname + '/' + filePath;
	};

	var _isImageFile = function (filePath) {
		var ext = filePath.split('.').pop();
		ext = ext.toLowerCase();
		console.debug(ext);
		if (ext == 'png' || ext == 'jpg' || ext == 'jpeg') {
			return true;
		}
		return false;
	};

	var _isPdfFile = function (filePath) {
		var ext = filePath.split('.').pop();
		ext = ext.toLowerCase();
		console.debug(ext);
		if (ext == 'pdf') {
			return true;
		}
		return false;
	};

	var _initializeValidators = function () {
		// Initialize validator on each menu
		var entityType = $('#profileEntityType').val();
		var _mapMenuValidatorFields = _mapEntityTypeMenuValidatorFields[entityType];
		Object.keys(_mapMenuValidatorFields).forEach((menu) => {
			var validatorFields = _mapMenuValidatorFields[menu];
			var form = KTUtil.getById(menu + 'Form');
			_allValidators[menu] = FormValidation.formValidation(form, {
				fields: validatorFields,
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					// Bootstrap Framework Integration
					bootstrap: new FormValidation.plugins.Bootstrap({
						//eleInvalidClass: '',
						eleValidClass: '',
					}),
				},
			});
		});
	};

	var _initializePasswordFields = function () {
		$('#accountSettingForm input[name=oldPassword]').on('change', function () {
			if (_validator) {
				_validator.revalidateField('newPassword');
				_validator.revalidateField('newPasswordConfirmation');
			}
		});

		$('#accountSettingForm input[name=newPassword]').on('change', function () {
			if (_validator) {
				_validator.revalidateField('oldPassword');
				_validator.revalidateField('newPasswordConfirmation');
			}
		});

		$('#accountSettingForm input[name=newPasswordConfirmation]').on('change', function () {
			if (_validator) {
				_validator.revalidateField('oldPassword');
				_validator.revalidateField('newPassword');
			}
		});
	};

	var _initializePaymentMethodCheckboxes = function () {
		$('#paymentMethodContainer').after(
			'<div id="paymentMethodErrorLabel" class="fv-plugins-message-container" style="display: none;"><div class="fv-help-block">' +
				WebAppLocals.getMessage('required') +
				'</div></div>'
		);
		$('#paymentSettingForm input[name=paymentMethodCheckbox]').on('click', function () {
			var isChecked = false;
			$('#paymentSettingForm input[name=paymentMethodCheckbox]').each(function (index, element) {
				if ($(element).is(':checked')) {
					isChecked = true;
				}
			});

			if (isChecked) {
				$('#paymentMethodErrorLabel').hide();
			} else {
				$('#paymentMethodErrorLabel').show();
			}
		});
	};

	var _initializeMinimumValueOrderSection = function (webResponse) {
		var repeaterElementTemplate;
		$('#minimumValueOrderList > div').each(function (index, element) {
			repeaterElementTemplate = $(element).clone();
		});

		var allCity = webResponse.data;
		$('#minimumValueOrderRepeater').repeater({
			initEmpty: true,
			show: function () {
				_initializeRepeaterElements(allCity);
				$(this).slideDown();
			},
			hide: function (deleteElement) {
				$(this).slideUp(deleteElement);
			},
		});

		var allRepeaterDataStr = $('#minimumValueOrderList').attr('data-repeaterdata') || '[]';
		var allRepeaterData = JSON.parse(allRepeaterDataStr);
		allRepeaterData.forEach((repeaterData) => {
			var repeaterRow = $(repeaterElementTemplate).clone();
			$(repeaterRow).find('#minimumValueOrderId').val(repeaterData.entityMinimumValueOrderId);
			$(repeaterRow).find('#minimumValueOrder').val(repeaterData.minimumValueOrder);
			$(repeaterRow).find('#minimumValueOrderCityId').attr('data-values', repeaterData.allCity);
			$('#minimumValueOrderList').append(repeaterRow);
		});

		_initializeRepeaterElements(allCity);
	};

	var _initializeRepeaterElements = function (allCity) {
		$('.minimumValueOrderInput').each(function (index, element) {
			var minimumValueOrderErrorLabelCount = $(element).parent().find('#minimumValueOrderErrorLabel').length;
			if (minimumValueOrderErrorLabelCount === 0) {
				$(element)
					.parent()
					.append(
						'<div id="minimumValueOrderErrorLabel" class="fv-plugins-message-container" style="display: none;"><div class="fv-help-block">' +
							WebAppLocals.getMessage('minimumValueOrderRequired') +
							'</div></div>'
					);
			}
		});

		$('.minimumValueOrderInput').on('change', function () {
			var value = $(this).val();
			if (value) {
				$(this).parent().find('#minimumValueOrderErrorLabel').hide();
				$(this).removeClass('is-invalid');
			} else {
				$(this).parent().find('#minimumValueOrderErrorLabel').show();
				$(this).addClass('is-invalid');
			}
		});

		$('.selectpicker').each(function (index, element) {
			var allValues = $(element).attr('data-values');
			if (!allValues) allValues = [];
			else allValues = allValues.split(',');

			if (element.options.length === 0) {
				allCity.forEach((city) => {
					var selected = allValues.includes(city.id);
					$(element).append(new Option(city.name, city.id, false, selected));
				});
				$(element).selectpicker();
				$(element).selectpicker('val', allValues);
				$(element).selectpicker('refresh');
			}

			var cityErrorLabelCount = $(element).parent().parent().find('#cityErrorLabel').length;
			if (cityErrorLabelCount === 0) {
				$(element)
					.parent()
					.parent()
					.append(
						'<div id="cityErrorLabel" class="fv-plugins-message-container" style="display: none;"><div class="fv-help-block">' +
							WebAppLocals.getMessage('cityRequired') +
							'</div></div>'
					);
			}
		});

		$('.selectpicker').on('change', function () {
			var allValues = $(this).val();
			if (allValues.length > 0) {
				$(this).parent().parent().find('#cityErrorLabel').slideUp();
				$(this).parent().removeClass('is-invalid');
				$(this).parent().css('border', '');
			} else {
				$(this).parent().parent().find('#cityErrorLabel').slideDown();
				$(this).parent().addClass('is-invalid');
				$(this).parent().css('border', '1px solid #F64E60');
			}
		});
	};

	var _resetPasswordFields = function () {
		$('#accountSettingForm input[name=oldPassword]').val('');
		$('#accountSettingForm input[name=newPassword]').val('');
		$('#accountSettingForm input[name=newPasswordConfirmation]').val('');
	};

	var _handleMenuChange = function (menu) {
		if (_validator) {
			_validator.resetForm();
			_validator.destroy();
		}

		$('#' + menu + 'Button').css({
			'background-color': '#E8F8F6',
			cursor: '',
		});
		$('#' + menu + 'Section').show();

		var entityType = $('#profileEntityType').val();
		var allMenus = _mapEntityTypeMenus[entityType];

		allMenus.forEach((otherMenu) => {
			if (otherMenu !== menu) {
				$('#' + otherMenu + 'Button').css({
					'background-color': '',
					cursor: 'pointer',
				});
				$('#' + otherMenu + 'Section').hide();
			}
		});
		KTUtil.scrollTop();
	};

	var _save = function (menu, saveFunction) {
		var entityType = $('#profileEntityType').val();
		var mapMenuValidatorFields = _mapEntityTypeMenuValidatorFields[entityType];
		var validatorFields = mapMenuValidatorFields[menu];
		if (validatorFields) {
			if (_validator) {
				_validator.resetForm();
				_validator.destroy();
			}

			var form = KTUtil.getById(menu + 'Form');
			_validator = FormValidation.formValidation(form, {
				fields: validatorFields,
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					// Bootstrap Framework Integration
					bootstrap: new FormValidation.plugins.Bootstrap({
						//eleInvalidClass: '',
						eleValidClass: '',
					}),
				},
			});

			_validator.validate().then(function (status) {
				if (status == 'Valid') {
					_validator.resetForm();
					_validator.destroy();
					saveFunction();
				} else {
					KTUtil.scrollTop();
				}
			});
		} else {
			saveFunction();
		}
	};

	var _savePharmacyMyProfile = function () {
		let body = {
			entityDocument: _entityDocument,
		};

		let mapKeyElement = {
			userId: 'input',
			entityName: 'input',
			tradeLicenseNumber: 'input',
			address: 'textarea',
		};

		Object.keys(mapKeyElement).forEach((key) => {
			body[key] = $('#myProfileForm ' + mapKeyElement[key] + '[name=' + key + ']').val();
		});

		WebApp.post('/web/pharmacy/profile/myProfile', body, _savePharmacyMyProfileCallback);
	};

	var _savePharmacyMyProfileCallback = function (webResponse) {
		KTUtil.scrollTop();
		WebApp.alertSuccess(webResponse.message);
	};

	var _savePharmacyAccountSetting = function () {
		let body = {};

		let mapKeyElement = {
			userId: 'input',
			oldPassword: 'input',
			newPassword: 'input',
			newPasswordConfirmation: 'input',
		};

		Object.keys(mapKeyElement).forEach((key) => {
			body[key] = $('#accountSettingForm ' + mapKeyElement[key] + '[name=' + key + ']').val();
		});
		WebApp.post('/web/pharmacy/profile/accountSetting', body, _savePharmacyAccountSettingCallback);
	};

	var _savePharmacyAccountSettingCallback = function (webResponse) {
		_resetPasswordFields();
		KTUtil.scrollTop();
		WebApp.alertSuccess(webResponse.message);
	};

	var _saveDistributorMyProfile = function () {
		let body = {
			entityDocument: _entityDocument,
		};

		let mapKeyElement = {
			userId: 'input',
			entityName: 'input',
			tradeLicenseNumber: 'input',
			address: 'textarea',
		};

		Object.keys(mapKeyElement).forEach((key) => {
			body[key] = $('#myProfileForm ' + mapKeyElement[key] + '[name=' + key + ']').val();
		});

		WebApp.post('/web/distributor/profile/myProfile', body, _saveDistributorMyProfileCallback);
	};

	var _saveDistributorMyProfileCallback = function (webResponse) {
		KTUtil.scrollTop();
		WebApp.alertSuccess(webResponse.message);
	};

	var _saveDistributorAccountSetting = function () {
		let body = {};

		let mapKeyElement = {
			userId: 'input',
			oldPassword: 'input',
			newPassword: 'input',
			newPasswordConfirmation: 'input',
		};

		Object.keys(mapKeyElement).forEach((key) => {
			body[key] = $('#accountSettingForm ' + mapKeyElement[key] + '[name=' + key + ']').val();
		});
		WebApp.post('/web/distributor/profile/accountSetting', body, _saveDistributorAccountSettingCallback);
	};

	var _saveDistributorAccountSettingCallback = function (webResponse) {
		_resetPasswordFields();
		KTUtil.scrollTop();
		WebApp.alertSuccess(webResponse.message);
	};

	var _saveDistributorPaymentSetting = function () {
		var valid = true;
		var errorMessage = '';

		var allPaymentMethodId = [];
		$('#paymentSettingForm input[name=paymentMethodCheckbox]').each(function (index, element) {
			if ($(element).is(':checked')) {
				allPaymentMethodId.push($(element).val());
			}
		});

		if (allPaymentMethodId.length === 0) {
			valid = false;
			$('#paymentMethodErrorLabel').show();
		} else {
			$('#paymentMethodErrorLabel').hide();
		}

		$('.minimumValueOrderInput').each(function (index, element) {
			var value = $(element).val();
			if (value) {
				$(element).parent().find('#minimumValueOrderErrorLabel').hide();
				$(element).removeClass('is-invalid');
			} else {
				$(element).parent().find('#minimumValueOrderErrorLabel').show();
				$(element).addClass('is-invalid');
				valid = false;
			}
		});

		var allCityId = [];
		$('.selectpicker').each(function (index, element) {
			var allValues = $(this).val();
			allCityId.push(...allValues);
			if (allValues.length > 0) {
				$(element).parent().parent().find('#cityErrorLabel').slideUp();
				$(element).parent().removeClass('is-invalid');
				$(element).parent().css('border', '');
			} else {
				$(element).parent().parent().find('#cityErrorLabel').slideDown();
				$(element).parent().addClass('is-invalid');
				$(element).parent().css('border', '1px solid #F64E60');
				valid = false;
			}
		});

		var allCityIdUnique = allCityId.length === new Set(allCityId).size;
		if (valid && !allCityIdUnique) {
			var allCityNameDuplicates = [];
			var allCityIdDuplicates = allCityId.filter((city, index, arr) => arr.indexOf(city) !== index);
			$($('.selectpicker')[0])
				.find('option')
				.each(function (index, element) {
					if (allCityIdDuplicates.includes(element.value)) {
						allCityNameDuplicates.push(element.text);
					}
				});

			valid = false;
			errorMessage = WebAppLocals.getMessage('minimumValueOrderCityError') + ': ' + allCityNameDuplicates.join(', ');
		}

		if (valid) {
			var userId = $('#paymentSettingForm input[name=userId]').val();

			var allEntityMinimumValueOrder = [];
			$('#minimumValueOrderList > div').each(function (index, element) {
				var minimumValueOrder = $(element).find('#minimumValueOrder').val();
				var minimumValueOrderCityId = $(element).find('#minimumValueOrderCityId').val();
				allEntityMinimumValueOrder.push({
					minimumValueOrder,
					minimumValueOrderCityId,
				});
			});

			let body = {
				userId,
				allPaymentMethodId,
				allEntityMinimumValueOrder,
			};
			WebApp.post('/web/distributor/profile/paymentSetting', body, _saveDistributorPaymentSettingCallback);
		} else {
			KTUtil.scrollTop();
		}
	};

	var _saveDistributorPaymentSettingCallback = function (webResponse) {
		KTUtil.scrollTop();
		WebApp.alertSuccess(webResponse.message);
	};

	// upload profile image
	var _handleChangeProfileImage = function () {
		new KTImageInput('profile-image')
			.on('change', function (imageInput) {
				// let profileImage = $('#profile-image .image-input-wrapper').css('backgroundImage');
				// console.log(profileImage);

				var formData = new FormData();
				formData.append('profile_image', imageInput.input.files[0]);

				$.ajax({
					url: '/web/profile/image',
					type: 'POST',
					data: formData,
					cache: false,
					contentType: false,
					processData: false,
					beforeSend: function () {
						WebApp.blur();
						WebApp.block();
					},
					success: function (webResponse) {
						console.debug(webResponse);
						if (webResponse.data) {
							// success scenario
							Swal.fire({
								title: webResponse.message,
								icon: 'success',
								buttonsStyling: false,
								confirmButtonText: WebAppLocals.getMessage('success_confirmButtonText'),
								customClass: {
									confirmButton: 'btn btn-primary font-weight-bold',
								},
							}).then(function () {
								$('#side-panel-profile-image').css({ backgroundImage: 'url(' + webResponse.data['image'] + ')' });
							});
						} else {
							// error scenario
							Swal.fire({
								title: webResponse.message,
								icon: 'error',
								buttonsStyling: false,
								confirmButtonText: WebAppLocals.getMessage('success_confirmButtonText'),
								customClass: {
									confirmButton: 'btn btn-primary font-weight-bold',
								},
							});
						}
					},
					complete: function () {
						WebApp.unblock();
						WebApp.unblur();
					},
				});
			})
			.on('remove', function (imageInput) {
				Swal.fire({
					title: 'Image successfully removed !',
					type: 'error',
					buttonsStyling: false,
					confirmButtonText: WebAppLocals.getMessage('success_confirmButtonText'),
					customClass: {
						confirmButton: 'btn btn-primary font-weight-bold',
					},
				});
			});
	};

	// Public Functions
	return {
		init: function () {
			_init();
		},
		handleMenuChange: function (menuName) {
			_handleMenuChange(menuName);
		},
		savePharmacyMyProfile: function () {
			_save('myProfile', _savePharmacyMyProfile);
		},
		savePharmacyAccountSetting: function () {
			_save('accountSetting', _savePharmacyAccountSetting);
		},
		saveDistributorMyProfile: function () {
			_save('myProfile', _saveDistributorMyProfile);
		},
		saveDistributorAccountSetting: function () {
			_save('accountSetting', _saveDistributorAccountSetting);
		},
		saveDistributorPaymentSetting: function () {
			_saveDistributorPaymentSetting();
		},
	};
})();
