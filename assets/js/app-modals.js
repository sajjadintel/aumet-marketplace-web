'use strict';

// Class Definition
var WebAppModals = (function () {
	var isPharmacy = false;

	var columnDefsOrderDetails = [
		{
			className: 'export_datatable',
			targets: '_all',
		},
		{
			targets: 0,
			title: WebAppLocals.getMessage('productCode'),
			data: 'productCode',
		},
		{
			targets: 1,
			title: WebAppLocals.getMessage('productName'),
			data: 'productNameEn',
		},
		{
			targets: 2,
			title: WebAppLocals.getMessage('productScientificName'),
			data: 'scientificName',
		},
		{
			targets: 3,
			title: WebAppLocals.getMessage('orderShippedQuantity'),
			data: 'shippedQuantity',
			render: function (data, type, row, meta) {
				return row.shippedQuantity;
			},
		},
		{
			targets: 4,
			title: WebAppLocals.getMessage('orderOrderedQuantity'),
			data: 'requestedQuantity',
			visible: false,
		},
		{
			targets: 5,
			title: WebAppLocals.getMessage('unitPrice'),
			data: 'unitPrice',
			render: function (data, type, row, meta) {
				var output = row.currency + ' <strong>' + WebApp.formatMoney(row.unitPrice) + '</strong>';
				return output;
			},
		},
		{
			targets: 6,
			title: WebAppLocals.getMessage('tax'),
			data: 'tax',
			render: function (data, type, row, meta) {
				var output = WebApp.formatMoney(row.tax) + '%';
				return output;
			},
		},
		{
			targets: 7,
			title: WebAppLocals.getMessage('orderTotal'),
			data: 'unitPrice',
			render: function (data, type, row, meta) {
				var output = parseFloat(row.unitPrice) * parseFloat(row.quantity) * (1 + parseFloat(row.tax) / 100);
				output = row.currency + ' <strong>' + WebApp.formatMoney(output) + '</strong>';
				return output;
			},
		},
	];

	var columnDefsOrderLogs = [
		{
			className: 'export_datatable',
			targets: '_all',
		},
		{
			targets: 0,
			title: WebAppLocals.getMessage('orderStatus'),
			data: 'name_en',
			render: function (data, type, row, meta) {
				var output = row['name_' + docLang];
				return output;
			},
		},
		{
			targets: 1,
			title: WebAppLocals.getMessage('userSeller'),
			data: 'fullname',
		},
		{
			targets: 2,
			title: WebAppLocals.getMessage('date'),
			data: 'updatedAt',
			render: function (data, type, row, meta) {
				var output = '';
				if (row.updatedAt) {
					output = '<span class="label label-lg font-weight-bold label-inline" style="direction: ltr">' + moment(row.updatedAt).format('DD / MM / YYYY') + '</span>';
				}
				return output;
			},
		},
	];

	var _orderStatusModal = function (orderId, statusId) {
		WebApp.get('/web/distributor/order/confirm/' + orderId + '/' + statusId + '/dashboard', WebApp.openModal);
	};

	var _orderViewModal = function (orderId, mIsPharmacy) {
		isPharmacy = mIsPharmacy;
		var url = mIsPharmacy ? '/web/pharmacy/order/' : '/web/distributor/order/'
		WebApp.get(url + orderId, _orderViewModalOpen);
	};

	var _orderViewModalOpen = function (webResponse) {
		var status = '';
		switch (webResponse.data.order.statusId) {
			case 1:
				status = WebAppLocals.getMessage('orderStatus_Pending');
				break;
			case 2:
				status = WebAppLocals.getMessage('orderStatus_OnHold');
				break;
			case 3:
				status = WebAppLocals.getMessage('orderStatus_Processing');
				break;
			case 4:
				status = WebAppLocals.getMessage('orderStatus_Completed');
				break;
			case 5:
				status = WebAppLocals.getMessage('orderStatus_Canceled');
				break;
			case 6:
				status = WebAppLocals.getMessage('orderStatus_Received');
				break;
			case 7:
				status = WebAppLocals.getMessage('orderStatus_Paid');
				break;
			case 8:
				status = WebAppLocals.getMessage('orderStatus_MissingProducts');
				break;
			case 9:
				status = WebAppLocals.getMessage('orderStatus_Canceled_Pharmacy');
				break;
		}
		$('#viewModalTitle').html(WebAppLocals.getMessage('orderDetails'));
		$('#modalBranchLabel').html(WebAppLocals.getMessage('branch'));
		if (isPharmacy) {
			$('#modalCustomerNameLabel').html(WebAppLocals.getMessage('entitySeller'));
			$('#modalCustomerNameText').html(webResponse.data.order.entitySeller + ' (' + webResponse.data.order.userSeller + ')');
			$('#modalBranchText').html(webResponse.data.order.branchSeller);
		} else {
			$('#modalCustomerNameLabel').html(WebAppLocals.getMessage('entityBuyer'));
			$('#modalCustomerNameText').html(webResponse.data.order.entityBuyer + ' (' + webResponse.data.order.userBuyer + ')');
			$('#modalBranchText').html(webResponse.data.order.branchBuyer);
		}
		$('#modalStatusLabel').html(WebAppLocals.getMessage('orderStatus'));
		$('#modalStatusText').html(status);
		$('#modalTotalLabel').html(WebAppLocals.getMessage('orderTotal'));
		$('#modalTotalText').html(webResponse.data.order.currency + WebApp.formatMoney(webResponse.data.order.total));
		$('#modalDateLabel').html(WebAppLocals.getMessage('insertDate'));
		$('#modalDateText').html(webResponse.data.order.insertDateTime);
		$('#modalAddressLabel').html(WebAppLocals.getMessage('address'));
		$('#modalAddressText').html(webResponse.data.order.addressBuyer);

		$('#modalBootstrapOrderDetailLog').attr('data-on-text', WebAppLocals.getMessage('orderDetails'));
		$('#modalBootstrapOrderDetailLog').attr('data-off-text', WebAppLocals.getMessage('orderLogs'));

		$('#modalBootstrapOrderDetailLog')
			.bootstrapSwitch()
			.on('switchChange.bootstrapSwitch', function (event, state) {
				if (state) {
					WebApp.DestroyDatatable('#order_details_datatable');
					WebApp.CreateDatatableLocal('Order Details', '#order_details_datatable', webResponse.data.orderDetail, columnDefsOrderDetails);
				} else {
					WebApp.DestroyDatatable('#order_details_datatable');
					WebApp.CreateDatatableLocal('Order Details', '#order_details_datatable', webResponse.data.orderLog, columnDefsOrderLogs);
				}
			});

		if (isPharmacy) {
			$('#modalPrint').attr('href', '/web/pharmacy/order/print/' + webResponse.data.order.id);
		} else {
			$('#modalPrint').attr('href', '/web/distributor/order/print/' + webResponse.data.order.id);
		}

		WebApp.CreateDatatableLocal('Order Details', '#order_details_datatable', webResponse.data.orderDetail, columnDefsOrderDetails);
		$('#viewModal').modal('show');
		// WebApp.ReloadDatatableLocal('#order_details_datatable');
	};

	return {
		orderStatusModal: function (orderId, statusId) {
			_orderStatusModal(orderId, statusId);
		},
		orderViewModal: function (orderId) {
			_orderViewModal(orderId, false);
		},
		orderViewPharmacyModal: function (orderId) {
			_orderViewModal(orderId, true);
		},
	};
})();

