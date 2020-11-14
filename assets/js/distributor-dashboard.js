'use strict';
// Class definition
var DistributorDashboardDataTable = (function () {
	// Private functions

	var datatableOrders;
	var datatableProducts;

	var _initRecentOrders = function () {
		datatableOrders = $('#kt_datatable_orders').KTDatatable({
			// datasource definition

			data: {
				type: 'remote',
				source: {
					read: {
						url: '/web/distributor/order/recent',
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
					width: 80,
					autoHide: false,
					template: function (row) {
						var output = '(' + row.id + ') - #' + row.serial;
						return output;
					},
				},
				{
					field: 'entityBuyer',
					title: WebAppLocals.getMessage('entityBuyer'),
					autoHide: false,
					template: function (row) {
						var output = row.entityBuyer;
						//if (row.userBuyer != null) {
						//	output += ' (' + row.userBuyer + ')';
						//}
						return output;
					},
				},
				{
					field: 'insertDateTime',
					title: WebAppLocals.getMessage('insertDate'),
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
					field: 'total', // + docLang,
					title: WebAppLocals.getMessage('orderTotal'),
					width: 120,
					template: function (row) {
						var output = row.currency + ' <strong>' + Math.round((parseFloat(row.total) + Number.EPSILON) * 100) / 100 + ' </strong>';
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
						var output = Math.round((parseFloat(row.tax) + Number.EPSILON) * 100) / 100 + '%';
						return output;
					},
				},
				{
					field: 'total', // + docLang,
					title: WebAppLocals.getMessage('orderTotalWithVAT'),
					width: 120,
					autoHide: false,
					template: function (row) {
						var output = row.currency + ' <strong>' + Math.round((parseFloat(row.total) + Number.EPSILON) * 100) / 100 + ' </strong>';
						return output;
					},
				},
				{
					field: 'Actions',
					title: '',
					sortable: false,
					overflow: 'visible',
					width: 150,
					autoHide: false,
					template: function (row) {
						var dropdownStart =
							'<div class="dropdown dropdown-inline">\
                            <a href="javascript:;" class="btn btn-sm navi-link btn-primary btn-hover-primary mr-2" data-toggle="dropdown">\
								<i class="nav-icon la la-ellipsis-h p-0"></i></a>\
                            <div class="dropdown-menu dropdown-menu-md">\
                                <ul class="navi flex-column navi-hover py-2">';
						var dropdownEnd = '</ul>\
                            </div>\
						</div>';
						var dropdownItemStart = '<li class="navi-item">';
						var dropdownItemEnd = '</li>';

						var btnPrint =
							'<a href="/web/distributor/order/print/' +
							row.id +
							'" target="_blank" class="btn btn-sm navi-link btn-outline-primary btn-hover-primary mr-2" title="Print Order">\
						<i class="nav-icon la la-print p-0"></i></a>';
						var btnView =
							'<a href="javascript:;" onclick=\'DistributorOrdersDataTable.orderViewModal(' +
							row.id +
							')\' \
						class="btn btn-sm navi-link btn-outline-primary btn-hover-primary mr-2" title="View">\
						<i class="nav-icon la la-eye p-0"></i></a>';

						var btnOrderProcess =
							'<a class="navi-link" href="javascript:;" onclick=\'DistributorOrdersDataTable.orderStatusModal(' +
							row.id +
							',3)\' \
						class="btn btn-sm btn-primary btn-hover-primary  mr-2 navi-link" title="Order Process">\
						<span class="navi-icon"><i class="la la-check"></i></span><span class="navi-text"> &nbsp&nbsp' +
							WebAppLocals.getMessage('orderStatusMove') +
							WebAppLocals.getMessage('orderStatus_Processing') +
							'</span></a>';
						var btnOrderComplete =
							'<a class="navi-link" href="javascript:;" onclick=\'DistributorOrdersDataTable.orderStatusModal(' +
							row.id +
							',4)\' \
						class="btn btn-sm btn-primary btn-hover-primary  mr-2" navi-link title="Order Complete">\
						<span class="navi-icon"><i class="la la-check"></i></span><span class="navi-text"> &nbsp&nbsp' +
							WebAppLocals.getMessage('orderStatusMove') +
							WebAppLocals.getMessage('orderStatus_Completed') +
							'</span></a>';
						var btnOrderOnHold =
							'<a class="navi-link bg-danger-hover" href="javascript:;" onclick=\'DistributorOrdersDataTable.orderStatusModal(' +
							row.id +
							',2)\' \
						class="btn btn-sm btn-primary btn-hover-primary mr-2 navi-link" title="Order On Hold">\
						<span class="navi-icon"><i class="la la-times"></i></span><span class="navi-text"> &nbsp&nbsp' +
							WebAppLocals.getMessage('orderStatusMove') +
							WebAppLocals.getMessage('orderStatus_OnHold') +
							'</span></a>';
						var btnOrderCancel =
							'<a class="navi-link bg-danger-hover" href="javascript:;" onclick=\'DistributorOrdersDataTable.orderStatusModal(' +
							row.id +
							',5)\' \
						class="btn btn-sm btn-primary btn-hover-primary  mr-2 navi-link" title="Order Cancel">\
						<span class="navi-icon"><i class="la la-times"></i></span><span class="navi-text"> &nbsp&nbsp' +
							WebAppLocals.getMessage('orderStatusMove') +
							WebAppLocals.getMessage('orderStatus_Canceled') +
							'</span></a>';

						var btnOrderPaid =
							'<a href="javascript:;" onclick=\'DistributorOrdersDataTable.orderStatusModal(' +
							row.id +
							',7)\' \
						class="btn btn-sm btn-primary btn-hover-primary  mr-2 navi-link" title="Order Paid">\
						<i class="nav-icon la la-dollar p-0"></i> &nbsp&nbsp' +
							WebAppLocals.getMessage('orderStatusMove') +
							WebAppLocals.getMessage('orderStatus_Paid') +
							'</a>';
						var outActions = '';

						outActions += btnView;
						outActions += btnPrint;

						switch (row.statusId) {
							case 1:
								outActions += dropdownStart;
								outActions += dropdownItemStart + btnOrderProcess + dropdownItemEnd;
								outActions += dropdownItemStart + btnOrderOnHold + dropdownItemEnd;
								outActions += dropdownEnd;
								break;
							case 2:
								outActions += dropdownStart;
								outActions += dropdownItemStart + btnOrderProcess + dropdownItemEnd;
								outActions += dropdownItemStart + btnOrderCancel + dropdownItemEnd;
								outActions += dropdownEnd;
								break;
							case 3:
								outActions += dropdownStart;
								outActions += dropdownItemStart + btnOrderComplete + dropdownItemEnd;
								outActions += dropdownItemStart + btnOrderOnHold + dropdownItemEnd;
								outActions += dropdownEnd;
								break;
							case 6:
								outActions += btnOrderPaid;
						}

						return outActions;
					},
				},
			],
		});
	};

	var _initTopProducts = function () {
		datatableProducts = $('#kt_datatable_products').KTDatatable({
			// datasource definition

			data: {
				type: 'remote',
				source: {
					read: {
						url: '/web/distributor/product/bestselling',
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
					width: 40,
					type: 'number',
					selector: false,
					textAlign: 'left',
					autoHide: false,
				},
				{
					field: 'productName_en', // + docLang,
					title: WebAppLocals.getMessage('productName'),
					autoHide: false,
				},
				{
					field: 'image',
					title: '',
					autoHide: true,
					sortable: false,
					template: function (row) {
						return (
							'<div class="symbol symbol-60 flex-shrink-0 mr-4 bg-light"> <div class="symbol-label" style="background-image: url(\'' +
							row.image +
							'\')" ></div></div>'
						);
					},
				},
				{
					field: 'quantityOrdered',
					title: WebAppLocals.getMessage('quantityOrdered'),
					autoHide: false,
				},
				{
					field: 'scientificName',
					title: WebAppLocals.getMessage('productScientificName'),
					autoHide: true,
				},
				{
					field: 'expiryDate',
					title: WebAppLocals.getMessage('expiryDate'),
					autoHide: true,
					template: function (row) {
						if (row.expiryDate) {
							return (
								'<span class="label label-lg font-weight-bold label-inline" style="direction: ltr">' + moment(row.expiryDate).format('DD / MM / YYYY') + '</span>'
							);
						} else {
							return '';
						}
					},
				},
				{
					field: 'stockStatusId',
					sortable: false,
					title: WebAppLocals.getMessage('stockAvailability'),
					autoHide: true,
					// callback function support for column rendering
					template: function (row) {
						var status = {
							1: {
								title: WebAppLocals.getMessage('stockAvailability_available'),
								class: ' label-primary',
							},
							2: {
								title: WebAppLocals.getMessage('stockAvailability_notAvailable'),
								class: ' label-danger',
							},
							3: {
								title: WebAppLocals.getMessage('stockAvailability_availableSoon'),
								class: ' label-warning',
							},
						};

						var output = '';

						output +=
							'<div><span class="label label-lg font-weight-bold ' +
							status[row.stockStatusId].class +
							' label-inline">' +
							status[row.stockStatusId].title +
							'</span></div>';
						// output += '<div class="text-muted">' + (row.stockUpdateDateTime != null ? jQuery.timeago(row.stockUpdateDateTime) : 'NA') + '</div>';

						return output;
					},
				},
				{
					field: 'stockUpdateDateTime',
					title: WebAppLocals.getMessage('stockUpdateDateTime'),
					autoHide: true,
					template: function (row) {
						if (row.stockUpdateDateTime) {
							return '<span class="label label-lg font-weight-bold label-inline" style="direction: ltr">' + moment(row.stockUpdateDateTime).fromNow() + '</span>';
						} else {
							return '';
						}
					},
				},
				{
					field: 'unitPrice', // + docLang,
					title: WebAppLocals.getMessage('unitPrice'),
					autoHide: false,
					template: function (row) {
						return '<span class="font-size-sm">' + row.currency + '</span>' + ' <b class="font-size-h4">' + row.unitPrice + '</b>';
					},
				},
			],
		});
	};

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
		init: function () {
			_initRecentOrders();
			_initTopProducts();
		},
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
