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

	var _removeItem = function (productId, submitButton, forceCallback, forcePreventUnblur) {
		WebApp.post('/web/cart/remove', { id: productId }, (webResponse) => _updateQuantityDeleteCallback(webResponse), submitButton, forceCallback, forcePreventUnblur);
	};

	var _removeItemModal = function (itemId) {
		WebApp.get('/web/cart/remove/confirm/' + itemId, WebApp.openModal);
		WebApp.reloadDatatable();
	};

	var _removeItemSuccess = function (webResponse) {
		// Update cart count
		console.log(webResponse);
		let cartCount = webResponse.data > 9 ? '9+' : webResponse.data;
		if (webResponse.data !== 0) $('#cartCount').css('display', 'flex');
		else $('#cartCount').css('display', 'none');
		$('#cartCount').html(cartCount);
		WebApp.loadPage('/web/cart/checkout');
	};

	var _submitOrderModal = function (productsList,totalPrice) {
		var valid = true;
		$('.selectpicker.paymentMethodId').each(function (index, element) {
			if (!$(element).val()) {
				if (!$(element).parent().hasClass('is-invalid')) {
					$(element).parent().addClass('is-invalid');
					$(element).parent().css('border', '1px solid #F64E60');
				}
				valid = false;
			} else {
				$(element).parent().removeClass('is-invalid');
				$(element).parent().css('border', '');
			}
		});

		if (valid) {
			var mapSellerIdPaymentMethodId = {};
			$('.selectpicker.paymentMethodId').each(function (index, element) {
				let sellerId = $(element).attr('data-sellerId');
				let paymentMethodId = $(element).val();
				mapSellerIdPaymentMethodId[sellerId] = paymentMethodId;
			});

			dataLayer.push({
				'event': 'begin_checkout',
				'ecommerce': {
					'value':totalPrice,
					'currency':'AED',
					'items': [
						productsList,
					]
				}
			});
			WebApp.post('/web/cart/checkout/submit/confirm', { mapSellerIdPaymentMethodId }, WebApp.openModal);
		} else {
			$('.selectpicker.paymentMethodId').on('change', function () {
				if (!$(this).val()) {
					if (!$(this).parent().hasClass('is-invalid')) {
						$(this).parent().addClass('is-invalid');
						$(this).parent().css('border', '1px solid #F64E60');
					}
				} else {
					$(this).parent().removeClass('is-invalid');
					$(this).parent().css('border', '');
				}
			});

			Swal.fire({
				text: WebAppLocals.getMessage('cartError'),
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
	};

	var _submitOrderSuccess = function (webResponse) {
		Cart.emptyCart();
		WebApp.redirect('/web/thankyou/' + webResponse.data);
	};

	var _updateQuantity = function (
		productId,
		increment,
		stock,
		cartDetailId,
		sellerId,
		updateTotalPrice = 0,
		initializeBonusPopover = 0,
		oldValue = null,
		shouldShowRemoveModal = true,
		submitButton = null,
		forceCallback = false,
		forcePreventUnblur = false
	) {
		let quantityId = '#quantity-' + productId;
		let currentValue = 0;
		if ($(quantityId).val() > 0) currentValue = parseInt($(quantityId).val());
		let newValue = currentValue + increment;

		if (newValue === 0) {
			if (shouldShowRemoveModal) {
				_removeItemModal(cartDetailId);
			} else {
				_removeItem(cartDetailId, submitButton, forceCallback, forcePreventUnblur);
			}
		} else {
			// set previous value in case of error if it's succeeded will reload datatable and set new value
			if (newValue > 0) {
				// if oldValue is null plus icon is used so mines one else it's changed manually and we can revert to old value
				if (!oldValue) {
					$(quantityId).val(newValue - 1);
				} else {
					$(quantityId).val(oldValue);
				}
			}
			WebApp.post(
				'/web/cart/checkout/update',
				{ cartDetailId, sellerId, productId, quantity: newValue },
				(webResponse) => _updateQuantityCallback(webResponse, updateTotalPrice, initializeBonusPopover),
				submitButton,
				forceCallback,
				forcePreventUnblur
			);
		}
	};

	var _updateQuantityCallback = function (webResponse, updateTotalPrice, initializeBonusPopover) {
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
		$(quantityFreeHolderId).css('display', 'none');

		// Update product price
		let productPriceId = '#productPrice-' + productId;
		let unitPrice = $(productPriceId).attr('data-unitPrice');
		let currency = $(productPriceId).attr('data-currency');
		let vat = $(productPriceId).attr('data-vat');
		let subTotal = parseFloat(quantity) * parseFloat(unitPrice);
		let total = (subTotal * (100.0 + parseFloat(vat))) / 100.0;
		let productPrice = WebApp.formatMoney(total);

		$(productPriceId).attr('data-productPrice', subTotal);
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

		if (updateTotalPrice) {
			updateTotalPrice();
		}

		$('#bonusLabel-' + productId).attr('data-activeBonus', JSON.stringify(cartDetail.activeBonus));
		if (initializeBonusPopover) {
			initializeBonusPopover();
		}
		WebApp.reloadDatatable();
	};

	var _updateQuantityDeleteCallback = function (webResponse) {
		let cartDetail = webResponse.data;

		// Update cart count
		let cartCount = cartDetail > 9 ? '9+' : cartDetail;
		if (webResponse.data !== 0) $('#cartCount').css('display', 'flex');
		else $('#cartCount').css('display', 'none');
		$('#cartCount').html(cartCount);
		WebApp.reloadDatatable();
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
		submitOrderModal: function (productsList,totalPrice) {
			_submitOrderModal(productsList,totalPrice);
		},
		submitOrderSuccess: function (webResponse) {
			_submitOrderSuccess(webResponse);
		},
		updateQuantity: function (
			productId,
			increment,
			stock,
			cartDetailId,
			sellerId,
			updateTotalPrice,
			initializeBonusPopover,
			oldValue,
			shouldShowRemoveModal,
			submitButton,
			forceCallback,
			forcePreventUnblur
		) {
			_updateQuantity(
				productId,
				increment,
				stock,
				cartDetailId,
				sellerId,
				updateTotalPrice,
				initializeBonusPopover,
				oldValue,
				shouldShowRemoveModal,
				submitButton,
				forceCallback,
				forcePreventUnblur
			);
		},
		updateNote: function (productId, cardDetailId, sellerId) {
			_updateNote(productId, cardDetailId, sellerId);
		},
	};
})();