var OrderMissingProductListModals = (function () {
	var isPharmacy = false;

	var columnDefsOrderDetails = [
		{
			className: 'export_datatable',
			targets: '_all',
		},
		{
			targets: 0,
			title: WebAppLocals.getMessage('productCode'),
			data: 'productCode',
		},
		{
			targets: 1,
			title: WebAppLocals.getMessage('productName'),
			data: 'productName',
		},
		{
			targets: 2,
			title: WebAppLocals.getMessage('productScientificName'),
			data: 'scientificName',
		},
		{
			targets: 3,
			title: WebAppLocals.getMessage('quantity'),
			data: 'missingQuantity',
		},
		{
			targets: 4,
			title: WebAppLocals.getMessage('unitPrice'),
			data: 'unitPrice',
			render: function (data, type, row, meta) {
				var output = row.currency + ' <strong>' + WebApp.formatMoney(row.unitPrice) + '</strong>';
				return output;
			},
		},
		{
			targets: 5,
			title: WebAppLocals.getMessage('tax'),
			data: 'tax',
			render: function (data, type, row, meta) {
				var output = WebApp.formatMoney(row.tax) + '%';
				return output;
			},
		},
		{
			targets: 6,
			title: WebAppLocals.getMessage('orderTotal'),
			data: 'unitPrice',
			render: function (data, type, row, meta) {
				var output = parseFloat(row.unitPrice) * parseFloat(row.quantity) * (1 + parseFloat(row.tax) / 100);
				output = row.currency + ' <strong>' + WebApp.formatMoney(output) + '</strong>';
				return output;
			},
		},
	];

	var columnDefsOrderLogs = [
		{
			className: 'export_datatable',
			targets: '_all',
		},
		{
			targets: 0,
			title: WebAppLocals.getMessage('orderStatus'),
			data: 'name_en',
			render: function (data, type, row, meta) {
				var output = row['name_' + docLang];
				return output;
			},
		},
		{
			targets: 1,
			title: WebAppLocals.getMessage('userSeller'),
			data: 'fullname',
		},
		{
			targets: 2,
			title: WebAppLocals.getMessage('date'),
			data: 'updatedAt',
			render: function (data, type, row, meta) {
				var output = '';
				if (row.updatedAt) {
					output = '<span class="label label-lg font-weight-bold label-inline" style="direction: ltr">' + moment(row.updatedAt).format('DD / MM / YYYY') + '</span>';
				}
				return output;
			},
		},
	];

	var _orderMissingProductListPharmacyModal = function (orderId, mIsPharmacy) {
		isPharmacy = mIsPharmacy;
		WebApp.get('/web/distributor/orderMissingProducts/' + orderId, _missingProductListModalOpen);
	};

	var _missingProductListModalOpen = function (webResponse) {
		var status = '';
		switch (webResponse.data.order.statusId) {
			case 1:
				status = WebAppLocals.getMessage('orderStatus_Pending');
				break;
			case 2:
				status = WebAppLocals.getMessage('orderStatus_OnHold');
				break;
			case 3:
				status = WebAppLocals.getMessage('orderStatus_Processing');
				break;
			case 4:
				status = WebAppLocals.getMessage('orderStatus_Completed');
				break;
			case 5:
				status = WebAppLocals.getMessage('orderStatus_Canceled');
				break;
			case 6:
				status = WebAppLocals.getMessage('orderStatus_Received');
				break;
			case 7:
				status = WebAppLocals.getMessage('orderStatus_Paid');
				break;
			case 8:
				status = WebAppLocals.getMessage('orderStatus_MissingProducts');
				break;
		}

		$('#missingProductListModalTitle').html(WebAppLocals.getMessage('orderDetails'));
		$('#missingProductListModalBranchLabel').html(WebAppLocals.getMessage('branch'));
		if (isPharmacy) {
			$('#missingProductListModalNameLabel').html(WebAppLocals.getMessage('entitySeller'));
			$('#missingProductListModalNameText').html(webResponse.data.order.entitySeller + ' (' + webResponse.data.order.userSeller + ')');
			$('#missingProductListModalBranchText').html(webResponse.data.order.branchSeller);
		} else {
			$('#missingProductListModalNameLabel').html(WebAppLocals.getMessage('entityBuyer'));
			$('#missingProductListModalNameText').html(webResponse.data.order.entityBuyer + ' (' + webResponse.data.order.userBuyer + ')');
			$('#missingProductListModalBranchText').html(webResponse.data.order.branchBuyer);
		}
		$('#missingProductListModalStatusLabel').html(WebAppLocals.getMessage('orderStatus'));
		$('#missingProductListModalStatusText').html(status);
		$('#missingProductListModalTotalLabel').html(WebAppLocals.getMessage('orderTotal'));
		$('#missingProductListModalTotalText').html(webResponse.data.order.currency + WebApp.formatMoney(webResponse.data.order.total));
		$('#missingProductListModalDateLabel').html(WebAppLocals.getMessage('insertDate'));
		$('#missingProductListModalDateText').html(webResponse.data.order.insertDateTime);
		$('#missingProductListModalAddressLabel').html(WebAppLocals.getMessage('address'));
		$('#missingProductListModalAddressText').html(webResponse.data.order.addressBuyer);

		$('#modalBootstrapOrderDetailLog').attr('data-on-text', WebAppLocals.getMessage('orderDetails'));
		$('#modalBootstrapOrderDetailLog').attr('data-off-text', WebAppLocals.getMessage('orderLogs'));

		$('#modalBootstrapMissingProductListLog').attr('data-on-text', WebAppLocals.getMessage('orderMissingProduct'));
		$('#modalBootstrapMissingProductListLog').attr('data-off-text', WebAppLocals.getMessage('orderLogs'));

		$('#modalBootstrapMissingProductListLog')
			.bootstrapSwitch()
			.on('switchChange.bootstrapSwitch', function (event, state) {
				if (state) {
					WebApp.DestroyDatatable('#missingProductListModalDatatable');
					WebApp.CreateDatatableLocal('Order Missing Products', '#missingProductListModalDatatable', webResponse.data.orderDetail, columnDefsOrderDetails);
				} else {
					WebApp.DestroyDatatable('#missingProductListModalDatatable');
					WebApp.CreateDatatableLocal('Order Missing Products', '#missingProductListModalDatatable', webResponse.data.orderLog, columnDefsOrderLogs);
				}
			});

		WebApp.CreateDatatableLocal('Order Missing Products', '#missingProductListModalDatatable', webResponse.data.orderDetail, columnDefsOrderDetails);

		$('#missingProductListModal').modal('show');
	};

	return {
		orderMissingProductListPharmacyModal: function (orderId) {
			_orderMissingProductListPharmacyModal(orderId, true);
		},
		orderMissingProductListDistributorModal: function (orderId) {
			_orderMissingProductListPharmacyModal(orderId, false);
		},
	};
})();

