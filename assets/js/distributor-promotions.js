'use strict';
// Class definition
var DistributorPromotions = (function () {

    var _myDropZone;
    var _validator;
    var _mapProductIdImage = {};

    var _initSingle = function () {
        _initDatePicker();
        _initDropzone();
        _initRepeater();
        _initValidator();
    }

    var _initDatePicker = function () {
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
		
        $('#startDate').datepicker({
            rtl: KTUtil.isRTL(),
            todayHighlight: true,
            orientation: "bottom left",
            templates: arrows
        }).on('changeDate', function(e) {
            _validator.revalidateField('startDate');
        });;
		
        $('#endDate').datepicker({
            rtl: KTUtil.isRTL(),
            todayHighlight: true,
            orientation: "bottom left",
            templates: arrows
        }).on('changeDate', function(e) {
            _validator.revalidateField('endDate');
        });;
    }

    var _initDropzone = function () {
        // Set the dropzone container id
		var id = '#dropzone';

		_myDropZone = new Dropzone(id, {
			// Make the whole body a dropzone
			url: '/web/distributor/marketing/promotion/banner/upload', // Set the url for your upload script location
			acceptedFiles: '.jpeg, .jpg, .png',
			maxFilesize: 10, // Max filesize in MB
			maxFiles: 1,
            init: function () {
				this.on("error", function (file, message, xhr) {
					if (!file.accepted) this.removeFile(file);
					if (message == "You can not upload any more files.") {
						message = null;
					} else if(message.includes("too big")) {
						message = "File is too big (" + _bytesToSize(file.upload.total) + "). Max file size: 10MB."
					}
					$('.promotion-banner-container > #errorMessage').html(message);
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
			$('.promotion-banner-container > #errorMessage').html('');
			$(".promotion-banner-container > #image").val(response);

            var dropzoneImgElement = $(file.previewTemplate).find('.dz-image > img');
            $(dropzoneImgElement).attr('src', response);
            $(dropzoneImgElement).attr('style', 'width: 100%; height: 100%; cursor: pointer;');
            $(dropzoneImgElement).attr('onclick', "window.open('" + response + "', '_blank');");
        });
            
		// Remove file from the list
		_myDropZone.on('removedfile', function (file) {
			if (file.status === 'success') {
                $(".promotion-banner-container > #image").val(null);
			}
		});

		// Overwrite previous file
		_myDropZone.on('maxfilesexceeded', function (file) {
			_myDropZone.removeAllFiles();
			_myDropZone.addFile(file);
		});

		var initialFile = $('.promotion-banner-container > #image').val();
		if (initialFile) {
            _addDropzoneFile(initialFile);
		}
    }

    var _addDropzoneFile = function (fileUrl) {
        var file = {
            status: 'success',
            accepted: true,
            size: _getFileSize(fileUrl),
            url: fileUrl,
        };

        _myDropZone.removeAllFiles();
        _myDropZone.files.push(file);
        _myDropZone.emit('addedfile', file);
        _myDropZone.emit('complete', file);
        _myDropZone.emit('success', file, fileUrl);
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

	var _bytesToSize = function(bytes) {
		var sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
		if (bytes == 0) return '0B';
		var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
		return Math.round(bytes / Math.pow(1024, i), 2) + sizes[i];
	}

    var _initRepeater = function () {
        var repeaterElementTemplate;
        $('#featuredProductsList > div').each(function (index, element) {
            repeaterElementTemplate = $(element).clone();
        });

		var arrProducts = [];
		if($('#featuredProductsList').attr("data-arrProducts")) {
			arrProducts = JSON.parse($('#featuredProductsList').attr("data-arrProducts"));
		}
        
        arrProducts.forEach((product) => {
            _mapProductIdImage[product.id] = product.image;
        })

		var arrFeaturedProducts = [];
		if($('#featuredProductsList').attr("data-arrFeaturedProducts")) {
			arrFeaturedProducts = JSON.parse($('#featuredProductsList').attr("data-arrFeaturedProducts"));
		}

        $('#featuredProductsRepeater').repeater({
            initEmpty: true,
            show: function () {
                _initializeRepeaterElements(arrProducts);
                $(this).slideDown();
            },
            hide: function (deleteElement) {
                $(this).slideUp(deleteElement);
            },
        });

        arrFeaturedProducts.forEach((repeaterData) => {
            var repeaterRow = $(repeaterElementTemplate).clone();
            $(repeaterRow).find('#featuredProducts').attr('data-value', repeaterData.id);
            $('#featuredProductsList').append(repeaterRow);
        });

		_initializeRepeaterElements(arrProducts);
    }

	var _initializeRepeaterElements = function (arrProducts) {
		$('.selectpicker.featuredProductsSelect').each(function (index, element) {
			if (!$(element).parent().is('.dropdown.bootstrap-select.form-control.featuredProductsSelect')) {
				var value = $(element).attr('data-value');
				if (element.options.length === 0) {
					arrProducts.forEach((product) => {
						var selected = product.id == value;
						$(element).append(new Option(product.name, product.id, false, selected));
					});
					$(element).selectpicker({dropupAuto: false});
				}
				$(element).selectpicker('val', value ? value : null);
				$(element).selectpicker('refresh');
			}
		});
	};

    var _initValidator = function () {
        var form = KTUtil.getById('promotionForm');
        var formSubmitUrl = KTUtil.attr(form, 'action');
        var formSubmitButton = KTUtil.getById('promotionSubmitButton');

        var mandatoryFields = [
            "name",
            "startDate",
            "endDate",
            "message"
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

        _validator = FormValidation.formValidation(form, {
            fields: validatorFields,
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                submitButton: new FormValidation.plugins.SubmitButton(),
                // Bootstrap Framework Integration
                bootstrap: new FormValidation.plugins.Bootstrap({ eleValidClass: '' }),
            },
        });

        _validator.on('core.form.valid', function () {
            var valid = true;
            $('#featuredProductsList > div').each(function (index, element) {
                var productElement = $(element).find('#featuredProducts');
                var productId = $(productElement).val();
                if (!productId) {
                    if (!$(productElement).parent().hasClass('is-invalid')) {
                        $(productElement).parent().addClass('is-invalid');
                        $(productElement).parent().css('border', '1px solid #F64E60');
                    }
                } else {
                    $(productElement).parent().removeClass('is-invalid');
                    $(productElement).parent().css('border', '');
                }

                valid = valid && productId;
            });

            $('.selectpicker.featuredProductsSelect').on('change', function () {
                var value = $(this).val();
                if (value) {
                    $(this).parent().removeClass('is-invalid');
                    $(this).parent().css('border', '');
                } else {
                    $(this).parent().addClass('is-invalid');
                    $(this).parent().css('border', '1px solid #F64E60');
                }
            });

            if (valid) {
                var data = $(form).serializeJSON();
    
                var arrFeaturedProducts = [];
                $('#featuredProductsList > div').each(function (index, element) {
                    var productId = $(element).find('#featuredProducts').val();
                    arrFeaturedProducts.push({
                        productId
                    });
                });

                data = {
                    ...data,
                    arrFeaturedProducts
                };

                // Show loading state on button
                var buttonSpinnerClasses = "spinner spinner-right spinner-white pr-15";
                KTUtil.btnWait(formSubmitButton, buttonSpinnerClasses, WebAppLocals.getMessage('pleaseWait'));
                $(formSubmitButton).prop('disabled', true);

                WebApp.post(formSubmitUrl, data, _savePromotionSuccessCallback, formSubmitButton);
            } else {
                KTUtil.scrollTop();
            }
        });

        _validator.on('core.form.invalid', function () {
            KTUtil.scrollTop();
        });
    }

    var _useProductImage = function (element) {
        var productId = $(element).parent().parent().find("#featuredProducts").val();
        var image;
        if(productId in _mapProductIdImage) image = _mapProductIdImage[productId];
        if(image) {
            _addDropzoneFile(image);
        }
    }

	var _savePromotionSuccessCallback = function (webResponse) {
		WebApp.loadPage('/web/distributor/marketing/promotion');
	};

    var _deletePromotion = function (promotionId) {
		Swal.fire({
			html: WebAppLocals.getMessage("deletePromotion"),
			buttonsStyling: false,
			confirmButtonText: WebAppLocals.getMessage('delete'),
            showCloseButton: true,
            showCancelButton: true,
            cancelButtonText: WebAppLocals.getMessage('cancel'),
			customClass: {
				confirmButton: 'btn font-weight-bold btn-light-primary mx-10 px-10',
				cancelButton: 'btn font-weight-bold btn-secondary mx-10 px-10',
			},
		}).then(function (result) {
			if (result.isConfirmed) {
                WebApp.get('/web/distributor/marketing/promotion/delete/' + promotionId, _deletePromotionSuccessCallback);
            }
		});
    }

    var _deletePromotionSuccessCallback = function (webResponse) {
        WebApp.alertSuccess(webResponse.message);
        WebApp.reloadDatatable();
    }

    return {
        // public functions
        initSingle: function () {
            _initSingle();
        },
        useProductImage: function (element) {
            _useProductImage(element);
        },
        deletePromotion: function (promotionId) {
            _deletePromotion(promotionId);
        }
    };
})();
