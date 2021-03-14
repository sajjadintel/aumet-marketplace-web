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

	var _addItemSuccessCallback = function (webResponse, fromProductPage) {
		_itemsCount++;
		_topbarItemText.html(_itemsCount);

		// Update cart count
		let cartCount = webResponse.data.cartCount > 9 ? '9+' : webResponse.data.cartCount;
		if (webResponse.data !== 0) $('#cartCount').css('display', 'flex');
		else $('#cartCount').css('display', 'none');
		$('#cartCount').html(cartCount);

		if (webResponse.data.activeBonus && fromProductPage) {
			$('#mainBonusLabel').attr('data-activeBonus', JSON.stringify(webResponse.data.activeBonus));
			_initializeBonusPopover('.mainBonusLabel');
		}

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

	var _addItem = function (
		entityId,
		productId,
		quantityInputId = null,
		quantityFreeInputId = null,
		submitButton = null,
		forceCallback = false,
		forcePreventUnblur = false,
		fromProductPage = false
	) {
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
		WebApp.post('/web/cart/add', body, (webResponse) => _addItemSuccessCallback(webResponse, fromProductPage), submitButton, forceCallback, forcePreventUnblur);
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

	var _initializeBonusPopover = function (selector) {
		$(selector).popover('dispose');
		$(selector).each(function (index, element) {
			var arrBonusStr = $(element).attr('data-arrBonus') || '[]';
			var arrBonus = JSON.parse(arrBonusStr);
			if (arrBonus.length > 0) {
				$(element)
					.popover({
						html: true,
						sanitize: false,
						trigger: 'manual',
						placement: 'bottom',
						content: _getBonusPopoverContent(element),
					})
					.on('mouseenter', function () {
						var _this = this;
						$(this).popover('show');
						$('.popover').on('mouseleave', function () {
							$(_this).popover('hide');
						});
					})
					.on('mouseleave', function () {
						var _this = this;
						setTimeout(function () {
							if (!$('.popover:hover').length) {
								$(_this).popover('hide');
							}
						}, 300);
					});
			} else {
				$(element).hide();
			}
		});
	};

	var _getBonusPopoverContent = function (element) {
		var arrBonusStr = $(element).attr('data-arrBonus') || '[]';
		var arrBonus = JSON.parse(arrBonusStr);
		var activeBonusStr = $(element).attr('data-activeBonus') || '{}';
		var activeBonus = JSON.parse(activeBonusStr);

		var tableElement = document.createElement('table');

		var tableHead = ['BONUSES TYPE', 'MIN QTY', 'BONUSES'];
		var allTableData = [tableHead, ...arrBonus];
		for (var i = 0; i < allTableData.length; i++) {
			var row = allTableData[i];

			if (i == 0) {
				/* Add table head*/
				var trElement = document.createElement('tr');
				for (var j = 0; j < row.length; j++) {
					var item = row[j];
					var thElement = document.createElement('th');
					thElement.className = 'cart-checkout-bonus-th text-center p-1 pb-3';
					thElement.innerHTML = item;
					trElement.append(thElement);
				}
				tableElement.append(trElement);
			} else {
				var arrMinQty = row.arrMinQty || [];
				var arrBonuses = row.arrBonuses || [];
				if (arrMinQty.length > 0 && arrMinQty.length === arrBonuses.length) {
					/* Add bonus type column*/
					var trElement = document.createElement('tr');

					var bonusType = row.bonusType;
					var tdBonusTypeElement = document.createElement('td');
					tdBonusTypeElement.className = 'cart-checkout-bonus-td text-center p-1';
					if (i != allTableData.length - 1) tdBonusTypeElement.className += ' border-bottom';
					if (arrMinQty.length > 1) tdBonusTypeElement.setAttribute('rowspan', arrMinQty.length);
					tdBonusTypeElement.innerHTML = bonusType;
					trElement.append(tdBonusTypeElement);

					/* Add minQty and bonuses columns*/
					for (var j = 0; j < arrMinQty.length; j++) {
						if (j != 0) {
							trElement = document.createElement('tr');
						}

						var minQty = arrMinQty[j];
						var tdMinQtyElement = document.createElement('td');
						tdMinQtyElement.className = 'cart-checkout-bonus-td text-center p-1 border-left';
						if (i != allTableData.length - 1 || j != arrMinQty.length - 1) {
							tdMinQtyElement.className += ' border-bottom';
						}
						tdMinQtyElement.innerHTML = minQty;
						trElement.append(tdMinQtyElement);

						var bonuses = arrBonuses[j];
						var tdBonusesElement = document.createElement('td');
						tdBonusesElement.className = 'cart-checkout-bonus-td text-center p-1 border-left';
						if (i != allTableData.length - 1 || j != arrMinQty.length - 1) {
							tdBonusesElement.className += ' border-bottom';
						}
						tdBonusesElement.innerHTML = bonuses;
						trElement.append(tdBonusesElement);

						if (activeBonus) {
							if (bonusType == activeBonus.bonusType && minQty == activeBonus.minQty && bonuses == activeBonus.bonuses) {
								var tdCheckElement = document.createElement('td');
								tdCheckElement.className = 'cart-checkout-bonus-td text-center p-1';
								tdCheckElement.innerHTML = "<i class='las la-check check'></i>";
								trElement.append(tdCheckElement);
							}
						}

						tableElement.append(trElement);
					}
				}
			}
		}
		if (activeBonus && activeBonus.totalBonus) {
			$(element)
				.find('.bonus')
				.text('(+' + activeBonus.totalBonus + ')');
		} else {
			$(element).find('.bonus').text('');
		}
		return tableElement.outerHTML;
	};

	// Public Functions
	return {
		init: function () {
			_int();
		},
		addItem: function (entityId, productId, quantityInputId = null, quantityFreeInputId, submitButton, forceCallback, forcePreventUnblur, fromProductPage) {
			_addItem(entityId, productId, quantityInputId, quantityFreeInputId, submitButton, forceCallback, forcePreventUnblur, fromProductPage);
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