var WebMissingProductModals = (function () {
	var repeater;
	var products;

	var _orderMissingProductPharmacyModal = function (orderId) {
		WebApp.get('/web/distributor/order/' + orderId, _missingProductModalOpen);
	};

	var _missingProductModalOpen = function (webResponse) {
		var status = '';
		switch (webResponse.data.order.statusId) {
			case 1:
				status = WebAppLocals.getMessage('orderStatus_Pending');
				break;
			case 2:
				status = WebAppLocals.getMessage('orderStatus_OnHold');
				break;
			case 3:
				status = WebAppLocals.getMessage('orderStatus_Processing');
				break;
			case 4:
				status = WebAppLocals.getMessage('orderStatus_Completed');
				break;
			case 5:
				status = WebAppLocals.getMessage('orderStatus_Canceled');
				break;
			case 6:
				status = WebAppLocals.getMessage('orderStatus_Received');
				break;
			case 7:
				status = WebAppLocals.getMessage('orderStatus_Paid');
				break;
			case 8:
				status = WebAppLocals.getMessage('orderStatus_MissingProducts');
				break;
		}
		$('#missingProductModalTitle').html(WebAppLocals.getMessage('orderReportMissing'));
		$('#missingProductModalCustomerNameLabel').html(WebAppLocals.getMessage('entitySeller'));
		$('#missingProductModalCustomerNameText').html(webResponse.data.order.entitySeller + ' (' + webResponse.data.order.userSeller + ')');
		$('#missingProductModalStatusLabel').html(WebAppLocals.getMessage('orderStatus'));
		$('#missingProductModalStatusText').html(status);
		$('#missingProductModalTotalLabel').html(WebAppLocals.getMessage('orderTotal'));
		$('#missingProductModalTotalText').html(webResponse.data.order.currency + ' ' + WebApp.formatMoney(webResponse.data.order.total));
		$('#missingProductModalDateLabel').html(WebAppLocals.getMessage('insertDate'));
		$('#missingProductModalDateText').html(webResponse.data.order.insertDateTime);
		$('#missingProductModalBranchLabel').html(WebAppLocals.getMessage('branch'));
		$('#missingProductModalBranchText').html(webResponse.data.order.branchBuyer);
		$('#missingProductModalAddressLabel').html(WebAppLocals.getMessage('address'));
		$('#missingProductModalAddressText').html(webResponse.data.order.addressBuyer);

		$('#missingProductModalForm').attr('action', '/web/pharmacy/order/missingProducts');

		$('#missingProductOrderId').val(webResponse.data.order.id);

		repeater = $('#missingProductListRepeater').repeater({
			isFirstItemUndeletable: true,
			show: function () {
				$(this).slideDown();
				_validateInput(this);
				_initSelect2(this);
			},
			hide: function (deleteElement) {
				if (confirm(WebAppLocals.getMessage('missingProducts_deleteConfirmation'))) {
					$(this).slideUp(deleteElement);
				}
			},
		});

		repeater.setList([]);

		products = webResponse.data.orderDetail;

		products = $.map(products, function (obj) {
			obj.text = obj.productName;
			obj.id = obj.productCode;
			return obj;
		});

		$('#missingProductModal').appendTo('body').modal('show');
	};

	var _initSelect2 = function initSelect2(input) {
		$(input)
			.find('.select2')
			.select2({
				placeholder: WebAppLocals.getMessage('missingProducts_filterByProduct'),
				data: products,
			});
	};

	var _validateInput = function validateInput(input) {
		$(input)
			.find('.missingProductQuantity')
			.keydown(function () {
				// Save old value.
				if (!$(this).val() || (parseInt($(this).val()) <= $(this).attr('max') && parseInt($(this).val()) >= $(this).attr('min'))) $(this).data('old', $(this).val());
			});
		$(input)
			.find('.missingProductQuantity')
			.keyup(function () {
				// Check correct, else revert back to old value.
				if (!$(this).val() || (parseInt($(this).val()) <= $(this).attr('max') && parseInt($(this).val()) >= $(this).attr('min')));
				else $(this).val($(this).data('old'));
			});
	};

	return {
		orderMissingProductPharmacyModal: function (orderId) {
			_orderMissingProductPharmacyModal(orderId);
		},
	};
})();

