'use strict';
// Class definition

var DistributorCustomersDataTable = (function () {
	// Private functions

	var datatable;
	var datatableDetail;
	var _readParams;

	var _init = function (objQuery) {
		_readParams = objQuery;
		datatable = $('#kt_datatable').KTDatatable({
			// datasource definition

			data: {
				type: 'remote',
				source: {
					read: {
						url: '/web/distributor/customers',
						params: _readParams,
					},
				},
				serverPaging: true,
				serverFiltering: true,
				serverSorting: true,
			},

			// layout definition
			layout: {
				scroll: false, // enable/disable datatable scroll both horizontal and vertical when needed.
				footer: false, // display/hide footer
			},

			// column sorting
			sortable: true,

			pagination: true,

			// Order settings
			order: [[2, 'asc']],

			// columns definition
			columns: [
				{
					field: 'id',
					title: '#',
					sortable: 'asc',
					selector: false,
					textAlign: 'left',
					width: 120,
					autoHide: false,
					template: function (row) {
						var output = '(' + row.id + ') - #' + row.serial;
						return output;
					},
				},
				{
					field: 'entityCustomer',
					title: WebAppLocals.getMessage('entityCustomer'),
					autoHide: false,
					template: function (row) {
						var output = row.entityCustomer;
						if (row.userCustomer != null) {
							output += ' (' + row.userCustomer + ')';
						}
						return output;
					},
				},
				// {
				// 	field: 'entityDistributor', // + docLang,
				// 	title: WebAppLocals.getMessage('entityDistributor'),
				// 	autoHide: false,
				// 	template: function (row) {
				// 		var output = row.entityDistributor;
				// 		if (row.userDistributor != null) {
				// 			output += ' (' + row.userDistributor + ')';
				// 		}
				// 		return output;
				// 	},
				// },
				{
					field: 'insertDateTime',
					title: WebAppLocals.getMessage('insertDate'),
					autoHide: false,
					width: 120,
					template: function (row) {
						if (row.insertDateTime) {
							return (
								'<span class="label label-lg font-weight-bold label-inline" style="direction: ltr">' +
								moment(row.insertDateTime).format('DD / MM / YYYY') +
								'</span>'
							);
						} else {
							return '';
						}
					},
				},
				{
					field: 'statusId',
					sortable: false,
					width: 120,
					title: WebAppLocals.getMessage('orderStatus'),
					autoHide: false,
					// callback function support for column rendering
					template: function (row) {
						var status = {
							1: {
								title: WebAppLocals.getMessage('orderStatus_Pending'),
								class: ' label-primary',
							},
							2: {
								title: WebAppLocals.getMessage('orderStatus_OnHold'),
								class: ' label-warning',
							},
							3: {
								title: WebAppLocals.getMessage('orderStatus_Processing'),
								class: ' label-warning',
							},
							4: {
								title: WebAppLocals.getMessage('orderStatus_Completed'),
								class: ' label-success',
							},
							5: {
								title: WebAppLocals.getMessage('orderStatus_Canceled'),
								class: ' label-danger',
							},
							6: {
								title: WebAppLocals.getMessage('orderStatus_Received'),
								class: ' label-success',
							},
						};

						var output = '';

						output +=
							'<div><span class="label label-lg font-weight-bold ' + status[row.statusId].class + ' label-inline">' + status[row.statusId].title + '</span></div>';
						// output += '<div class="text-muted">' + (row.stockUpdateDateTime != null ? jQuery.timeago(row.stockUpdateDateTime) : 'NA') + '</div>';

						return output;
					},
				},
				{
					field: 'total', // + docLang,
					title: WebAppLocals.getMessage('orderTotal'),
					width: 120,
					autoHide: false,
					template: function (row) {
						var output = row.currency + ' ' + Math.round((parseFloat(row.total) + Number.EPSILON) * 100) / 100;
						return output;
					},
				},
				{
					field: 'Actions',
					title: '',
					sortable: false,
					overflow: 'visible',
					width: 400,
					autoHide: false,
					template: function (row) {
						var btnView =
							'<a href="javascript:;" onclick=\'DistributorOrdersDataTable.orderViewModal(' +
							row.id +
							')\' \
						class="btn btn-sm btn-outline-primary btn-hover-primary mr-2 mt-2" title="View">\
						<i class="nav-icon la la-eye p-0"></i> ' +
							WebAppLocals.getMessage('view') +
							'</a>';
						var btnOrderOnHold =
							'<a href="javascript:;" onclick=\'DistributorOrdersDataTable.orderStatusModal(' +
							row.id +
							',2)\' \
						class="btn btn-sm btn-info btn-hover-primary mr-2 mt-2" title="Order On Hold">\
						<i class="nav-icon la la-times p-0"></i> ' +
							WebAppLocals.getMessage('orderStatusMove') +
							WebAppLocals.getMessage('orderStatus_OnHold') +
							'</a>';
						var btnOrderProcess =
							'<a href="javascript:;" onclick=\'DistributorOrdersDataTable.orderStatusModal(' +
							row.id +
							',3)\' \
						class="btn btn-sm btn-success btn-hover-primary  mr-2 mt-2" title="Order Process">\
						<i class="nav-icon la la-check p-0"></i> ' +
							WebAppLocals.getMessage('orderStatusMove') +
							WebAppLocals.getMessage('orderStatus_Processing') +
							'</a>';
						var btnOrderComplete =
							'<a href="javascript:;" onclick=\'DistributorOrdersDataTable.orderStatusModal(' +
							row.id +
							',4)\' \
						class="btn btn-sm btn-success btn-hover-primary  mr-2 mt-2" title="Order Complete">\
						<i class="nav-icon la la-check p-0"></i> ' +
							WebAppLocals.getMessage('orderStatusMove') +
							WebAppLocals.getMessage('orderStatus_Completed') +
							'</a>';
						var btnOrderCancel =
							'<a href="javascript:;" onclick=\'DistributorOrdersDataTable.orderStatusModal(' +
							row.id +
							',5)\' \
						class="btn btn-sm btn-danger btn-hover-primary  mr-2 mt-2" title="Order Cancel">\
						<i class="nav-icon la la-times p-0"></i> ' +
							WebAppLocals.getMessage('orderStatusMove') +
							WebAppLocals.getMessage('orderStatus_Canceled') +
							'</a>';
						var outActions = '';
						outActions += btnView;

						switch (row.statusId) {
							case 1:
								outActions += btnOrderProcess;
								outActions += btnOrderOnHold;
								break;
							case 2:
								outActions += btnOrderProcess;
								outActions += btnOrderCancel;
								break;
							case 3:
								outActions += btnOrderComplete;
								outActions += btnOrderOnHold;
								break;
						}

						return outActions;
					},
				},
			],
		});
	};

	var _initOrderDetailDatatable = function (data) {
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
			columns: [
				{
					field: 'productNameEn',
					title: WebAppLocals.getMessage('productName'),
					overflow: 'visible',
					width: 200,
					autoHide: false,
				},
				{
					field: 'scientificName',
					title: WebAppLocals.getMessage('productScintificName'),
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

						output = row.quantity + ' (' + row.stock + ')';
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
						var output = '';

						var output = Math.round((parseFloat(row.unitPrice) + Number.EPSILON) * 100) / 100;
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
						output = Math.round((output + Number.EPSILON) * 100) / 100;
						return output;
					},
				},
			],
		});
	};

	var _orderStatusModal = function (orderId, statusId) {
		WebApp.get('/web/distributor/order/confirm/' + orderId + '/' + statusId, WebApp.openModal);
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
		}
		$('#viewModalTitle').html(WebAppLocals.getMessage('order'));
		$('#modalCustomerNameLabel').html(WebAppLocals.getMessage('entityCustomer'));
		$('#modalCustomerNameText').html(webResponse.data.order.entityCustomer + ' (' + webResponse.data.order.userCustomer + ')');
		$('#modalStatusLabel').html(WebAppLocals.getMessage('orderStatus'));
		$('#modalStatusText').html(status);
		$('#modalTotalLabel').html(WebAppLocals.getMessage('orderTotal'));
		$('#modalTotalText').html(webResponse.data.order.currency + Math.round((parseFloat(webResponse.data.order.total) + Number.EPSILON) * 100) / 100);
		$('#modalDateLabel').html(WebAppLocals.getMessage('insertDate'));
		$('#modalDateText').html(webResponse.data.order.insertDateTime);
		$('#modalOrderDetailLabel').html(WebAppLocals.getMessage('orderDetails'));
		$('#modalPrint').attr('href', '/web/distributor/order/print/' + webResponse.data.order.id);
		_initOrderDetailDatatable(webResponse.data.orderDetail);
		$('#viewModal').appendTo('body').modal('show');
		datatableDetail.reload();
	};

	return {
		// public functions
		init: function (objQuery) {
			_init(objQuery);
		},
		initOrderDetailDatatable: function (data) {
			_initOrderDetailDatatable(data);
		},
		setReadParams: function (objQuery) {
			_readParams = objQuery;
			datatable.setDataSourceParam('query', _readParams);
			datatable.reload();
		},
		orderStatusModal: function (orderId, statusId) {
			_orderStatusModal(orderId, statusId);
		},
		orderViewModal: function (orderId) {
			_orderViewModal(orderId);
		},
		showColumn: function (columnName) {
			datatable.showColumn(columnName);
		},
		hideColumn: function (columnName) {
			datatable.hideColumn(columnName);
		},
		reloadDatatable: function (webResponse) {
			if ($('#popupModal').is(':visible')) {
				$('#popupModal').modal('hide');
			}
			datatable.reload();
		},
	};
})();
