"use strict";
var ProductsBulkAdd = function () {
    var _init = function () {
        var _id = '#dropZoneProductsBulkAdd';
        var previewNode = $(_id + " .dropzone-item");
        previewNode.id = "";
        var previewTemplate = previewNode.parent('.dropzone-items').html();
        previewNode.remove();
        $(_id).dropzone({
            url: "/web/distributor/product/bulk/add/upload",
            paramName: "file",
            maxFiles: 1,
            maxFilesize: 100,
            addRemoveLinks: false,
            acceptedFiles: ".xlsx,.xls,.csv",
            createImageThumbnails: false,
            //previewTemplate: previewTemplate,
            //previewsContainer: _id + " .dropzone-items",
            //clickable: "#btnProductsBulkAdd",
            accept: function (file, done) {
                if (file.name == "justinbieber.jpg") {
                    done("Naha, you don't.");
                } else {
                    done();
                }
            },
            addedfile: function (file) {
                $("#dropZoneProductsBulkAdd").fadeOut();
                $("#dropZoneProductsBulkAddProgressContainer").fadeIn();
            },

            sending: function (file) {

            },
            totaluploadprogress: function (progress) {
                $("#dropZoneProductsBulkAddProgress").html('Uploading: '+ progress + " %");
                $("#dropZoneProductsBulkAddProgress").css('width', progress + "%");
                if(progress == 100){
                }

            },
            complete: function (progress) {
                $("#dropZoneProductsBulkAddProgress").html('Upload Completed Successfully');
                WebApp.post('/web/distributor/product/bulk/add/upload/process', null, ProductsBulkAdd.processUpload)
            }
        });
    }

    var _processUpload = function(webReponse){
        $('#productsBulkAddProcessResultContainer').html(webReponse.data);
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
    ProductsBulkAdd.init();
});