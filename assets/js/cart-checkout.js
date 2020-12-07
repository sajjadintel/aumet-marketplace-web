'use strict';

// Class Definition
var CartCheckout = (function () {
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
	
	var _removeItemModal = function (itemId) {
		WebApp.get('/web/cart/remove/confirm/' + itemId, WebApp.openModal);
	};
	
	var _removeItemSuccess = function () {
		WebApp.loadPage('/web/cart/checkout');
	};
	
	var _submitOrderModal = function() {
		WebApp.get('/web/cart/checkout/submit/confirm', WebApp.openModal);
	};

	var _submitOrderSuccess = function() {
		WebApp.alertSuccess('Order submitted successfully')
	}

	var _updateQuantity = function(productId, increment, stock, cartDetailId, sellerId, updateTotalPrice) {
		let quantityId = "#quantity-" + productId;
		let currentValue = parseInt($(quantityId).val());
		let newValue = currentValue + increment;
		if(newValue > stock) newValue = stock;
		else if(newValue < 0) newValue = 0;

		WebApp.post('/web/cart/checkout/update', { cartDetailId, sellerId, productId, quantity: newValue }, (webResponse) => _updateQuantityCallback(webResponse, updateTotalPrice));
	}

	var _updateQuantityCallback = function(webResponse, updateTotalPrice) {
		let cartDetail = webResponse.data;

		let productId = cartDetail.productId;
		let quantity = cartDetail.quantity;
		let sellerId = cartDetail.entityId;
		
		// Update quantity input
		let quantityId = "#quantity-" + productId;
		$(quantityId).val(quantity);

		// Update product price
		let productPriceId = "#productPrice-" + productId;
		let unitPrice = $(productPriceId).attr("data-unitPrice");
		let currency = $(productPriceId).attr("data-currency");
		let productPrice = (quantity * unitPrice).toFixed(2);
		
		$(productPriceId).attr("data-productPrice", productPrice);
		$(productPriceId).html(productPrice + " " + currency);

		// Update sub total price
		let productPriceClass = ".productPrice-" + sellerId;
		let subTotalPrice = 0;
		$(productPriceClass).each(function(index, element) {
			subTotalPrice += parseFloat($(element).attr("data-productPrice"));
		});

		subTotalPrice = subTotalPrice.toFixed(2);
		let subTotalPriceId = "#subTotalPrice-" + sellerId;
		$(subTotalPriceId).attr("data-subTotalPrice", subTotalPrice)
		$(subTotalPriceId).html(subTotalPrice + " " + currency);
		
		updateTotalPrice();
	}

	// Public Functions
	return {
		init: function () {
			_int();
		},
		removeItemModal: function (itemId) {
			_removeItemModal(itemId)
		},
		removeItemSuccess: function () {
			_removeItemSuccess()
		},
		submitOrderModal: function () {
			_submitOrderModal()
		},
		submitOrderSuccess: function () {
			_submitOrderSuccess()
		},
		updateQuantity: function(productId, increment, stock, cardDetailId, sellerId, updateTotalPrice) {
			_updateQuantity(productId, increment, stock, cardDetailId, sellerId, updateTotalPrice)
		}
	};
})();
