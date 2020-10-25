'use strict';
// Class definition

var DistributorCustomersDataTable = (function () {
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
						url: window.location.pathname,
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
				},
				{
					field: 'buyerName',
					title: WebAppLocals.getMessage('entityBuyer'),
					autoHide: false,
				},
				{
					field: 'orderCount',
					title: WebAppLocals.getMessage('orderCount'),
					autoHide: false,
					template: function (row) {
						var output = null;

						output = row.orderCount;
						return output;
					},
				},
				{
					field: 'orderTotal',
					title: WebAppLocals.getMessage('orderTotal'),
					autoHide: false,
					template: function (row) {
						var output = null;

						output = row.currencySymbol + ' ' +row.orderTotal;
						return output;
					},
				},
				{
					field: 'orderTotalPaid',
					title: WebAppLocals.getMessage('orderTotalPaid'),
					autoHide: false,
					template: function (row) {
						var output = null;

						output = row.currencySymbol + ' ' +row.orderTotalPaid;
						return output;
					},
				},
				{
					field: 'orderTotalUnPaid',
					title: WebAppLocals.getMessage('orderTotalUnPaid'),
					autoHide: false,
					tempplate: function (row) {
						var output = null;

						output = row.currencySymbol + ' ' + row.orderTotalUnPaid;

						return output;
					},
				},
				{
					field: 'statusId',
					sortable: false,
					width: 120,
					title: WebAppLocals.getMessage('customerStatus'),
					autoHide: false,
					// callback function support for column rendering
					template: function (row) {
						var status = {
							1: {
								title: WebAppLocals.getMessage('relationAvailable'),
								class: ' label-success',
							},
							2: {
								title: WebAppLocals.getMessage('relationBlacklisted'),
								class: ' label-danger',
							},
						};

						var output = '';

						output +=
							'<div><span class="label label-lg font-weight-bold ' + status[row.statusId].class + ' label-inline">' + status[row.statusId].title + '</span></div>';
						// output += '<div class="text-muted">' + (row.stockUpdateDateTime != null ? jQuery.timeago(row.stockUpdateDateTime) : 'NA') + '</div>';

						return output;
					},
				},
			],
		});
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
