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
	var _removeItemSuccess = function (webResponse) {
		// Update cart count
		let cartCount = webResponse.data > 9 ? '9+' : webResponse.data;
		if (webResponse.data !== 0) $('#cartCount').css('display', 'flex');
		else $('#cartCount').css('display', 'none');
		$('#cartCount').html(cartCount);
		WebApp.loadPage('/web/cart/checkout');
	};

	var _submitOrderModal = function () {
		// let paymentMethodInputId = $("input[name='paymentMethod']:checked").attr('id');
		// let allParts = paymentMethodInputId.split('-');
		// let paymentMethodId = allParts[1];
		WebApp.get('/web/cart/checkout/submit/confirm', WebApp.openModal);
	};

	var _submitOrderSuccess = function (webResponse) {
		Cart.emptyCart();
		WebApp.redirect('/web/thankyou/' + webResponse.data);
	};

	var _updateQuantity = function (productId, increment, stock, cartDetailId, sellerId, updateTotalPrice = 0, oldValue = null) {
		let quantityId = '#quantity-' + productId;
		let currentValue = 0;
		if ($(quantityId).val() > 0) currentValue = parseInt($(quantityId).val());
		let newValue = currentValue + increment;
		if (newValue < 0) newValue = 0;
		else if (newValue > stock && oldValue) $(quantityId).val(oldValue);

		if (newValue === 0) {
			_removeItemModal(cartDetailId);
		} else {
			WebApp.post('/web/cart/checkout/update', { cartDetailId, sellerId, productId, quantity: newValue }, (webResponse) =>
				_updateQuantityCallback(webResponse, updateTotalPrice)
			);
		}
	};

	var _updateQuantityCallback = function (webResponse, updateTotalPrice) {
		let cartDetail = webResponse.data;

		// Update cart count
		let cartCount = cartDetail.cartCount > 9 ? '9+' : cartDetail.cartCount;
		if (webResponse.data !== 0) $('#cartCount').css('display', 'flex');
		else $('#cartCount').css('display', 'none');
		$('#cartCount').html(cartCount);

		let productId = cartDetail.productId;
		let quantity = cartDetail.quantity;
		let quantityFree = cartDetail.quantityFree;
		let sellerId = cartDetail.entityId;

		// Update quantity input
		let quantityId = '#quantity-' + productId;
		$(quantityId).val(quantity);

		// Update quantity free
		let quantityFreeId = '#quantityFree-' + productId;
		$(quantityFreeId).html(quantityFree);

		let quantityFreeHolderId = '#quantityFreeHolder-' + productId;
		$(quantityFreeHolderId).css('display', quantityFree > 0 ? 'block' : 'none');

		// Update product price
		let productPriceId = '#productPrice-' + productId;
		let unitPrice = $(productPriceId).attr('data-unitPrice');
		let currency = $(productPriceId).attr('data-currency');
		let productPrice = (quantity * unitPrice).toFixed(2);

		$(productPriceId).attr('data-productPrice', productPrice);
		$(productPriceId).html(productPrice + ' ' + currency);

		// Update total price
		let tax = 0;
		let subTotalPrice = 0;
		let productPriceClass = '.productPrice-' + sellerId;
		$(productPriceClass).each(function (index, element) {
			let price = parseFloat($(element).attr('data-productPrice'));
			subTotalPrice += price;
			tax += (price * parseFloat($(element).attr('data-vat'))) / 100;
		});

		let totalPrice = subTotalPrice + tax;

		tax = tax.toFixed(2);
		let taxId = '#tax-' + sellerId;
		$(taxId).attr('data-vat', tax);
		$(taxId).html(tax + ' ' + currency);

		subTotalPrice = subTotalPrice.toFixed(2);
		let subTotalPriceId = '#subTotalPrice-' + sellerId;
		$(subTotalPriceId).attr('data-subTotalPrice', subTotalPrice);
		$(subTotalPriceId).html(subTotalPrice + ' ' + currency);

		totalPrice = totalPrice.toFixed(2);
		let totalPriceId = '#totalPrice-' + sellerId;
		$(totalPriceId).attr('data-totalPrice', totalPrice);
		$(totalPriceId).html(totalPrice + ' ' + currency);

		if(updateTotalPrice){
			updateTotalPrice();
		}
	};

	var _updateNote = function (productId, cartDetailId, sellerId) {
		let noteId = '#note-' + productId;
		let currentValue = $(noteId).val();

		WebApp.post('/web/cart/checkout/note', { cartDetailId, sellerId, productId, note: currentValue }, (webResponse) => _updateNoteCallback(webResponse));
	};

	var _updateNoteCallback = function (webResponse) {};

	// Public Functions
	return {
		init: function () {
			_int();
		},
		removeItemModal: function (itemId) {
			_removeItemModal(itemId);
		},
		removeItemSuccess: function (webResponse) {
			_removeItemSuccess(webResponse);
		},
		submitOrderModal: function () {
			_submitOrderModal();
		},
		submitOrderSuccess: function (webResponse) {
			_submitOrderSuccess(webResponse);
		},
		updateQuantity: function (productId, increment, stock, cardDetailId, sellerId, updateTotalPrice, oldValue) {
			_updateQuantity(productId, increment, stock, cardDetailId, sellerId, updateTotalPrice, oldValue);
		},
		updateNote: function (productId, cardDetailId, sellerId) {
			_updateNote(productId, cardDetailId, sellerId);
		},
	};
})();
