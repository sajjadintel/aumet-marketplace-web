'use strict';

// Class Definition
var WebAppModals = (function () {
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
			title: WebAppLocals.getMessage('quantity'),
			data: 'quantity',
		},
		{
			targets: 4,
			title: WebAppLocals.getMessage('unitPrice'),
			data: 'unitPrice',
			render: function (data, type, row, meta) {
				var output = row.currency + ' <strong>' + Math.round((parseFloat(row.unitPrice) + Number.EPSILON) * 100) / 100 + '</strong>';
				return output;
			},
		},
		{
			targets: 5,
			title: WebAppLocals.getMessage('tax'),
			data: 'tax',
			render: function (data, type, row, meta) {
				var output = Math.round((parseFloat(row.tax) + Number.EPSILON) * 100) / 100 + '%';
				return output;
			},
		},
		{
			targets: 6,
			title: WebAppLocals.getMessage('orderTotal'),
			data: 'unitPrice',
			render: function (data, type, row, meta) {
				var output = parseFloat(row.unitPrice) * parseFloat(row.quantity) * (1 + parseFloat(row.tax) / 100);
				output = row.currency + ' <strong>' + Math.round((output + Number.EPSILON) * 100) / 100 + '</strong>';
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
			title: WebAppLocals.getMessage('insertDate'),
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

	var _orderViewModal = function (orderId) {
		WebApp.get('/web/distributor/order/' + orderId, _orderViewModalOpen);
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
		}
		$('#viewModalTitle').html(WebAppLocals.getMessage('orderDetails'));
		$('#modalCustomerNameLabel').html(WebAppLocals.getMessage('entityBuyer'));
		$('#modalCustomerNameText').html(webResponse.data.order.entityBuyer + ' (' + webResponse.data.order.userBuyer + ')');
		$('#modalStatusLabel').html(WebAppLocals.getMessage('orderStatus'));
		$('#modalStatusText').html(status);
		$('#modalTotalLabel').html(WebAppLocals.getMessage('orderTotal'));
		$('#modalTotalText').html(webResponse.data.order.currency + Math.round((parseFloat(webResponse.data.order.total) + Number.EPSILON) * 100) / 100);
		$('#modalDateLabel').html(WebAppLocals.getMessage('insertDate'));
		$('#modalDateText').html(webResponse.data.order.insertDateTime);
		$('#modalBranchLabel').html(WebAppLocals.getMessage('branch'));
		$('#modalBranchText').html(webResponse.data.order.branchBuyer);
		$('#modalAddressLabel').html(WebAppLocals.getMessage('address'));
		$('#modalAddressText').html(webResponse.data.order.addressBuyer);

		$('#modalBootstrapOrderDetailLog').attr('data-on-text', WebAppLocals.getMessage('orderDetails'));
		$('#modalBootstrapOrderDetailLog').attr('data-off-text', WebAppLocals.getMessage('orderLogs'));

		$('#modalBootstrapOrderDetailLog')
			.bootstrapSwitch()
			.on('switchChange.bootstrapSwitch', function (event, state) {
				if (state) {
					WebApp.CreateDatatableLocal('Order Details', '#order_details_datatable', webResponse.data.orderDetail, columnDefsOrderDetails);
				} else {
					WebApp.CreateDatatableLocal('Order Details', '#order_details_datatable', webResponse.data.orderLog, columnDefsOrderLogs);
				}
			});

		$('#modalPrint').attr('href', '/web/distributor/order/print/' + webResponse.data.order.id);

		WebApp.CreateDatatableLocal('Order Details', '#order_details_datatable', webResponse.data.orderDetail, columnDefsOrderDetails);
		$('#viewModal').modal('show');
		// WebApp.ReloadDatatableLocal('#order_details_datatable');
	};

	return {
		orderStatusModal: function (orderId, statusId) {
			_orderStatusModal(orderId, statusId);
		},
		orderViewModal: function (orderId) {
			_orderViewModal(orderId);
		},
	};
})();
