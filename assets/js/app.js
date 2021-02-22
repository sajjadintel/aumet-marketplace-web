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
	var _supportModalValidator;

	var blurStack = 0;
	var blockStack = 0;
	var tempBlurStack = 0;
	var tempBlockStack = 0;

	var _notificationInProgress = false;

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
		while (blurStack > 0) {
			_unblurPage(44);
			++tempBlurStack;
		}
		while (blockStack > 0) {
			_unblockPage(48);
			++tempBlockStack;
		}

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

			while (tempBlurStack >= 0) {
				--tempBlurStack;
				_blurPage(63);
			}
			while (tempBlockStack >= 0) {
				--tempBlockStack;
				_blockPage(67);
			}
			console.debug('blurStack', blurStack);
			console.debug('blockStack', blockStack);
			console.debug('tempBlurStack', tempBlurStack);
			console.debug('tempBlockStack', tempBlockStack);
		});
	};

	var _alertNewOrders = function (ordersCount) {
		Swal.fire({
			text: WebAppLocals.getMessage('newOrderMessage').format(ordersCount),
			icon: 'success',
			buttonsStyling: false,
			confirmButtonText: WebAppLocals.getMessage('review'),
			showCancelButton: true,
			cancelButtonText: WebAppLocals.getMessage('ignore'),
			customClass: {
				confirmButton: 'btn font-weight-bold btn-primary',
				cancelButton: 'btn font-weight-bold btn-outline-primary',
			},
		}).then((result) => {
			KTUtil.scrollTop();
			if (result.value) {
				WebApp.loadPage('/web/distributor/order/pending');
			} else {
				_notificationInProgress = false;
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
		$.ajax({
			url: url + '?_t=' + Date.now(),
			type: 'GET',
			dataType: 'json',
			async: true,
			beforeSend: function (jqXHR, settings) {
				_blurPage(96);
				_blockPage(97);
			},
		})
			.done(function (webResponse) {
				if (webResponse && typeof webResponse === 'object') {
					if (webResponse.errorCode == 1) {
						if (typeof fnCallback === 'function') {
							fnCallback(webResponse);
						}
					} else if (webResponse.errorCode == 0) {
						window.location.href = '/web';
					} else {
						_alertError(webResponse.message);
					}
				} else {
					_alertError(WebAppLocals.getMessage('error'));
				}
			})
			.fail(function (jqXHR, textStatus, errorThrown) {
				_alertError(WebAppLocals.getMessage('error'));
			})
			.always(function (jqXHR, textStatus, errorThrown) {
				_unblurPage(119);
				_unblockPage(120);
			});
	};

	var _post = function (url, data = null, fnCallback = null, submitButton = null, forceCallback = false, forcePreventUnblur = false) {
		$.ajax({
			url: url + '?_t=' + Date.now(),
			type: 'POST',
			dataType: 'json',
			data: data,
			async: true,
			beforeSend: function (jqXHR, settings) {
				_blurPage(132);
				_blockPage(133);
			},
		})
			.done(function (webResponse) {
				if (webResponse && typeof webResponse === 'object') {
					if (webResponse.errorCode == 1) {
						if (typeof fnCallback === 'function') {
							fnCallback(webResponse);
						}
					} else if (webResponse.errorCode == 0) {
						window.location.href = '/web';
					} else if (webResponse.errorCode == 3) {
						if (typeof fnCallback === 'function') {
							fnCallback(webResponse);
						}
						_alertSuccess(webResponse.message);
					} else {
						if (forceCallback) {
							if (typeof fnCallback === 'function') {
								fnCallback(webResponse);
							}
						}
						_alertError(webResponse.message);
					}
				} else {
					if (forceCallback) {
						if (typeof fnCallback === 'function') {
							fnCallback(webResponse);
						}
					}
					_alertError(WebAppLocals.getMessage('error'));
				}
				if (submitButton) {
					KTUtil.btnRelease(submitButton);
					$(submitButton).prop('disabled', false);
				}
			})
			.fail(function (jqXHR, textStatus, errorThrown) {
				if (forceCallback) {
					if (typeof fnCallback === 'function') {
						fnCallback(webResponse);
					}
				}
				_alertError(WebAppLocals.getMessage('error'));
				if (submitButton) {
					KTUtil.btnRelease(submitButton);
					$(submitButton).prop('disabled', false);
				}
			})
			.always(function (jqXHR, textStatus, errorThrown) {
				_unblurPage(182);
				_unblockPage(184);
			});
	};

	var _loadPage = function (url, isSubPage = false, fnCallback = null) {
		var fullUrl;
		if (url.includes('?')) {
			var allParts = url.split('?');
			var mainUrl = allParts.shift();
			var queryParams = allParts.join('?');

			fullUrl = mainUrl + '?' + queryParams + '&_t=' + Date.now();
		} else {
			fullUrl = url + '?_t=' + Date.now();
		}

		$.ajax({
			url: fullUrl,
			type: 'GET',
			dataType: 'json',
			async: true,
			beforeSend: function (jqXHR, settings) {
				_blurPage(207);
				_blockPage(208);
			},
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
							window.history.pushState({ id: _id, url: url, title: title }, title, url);
							console.debug('pushState', { id: _id, url: url, title: title }, title, url);

							// update title of webpage
							if (title !== WebAppLocals.getMessage('appName')) {
								document.title = title + ' | ' + WebAppLocals.getMessage('appName');
							}
						} else {
							console.error('window.history.pushState not available. Are you using older browser?');
						}

						if (typeof fnCallback === 'function') {
							fnCallback();
						}
					} else if (webResponse.errorCode == 0) {
						window.location.href = '/web';
					} else {
						_alertError(webResponse.message);
					}
				} else {
					_alertError(WebAppLocals.getMessage('error'));
				}

				_initModal();
			})
			.fail(function (jqXHR, textStatus, errorThrown) {
				_alertError(WebAppLocals.getMessage('error'));
			})
			.always(function (jqXHR, textStatus, errorThrown) {
				_unblurPage(256);
				_unblockPage(257);
			});
	};

	var _handleBrowserNavigation = function (url, state = null, isSubPage = false, fnCallback = null) {
		var fullUrl;
		if (url.includes('?')) {
			var allParts = url.split('?');
			var mainUrl = allParts.shift();
			var queryParams = allParts.join('?');

			fullUrl = mainUrl + '?' + queryParams + '&_t=' + Date.now();
		} else {
			fullUrl = url + '?_t=' + Date.now();
		}

		$.ajax({
			url: fullUrl,
			type: 'GET',
			dataType: 'json',
			async: true,
			beforeSend: function (jqXHR, settings) {
				_blurPage(279);
				_blockPage(280);
			},
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
					} else if (webResponse.errorCode == 0) {
						window.location.href = '/web';
					} else {
						_alertError(webResponse.message);
					}
				} else {
					_alertError(WebAppLocals.getMessage('error'));
				}

				_initModal();
			})
			.fail(function (jqXHR, textStatus, errorThrown) {
				_alertError(WebAppLocals.getMessage('error'));
			})
			.always(function (jqXHR, textStatus, errorThrown) {
				_unblurPage(324);
				_unblockPage(325);
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
			_unblurPage(348);
			_unblockPage(349);
		} else {
			_loadPage('/web/pharmacy/product/search');
		}
	};

	var _blockPage = function (line, _msgKey = 'loading') {
		++blockStack;

		KTApp.blockPage({
			overlayColor: 'black',
			opacity: 0.2,
			message: WebAppLocals.getMessage(_msgKey),
			state: 'primary', // a bootstrap color
		});
		console.debug('block', line, blockStack);
	};

	var _unblockPage = function (line) {
		--blockStack;
		if (blockStack <= 0) {
			blockStack = 0;
			KTApp.unblockPage();
		}
		console.debug('unblock', line, blockStack);
	};

	var _blurPage = function (line) {
		++blurStack;

		$(_pageContainerId).foggy({
			blurRadius: 3,
			opacity: 1,
			cssFilterSupport: true,
		});
		console.debug('blur', line, blurStack);
	};

	var _unblurPage = function (line) {
		--blurStack;
		if (blurStack <= 0) {
			blurStack = 0;
			$(_pageContainerId).foggy(false);
		}
		console.debug('unblur', line, blurStack);
	};

	var _initModal = function () {
		$('.modalAction').each(function () {
			$(this).off('click');
			$(this).click(function (e) {
				e.preventDefault();
				var form = $(this).parent().parent().parent();
				var url = $(form).attr('action');
				var data = $(form).serializeJSON();
				if (data.body) {
					var dataBodyStr = $(form).find('.modalValueBody').val();
					data.body = JSON.parse(decodeURIComponent(dataBodyStr));
				}
				var callback = null;
				if ($(form).find('.modalValueCallback').val() != '') {
					callback = eval($(form).find('.modalValueCallback').val());
				}

				if ($(this).attr('data-modalValidatorFields')) {
					var fullForm = KTUtil.getById($(form).attr('id'));
					var validatorFields = JSON.parse($(this).attr('data-modalValidatorFields'));

					if (_validator) _validator.destroy();
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
					});

					$('.select2').on('change', function (ev) {
						var field = $(this).attr('name');
						if (field in _validatorFields) {
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
		if (webResponse.data.body) {
			$('.modalValueBody').val(encodeURIComponent(JSON.stringify(webResponse.data.body)));
		}
		$('.modalAction').html(webResponse.data.modalButton);
		$('.modalActionCancel').each(function () {
			$(this).off('click');
			$(this).click(function (e) {
				e.preventDefault();
				$('#popupModal').modal('hide');
			});
		});
		$('#popupModal').modal('show');
	};

	var _resetModal = function () {
		$('.modalForm').attr('action', '#');
		$('#popupModalTitle').html('');
		$('#popupModalText').html('');
		$('#popupModalValueId').val('');
		$('.modalValueCallback').val('');
		$('.modalValueBody').val('');
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
		// delete cached datatable
		if ($.fn.DataTable.isDataTable(vElementId)) {
			if (datatableVar.length > 0) {
				datatableVar.pop();
			}
		}

		var logo =
			'data:image/png;base64,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';
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
				// infoFiltered: '(from _MAX_ total)',
				infoFiltered: '',
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
					pageSize: 'A4',
					exportOptions: {
						columns: '.export_datatable',
					},
					customize: function (doc) {
						/* margin: left top right bottom */
						doc.pageMargins = [20, 60, 20, 20];
						doc.defaultStyle.fontSize = 9;
						doc.styles.tableHeader.fontSize = 9;
						doc.styles.title.fontSize = 11;

						/* Remove spaces around page title */
						doc.content[0].text = doc.content[0].text.trim();

						/* Create a header image */
						doc['header'] = function () {
							return {
								columns: [
									{
										image: logo,
										width: 80,
									},
								],
								margin: 20,
							};
						};

						/* change table header background color */
						doc.content[1].table.body[0].forEach(function (h) {
							h.fillColor = '#4ab8a8';
							h.alignment = 'center';
						});

						/* remove title above table */
						doc.styles.title = {
							color: '#000',
							fontSize: '0',
							alignment: 'center',
						};

						/* Styling the table: create style object */
						var objLayout = {};
						/* Horizontal line thickness */
						objLayout['hLineWidth'] = function (i) {
							return 0.5;
						};
						/* Vertical line thickness */
						objLayout['vLineWidth'] = function (i) {
							return 0.5;
						};
						/* Horizontal line color */
						objLayout['hLineColor'] = function (i) {
							return '#aaa';
						};
						/* Vertical line color */
						objLayout['vLineColor'] = function (i) {
							return '#aaa';
						};
						/* Left padding of the cell */
						objLayout['paddingLeft'] = function (i) {
							return 4;
						};
						/* Right padding of the cell */
						objLayout['paddingRight'] = function (i) {
							return 4;
						};
						/* Inject the object in the document */
						doc.content[1].layout = objLayout;
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
				beforeSend: function (jqXHR, settings) {
					_blurPage(662);
					_blockPage(663);
					console.debug('here');
				},
				complete: function () {
					while (blurStack > 0) {
						_unblurPage(667);
					}
					while (blockStack > 0) {
						_unblockPage(668);
					}
					console.debug('there');
				},
			},
			columnDefs: vColumnDefs,
		};

		if (vParams != null) {
			dbOptions['ajax']['data']['query'] = vParams;
		}

		var dbOptionsObj = { ...dbOptions };

		if (vAdditionalOptions && vAdditionalOptions.datatableOptions) {
			dbOptionsObj = { ...dbOptions, ...vAdditionalOptions.datatableOptions };
		}

		datatableVar.push($('' + vElementId).DataTable(dbOptionsObj));

		datatableVar[datatableVar.length - 1].on('draw', function () {

		});

		$.fn.dataTable.ext.errMode = function (settings, helpPage, message) {
			console.log(message);
		};

		return datatableVar;
	};

	var _createDatatableLocal = function (vTableName, vElementId, vData, vColumnDefs, vAdditionalOptions = null) {
		// delete cached datatable
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
				// infoFiltered: '(from _MAX_ total)',
				infoFiltered: '',
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
			dbOptionsObj = { ...dbOptions, ...vAdditionalOptions.datatableOptions };
		}

		datatableVar.push($('' + vElementId).DataTable(dbOptionsObj));

		datatableVar[datatableVar.length - 1].on('draw', function () {

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
		if (webResponse && typeof webResponse === 'object' && webResponse.hasOwnProperty('data') && webResponse.data > 0) {
			_alertNewOrders(webResponse.data);
		} else {
			_notificationInProgress = false;
		}
	};

	var _initNotificationTimer = function () {
		setInterval(function () {
			if (_notificationInProgress) {
				console.debug('Notification is taking longer than usual...');
			} else {
				_notificationInProgress = true;
				WebApp.getAsync('/web/notification/order/new', _handleNotificationTimer);
			}
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
					$.fn.dataTable.ext.buttons.excelHtml5.available(dt, config)
						? $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config)
						: $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
				} else if (button[0].className.indexOf('buttons-csv') >= 0) {
					$.fn.dataTable.ext.buttons.csvHtml5.available(dt, config)
						? $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config)
						: $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
				} else if (button[0].className.indexOf('buttons-pdf') >= 0) {
					$.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config)
						? $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config)
						: $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
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
	};

	var _truncateText = function truncateText(str, n) {
		return str.length > n ? str.substr(0, n - 1) + '&hellip;' : str;
	};

	var _formatMoney = function formatMoney(number, n = 2, x) {
		var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\.' : '$') + ')';
		return parseFloat(number)
			.toFixed(Math.max(0, ~~n))
			.replace(new RegExp(re, 'g'), '$&,');
	};

	var _initSupportModalForm = function () {
		var _buttonSpinnerClasses = 'spinner spinner-right spinner-white pr-15';
		var form = KTUtil.getById('supportModalForm');
		var formSubmitUrl = KTUtil.attr(form, 'action');
		var formSubmitButton = KTUtil.getById('kt_cs_form_submit_button');

		if (!form) {
			return;
		}

		$('#supportModalForm select[name=supportReasonId]').on('change', function (ev) {
			var field = $(this).attr('name');
			_supportModalValidator.revalidateField(field);
		});

		$('#support_modal').on('shown.bs.modal', function () {
			if (_supportModalValidator) {
				_supportModalValidator.resetForm();
				_supportModalValidator.destroy();
			}

			_supportModalValidator = FormValidation.formValidation(form, {
				fields: {
					supportEmail: {
						validators: {
							notEmpty: {
								message: WebAppLocals.getMessage('supportEmailRequired'),
							},
							emailAddress: {
								message: WebAppLocals.getMessage('supportEmailInvalid'),
							},
						},
					},
					supportPhone: {
						validators: {
							notEmpty: {
								message: WebAppLocals.getMessage('supportPhoneRequired'),
							},
						},
					},
					supportReasonId: {
						validators: {
							notEmpty: {
								message: WebAppLocals.getMessage('supportReasonRequired'),
							},
						},
					},
				},
				plugins: {
					trigger: new FormValidation.plugins.Trigger(),
					submitButton: new FormValidation.plugins.SubmitButton(),
					// Bootstrap Framework Integration
					bootstrap: new FormValidation.plugins.Bootstrap({
						//eleInvalidClass: '',
						eleValidClass: '',
					}),
				},
			});

			_supportModalValidator.on('core.form.valid', function () {
				let body = {};

				let mapKeyElement = {
					supportEmail: 'input',
					supportPhone: 'input',
					supportReasonId: 'select',
				};

				Object.keys(mapKeyElement).forEach((key) => {
					body[key] = $('#supportModalForm ' + mapKeyElement[key] + '[name=' + key + ']').val();
				});

				// Show loading state on button
				KTUtil.btnWait(formSubmitButton, _buttonSpinnerClasses, 'Please wait');
				$(formSubmitButton).prop('disabled', true);

				_post(formSubmitUrl, body, _supportModalSuccessCallback, formSubmitButton);
			});
		});

		$('#support_modal').on('hidden.bs.modal', function () {
			if (_supportModalValidator) {
				_supportModalValidator.resetForm();
				_supportModalValidator.destroy();
			}
		});
	};

	var _supportModalSuccessCallback = function () {
		if (_supportModalValidator) {
			_supportModalValidator.resetForm();
			_supportModalValidator.destroy();
		}

		$('#supportModalForm select[name=supportReasonId]').val('').trigger('change');
		$('#support_modal').modal('hide');
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
			$(window).on('popstate', function () {
				_handleBrowserNavigation(window.history.state.url, window.history.state);
			});
		},
		initSupportModalForm: function () {
			_initSupportModalForm();
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
			return _blockPage(981, _msgKey);
		},
		unblock: function () {
			return _unblockPage(984);
		},
		blur: function () {
			return _blurPage(978);
		},
		unblur: function () {
			return _unblurPage(981);
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
		post: function (url, data = null, fnCallback = null, submitButton = null, forceCallback = false, forcePreventUnblur = false) {
			return _post(url, data, fnCallback, submitButton, forceCallback, forcePreventUnblur);
		},
		openModal: function (webResponse) {
			_openModal(webResponse);
		},
		CreateDatatableServerside: function (vTableName, vElementId, vUrl, vColumnDefs, vParams = null, vAdditionalOptions = null) {
			_blurPage(1040);
			_blockPage(1041);
			_createDatatableServerside(vTableName, vElementId, vUrl, vColumnDefs, vParams, vAdditionalOptions);
			_unblurPage(1050);
			_unblockPage(1051);
		},
		CreateDatatableLocal: function (vTableName, vElementId, vData, vColumnDefs, vAdditionalOptions = null) {
			_blurPage(1060);
			_blockPage(1061);
			_createDatatableLocal(vTableName, vElementId, vData, vColumnDefs, vAdditionalOptions);
			_unblurPage(1070);
			_unblockPage(1071);
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
			_redirect(url);
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
				_blurPage(1071);
				_blockPage(1072);

				$.when(vValue.ajax.reload(null, false)).then(function () {
					_unblurPage(1075);
					_unblockPage(1076);
				});
			});
		},
	};
})();
