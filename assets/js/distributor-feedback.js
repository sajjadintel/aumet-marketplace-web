'use strict';
// Class definition

var CustomerFeedbackDataTable = (function () {
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
			order: [[2, 'desc']],

			// columns definition
			columns: [
				{
					field: 'id',
					title: '#',
					sortable: 'desc',
					selector: false,
					textAlign: 'left',
					autoHide: false,
					width: 75,
				},
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
					field: 'entityName',
					title: WebAppLocals.getMessage('entityBuyer'),
					autoHide: false,
				},
				{
					field: 'userFullname',
					title: WebAppLocals.getMessage('userFullname'),
					autoHide: false,
					template: row => {
						return "<a href='mailto:"+row.userEmail+"' >"+row.userFullname+"</a>"
					}
				},
				{
					field: 'rateId',
					sortable: false,
					width: 120,
					title: WebAppLocals.getMessage('orderRating'),
					autoHide: false,
					// callback function support for column rendering
					template: function (row) {
						var status = {
							1: {
								title: row.rateName,
								class: ' label-danger',
							},
							2: {
								title: row.rateName,
								class: ' label-warning',
							},
							3: {
								title: row.rateName,
								class: ' label-dark',
							},
							4: {
								title: row.rateName,
								class: ' label-info',
							},
							5: {
								title: row.rateName,
								class: ' label-primary',
							}
						};

						var output = '';

						output +=
							'<div><span class="label label-lg font-weight-bold ' + status[row.rateId].class + ' label-inline">' + status[row.rateId].title + '</span></div>';

						return output;
					},
				},
				{
					field: 'stars',
					sortable: false,
					width: 150,
					title: "",
					autoHide: false,
					// callback function support for column rendering
					template: function (row) {


						var cssData = "text-muted";
						switch (row.rateId) {
							case 1:
								cssData = "text-danger";
								break;
							case 2:
								cssData = "text-warning";
								break;
							case 3:
								cssData = "text-dark";
								break;
							case 4:
								cssData = "text-info";
								break;
							case 5:
								cssData = "text-primary";
								break;
						}

						var output = '<div>';
						for(var i=0; i<row.stars; i++){
							output +=
								'<i class="icon-xl fas fa-star mr-1 '+cssData+'"></i>';
						}
						for(; i<5; i++){
							output +=
								'<i class="icon-xl far fa-star mr-1 text-muted"></i>';
						}
						output += '</div>';

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
