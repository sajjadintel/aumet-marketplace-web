'use strict';
// Class definition

var DistributorOrdersDataTable = (function () {
	// Private functions

	var datatable;
	var _readParams;

	var _init = function (objQuery) {
		_readParams = objQuery;
		datatable = $('#kt_datatable').KTDatatable({
			// datasource definition

			data: {
				type: 'remote',
				source: {
					read: {
						url: '/web/distributor/order',
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
				{
					field: 'entityDistributor', // + docLang,
					title: WebAppLocals.getMessage('entityDistributor'),
					autoHide: false,
					template: function (row) {
						var output = row.entityDistributor;
						if (row.userDistributor != null) {
							output += ' (' + row.userDistributor + ')';
						}
						return output;
					},
				},
				{
					field: 'insertDateTime',
					title: WebAppLocals.getMessage('insertDate'),
					autoHide: false,
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
					width: 130,
					overflow: 'visible',
					autoHide: false,
					template: function (row) {
						// TODO: Adjust call to action buttons
						// First make them call the getOrderConfirmation API
						// have getOrderConfirmation open up the modal afterwards
						// make sure the modal proceeds with the action
						var btnView =
							'<a href="javascript:;" onclick=\'DistributorOrdersDataTable.orderStatusModal(' +
							row.id +
							',2)\' \
						class="btn btn-sm btn-primary btn-text-primary btn-hover-primary mr-2" title="Order On Hold">\
						<i class="nav-icon la la-times p-0"></i></a>';
						var btnOrderOnHold =
							'<a href="javascript:;" onclick=\'DistributorOrdersDataTable.orderStatusModal(' +
							row.id +
							',2)\' \
						class="btn btn-sm btn-primary btn-text-primary btn-hover-primary mr-2" title="Order On Hold">\
						<i class="nav-icon la la-times p-0"></i></a>';
						var btnOrderProcess =
							'<a href="javascript:;" onclick=\'DistributorOrdersDataTable.orderStatusModal(' +
							row.id +
							',3)\' \
						class="btn btn-sm btn-primary btn-text-primary btn-hover-primary  mr-2" title="Order Process">\
						<i class="nav-icon la la-check p-0"></i></a>';
						var btnOrderComplete =
							'<a href="javascript:;" onclick=\'DistributorOrdersDataTable.orderStatusModal(' +
							row.id +
							',4)\' \
						class="btn btn-sm btn-primary btn-text-primary btn-hover-primary  mr-2" title="Order Complete">\
						<i class="nav-icon la la-check p-0"></i></a>';
						var btnOrderCancel =
							'<a href="javascript:;" onclick=\'DistributorOrdersDataTable.orderStatusModal(' +
							row.id +
							',5)\' \
						class="btn btn-sm btn-primary btn-text-primary btn-hover-primary  mr-2" title="Order Cancel">\
						<i class="nav-icon la la-times p-0"></i></a>';
						var outActions = '';

						switch (row.statusId) {
							case 1:
								outActions += btnOrderOnHold;
								outActions += btnOrderProcess;
								break;
							case 2:
								outActions += btnOrderProcess;
								outActions += btnOrderCancel;
								break;
							case 3:
								outActions += btnOrderOnHold;
								outActions += btnOrderComplete;
								break;
						}

						return outActions;
					},
				},
			],
		});
	};

	var _orderStatusModal = function (orderId, statusId) {
		WebApp.get('/web/distributor/order/confirm/' + orderId + '/' + statusId, WebApp.openModal);
	};

	var _orderViewModal = function (orderId) {
		WebApp.get('/web/distributor/order/' + orderId, WebApp.openModal);
	};

	return {
		// public functions
		init: function (objQuery) {
			_init(objQuery);
		},
		setReadParams: function (objQuery) {
			_readParams = objQuery;
			datatable.setDataSourceParam('query', _readParams);
			datatable.reload();
		},
		orderStatusModal: function (orderId, statusId) {
			_orderStatusModal(orderId, statusId);
		},
		orderViewModal: function (orderId, statusId) {
			_orderViewModal(orderId, statusId);
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
