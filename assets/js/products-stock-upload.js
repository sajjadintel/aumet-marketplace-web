"use strict";
var ProductsStockUpload = function () {
    var _init = function () {
        var _id = '#dropZoneProductsStockUpload';
        var previewNode = $(_id + " .dropzone-item");
        previewNode.id = "";
        var previewTemplate = previewNode.parent('.dropzone-items').html();
        previewNode.remove();
        $(_id).dropzone({
            url: "/web/distributor/product/stock/upload",
            paramName: "file",
            maxFiles: 1,
            maxFilesize: 100,
            addRemoveLinks: false,
            acceptedFiles: ".xlsx,.xls,.csv",
            createImageThumbnails: false,
            //previewTemplate: previewTemplate,
            //previewsContainer: _id + " .dropzone-items",
            //clickable: "#btnProductsStockUpload",
            accept: function (file, done) {
                if (file.name == "justinbieber.jpg") {
                    done("Naha, you don't.");
                } else {
                    done();
                }
            },
            addedfile: function (file) {
                $("#dropZoneProductsStockUpload").fadeOut();
                $("#dropZoneProductsStockUploadProgressContainer").fadeIn();
            },

            sending: function (file) {

            },
            totaluploadprogress: function (progress) {
                $("#dropZoneProductsStockUploadProgress").html('Uploading: '+ progress + " %");
                $("#dropZoneProductsStockUploadProgress").css('width', progress + "%");
                if(progress == 100){
                }

            },
            complete: function (progress) {
                $("#dropZoneProductsStockUploadProgress").html('Upload Completed Successfully');
                WebApp.post('/web/distributor/product/stock/upload/process', null, ProductsStockUpload.processUpload)
            }
        });
    }

    var _processUpload = function(webReponse){
        $('#productsStockUploadProcessResultContainer').html(webReponse.data);
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
    ProductsStockUpload.init();
});