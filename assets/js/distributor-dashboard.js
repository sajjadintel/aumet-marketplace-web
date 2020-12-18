'use strict';
// Class definition
var DistributorDashboardDataTable = (function () {
	// Private functions

	var datatableOrders;
	var datatableProducts;

	var _initDatatable = function (data, columns) {
		if (datatableDetail != null) {
			datatableDetail.destroy();
		}
		datatableDetail = $('#kt_datatable_detail').KTDatatable({
			// datasource definition

			data: {
				type: 'local',
				source: data,
				pageSize: 10,
			},

			// layout definition
			layout: {
				scroll: false, // enable/disable datatable scroll both horizontal and vertical when needed.
				footer: false, // display/hide footer
			},

			// column sorting
			sortable: true,
			pagination: true,

			// columns definition
			columns: columns,
		});
	};

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
			case 8:
				status = WebAppLocals.getMessage('orderStatus_MissingProducts');
				break;
			case 9:
				status = WebAppLocals.getMessage('orderStatus_Canceled_Pharmacy');
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

		var orderDetailColumns = [
			{
				field: 'productCode',
				title: WebAppLocals.getMessage('productCode'),
				overflow: 'visible',
				width: 120,
				autoHide: false,
			},
			{
				field: 'productNameEn',
				title: WebAppLocals.getMessage('productName'),
				overflow: 'visible',
				width: 200,
				autoHide: false,
			},
			{
				field: 'scientificName',
				title: WebAppLocals.getMessage('productScientificName'),
				overflow: 'visible',
				width: 200,
				autoHide: false,
			},
			{
				field: 'quantity',
				title: WebAppLocals.getMessage('quantity'),
				sortable: false,
				autoHide: false,
				width: 100,
				// callback function support for column rendering
				template: function (row) {
					var output = '';

					output = row.quantity;
					return output;
				},
			},
			{
				field: 'unitPrice',
				title: WebAppLocals.getMessage('unitPrice'),
				sortable: false,
				autoHide: false,
				width: 80,
				// callback function support for column rendering
				template: function (row) {
					var output = row.currency + ' <strong>' + Math.round((parseFloat(row.unitPrice) + Number.EPSILON) * 100) / 100 + '</strong>';
					return output;
				},
			},
			{
				field: 'tax',
				title: WebAppLocals.getMessage('tax'),
				sortable: false,
				width: 50,
				// callback function support for column rendering
				template: function (row) {
					var output = '';

					var output = Math.round((parseFloat(row.tax) + Number.EPSILON) * 100) / 100 + '%';
					return output;
				},
			},
			{
				field: 'total', // + docLang,
				title: WebAppLocals.getMessage('orderTotal'),
				autoHide: false,
				width: 80,
				template: function (row) {
					var output = parseFloat(row.unitPrice) * parseFloat(row.quantity) * (1 + parseFloat(row.tax) / 100);
					output = row.currency + ' <strong>' + Math.round((output + Number.EPSILON) * 100) / 100 + '</strong>';
					return output;
				},
			},
		];

		var orderLogColumns = [
			{
				field: 'name_en',
				title: WebAppLocals.getMessage('orderStatus'),
				autoHide: false,
				template: function (row) {
					var output = row['name_' + docLang];
					return output;
				},
			},
			{
				field: 'fullname',
				title: WebAppLocals.getMessage('userSeller'),
				overflow: 'visible',
				autoHide: false,
			},
			{
				field: 'updatedAt',
				title: WebAppLocals.getMessage('insertDate'),
				autoHide: false,
				width: 120,
				template: function (row) {
					if (row.updatedAt) {
						return '<span class="label label-lg font-weight-bold label-inline" style="direction: ltr">' + moment(row.updatedAt).format('DD / MM / YYYY') + '</span>';
					} else {
						return '';
					}
				},
			},
		];

		$('#modalBootstrapOrderDetailLog')
			.bootstrapSwitch()
			.on('switchChange.bootstrapSwitch', function (event, state) {
				if (state) {
					initDatatable(webResponse.data.orderDetail, orderDetailColumns);
					datatableDetail.reload();
				} else {
					initDatatable(webResponse.data.orderLog, orderLogColumns);
					datatableDetail.reload();
				}
			});

		$('#modalPrint').attr('href', '/web/distributor/order/print/' + webResponse.data.order.id);
		initDatatable(webResponse.data.orderDetail, orderDetailColumns);
		$('#viewModal').appendTo('body').modal('show');
		datatableDetail.reload();
	};

	return {
		// public functions
		initDatatable: function (data, columns) {
			_initDatatable(data, columns);
		},
		orderStatusModal: function (orderId, statusId) {
			_orderStatusModal(orderId, statusId);
		},
		orderViewModal: function (orderId) {
			_orderViewModal(orderId);
		},
		reloadDatatable: function (webResponse) {
			if ($('#popupModal').is(':visible')) {
				$('#popupModal').modal('hide');
			}
			datatableOrders.reload();
		},
	};
})();
