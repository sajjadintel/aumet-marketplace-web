'use strict';

var datatableVar = [];

// Class Definition
var WebApp = (function () {
	var _buttonSpinnerClasses = 'spinner spinner-right spinner-white pr-15';
	var _pageContainerId = '#pageContent';

	var _lastWebResponse = null;
	var _stackWebResponse = [];

	var _idToken = '';

	var _timeoutSession;

	var _validator;

	var _alertError = function (msg) {
		Swal.fire({
			html: msg,
			icon: 'error',
			buttonsStyling: false,
			confirmButtonText: WebAppLocals.getMessage('error_confirmButtonText'),
			customClass: {
				confirmButton: 'btn font-weight-bold btn-light-primary',
			},
		}).then(function () {
			KTUtil.scrollTop();
		});
	};

	var _alertSuccess = function (msg) {
		Swal.fire({
			text: msg,
			icon: 'success',
			buttonsStyling: false,
			confirmButtonText: WebAppLocals.getMessage('success_confirmButtonText'),
			customClass: {
				confirmButton: 'btn font-weight-bold btn-light-primary',
			},
		}).then(function () {
			KTUtil.scrollTop();
		});
	};

	var _alertNewOrders = function (ordersCount) {
		Swal.fire({
			text: 'You have received ( ' + ordersCount + ' ) orders',
			icon: 'success',
			buttonsStyling: false,
			confirmButtonText: 'Start Working On Them',
			showCancelButton: true,
			cancelButtonText: 'Ok',
			customClass: {
				confirmButton: 'btn font-weight-bold btn-primary',
				cancelButton: 'btn font-weight-bold btn-outline-primary',
			},
		}).then((result) => {
			KTUtil.scrollTop();
			if (result.value) {
				WebApp.loadPage('/web/distributor/order/pending');
			}
		});
	};

	var _getAsync = function (url, fnCallback = null) {
		$.ajax({
			url: url + '?_t=' + Date.now(),
			type: 'GET',
			dataType: 'json',
			async: true,
		})
			.done(function (webResponse) {
				if (webResponse && typeof webResponse === 'object') {
					fnCallback(webResponse);
				} else {
					fnCallback(false);
				}
			})
			.fail(function (jqXHR, textStatus, errorThrown) {
				fnCallback(false);
			});
	};

	var _get = function (url, fnCallback = null) {
		_blurPage();
		_blockPage();
		$.ajax({
			url: url + '?_t=' + Date.now(),
			type: 'GET',
			dataType: 'json',
			async: true,
		})
			.done(function (webResponse) {
				if (webResponse && typeof webResponse === 'object') {
					if (webResponse.errorCode == 1) {
						if (typeof fnCallback === 'function') {
							fnCallback(webResponse);
						}
						_unblurPage();
						_unblockPage();
					} else if (webResponse.errorCode == 0) {
						window.location.href = '/web';
					} else {
						_unblurPage();
						_unblockPage();
						_alertError(webResponse.message);
					}
				} else {
					_unblurPage();
					_unblockPage();
					_alertError(WebAppLocals.getMessage('error'));
				}
			})
			.fail(function (jqXHR, textStatus, errorThrown) {
				_alertError(WebAppLocals.getMessage('error'));
				_unblurPage();
				_unblockPage();
			});
	};

	var _post = function (url, data = null, fnCallback = null) {
		_blurPage();
		_blockPage();
		$.ajax({
			url: url + '?_t=' + Date.now(),
			type: 'POST',
			dataType: 'json',
			data: data,
			async: true,
		})
			.done(function (webResponse) {
				if (webResponse && typeof webResponse === 'object') {
					if (webResponse.errorCode == 1) {
						if (typeof fnCallback === 'function') {
							fnCallback(webResponse);
						}
						_unblurPage();
						_unblockPage();
					} else if (webResponse.errorCode == 0) {
						window.location.href = '/web';
					} else if (webResponse.errorCode == 3) {
						if (typeof fnCallback === 'function') {
							fnCallback(webResponse);
						}
						_unblurPage();
						_unblockPage();
						_alertSuccess(webResponse.message);
					} else {
						_unblurPage();
						_unblockPage();
						_alertError(webResponse.message);
					}
				} else {
					_unblurPage();
					_unblockPage();
					_alertError(WebAppLocals.getMessage('error'));
				}
			})
			.fail(function (jqXHR, textStatus, errorThrown) {
				_alertError(WebAppLocals.getMessage('error'));
				_unblurPage();
				_unblockPage();
			});
	};

	var _loadPage = function (url, isSubPage = false, fnCallback = null) {
		_blurPage();
		_blockPage();
		var fullUrl;
		if(url.includes("?")) {
			var allParts = url.split("?");
			var mainUrl = allParts.shift();
			var queryParams = allParts.join("?");

			fullUrl = mainUrl + '?' + queryParams + '&_t=' + Date.now();
		} else {
			fullUrl = url + '?_t=' + Date.now();
		}

		$.ajax({
			url: fullUrl,
			type: 'GET',
			dataType: 'json',
			async: true,
		})
			.done(function (webResponse) {
				if (webResponse && typeof webResponse === 'object') {
					if (webResponse.errorCode == 1) {
						var title = webResponse.title != null ? webResponse.title : document.title;

						$('#subHeaderPageTitle').text(title);

						webResponse.url = url;
						if (!isSubPage) {
							_lastWebResponse = webResponse;
						} else {
							_stackWebResponse.push(webResponse);
						}

						$(_pageContainerId).html(webResponse.data);

						if (window.history && window.history.pushState) {
							window.history.pushState({id: _id, url: url, title: title}, title, url);
							console.debug('pushState', {id: _id, url: url, title: title,}, title, url);

							// update title of webpage
							if (title !== WebAppLocals.getMessage('appName')) {
								document.title = title + ' | ' + WebAppLocals.getMessage('appName');
							}
						} else {
							console.error('window.history.pushState not available. Are you using older browser?')
						}

						if (typeof fnCallback === 'function') {
							fnCallback();
						}
						_unblurPage();
						_unblockPage();
					} else if (webResponse.errorCode == 0) {
						window.location.href = '/web';
					} else {
						_unblurPage();
						_unblockPage();
						_alertError(webResponse.message);
					}
				} else {
					_unblurPage();
					_unblockPage();
					_alertError(WebAppLocals.getMessage('error'));
				}

				_initModal();
			})
			.fail(function (jqXHR, textStatus, errorThrown) {
				_alertError(WebAppLocals.getMessage('error'));
				_unblurPage();
				_unblockPage();
			});
	};

	var _handleBrowserNavigation = function (url, state = null, isSubPage = false, fnCallback = null) {
		_blurPage();
		_blockPage();
		var fullUrl;
		if(url.includes("?")) {
			var allParts = url.split("?");
			var mainUrl = allParts.shift();
			var queryParams = allParts.join("?");

			fullUrl = mainUrl + '?' + queryParams + '&_t=' + Date.now();
		} else {
			fullUrl = url + '?_t=' + Date.now();
		}

		$.ajax({
			url: fullUrl,
			type: 'GET',
			dataType: 'json',
			async: true,
		})
			.done(function (webResponse) {
				if (webResponse && typeof webResponse === 'object') {
					if (webResponse.errorCode == 1) {
						var title = webResponse.title != null ? webResponse.title : document.title;

						$('#subHeaderPageTitle').text(title);

						webResponse.url = url;
						if (!isSubPage) {
							_lastWebResponse = webResponse;
						} else {
							_stackWebResponse.push(webResponse);
						}

						$(_pageContainerId).html(webResponse.data);

						console.debug('browserNavigation', state, title, url);

						// update title of webpage
						if (title !== WebAppLocals.getMessage('appName')) {
							document.title = title + ' | ' + WebAppLocals.getMessage('appName');
						}

						if (typeof fnCallback === 'function') {
							fnCallback();
						}
						_unblurPage();
						_unblockPage();
					} else if (webResponse.errorCode == 0) {
						window.location.href = '/web';
					} else {
						_unblurPage();
						_unblockPage();
						_alertError(webResponse.message);
					}
				} else {
					_unblurPage();
					_unblockPage();
					_alertError(WebAppLocals.getMessage('error'));
				}

				_initModal();
			})
			.fail(function (jqXHR, textStatus, errorThrown) {
				_alertError(WebAppLocals.getMessage('error'));
				_unblurPage();
				_unblockPage();
			});
	};

	var _closeSubPage = function (fnCallback = null) {
		var _pre = _stackWebResponse.pop();
		if (!_pre) {
			_pre = _lastWebResponse;
			_lastWebResponse = null;
		}

		console.log(_pre);

		if (_pre) {
			var title = _pre.title != null ? _pre.title : document.title;

			$('#subHeaderPageTitle').text(title);

			$(_pageContainerId).html(_pre.data);

			if (typeof fnCallback === 'function') {
				fnCallback();
			}
			_unblurPage();
			_unblockPage();
		} else {
			_loadPage('/web/product/search');
		}
	};

	var _blockPage = function (_msgKey = 'loading') {
		KTApp.blockPage({
			overlayColor: 'black',
			opacity: 0.2,
			message: WebAppLocals.getMessage(_msgKey),
			state: 'primary', // a bootstrap color
		});
	};

	var _unblockPage = function () {
		KTApp.unblockPage();
	};

	var _blurPage = function () {
		$(_pageContainerId).foggy({
			blurRadius: 3,
			opacity: 1,
			cssFilterSupport: true,
		});
	};

	var _unblurPage = function () {
		$(_pageContainerId).foggy(false);
	};

	var _initModal = function () {
		$('.modalAction').each(function () {
			$(this).off('click');
			$(this).click(function (e) {
				e.preventDefault();
				var form = $(this).parent().parent().parent();
				var url = $(form).attr('action');
				var data = $(form).serializeJSON();
				var callback = null;
				if ($(form).find('.modalValueCallback').val() != '') {
					callback = eval($(form).find('.modalValueCallback').val());
				}

				if($(this).attr("data-modalValidatorFields")) {
					var fullForm = KTUtil.getById($(form).attr("id"));
					var validatorFields = JSON.parse($(this).attr("data-modalValidatorFields"));

					if(_validator) _validator.destroy();
					_validator = FormValidation.formValidation(fullForm, {
						fields: validatorFields,
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

					_validator.validate().then(function (status) {
						if (status == 'Valid') {
							_post(url, data, callback);
							$(form).parent().parent().parent().modal('hide');
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
				} else {
					_post(url, data, callback);
					$(form).parent().parent().parent().modal('hide');
				}
			});
		});
	};

	var _openModal = function (webResponse) {
		_resetModal();
		$('.modalForm').attr('action', webResponse.data.modalRoute);
		$('#popupModalTitle').html(webResponse.data.modalTitle);
		$('#popupModalText').html(webResponse.data.modalText);
		$('#popupModalValueId').val(webResponse.data.id);
		$('.modalValueCallback').val(webResponse.data.fnCallback);
		$('.modalAction').html(webResponse.data.modalButton);
		$('#popupModal').modal('show');
	};

	var _resetModal = function () {
		$('.modalForm').attr('action', '#');
		$('#popupModalTitle').html('');
		$('#popupModalText').html('');
		$('#popupModalValueId').val('');
		$('.modalValueCallback').val('');
		$('.modalAction').html('');
	};

	var _signout = function () {
		firebase
			.auth()
			.signOut()
			.then(function () {
				// Sign-out successful.
			})
			.catch(function (error) {
				// An error happened.
			});
	};

	var _setUpFirebase = function () {
		firebase.auth().onAuthStateChanged(function (user) {
			if (!user) {
				_loadPage('/web/auth/signout', false, null);
			} else {
				firebase
					.auth()
					.currentUser.getIdToken(true)
					.then(function (idToken) {
						_idToken = idToken;
					})
					.catch(function (error) {
						// Handle error
					});
			}
		});
	};

	var _setSessionTimeoutCallback = function () {
		/*
		_timeoutSession = setTimeout(function () {
			if (confirm("Press a button!\nEither OK or Cancel.")) {
				_setSessionTimeoutCallback();
			}
			else {
				_signout();
			}
		}, 5000); //30s
		*/
	};

	var _initSessionTimeout = function () {
		document.addEventListener(
			'mousemove',
			function (e) {
				clearTimeout(_timeoutSession);
				_setSessionTimeoutCallback();
			},
			true
		);
	};

	var _createDatatableServerside = function (vTableName, vElementId, vUrl, vColumnDefs, vParams = null, vAdditionalOptions = null) {
		_blurPage();
		_blockPage();

		// // delete cached datatable
		// if ($.fn.DataTable.isDataTable(datatableVar)) {
		// 	datatableVar.clear().destroy();
		// }

		var fileName = 'Aumet Marketplace - ' + vTableName;

		var dbOptions = {
			dom: 'Brt<"float-right"i><"float-right"l><"float-left"p>',
			responsive: true,
			scrollX: false,
			orderCellsTop: true,
			order: [[0, 'asc']],
			destroy: true,
			language: {
				lengthMenu: '_MENU_',
				info: 'Showing _START_ - _END_ of _TOTAL_',
				infoEmpty: 'Showing 0',
				infoFiltered: '(from _MAX_ total)',
			},
			buttons: [
				{
					extend: 'excelHtml5',
					filename: fileName,
					exportOptions: {
						columns: '.export_datatable',
					},
					action: _exportAllAction,
				},
				{
					extend: 'pdfHtml5',
					filename: fileName,
					exportOptions: {
						columns: '.export_datatable',
					},
					action: _exportAllAction,
				},
			],
			processing: true,
			serverSide: true,
			ajax: {
				url: vUrl,
				dataType: 'json',
				type: 'POST',
				data: {},
			},
			columnDefs: vColumnDefs,
		};

		if (vParams != null) {
			dbOptions['ajax']['data']['query'] = vParams;
		}

		var dbOptionsObj = { ...dbOptions };

		if (vAdditionalOptions && vAdditionalOptions.datatableOptions) {
			var dbOptionsObj = { ...dbOptions, ...vAdditionalOptions.datatableOptions };
		}

		datatableVar.push($('' + vElementId).DataTable(dbOptionsObj));

		datatableVar[datatableVar.length - 1].on('draw', function () {
			_unblurPage();
			_unblockPage();
		});

		$.fn.dataTable.ext.errMode = function (settings, helpPage, message) {
			console.log(message);
		};

		return datatableVar;
	};

	var _createDatatableLocal = function (vTableName, vElementId, vData, vColumnDefs, vAdditionalOptions = null) {
		_blurPage();
		_blockPage();

		// // delete cached datatable
		// if ($.fn.DataTable.isDataTable(datatableVar)) {
		// 	datatableVar.clear().destroy();
		// }

		var fileName = 'Aumet Marketplace - ' + vTableName;

		var dbOptions = {
			dom: 'Brt<"float-right"i><"float-right"l><"float-left"p>',
			responsive: true,
			scrollX: false,
			orderCellsTop: true,
			order: [[0, 'asc']],
			destroy: true,
			language: {
				lengthMenu: '_MENU_',
				info: 'Showing _START_ - _END_ of _TOTAL_',
				infoEmpty: 'Showing 0',
				infoFiltered: '(from _MAX_ total)',
			},
			buttons: [
				{
					extend: 'excelHtml5',
					filename: fileName,
					exportOptions: {
						columns: '.export_datatable',
					},
				},
				{
					extend: 'pdfHtml5',
					filename: fileName,
					exportOptions: {
						columns: '.export_datatable',
					},
				},
			],
			processing: true,
			data: vData,
			columnDefs: vColumnDefs,
		};

		var dbOptionsObj = { ...dbOptions };

		if (vAdditionalOptions && vAdditionalOptions.datatableOptions) {
			var dbOptionsObj = { ...dbOptions, ...vAdditionalOptions.datatableOptions };
		}

		datatableVar.push($('' + vElementId).DataTable(dbOptionsObj));

		datatableVar[datatableVar.length - 1].on('draw', function () {
			_unblurPage();
			_unblockPage();
		});

		return datatableVar;
	};

	var _destroyDatatable = function (vElementId) {
		if ($.fn.DataTable.isDataTable(vElementId)) {
			$(vElementId).DataTable().destroy();
		}
		$(vElementId).empty();
	};

	var _reloadDatatable = function (vType, vElementId) {
		if ($.fn.DataTable.isDataTable('' + vElementId)) {
			$('' + vElementId).ajax.reload();
		}
	};

	var _handleNotificationTimer = function (webResponse) {
		if (webResponse && typeof webResponse === 'object') {
			if (webResponse.data > 0) {
				_alertNewOrders(webResponse.data);
			}
		}
	};

	var _initNotificationTimer = function () {
		setInterval(function () {
			WebApp.getAsync('/web/notification/order/new', _handleNotificationTimer);
		}, 5000);
	};

	var _redirect = function (url) {
		$(location).attr('href', url);
	};

	var _exportAllAction = function exportAllAction(e, dt, button, config) {
		var self = this;
		var oldStart = dt.settings()[0]._iDisplayStart;
		dt.one('preXhr', function (e, s, data) {
			/* Just this once, load all data from the server...  */
			data.start = 0;
			data.length = 2147483647;
			dt.one('preDraw', function (e, settings) {
				/* Call the original action function  */
				if (button[0].className.indexOf('buttons-copy') >= 0) {
					$.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
				} else if (button[0].className.indexOf('buttons-excel') >= 0) {
					$.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
						$.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
						$.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
				} else if (button[0].className.indexOf('buttons-csv') >= 0) {
					$.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
						$.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
						$.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
				} else if (button[0].className.indexOf('buttons-pdf') >= 0) {
					$.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
						$.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
						$.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
				} else if (button[0].className.indexOf('buttons-print') >= 0) {
					$.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
				}
				dt.one('preXhr', function (e, s, data) {
					/* DataTables thinks the first item displayed is index 0, but we're not drawing that. */
					/* Set the property to what it was before exporting. */
					settings._iDisplayStart = oldStart;
					data.start = oldStart;
				});
				/* Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.  */
				setTimeout(dt.ajax.reload, 0);
				/* Prevent rendering of the full data to the DOM  */
				return false;
			});
		});
		/* Requery the server with the new one-time export settings  */
		dt.ajax.reload();
	}

	var _truncateText = function truncateText(str, n) {
		return (str.length > n) ? str.substr(0, n - 1) + '&hellip;' : str;
	}

	var _formatMoney = function formatMoney(number, n=2, x) {
		var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
		return parseFloat(number).toFixed(Math.max(0, ~~n)).replace(new RegExp(re, 'g'), '$&,');
	}

	var _supportModalForm = function () {
		var _buttonSpinnerClasses = 'spinner spinner-right spinner-white pr-15';
		var form = KTUtil.getById('supportModalForm');
		var formSubmitUrl = KTUtil.attr(form, 'action');
		var data = $(form).serializeJSON();
		var formSubmitButton = KTUtil.getById('kt_cs_form_submit_button');

		if (!form) {
			return;
		}

		FormValidation.formValidation(form, {
			fields: {
				supportEmail: {
					validators: {
						notEmpty: {
							message: 'Email is required',
						},
						emailAddress: {
							message: 'The value is not a valid email address',
						},
					},
				},
				phone: {
					validators: {
						notEmpty: {
							message: 'Phone Number is required',
						},
					},
				},
				supportReasonId: {
					validators: {
						notEmpty: {
							message: 'Reason is required',
						},
					},
				},
			},
			plugins: {
				trigger: new FormValidation.plugins.Trigger(),
				submitButton: new FormValidation.plugins.SubmitButton(),
				//defaultSubmit: new FormValidation.plugins.DefaultSubmit(), // Uncomment this line to enable normal button submit after form validation
				bootstrap: new FormValidation.plugins.Bootstrap({
					//	eleInvalidClass: '', // Repace with uncomment to hide bootstrap validation icons
					//	eleValidClass: '',   // Repace with uncomment to hide bootstrap validation icons
				}),
			},
		})
			.on('core.form.valid', function () {
				// Show loading state on button
				KTUtil.btnWait(formSubmitButton, _buttonSpinnerClasses, 'Please wait');

				var url = KTUtil.attr(form, 'action');
				var data = $(form).serializeJSON();

				if (!form) {
					console.log('No Form');
					return;
				}
				$('#support_modal').modal('hide');
				WebApp.post(url, data);
			})
			.on('core.form.invalid', function () {
				Swal.fire({
					text: 'Sorry, looks like there are some errors detected, please try again.',
					icon: 'error',
					buttonsStyling: false,
					confirmButtonText: 'Ok, got it!',
					customClass: {
						confirmButton: 'btn font-weight-bold btn-light-primary',
					},
				}).then(function () {
					KTUtil.scrollTop();
				});
			});
	};

	// Public Functions
	return {
		init: function () {
			//_setUpFirebase();
			WebAppLocals.init();
			_initModal();
			_loadPage(window.location.href);
			//Cart.init();

			//RegistrationWizard.init();

			//_initSessionTimeout();

			//$("#webGuidedTourModal").modal();

			_initNotificationTimer();

			// handle browser navigation
			$(window).on('popstate', function() {
				_handleBrowserNavigation(window.history.state.url, window.history.state);
			});
		},
		signout: function () {
			return _signout();
		},
		loadPage: function (url, fnCallback = null) {
			return _loadPage(url, false, fnCallback);
		},
		loadSubPage: function (url, fnCallback = null) {
			return _loadPage(url, true, fnCallback);
		},
		closeSubPage: function (fnCallback = null) {
			return _closeSubPage(fnCallback);
		},
		block: function (_msgKey = 'loading') {
			return _blockPage(_msgKey);
		},
		unblock: function () {
			return _unblockPage();
		},
		alertSuccess: function (msg) {
			return _alertSuccess(msg);
		},
		alertError: function (msg) {
			return _alertError(msg);
		},
		get: function (url, fnCallback = null) {
			return _get(url, fnCallback);
		},
		getAsync: function (url, fnCallback = null) {
			return _getAsync(url, fnCallback);
		},
		post: function (url, data = null, fnCallback = null) {
			return _post(url, data, fnCallback);
		},
		openModal: function (webResponse) {
			_openModal(webResponse);
		},
		CreateDatatableServerside: function (vTableName, vElementId, vUrl, vColumnDefs, vParams = null, vAdditionalOptions = null) {
			_createDatatableServerside(vTableName, vElementId, vUrl, vColumnDefs, vParams, vAdditionalOptions);
		},
		CreateDatatableLocal: function (vTableName, vElementId, vData, vColumnDefs, vAdditionalOptions = null) {
			_createDatatableLocal(vTableName, vElementId, vData, vColumnDefs, vAdditionalOptions);
		},
		DestroyDatatable: function (vElementId) {
			_destroyDatatable(vElementId);
		},
		ReloadDatatableLocal: function (vElementId) {
			_reloadDatatable('local', vElementId);
		},
		ReloadDatatableServerside: function (vElementId) {
			_reloadDatatable('serverside', vElementId);
		},
		redirect: function (url) {
			_redirect( url);
		},
		truncateText: function (text, n) {
			return _truncateText(text, n);
		},
		formatMoney: function (number, n, x) {
			return _formatMoney(number, n, x);
		},
		supportModalFormValidation: function () {
			return _supportModalForm();
		},
		reloadDatatable: function (webResponse) {
			if ($('#popupModal').is(':visible')) {
				$('#popupModal').modal('hide');
			}
			datatableVar.forEach(function (vValue) {
				vValue.ajax.reload();
			});
		},
	};
})();
