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

        
        $('.productName').each(function(i, elem) {
            console.log('elem');
            console.log(elem);
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
            console.log("productId")
            console.log(productId);
            if(productId) {
                $(elem).append(new Option(productName, productId));
                $(elem).val(productId);
            }
		
        });

		$('#addImageModalTitle').html(WebAppLocals.getMessage('addImageTitle'));
		$('#addImageDone').html(WebAppLocals.getMessage('done'));
		$('#addImageModal').appendTo('body').modal('show');
    };

    return {
        init: function () {
            _init();
        },
        processUpload: function (webReponse) {
            _processUpload(webReponse);
        }
    };
}();
KTUtil.ready(function () {
    ProductsBulkAddImage.init();
});