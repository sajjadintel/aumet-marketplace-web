'use strict';
// Class definition

var GenericDatatable = (function () {
	// Private functions
	var datatableVar = null;

	var _initDatatable = function (vElementId, vUrl, vColumnDefs = null, vAdditionalOptions = null, vParams = null) {
		//////////////////////////
		// delete cached datatable
		if ($.fn.DataTable.isDataTable(datatableVar)) {
			datatableVar.clear();
			datatableVar.destroy();
		}

		var fileName = 'Aumet Marketplace';
		if (!$('#pageTitle').is(':hidden')) {
			fileName += ' - ' + $('#pageTitle').text();
		}
		if (!$('#dashboard_daterangepicker_start').is(':hidden')) {
			fileName += ' - ' + $('#dashboard_daterangepicker_start').data('dateFrom') + ' to ' + $('#dashboard_daterangepicker_start').data('dateTo');
		}
		if (!$('#dashboard_daterangepicker_end').is(':hidden')) {
			fileName += ' -- ' + $('#dashboard_daterangepicker_end').data('dateFrom') + ' to ' + $('#dashboard_daterangepicker_start').data('dateTo');
		}

		var dbOptions = {
			dom: 'Brt<"float-right"i><"float-right"l><"float-left"p>',
			responsive: true,
			scrollX: false,
			orderCellsTop: true,
			order: [[0, 'asc']],
			language: {
				lengthMenu: '_MENU_',
				info: 'Showing _START_ - _END_ of _TOTAL_',
				infoEmpty: 'Showing 0',
				infoFiltered: '(from _MAX_ total)',
			},
			buttons: [
				{
					extend: 'excelHtml5',
					filename: fileName,
				},
				{
					extend: 'pdfHtml5',
					filename: fileName,
				},
			],
			processing: true,
			serverSide: true,
			ajax: {
				url: vUrl,
				dataType: 'json',
				type: 'POST',
			},
			columnDefs: vColumnDefs,
		};

		if (vParams != null) {
			dbOptions['ajax']['data'] = function (data) {
				vParams.forEach(function (vValue) {
					data[vValue.value] = $(vValue.id).val();
				});
			};
		}

		var dbOptionsObj = { ...dbOptions };

		if (vAdditionalOptions && vAdditionalOptions.datatableOptions) {
			var dbOptionsObj = { ...dbOptions, ...vAdditionalOptions.datatableOptions };
		}

		datatableVar = $('' + vElementId).DataTable(dbOptionsObj);

		return datatableVar;
	};

	return {
		// public functions
		init: function (vElementId, vUrl, vColumnDefs, vAdditionalOptions = null, vParams = null) {
			_initDatatable(vElementId, vUrl, vColumnDefs, vAdditionalOptions, vParams);
		},
		initDatatable: function (vElementId, vUrl, vColumnDefs, vAdditionalOptions = null, vParams = null) {
			initDatatable(vElementId, vUrl, vColumnDefs, vAdditionalOptions, vParams);
		},
	};
})();
