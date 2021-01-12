"use strict";
var ProductsBulkAddImage = function () {
    var _init = function () {
        var _id = '#dropZoneProductsBulkAddImage';
        var previewNode = $(_id + " .dropzone-item");
        previewNode.id = "";
        var previewTemplate = previewNode.parent('.dropzone-items').html();
        previewNode.remove();
        $(_id).dropzone({
            url: "/web/distributor/product/bulk/add/image/upload",
            paramName: "file",
            maxFiles: 100,
            maxFilesize: 1000,
            addRemoveLinks: false,
            acceptedFiles: ".png,.jpg,.jpeg",
            createImageThumbnails: false,
            //previewTemplate: previewTemplate,
            //previewsContainer: _id + " .dropzone-items",
            //clickable: "#btnProductsBulkAddImage",
            uploadMultiple: true,
            parallelUploads: 100,
            accept: function (file, done) {
                if (file.name == "justinbieber.jpg") {
                    done("Naha, you don't.");
                } else {
                    done();
                }
            },
            addedfiles: function () {
                $("#dropZoneProductsBulkAddImage").fadeOut();
                $("#dropZoneProductsBulkAddImageProgressContainer").fadeIn();
            },
            totaluploadprogress: function (progress) {
                $("#dropZoneProductsBulkAddImageProgress").html('Uploading: '+ progress + " %");
                $("#dropZoneProductsBulkAddImageProgress").css('width', progress + "%");
                if(progress == 100){
                }

            },
            successmultiple: function (files, response) {
                $("#dropZoneProductsBulkAddImageProgress").html('Upload Completed Successfully');
                WebApp.post('/web/distributor/product/bulk/add/image/upload/process', { mapNewOldFileName: response}, ProductsBulkAddImage.processUpload)
            }
        });
    }

    var _processUpload = function(webReponse){
        $('#productsBulkAddImageProcessResultContainer').html(webReponse.data);

        let valid = true;
        $('.productId').each(function(i, elem) {
            $(elem).select2({
                placeholder: WebAppLocals.getMessage('product'),
        
                ajax: {
                    url: '/web/distributor/product/list',
                    dataType: 'json',
                    processResults: function(response) {
                        return {
                            results: response.data.results,
                            pagination: {
                                more: response.data.pagination
                            }
                        }
                    }
                }
            });

            var productId = $(elem).attr('data-productId');
            var productName = $(elem).attr('data-productName');
            if(productId) {
                $(elem).append(new Option(productName, productId));
                $(elem).val(productId);
            } else {
                valid = false;
            }
		
        });

        $('.productId').each(function(i, elem) {
            $(elem).on('change', (ev) => _checkModalForm());
        });
        $('#addImageDone').prop("disabled", !valid);

		$('#addImageModalTitle').html(WebAppLocals.getMessage('addImageTitle'));
		$('#addImageDone').html(WebAppLocals.getMessage('done'));
		$('#addImageModal').appendTo('body').modal('show');
    };

    var _checkModalForm = function () {
        let valid = true;

        $('.productId').each(function(i, elem) {
            if(!$(elem).val()) valid = false;
        });

        $('#addImageDone').prop("disabled", !valid);
    }

    var _deleteRow = function (index) {
        if($('.productRow').length > 1) {
            $("#productRow-" + index).remove();
            _checkModalForm();
        } else {
		    $('#addImageModal').appendTo('body').modal('hide');
        }
    }

    var _submit = function () {
        let mapProductIdImage = {};
        $('.productRow').each(function(index, element) {
            let rowId = $(element).attr('id');
            let allParts = rowId.split("-");
            let id = allParts[1];

            let productId = $('#productId-' + id).val();
            let image = $('#productImage-' + id).val();

            mapProductIdImage[productId] = image;
        })

        WebApp.post('/web/distributor/product/bulk/add/image', { mapProductIdImage }, null);
    }

    return {
        init: function () {
            _init();
        },
        processUpload: function (webReponse) {
            _processUpload(webReponse);
        },
        deleteRow: function (index) {
            _deleteRow(index);
        },
        submit: function () {
            _submit();
        }
    };
}();
KTUtil.ready(function () {
    ProductsBulkAddImage.init();
});