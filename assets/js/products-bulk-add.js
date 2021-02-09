'use strict';
var ProductsBulkAdd = (function () {
	var _init = function () {
		var _id = '#dropZoneProductsBulkAdd';
		var previewNode = $(_id + ' .dropzone-item');
		previewNode.id = '';
		var previewTemplate = previewNode.parent('.dropzone-items').html();
		previewNode.remove();
		$(_id).dropzone({
			url: '/web/distributor/product/bulk/add/upload',
			paramName: 'file',
			maxFiles: 1,
			maxFilesize: 100,
			addRemoveLinks: false,
			acceptedFiles: '.xlsm,.xlsx',
			createImageThumbnails: false,
			accept: function (file, done) {
				done();
			},
			addedfile: function (file) {
				$('#dropZoneProductsBulkAdd').fadeOut();
				$('#dropZoneProductsBulkAddProgressContainer').fadeIn();
			},
			totaluploadprogress: function (progress) {
				$('#dropZoneProductsBulkAddProgress').html('Uploading: ' + progress + ' %');
				$('#dropZoneProductsBulkAddProgress').css('width', progress + '%');
			},
			success: function (file, response) {
				$('#goBackContainer').show();
				$('#dropZoneProductsBulkAddProgress').html(WebAppLocals.getMessage('uploadSuccess'));
				WebApp.post('/web/distributor/product/bulk/add/upload/process', null, _uploadProcessSuccessCallback);
			},
			error: function (file, error) {
				// Show error message
				WebApp.alertError(WebAppLocals.getMessage('bulkAddUploadError'));

				// Change upload bar
				$('#dropZoneProductsBulkAddProgress').removeClass('bg-primary');
				$('#dropZoneProductsBulkAddProgress').addClass('bg-danger');
				$('#dropZoneProductsBulkAddProgress').css('width', '100%');
				$('#dropZoneProductsBulkAddProgress').html(WebAppLocals.getMessage('uploadError'));

				// Show go back button
				$('#goBackContainer').show();
			},
		});
	};

	var _uploadProcessSuccessCallback = function (webReponse) {
		$('#productsBulkAddProcessResultContainer').addClass('mt-20');
		$('#productsBulkAddProcessResultContainer').html(webReponse.data);
	};

	return {
		init: function () {
			_init();
		},
	};
})();
KTUtil.ready(function () {
	ProductsBulkAdd.init();
});