var ModifyQuantityOrderModals = (function () {
	var repeater;
	var products;

	var _openModal = function (orderId) {
		WebApp.get('/web/distributor/order/' + orderId, _openModalCallBack);
	};

	var _openModalCallBack = function (webResponse) {
		console.log(webResponse);
		var status = '';
		switch (webResponse.data.order.statusId) {
			case 1:
				status = WebAppLocals.getMessage('orderStatus_Pending');
				break;
			case 2:
				status = WebAppLocals.getMessage('orderStatus_OnHold');
				break;
			case 3:
				status = WebAppLocals.getMessage('orderStatus_Processing');
				break;
			case 4:
				status = WebAppLocals.getMessage('orderStatus_Completed');
				break;
			case 5:
				status = WebAppLocals.getMessage('orderStatus_Canceled');
				break;
			case 6:
				status = WebAppLocals.getMessage('orderStatus_Received');
				break;
			case 7:
				status = WebAppLocals.getMessage('orderStatus_Paid');
				break;
			case 8:
				status = WebAppLocals.getMessage('orderStatus_MissingProducts');
				break;
		}
		$('#modifyQuantityOrderModalTitle').html(WebAppLocals.getMessage('order_modifyQuantity'));
		$('#modifyQuantityOrderModalCustomerNameLabel').html(WebAppLocals.getMessage('entityBuyer'));
		$('#modifyQuantityOrderModalCustomerNameText').html(webResponse.data.order.entityBuyer + ' (' + webResponse.data.order.userBuyer + ')');
		$('#modifyQuantityOrderModalStatusLabel').html(WebAppLocals.getMessage('orderStatus'));
		$('#modifyQuantityOrderModalStatusText').html(status);
		$('#modifyQuantityOrderModalTotalLabel').html(WebAppLocals.getMessage('orderTotal'));
		$('#modifyQuantityOrderModalTotalText').html(webResponse.data.order.currency + WebApp.formatMoney(webResponse.data.order.total));
		$('#modifyQuantityOrderModalDateLabel').html(WebAppLocals.getMessage('insertDate'));
		$('#modifyQuantityOrderModalDateText').html(webResponse.data.order.insertDateTime);
		$('#modifyQuantityOrderModalBranchLabel').html(WebAppLocals.getMessage('branch'));
		$('#modifyQuantityOrderModalBranchText').html(webResponse.data.order.branchBuyer);
		$('#modifyQuantityOrderModalAddressLabel').html(WebAppLocals.getMessage('address'));
		$('#modifyQuantityOrderModalAddressText').html(webResponse.data.order.addressBuyer);

		$('#modifyQuantityOrderModalForm').attr('action', '/web/distributor/order/editQuantityOrder');

		$('#modifyQuantityOrderOrderId').val(webResponse.data.order.id);

		repeater = $('#modifyQuantityOrderListRepeater').repeater({
			isFirstItemUndeletable: true,
			show: function () {
				$(this).slideDown();
				_setValues(this);
				_validateInput(this);
			},
			hide: function (deleteElement) {
				if (confirm(WebAppLocals.getMessage('missingProducts_deleteConfirmation'))) {
					$(this).slideUp(deleteElement);
				}
			},
		});

		products = webResponse.data.orderDetail;

		console.log(products);
		repeater.setList(products);

		$('#modifyQuantityOrderModal').appendTo('body').modal('show');
	};

	var _setValues = function setValues(input) {
		var requestedQuantity = $(input).find('#modifyQuantityOrderRequestedQuantity').val();
		$(input).find('#modifyQuantityOrderQuantity').attr('max', requestedQuantity);
		$(input).find('#modifyQuantityOrderQuantity').attr('min', 0);
		$(input)
			.find('#modifyQuantityOrderQuantityTitle')
			.text('Quantity (initially ' + requestedQuantity + '):');
	};

	var _validateInput = function validateInput(input) {
		$(input)
			.find('#modifyQuantityOrderQuantity')
			.keydown(function () {
				// Save old value.
				if (!$(this).val() || (parseInt($(this).val()) <= $(this).attr('max') && parseInt($(this).val()) >= $(this).attr('min'))) $(this).data('old', $(this).val());
			});
		$(input)
			.find('#modifyQuantityOrderQuantity')
			.keyup(function () {
				// Check correct, else revert back to old value.
				if (!$(this).val() || (parseInt($(this).val()) <= $(this).attr('max') && parseInt($(this).val()) >= $(this).attr('min')));
				else $(this).val($(this).data('old'));
			});
	};

	return {
		openModal: function (orderId) {
			_openModal(orderId);
		},
	};
})();
