'use strict';

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

	var _blockPage = function () {
		KTApp.blockPage({
			overlayColor: 'black',
			opacity: 0.2,
			message: WebAppLocals.getMessage('loading'),
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
		$('#popupModalAction').on('click', function (e) {
			e.preventDefault();
			var url = $('#popupModalForm').attr('action');
			var data = $('form#popupModalForm').serialize();
			var callback = null;
			if ($('#popupModalValueCallback').val() != '') {
				callback = eval($('#popupModalValueCallback').val());
			}
			_post(url, data, callback);
		});
	};

	var _openModal = function (webResponse) {
		_resetModal();
		$('#popupModalForm').attr('action', webResponse.data.modalRoute);
		$('#popupModalTitle').html(webResponse.data.modalTitle);
		$('#popupModalText').html(webResponse.data.modalText);
		$('#popupModalValueId').val(webResponse.data.id);
		$('#popupModalValueCallback').val(webResponse.data.fnCallback);
		$('#popupModalAction').html(webResponse.data.modalButton);
		$('#popupModal').modal('show');
	};

	var _resetModal = function () {
		$('#popupModalForm').attr('action', '#');
		$('#popupModalTitle').html('');
		$('#popupModalText').html('');
		$('#popupModalValueId').val('');
		$('#popupModalValueCallback').val('');
		$('#popupModalAction').html('');
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
		// Your web app's Firebase configuration
		var firebaseConfig = {
			apiKey: 'AIzaSyApi3WBeQ3HmB_we8CSOF8k1qgU1SEpxao',
			authDomain: 'aumet-marketplace.firebaseapp.com',
			databaseURL: 'https://aumet-marketplace.firebaseio.com',
			projectId: 'aumet-marketplace',
			storageBucket: 'aumet-marketplace.appspot.com',
			messagingSenderId: '418237979621',
			appId: '1:418237979621:web:5fe1d4393d8676f5a3ad0c',
			measurementId: 'G-QEWB1B33ZE',
		};
		// Initialize Firebase
		firebase.initializeApp(firebaseConfig);
		firebase.analytics();

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
	}

	var _initSessionTimeout = function () {

		document.addEventListener('mousemove', function (e) {
			clearTimeout(_timeoutSession);
			_setSessionTimeoutCallback();
		}, true);
	};

	// Public Functions
	return {
		init: function () {
			_setUpFirebase();
			WebAppLocals.init();
			_initModal();
			_loadPage(window.location.href);
			Cart.init();

			_initSessionTimeout();
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
		block: function () {
			return _blockPage();
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
		post: function (url, data = null, fnCallback = null) {
			return _post(url, data, fnCallback);
		},
		openModal: function (webResponse) {
			_openModal(webResponse);
		},
	};
})();

jQuery(document).ready(function () {
	WebApp.init();
});
