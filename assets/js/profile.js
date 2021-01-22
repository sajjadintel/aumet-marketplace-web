'use strict';

// Class Definition
var Profile = (function () {

    var _entityDocument;
	var _myDropZone;
	var _allValidators = {};
	var _mapEntityTypeMenus = {
		pharmacy: [
			"myProfile",
			"accountSetting"
		],
		distributor: [
			"myProfile",
			"accountSetting"
		]
	};
	var _mapEntityTypeMenuValidatorFields = {
		pharmacy: {
			myProfile: {
				entityName: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('required'),
						}
					},
				},
				tradeLicenseNumber: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('required'),
						}
					},
				},
				country: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('required'),
						}
					},
				},
				city: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('required'),
						}
					},
				},
				address: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('required'),
						}
					},
				}
			},
			accountSetting: {
				oldPassword: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('required'),
						}
					},
				},
				newPassword: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('required'),
						}
					},
				},
				newPasswordConfirmation: {
					validators: {
						identical: {
							compare: function () {
								return accountSettingForm.querySelector("[name=newPassword]").value;
							},
							message: WebAppLocals.getMessage('wrongPasswordConfirmation')
						}
					},
				}
			}
		},
		distributor: {
			myProfile: {
				entityName: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('required'),
						}
					},
				},
				tradeLicenseNumber: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('required'),
						}
					},
				},
				country: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('required'),
						}
					},
				},
				city: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('required'),
						}
					},
				},
				address: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('required'),
						}
					},
				}
			},
			accountSetting: {
				oldPassword: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('required'),
						}
					},
				},
				newPassword: {
					validators: {
						notEmpty: {
							message: WebAppLocals.getMessage('required'),
						}
					},
				},
				newPasswordConfirmation: {
					validators: {
						identical: {
							compare: function () {
								return accountSettingForm.querySelector("[name=newPassword]").value;
							},
							message: WebAppLocals.getMessage('wrongPasswordConfirmation')
						}
					},
				}
			}
		}
	};

    var _init = function () {
        if($('#myProfileForm select[name=country]').val()) {
            _handleCountrySelectChange(true);
        }
        $('#myProfileForm select[name=country]').on('change', () => _handleCountrySelectChange(false));
		
		var initialFile = $('#myProfileForm input[name=entityBranchTradeLicenseUrl]').val();
		_initializeDropzone(initialFile);

		_initializeValidators();
		_initializePasswordFields();

		_handleMenuChange('myProfile');
    }

    var _handleCountrySelectChange = function (initial) {
        var countryId = $('#myProfileForm select[name=country]').val();
        var cityId = $('#myProfileForm select[name=city]').val();
        if (countryId) {
            WebApp.get('/web/city/list/' + countryId, function (webResponse) {
                $('#myProfileForm select[name=city]')
                    .empty()
                    .append('<option value="">' + WebAppLocals.getMessage('city') + '</option>');
                var allCities = webResponse.data;
                allCities.forEach((city) => {
                    var selected = initial && city.id == cityId;
                    $('#myProfileForm select[name=city]').append(new Option(city.name, city.id, false, selected));
                });
                $('#myProfileForm select[name=city]').prop('disabled', false);
            });
        } else {
            $('#myProfileForm select[name=city]').prop('disabled', true);
            $('#myProfileForm select[name=city]')
                .empty()
                .append('<option value="">' + WebAppLocals.getMessage('city') + '</option>');
        }
    }

    var _initializeDropzone = function (initialFile) {
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
			acceptedFiles: '.pdf, .ppt, .xcl, .docx, .jpeg, .jpg, .png',
			maxFilesize: 10, // Max filesize in MB
			maxFiles: 1,
			previewTemplate: previewTemplate,
			previewsContainer: id + ' .dropzone-items', // Define the container to display the previews
			clickable: id + ' .dropzone-select', // Define the element that should be used as click trigger to select files.
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
			_entityDocument = response;
			
			var dropzoneFilenameElement = $(file.previewTemplate).find("#dropzoneFilename");
			$(dropzoneFilenameElement).attr("target", "_blank");
			$(dropzoneFilenameElement).attr("href", _getFullUrl(response));
		});

		// Remove file from the list
		_myDropZone.on('removedfile', function (file) {
			if (file.status === 'success') {
				_entityDocument = null;
			}
		});

		// Overwrite previous file
		_myDropZone.on("maxfilesexceeded", function(file) {
            _myDropZone.removeAllFiles();
			_myDropZone.addFile(file);
		});

		if(initialFile) {
			var fileUrl = _getFullUrl(initialFile);
			var fileName = fileUrl.substring(fileUrl.lastIndexOf('/') + 1);
			var fileExtension = fileName.substr(fileName.lastIndexOf('.') + 1);
			var fileNameWithoutTimestamp = fileName.substring(0, fileName.lastIndexOf('-')) + "." + fileExtension;
			var file = {
				status: 'success',
				accepted: true,
				name: fileNameWithoutTimestamp,
				size: _getFileSize(fileUrl),
				url: fileUrl
			};
			
			_myDropZone.files.push(file);
			_myDropZone.emit('addedfile', file);
			_myDropZone.emit('complete', file);
			_myDropZone.emit('success', file, initialFile);
		}
	}

	var _getFileSize = function (url) {
		var fileSize = '';
		var http = new XMLHttpRequest();
		http.open('HEAD', url, false); // false = Synchronous

		http.send(null); // it will stop here until this http request is complete

		// when we are here, we already have a response, b/c we used Synchronous XHR

		if (http.status === 200) {
			fileSize = http.getResponseHeader('content-length');
		}

		return fileSize;
	}

	var _getFullUrl = function (filePath) {
		return window.location.protocol + "//" + window.location.hostname + "/" + filePath;
	}

	var _initializeValidators = function () {
		// Initialize validator on each menu
		var entityType = $("#profileEntityType").val();
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
		})
	}

	var _initializePasswordFields = function () {
		$("#accountSettingForm input[name=oldPassword]").on("change", function() {
			_allValidators.accountSetting.revalidateField('newPassword');
			_allValidators.accountSetting.revalidateField('newPasswordConfirmation');
		});

		$("#accountSettingForm input[name=newPassword]").on("change", function() {
			_allValidators.accountSetting.revalidateField('oldPassword');
			_allValidators.accountSetting.revalidateField('newPasswordConfirmation');
		});

		$("#accountSettingForm input[name=newPasswordConfirmation]").on("change", function() {
			_allValidators.accountSetting.revalidateField('oldPassword');
			_allValidators.accountSetting.revalidateField('newPassword');
		});
	}

	var _resetPasswordFields = function () {
		$("#accountSettingForm input[name=oldPassword]").val('');
		$("#accountSettingForm input[name=newPassword]").val('');
		$("#accountSettingForm input[name=newPasswordConfirmation]").val('');
	}

	var _handleMenuChange = function (menu) {
		$('#' + menu + 'Button').css({
			'background-color': '#E8F8F6',
			'cursor': ''
		});
		$('#' + menu + 'Section').show();
		
		var entityType = $("#profileEntityType").val();
		var allMenus = _mapEntityTypeMenus[entityType];

		allMenus.forEach((otherMenu) => {
			if(otherMenu !== menu) {
				$('#' + otherMenu + 'Button').css({
					'background-color': '',
					'cursor': 'pointer'
				});
				$('#' + otherMenu + 'Section').hide();
			}
		})
		KTUtil.scrollTop();
	}

	var _save = function (menu, saveFunction) {
		_allValidators[menu].validate().then(function (status) {
			if (status == 'Valid') {
				saveFunction();
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
		})
	};

	var _savePharmacyMyProfile = function () {
		let body = {
			entityDocument: _entityDocument,
		};

		let mapKeyElement = {
			userId: 'input',
			entityName: 'input',
			tradeLicenseNumber: 'input',
			country: 'select',
			city: 'select',
			address: 'textarea'
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
			newPassword: 'input'
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
			country: 'select',
			city: 'select',
			address: 'textarea'
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
			newPassword: 'input'
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
	};
})();
