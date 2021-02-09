'use strict';

// Class Definition
var Cart = (function () {
	var _topbarItem = $('#cartTopBarItem');
	var _topbarItemButton = $('#cartTopBarItemButton');
	var _topBarItemSvgIcon = $('#cartTopBarItemSvgIcon');
	var _topbarItemText = $('#cartTopBarItemText');
	var _topbarItemTextContainer = $('#cartTopBarItemTextContainer');

	const _svgIconNoColor_NoItems = 'svg-icon-light';
	const _svgIconNoColor_Items = 'svg-icon-primary';
	const _addPulse = 'pulse';
	const _addPulseColor = 'label-danger';

	var _itemsCount = 0;

	var _int = function () {
		_topbarItemText.hide();
		_topBarItemSvgIcon.removeClass(_svgIconNoColor_Items).addClass(_svgIconNoColor_NoItems);
		_topbarItemTextContainer.removeClass(_addPulse);

		WebApp.get('/web/cart/status', _getCartStatusCallback);
	};

	var _getCartStatusCallback = function (webResponse) {
		_itemsCount = webResponse.data.itemsCount;
		if (_itemsCount > 0) {
			_topBarItemSvgIcon.removeClass(_svgIconNoColor_NoItems).addClass(_svgIconNoColor_Items);
			_topbarItemTextContainer.addClass(_addPulse).addClass(_addPulseColor);
			_topbarItemText.show();
			_topbarItemText.html(_itemsCount);
		} else {
			_topbarItemText.hide();
			_topBarItemSvgIcon.removeClass(_svgIconNoColor_Items).addClass(_svgIconNoColor_NoItems);
			_topbarItemTextContainer.removeClass(_addPulse).removeClass(_addPulseColor);
		}
	};

	var _addItemSuccessCallback = function (webResponse) {
		_itemsCount++;
		_topbarItemText.html(_itemsCount);

		// Update cart count
		let cartCount = webResponse.data > 9 ? '9+' : webResponse.data;
		if (webResponse.data !== 0) $('#cartCount').css('display', 'flex');
		else $('#cartCount').css('display', 'none');
		$('#cartCount').html(cartCount);
		WebApp.reloadDatatable();
	};

	var _removeItemSuccessCallback = function (webResponse) {
		// Update cart count
		let cartCount = webResponse.data > 9 ? '9+' : webResponse.data;
		if (webResponse.data !== 0) $('#cartCount').css('display', 'flex');
		else $('#cartCount').css('display', 'none');
		$('#cartCount').html(cartCount);

		WebApp.loadPage('/web/cart');
	};

	var _addItem = function (entityId, productId, quantityInputId = null, quantityFreeInputId = null, submitButton = null, forceCallback = false, forcePreventUnblur = false) {
		if (_itemsCount <= 0) {
			_topBarItemSvgIcon.removeClass(_svgIconNoColor_NoItems).addClass(_svgIconNoColor_Items);
			_topbarItemTextContainer.addClass(_addPulse).addClass(_addPulseColor);
			_topbarItemText.show();
		}

		//if (quantityInputId != null && $(quantityInputId).val() < 1) return;

		let body = {
			entityId,
			productId,
			quantity: quantityInputId == null ? 1 : $(quantityInputId).val(),
			quantityFree: quantityFreeInputId == null ? 0 : $(quantityFreeInputId).val(),
		};
		WebApp.post('/web/cart/add', body, _addItemSuccessCallback, submitButton, forceCallback, forcePreventUnblur);
	};

	var _removeItem = function (id) {
		WebApp.post('/web/cart/remove', { id: id }, _removeItemSuccessCallback);
	};

	var _loadCartPage = function () {
		if (_itemsCount > 0) {
			WebApp.loadPage('/web/cart');
		} else {
			WebApp.alertSuccess('Continue Shopping');
		}
	};

	var _emptyCart = function () {
		$('#cartCount').html(0);
		$('#cartCount').css('display', 'none');
	};

	// Public Functions
	return {
		init: function () {
			_int();
		},
		addItem: function (entityId, productId, quantityInputId = null, quantityFreeInputId, submitButton, forceCallback, forcePreventUnblur) {
			_addItem(entityId, productId, quantityInputId, quantityFreeInputId, submitButton, forceCallback, forcePreventUnblur);
		},
		removeItem: function (id) {
			_removeItem(id);
		},
		loadCartPage: function () {
			_loadCartPage();
		},
		emptyCart: function () {
			_emptyCart();
		},
	};
})();
