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

	var _alertError = function (msg) {
		Swal.fire({
			text: msg,
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
				WebApp.loadPage('/web/distributor/order/new');
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
		$.ajax({
			url: url + '?_t=' + Date.now(),
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

						window.history.pushState(
							{
								id: _id,
								url: url,
								title: title,
							},
							title,
							url
						);

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
				console.log('JSON data from Modal:');
				console.log(data);
				var callback = null;
				if ($(form).find('.modalValueCallback').val() != '') {
					callback = eval($(form).find('.modalValueCallback').val());
				}
				_post(url, data, callback);
				$(form).parent().parent().parent().modal('hide');
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

	var _createDatatableServerside = function (vTableName, vElementId, vUrl, vColumnDefs, vParams = null, vAdditionalOptions = null, defaultOrder = null) {
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
			order: defaultOrder || [[0, 'asc']],
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
		CreateDatatableServerside: function (vTableName, vElementId, vUrl, vColumnDefs, vParams = null, vAdditionalOptions = null, defaultOrder = null) {
			console.log(vParams);
			_createDatatableServerside(vTableName, vElementId, vUrl, vColumnDefs, vParams, vAdditionalOptions, defaultOrder);
		},
		CreateDatatableLocal: function (vTableName, vElementId, vData, vColumnDefs, vAdditionalOptions = null) {
			_createDatatableLocal(vTableName, vElementId, vData, vColumnDefs, vAdditionalOptions);
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
